<?php

namespace App\Modules\AulasHorarios\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AulasHorarios\Services\{ReservasService, AulasService};
use App\Modules\AdministracionUsuariosSeguridad\Services\BitacoraService;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\{Validator, Auth};

class ReservasController extends Controller
{
    protected ReservasService $service;
    protected AulasService $aulasService;
    protected BitacoraService $bitacora;

    public function __construct(
        ReservasService $service,
        AulasService $aulasService,
        BitacoraService $bitacora
    ) {
        $this->service = $service;
        $this->aulasService = $aulasService;
        $this->bitacora = $bitacora;
    }

    // ========== API ==========
    
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['fecha', 'id_aula', 'id_usuario']);
        $items = $this->service->paginate($request->integer('per_page', 15), $filters);
        return response()->json(['success' => true, 'data' => $items]);
    }

    public function show($id): JsonResponse
    {
        $item = $this->service->find($id);
        if (!$item) return response()->json(['success' => false, 'message' => 'Reserva no encontrada'], 404);
        return response()->json(['success' => true, 'data' => $item]);
    }

    public function store(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'fecha' => 'required|date|after_or_equal:today',
            'hora_i' => 'required|date_format:H:i',
            'hora_f' => 'required|date_format:H:i|after:hora_i',
            'motivo' => 'required|string|max:500',
            'id_aula' => 'required|exists:aula,id_aula',
            'id_usuario' => 'required|exists:users,id',
        ]);

        if ($v->fails()) return response()->json(['success' => false, 'errors' => $v->errors()], 422);

        // Verificar conflictos
        $conflictos = $this->service->verificarConflictos(
            $request->id_aula,
            $request->fecha,
            $request->hora_i,
            $request->hora_f
        );

        if (count($conflictos) > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Se detectaron conflictos de horario',
                'conflictos' => $conflictos
            ], 422);
        }

        $item = $this->service->create($v->validated());
        return response()->json(['success' => true, 'message' => 'Reserva creada', 'data' => $item], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $exists = $this->service->find($id);
        if (!$exists) return response()->json(['success' => false, 'message' => 'Reserva no encontrada'], 404);

        $v = Validator::make($request->all(), [
            'fecha' => 'date|after_or_equal:today',
            'hora_i' => 'date_format:H:i',
            'hora_f' => 'date_format:H:i|after:hora_i',
            'motivo' => 'string|max:500',
            'id_aula' => 'exists:aula,id_aula',
        ]);

        if ($v->fails()) return response()->json(['success' => false, 'errors' => $v->errors()], 422);

        $validated = $v->validated();

        // Verificar conflictos si se cambian fecha u horarios
        if (isset($validated['fecha']) || isset($validated['hora_i']) || isset($validated['hora_f'])) {
            $conflictos = $this->service->verificarConflictos(
                $validated['id_aula'] ?? $exists->id_aula,
                $validated['fecha'] ?? $exists->fecha,
                $validated['hora_i'] ?? $exists->hora_i,
                $validated['hora_f'] ?? $exists->hora_f,
                $id
            );

            if (count($conflictos) > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Se detectaron conflictos de horario',
                    'conflictos' => $conflictos
                ], 422);
            }
        }

        $item = $this->service->update($id, $validated);
        return response()->json(['success' => true, 'message' => 'Reserva actualizada', 'data' => $item]);
    }

    public function destroy($id): JsonResponse
    {
        $reserva = $this->service->find($id);
        if (!$reserva) return response()->json(['success' => false, 'message' => 'Reserva no encontrada'], 404);

        $this->service->delete($id);
        return response()->json(['success' => true, 'message' => 'Reserva eliminada']);
    }

    /**
     * API: Verificar conflictos antes de guardar
     */
    public function verificarConflictosAPI(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'fecha' => 'required|date',
            'hora_i' => 'required|date_format:H:i',
            'hora_f' => 'required|date_format:H:i|after:hora_i',
            'id_aula' => 'required|exists:aula,id_aula',
            'id_reserva' => 'nullable|exists:reserva,id_reserva',
        ]);

        if ($v->fails()) return response()->json(['success' => false, 'errors' => $v->errors()], 422);

        $conflictos = $this->service->verificarConflictos(
            $request->id_aula,
            $request->fecha,
            $request->hora_i,
            $request->hora_f,
            $request->id_reserva
        );

        return response()->json([
            'success' => true,
            'tiene_conflictos' => count($conflictos) > 0,
            'conflictos' => $conflictos
        ]);
    }

    /**
     * API: Obtener disponibilidad de aula en fecha específica
     */
    public function getDisponibilidadAPI(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'id_aula' => 'required|exists:aula,id_aula',
            'fecha' => 'required|date',
        ]);

        if ($v->fails()) return response()->json(['success' => false, 'errors' => $v->errors()], 422);

        $disponibilidad = $this->service->getDisponibilidadAula($request->id_aula, $request->fecha);

        return response()->json([
            'success' => true,
            'data' => $disponibilidad
        ]);
    }

    // ========== WEB ==========
    
    /**
     * Vista principal de gestión de reservas
     */
    public function vistaReservas(Request $request)
    {
        // Filtros opcionales
        $filters = [];
        if ($request->filled('fecha')) {
            $filters['fecha'] = $request->fecha;
        }
        if ($request->filled('aula')) {
            $filters['id_aula'] = $request->aula;
        }

        // Obtener reservas con filtros
        $reservas = $this->service->all($filters);

        // Obtener todas las aulas para el selector
        $aulas = $this->aulasService->all();

        // Calcular estadísticas
        $totalReservas = $reservas->count();
        $reservasHoy = $reservas->filter(function ($r) {
            return $r->fecha->isToday();
        })->count();
        $proximasReservas = $reservas->filter(function ($r) {
            return $r->fecha->isFuture();
        })->count();

        return view('reservas', compact('reservas', 'aulas', 'totalReservas', 'reservasHoy', 'proximasReservas'));
    }

    /**
     * Crear reserva desde formulario web
     */
    public function storeWeb(Request $request)
    {
        $v = Validator::make($request->all(), [
            'fecha' => 'required|date|after_or_equal:today',
            'hora_i' => 'required|date_format:H:i',
            'hora_f' => 'required|date_format:H:i|after:hora_i',
            'motivo' => 'required|string|max:500',
            'id_aula' => 'required|exists:aula,id_aula',
        ], [
            'fecha.required' => 'La fecha es obligatoria',
            'fecha.after_or_equal' => 'La fecha debe ser hoy o posterior',
            'hora_i.required' => 'La hora de inicio es obligatoria',
            'hora_f.required' => 'La hora de fin es obligatoria',
            'hora_f.after' => 'La hora de fin debe ser posterior a la hora de inicio',
            'motivo.required' => 'El motivo de la reserva es obligatorio',
            'id_aula.required' => 'Debe seleccionar un aula',
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v)->withInput();
        }

        // Verificar conflictos
        $conflictos = $this->service->verificarConflictos(
            $request->id_aula,
            $request->fecha,
            $request->hora_i,
            $request->hora_f
        );

        if (count($conflictos) > 0) {
            return redirect()->back()
                ->withErrors(['conflictos' => $conflictos])
                ->withInput();
        }

        // Crear reserva
        $data = $v->validated();
        $data['id_usuario'] = Auth::id();
        
        $reserva = $this->service->create($data);

        // Registrar en bitácora
        $aula = $this->aulasService->find($request->id_aula);
        $this->bitacora->registrar(
            "Reservó Aula {$aula->nro_aula} - Módulo {$aula->modulo} para el {$request->fecha} de {$request->hora_i} a {$request->hora_f}",
            Auth::id()
        );

        return redirect()->route('reservas.vista')->with('success', 'Reserva creada exitosamente');
    }

    /**
     * Actualizar reserva desde formulario web
     */
    public function updateWeb(Request $request, $id)
    {
        $reserva = $this->service->find($id);
        if (!$reserva) {
            return redirect()->back()->with('error', 'Reserva no encontrada');
        }

        $v = Validator::make($request->all(), [
            'fecha' => 'required|date|after_or_equal:today',
            'hora_i' => 'required|date_format:H:i',
            'hora_f' => 'required|date_format:H:i|after:hora_i',
            'motivo' => 'required|string|max:500',
            'id_aula' => 'required|exists:aula,id_aula',
        ]);

        if ($v->fails()) {
            return redirect()->back()->withErrors($v)->withInput();
        }

        // Verificar conflictos
        $conflictos = $this->service->verificarConflictos(
            $request->id_aula,
            $request->fecha,
            $request->hora_i,
            $request->hora_f,
            $id
        );

        if (count($conflictos) > 0) {
            return redirect()->back()
                ->withErrors(['conflictos' => $conflictos])
                ->withInput();
        }

        $this->service->update($id, $v->validated());

        // Registrar en bitácora
        $aula = $this->aulasService->find($request->id_aula);
        $this->bitacora->registrar(
            "Actualizó reserva de Aula {$aula->nro_aula} - Módulo {$aula->modulo}",
            Auth::id()
        );

        return redirect()->route('reservas.vista')->with('success', 'Reserva actualizada exitosamente');
    }

    /**
     * Eliminar reserva
     */
    public function destroyWeb($id)
    {
        $reserva = $this->service->find($id);
        if (!$reserva) {
            return redirect()->back()->with('error', 'Reserva no encontrada');
        }

        $aula = $reserva->aula;
        $this->service->delete($id);

        // Registrar en bitácora
        $this->bitacora->registrar(
            "Eliminó reserva de Aula {$aula->nro_aula} - Módulo {$aula->modulo} del {$reserva->fecha->format('d/m/Y')}",
            Auth::id()
        );

        return redirect()->route('reservas.vista')->with('success', 'Reserva eliminada exitosamente');
    }
}
