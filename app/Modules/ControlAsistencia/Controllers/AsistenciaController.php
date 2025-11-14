<?php

namespace App\Modules\ControlAsistencia\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\ControlAsistencia\Services\AsistenciaService;
use App\Modules\ControlAsistencia\Models\Asistencia;
use App\Modules\AdministracionUsuariosSeguridad\Services\BitacoraService;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\{Validator, Auth};
use Carbon\Carbon;

class AsistenciaController extends Controller
{
    protected AsistenciaService $service;
    protected BitacoraService $bitacora;

    public function __construct(
        AsistenciaService $service,
        BitacoraService $bitacora
    ) {
        $this->service = $service;
        $this->bitacora = $bitacora;
    }

    // ========== API ==========
    
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['fecha_registro', 'tipo', 'id_horario', 'id_docente']);
        $items = $this->service->paginate($request->integer('per_page', 15), $filters);
        return response()->json(['success' => true, 'data' => $items]);
    }

    public function show($id): JsonResponse
    {
        $item = $this->service->find($id);
        if (!$item) return response()->json(['success' => false, 'message' => 'Asistencia no encontrada'], 404);
        return response()->json(['success' => true, 'data' => $item]);
    }

    public function store(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'fecha_registro' => 'required|date',
            'tipo' => 'required|in:' . implode(',', Asistencia::getTiposAsistencia()),
            'id_horario' => 'required|exists:horario,id_horario',
        ]);

        if ($v->fails()) {
            return response()->json(['success' => false, 'errors' => $v->errors()], 422);
        }

        // Verificar si ya existe asistencia para ese horario en esa fecha
        if ($this->service->existeAsistencia($request->id_horario, $request->fecha_registro)) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe una asistencia registrada para este horario en esta fecha'
            ], 422);
        }

        $item = $this->service->create($v->validated());

        return response()->json([
            'success' => true,
            'message' => 'Asistencia registrada exitosamente',
            'data' => $item
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $item = $this->service->find($id);
        if (!$item) return response()->json(['success' => false, 'message' => 'Asistencia no encontrada'], 404);

        $v = Validator::make($request->all(), [
            'fecha_registro' => 'sometimes|date',
            'tipo' => 'sometimes|in:' . implode(',', Asistencia::getTiposAsistencia()),
            'id_horario' => 'sometimes|exists:horario,id_horario',
        ]);

        if ($v->fails()) {
            return response()->json(['success' => false, 'errors' => $v->errors()], 422);
        }

        $this->service->update($id, $v->validated());

        return response()->json([
            'success' => true,
            'message' => 'Asistencia actualizada exitosamente'
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $item = $this->service->find($id);
        if (!$item) return response()->json(['success' => false, 'message' => 'Asistencia no encontrada'], 404);

        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Asistencia eliminada exitosamente'
        ]);
    }

    /**
     * API: Obtener horarios programados para una fecha
     */
    public function getHorariosPorFecha(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'fecha' => 'required|date',
        ]);

        if ($v->fails()) {
            return response()->json(['success' => false, 'errors' => $v->errors()], 422);
        }

        $horarios = $this->service->getHorariosPorFecha($request->fecha);
        $horariosSinAsistencia = $this->service->getHorariosSinAsistencia($request->fecha);

        return response()->json([
            'success' => true,
            'data' => [
                'horarios' => $horarios,
                'horarios_sin_asistencia' => $horariosSinAsistencia
            ]
        ]);
    }

    /**
     * API: Obtener estadísticas
     */
    public function getEstadisticas(Request $request): JsonResponse
    {
        $filters = $request->only(['fecha_desde', 'fecha_hasta']);
        $estadisticas = $this->service->getEstadisticas($filters);

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }

    // ========== WEB ==========

    /**
     * Vista principal de asistencias
     */
    public function vistaAsistencia(Request $request)
    {
        // Filtros opcionales
        $filters = [];
        if ($request->filled('fecha')) {
            $filters['fecha_registro'] = $request->fecha;
        }
        if ($request->filled('tipo')) {
            $filters['tipo'] = $request->tipo;
        }
        if ($request->filled('docente')) {
            $filters['id_docente'] = $request->docente;
        }

        // Obtener asistencias con filtros
        $asistencias = $this->service->all($filters);

        // Obtener horarios del día actual
        $fechaHoy = Carbon::today()->toDateString();
        $horariosHoy = $this->service->getHorariosPorFecha($fechaHoy);
        $horariosSinAsistencia = $this->service->getHorariosSinAsistencia($fechaHoy);

        // Obtener tipos de asistencia
        $tiposAsistencia = Asistencia::getTiposAsistencia();

        // Calcular estadísticas
        $totalAsistencias = $asistencias->count();
        $asistenciasHoy = $asistencias->filter(function ($a) use ($fechaHoy) {
            return $a->fecha_registro->toDateString() === $fechaHoy;
        })->count();
        $pendientes = $horariosSinAsistencia->count();

        return view('asistencia', compact(
            'asistencias',
            'horariosHoy',
            'horariosSinAsistencia',
            'tiposAsistencia',
            'totalAsistencias',
            'asistenciasHoy',
            'pendientes'
        ));
    }

    /**
     * Crear asistencia desde formulario web
     */
    public function storeWeb(Request $request)
    {
        $v = Validator::make($request->all(), [
            'fecha_registro' => 'required|date',
            'tipo' => 'required|in:' . implode(',', Asistencia::getTiposAsistencia()),
            'id_horario' => 'required|exists:horario,id_horario',
        ], [
            'fecha_registro.required' => 'La fecha es obligatoria',
            'tipo.required' => 'El tipo de asistencia es obligatorio',
            'tipo.in' => 'El tipo de asistencia no es válido',
            'id_horario.required' => 'Debe seleccionar un horario',
            'id_horario.exists' => 'El horario seleccionado no existe',
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v)->withInput();
        }

        // Verificar si ya existe asistencia
        if ($this->service->existeAsistencia($request->id_horario, $request->fecha_registro)) {
            return redirect()->back()
                ->withErrors(['error' => 'Ya existe una asistencia registrada para este horario en esta fecha'])
                ->withInput();
        }

        // Crear asistencia
        $asistencia = $this->service->create($v->validated());

        // Obtener datos del horario para la bitácora
        $horario = $asistencia->horario;
        $docente = $horario->carga?->docente?->usuario?->nombre ?? 'Docente desconocido';
        $materia = $horario->carga?->grupo?->materia?->nombre ?? 'Materia desconocida';

        // Registrar en bitácora
        $this->bitacora->registrar(
            "Registró asistencia '{$request->tipo}' para {$docente} - {$materia} el {$request->fecha_registro}",
            Auth::id()
        );

        return redirect()->route('asistencia.vista')->with('success', 'Asistencia registrada exitosamente');
    }

    /**
     * Actualizar asistencia desde formulario web
     */
    public function updateWeb(Request $request, $id)
    {
        $asistencia = $this->service->find($id);
        if (!$asistencia) {
            return redirect()->route('asistencia.vista')->withErrors(['error' => 'Asistencia no encontrada']);
        }

        $v = Validator::make($request->all(), [
            'fecha_registro' => 'required|date',
            'tipo' => 'required|in:' . implode(',', Asistencia::getTiposAsistencia()),
            'id_horario' => 'required|exists:horario,id_horario',
        ], [
            'fecha_registro.required' => 'La fecha es obligatoria',
            'tipo.required' => 'El tipo de asistencia es obligatorio',
            'id_horario.required' => 'Debe seleccionar un horario',
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v)->withInput();
        }

        $this->service->update($id, $v->validated());

        // Registrar en bitácora
        $horario = $asistencia->horario;
        $docente = $horario->carga?->docente?->usuario?->nombre ?? 'Docente';
        
        $this->bitacora->registrar(
            "Actualizó asistencia de {$docente} a '{$request->tipo}'",
            Auth::id()
        );

        return redirect()->route('asistencia.vista')->with('success', 'Asistencia actualizada exitosamente');
    }

    /**
     * Eliminar asistencia desde web
     */
    public function destroyWeb($id)
    {
        $asistencia = $this->service->find($id);
        if (!$asistencia) {
            return redirect()->route('asistencia.vista')->withErrors(['error' => 'Asistencia no encontrada']);
        }

        $horario = $asistencia->horario;
        $docente = $horario->carga?->docente?->usuario?->nombre ?? 'Docente';
        $fecha = $asistencia->fecha_registro->format('d/m/Y');
        
        $this->service->delete($id);

        // Registrar en bitácora
        $this->bitacora->registrar(
            "Eliminó asistencia de {$docente} del {$fecha}",
            Auth::id()
        );

        return redirect()->route('asistencia.vista')->with('success', 'Asistencia eliminada exitosamente');
    }
}
