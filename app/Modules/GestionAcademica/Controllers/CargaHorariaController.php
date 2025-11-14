<?php

namespace App\Modules\GestionAcademica\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\GestionAcademica\Services\{CargaHorariaService, DocentesService, GruposService};
use App\Modules\AdministracionUsuariosSeguridad\Services\BitacoraService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CargaHorariaController extends Controller
{
    protected BitacoraService $bitacora;

    public function __construct(
        protected CargaHorariaService $service,
        protected DocentesService $docentesService,
        protected GruposService $gruposService,
        BitacoraService $bitacora
    ) {
        $this->bitacora = $bitacora;
    }

    // ========== API ==========
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['id_docente','id_grupo','id_gestion']);
        $items = $this->service->paginate($request->integer('per_page', 15), $filters);
        return response()->json(['success'=>true,'data'=>$items]);
    }

    public function show($id): JsonResponse
    {
        $item = $this->service->find($id);
        if (!$item) return response()->json(['success'=>false,'message'=>'Registro no encontrado'],404);
        return response()->json(['success'=>true,'data'=>$item]);
    }

    public function store(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'horas_asignadas' => 'required|integer|min:1',
            'id_docente' => 'required|exists:docente,id_docente',
            'id_grupo' => 'required|exists:grupo,id_grupo',
            'id_gestion' => 'nullable|integer',
        ]);
        if ($v->fails()) return response()->json(['success'=>false,'errors'=>$v->errors()],422);
        $item = $this->service->create($v->validated());
        return response()->json(['success'=>true,'message'=>'Carga horaria creada','data'=>$item],201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $exists = $this->service->find($id);
        if (!$exists) return response()->json(['success'=>false,'message'=>'Registro no encontrado'],404);
        $v = Validator::make($request->all(), [
            'horas_asignadas' => 'integer|min:1',
            'id_docente' => 'exists:docente,id_docente',
            'id_grupo' => 'exists:grupo,id_grupo',
            'id_gestion' => 'nullable|integer',
        ]);
        if ($v->fails()) return response()->json(['success'=>false,'errors'=>$v->errors()],422);
        $item = $this->service->update($id, $v->validated());
        return response()->json(['success'=>true,'message'=>'Carga horaria actualizada','data'=>$item]);
    }

    public function destroy($id): JsonResponse
    {
        if (!$this->service->delete($id)) return response()->json(['success'=>false,'message'=>'Registro no encontrado'],404);
        return response()->json(['success'=>true,'message'=>'Carga horaria eliminada']);
    }

    // ========== WEB - CU10: Asignar Grupos a Docentes ==========

    /**
     * Vista principal para asignar grupos a docentes
     */
    public function vistaAsignarGrupos(Request $request)
    {
        $docenteSeleccionado = $request->get('id_docente');
        
        // Obtener todos los docentes activos con sus usuarios
        $docentes = $this->docentesService->all([]);
        
        // Obtener grupos activos con sus materias
        $gruposDisponibles = $this->gruposService->all(['estado' => true]);
        
        // Si hay un docente seleccionado, obtener sus asignaciones actuales
        $asignacionesActuales = [];
        $docenteInfo = null;
        
        if ($docenteSeleccionado) {
            $docenteInfo = $this->docentesService->find($docenteSeleccionado);
            $asignacionesActuales = $this->service->all(['id_docente' => $docenteSeleccionado]);
        }
        
        return view('asignar_grupos', compact(
            'docentes', 
            'gruposDisponibles', 
            'docenteSeleccionado',
            'docenteInfo',
            'asignacionesActuales'
        ));
    }

    /**
     * Asignar un grupo a un docente (Web)
     */
    public function asignarGrupoWeb(Request $request)
    {
        $v = Validator::make($request->all(), [
            'id_docente' => 'required|exists:docente,id_docente',
            'id_grupo' => 'required|exists:grupo,id_grupo',
            'horas_asignadas' => 'required|integer|min:1|max:40',
            'id_gestion' => 'nullable|integer',
        ], [
            'id_docente.required' => 'Debe seleccionar un docente.',
            'id_grupo.required' => 'Debe seleccionar un grupo.',
            'horas_asignadas.required' => 'Las horas asignadas son obligatorias.',
            'horas_asignadas.min' => 'Las horas asignadas deben ser al menos 1.',
            'horas_asignadas.max' => 'Las horas asignadas no pueden exceder 40.',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        try {
            // Verificar si ya existe la asignación
            $existe = \App\Modules\GestionAcademica\Models\CargaHoraria::where('id_docente', $request->id_docente)
                ->where('id_grupo', $request->id_grupo)
                ->exists();

            if ($existe) {
                return back()->withErrors(['error' => 'Este grupo ya está asignado a este docente.'])->withInput();
            }

            $data = $v->validated();
            $carga = $this->service->create($data);

            // Obtener información para la bitácora
            $docente = $this->docentesService->find($request->id_docente);
            $grupo = $this->gruposService->find($request->id_grupo);

            // Registrar en bitácora
            $usuario = Auth::user();
            if ($usuario) {
                $this->bitacora->registrar(
                    "{$usuario->nombre} asignó el grupo {$grupo->codigo} ({$grupo->materia->nombre}) al docente {$docente->usuario->nombre} con {$data['horas_asignadas']} horas",
                    $usuario->id_usuario
                );
            }

            return redirect()
                ->route('carga-horaria.asignar')
                ->with('success', 'Grupo asignado exitosamente al docente.');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Error al asignar el grupo: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Eliminar asignación de grupo a docente (Web)
     */
    public function eliminarAsignacionWeb($id)
    {
        try {
            $carga = $this->service->find($id);
            
            if (!$carga) {
                return back()->with('error', 'Asignación no encontrada.');
            }

            // Guardar info para bitácora antes de eliminar
            $docenteNombre = $carga->docente->usuario->nombre ?? 'Desconocido';
            $grupoInfo = $carga->grupo->codigo . ' (' . $carga->grupo->materia->nombre . ')';

            $this->service->delete($id);

            // Registrar en bitácora
            $usuario = Auth::user();
            if ($usuario) {
                $this->bitacora->registrar(
                    "{$usuario->nombre} eliminó la asignación del grupo {$grupoInfo} del docente {$docenteNombre}",
                    $usuario->id_usuario
                );
            }

            return back()->with('success', 'Asignación eliminada correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la asignación: ' . $e->getMessage());
        }
    }

    /**
     * Vista para ver todas las asignaciones (tabla completa)
     */
    public function vistaAsignaciones(Request $request)
    {
        $buscar = $request->get('buscar');
        $filters = [];

        if ($buscar) {
            // Aquí podrías filtrar por nombre de docente o código de grupo
            $filters['buscar'] = $buscar;
        }

        $asignaciones = $this->service->paginate(15, $filters);

        return view('asignaciones_grupos', compact('asignaciones'));
    }
}

