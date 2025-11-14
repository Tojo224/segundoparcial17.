<?php

namespace App\Modules\AulasHorarios\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AulasHorarios\Services\{HorariosService, AulasService};
use App\Modules\GestionAcademica\Services\CargaHorariaService;
use App\Modules\AdministracionUsuariosSeguridad\Services\BitacoraService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class HorariosController extends Controller
{
    protected HorariosService $service;
    protected AulasService $aulasService;
    protected CargaHorariaService $cargaService;
    protected BitacoraService $bitacora;

    public function __construct(
        HorariosService $service,
        AulasService $aulasService,
        CargaHorariaService $cargaService,
        BitacoraService $bitacora
    ) {
        $this->service = $service;
        $this->aulasService = $aulasService;
        $this->cargaService = $cargaService;
        $this->bitacora = $bitacora;
    }

    // ========== API ==========
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['dia', 'id_carga', 'id_aula']);
        $items = $this->service->paginate($request->integer('per_page', 15), $filters);
        return response()->json(['success' => true, 'data' => $items]);
    }

    public function show($id): JsonResponse
    {
        $item = $this->service->find($id);
        if (!$item) return response()->json(['success' => false, 'message' => 'Horario no encontrado'], 404);
        return response()->json(['success' => true, 'data' => $item]);
    }

    public function store(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'dia' => 'required|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado',
            'hora_i' => 'required|date_format:H:i',
            'hora_f' => 'required|date_format:H:i|after:hora_i',
            'id_carga' => 'required|exists:carga_horaria,id_carga',
            'id_aula' => 'required|exists:aula,id_aula',
        ]);

        if ($v->fails()) return response()->json(['success' => false, 'errors' => $v->errors()], 422);

        // Verificar conflictos
        $conflictos = $this->service->verificarConflictos(
            $request->dia,
            $request->hora_i,
            $request->hora_f,
            $request->id_aula,
            $request->id_carga
        );

        if (count($conflictos) > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Se detectaron conflictos de horario',
                'conflictos' => $conflictos
            ], 422);
        }

        $item = $this->service->create($v->validated());
        return response()->json(['success' => true, 'message' => 'Horario creado', 'data' => $item], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $exists = $this->service->find($id);
        if (!$exists) return response()->json(['success' => false, 'message' => 'Horario no encontrado'], 404);

        $v = Validator::make($request->all(), [
            'dia' => 'in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado',
            'hora_i' => 'date_format:H:i',
            'hora_f' => 'date_format:H:i|after:hora_i',
            'id_carga' => 'exists:carga_horaria,id_carga',
            'id_aula' => 'exists:aula,id_aula',
        ]);

        if ($v->fails()) return response()->json(['success' => false, 'errors' => $v->errors()], 422);

        $item = $this->service->update($id, $v->validated());
        return response()->json(['success' => true, 'message' => 'Horario actualizado', 'data' => $item]);
    }

    public function destroy($id): JsonResponse
    {
        if (!$this->service->delete($id)) {
            return response()->json(['success' => false, 'message' => 'Horario no encontrado'], 404);
        }
        return response()->json(['success' => true, 'message' => 'Horario eliminado']);
    }

    // ========== WEB - CU11: Gestionar Horarios ==========

    /**
     * Vista principal del calendario semanal de horarios
     */
    public function vistaCalendario(Request $request)
    {
        // Filtros opcionales
        $filters = [];
        
        // Obtener horarios en formato semanal
        $horariosSemanal = $this->service->getHorariosSemanal($filters);
        
        // Obtener aulas y cargas para los selectores
        $aulas = $this->aulasService->all([]);
        $cargas = $this->cargaService->all([]);
        
        return view('horarios_calendario', compact('horariosSemanal', 'aulas', 'cargas'));
    }

    /**
     * Crear horario desde la vista web con validación de conflictos
     */
    public function storeWeb(Request $request)
    {
        $v = Validator::make($request->all(), [
            'dia' => 'required|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado',
            'hora_i' => 'required|date_format:H:i',
            'hora_f' => 'required|date_format:H:i|after:hora_i',
            'id_carga' => 'required|exists:carga_horaria,id_carga',
            'id_aula' => 'required|exists:aula,id_aula',
        ], [
            'dia.required' => 'El día de la semana es obligatorio.',
            'dia.in' => 'Día de semana inválido.',
            'hora_i.required' => 'La hora de inicio es obligatoria.',
            'hora_i.date_format' => 'Formato de hora inicio inválido (HH:MM).',
            'hora_f.required' => 'La hora de fin es obligatoria.',
            'hora_f.date_format' => 'Formato de hora fin inválido (HH:MM).',
            'hora_f.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
            'id_carga.required' => 'Debe seleccionar una asignación docente-grupo.',
            'id_aula.required' => 'Debe seleccionar un aula.',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        try {
            // Verificar conflictos
            $conflictos = $this->service->verificarConflictos(
                $request->dia,
                $request->hora_i,
                $request->hora_f,
                $request->id_aula,
                $request->id_carga
            );

            if (count($conflictos) > 0) {
                $mensajes = array_map(fn($c) => $c['mensaje'], $conflictos);
                return back()
                    ->withErrors(['conflictos' => $mensajes])
                    ->withInput()
                    ->with('conflictos_detalle', $conflictos);
            }

            $horario = $this->service->create($v->validated());

            // Obtener info para bitácora
            $carga = $horario->carga;
            $aula = $horario->aula;
            $docente = $carga->docente->usuario->nombre ?? 'Desconocido';
            $materia = $carga->grupo->materia->nombre ?? 'Desconocida';

            // Registrar en bitácora
            $usuario = Auth::user();
            if ($usuario) {
                $this->bitacora->registrar(
                    "{$usuario->nombre} creó horario: {$materia} - {$docente} - {$horario->dia} {$horario->hora_i}-{$horario->hora_f} en Aula {$aula->nro_aula}",
                    $usuario->id_usuario
                );
            }

            return redirect()
                ->route('horarios.calendario')
                ->with('success', 'Horario creado exitosamente.');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Error al crear el horario: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Actualizar horario desde la vista web
     */
    public function updateWeb(Request $request, $id)
    {
        $horario = $this->service->find($id);
        if (!$horario) {
            return back()->with('error', 'Horario no encontrado.');
        }

        $v = Validator::make($request->all(), [
            'dia' => 'required|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado',
            'hora_i' => 'required|date_format:H:i',
            'hora_f' => 'required|date_format:H:i|after:hora_i',
            'id_carga' => 'required|exists:carga_horaria,id_carga',
            'id_aula' => 'required|exists:aula,id_aula',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        try {
            // Verificar conflictos (excluyendo el horario actual)
            $conflictos = $this->service->verificarConflictos(
                $request->dia,
                $request->hora_i,
                $request->hora_f,
                $request->id_aula,
                $request->id_carga,
                $id
            );

            if (count($conflictos) > 0) {
                $mensajes = array_map(fn($c) => $c['mensaje'], $conflictos);
                return back()
                    ->withErrors(['conflictos' => $mensajes])
                    ->withInput()
                    ->with('conflictos_detalle', $conflictos);
            }

            $horarioActualizado = $this->service->update($id, $v->validated());

            // Bitácora
            $usuario = Auth::user();
            if ($usuario) {
                $this->bitacora->registrar(
                    "{$usuario->nombre} actualizó horario ID:{$id}",
                    $usuario->id_usuario
                );
            }

            return redirect()
                ->route('horarios.calendario')
                ->with('success', 'Horario actualizado correctamente.');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Eliminar horario
     */
    public function destroyWeb($id)
    {
        $horario = $this->service->find($id);
        if (!$horario) {
            return back()->with('error', 'Horario no encontrado.');
        }

        try {
            $this->service->delete($id);

            // Bitácora
            $usuario = Auth::user();
            if ($usuario) {
                $this->bitacora->registrar(
                    "{$usuario->nombre} eliminó horario ID:{$id}",
                    $usuario->id_usuario
                );
            }

            return back()->with('success', 'Horario eliminado correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    /**
     * API para verificar conflictos (AJAX)
     */
    public function verificarConflictosAPI(Request $request): JsonResponse
    {
        $conflictos = $this->service->verificarConflictos(
            $request->dia,
            $request->hora_i,
            $request->hora_f,
            $request->id_aula,
            $request->id_carga,
            $request->id_horario_excluir
        );

        return response()->json([
            'tiene_conflictos' => count($conflictos) > 0,
            'conflictos' => $conflictos
        ]);
    }
}
