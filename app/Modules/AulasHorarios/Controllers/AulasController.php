<?php

namespace App\Modules\AulasHorarios\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AulasHorarios\Services\AulasService;
use App\Modules\AulasHorarios\Models\Aula;
use App\Modules\AdministracionUsuariosSeguridad\Services\BitacoraService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AulasController extends Controller
{
    protected AulasService $service;
    protected BitacoraService $bitacora;

    public function __construct(
        AulasService $service,
        BitacoraService $bitacora
    ) {
        $this->service = $service;
        $this->bitacora = $bitacora;
    }

    // ========== API ==========
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['nro_aula', 'modulo']);
        $items = $this->service->paginate($request->integer('per_page', 15), $filters);
        return response()->json(['success' => true, 'data' => $items]);
    }

    public function show($id): JsonResponse
    {
        $item = $this->service->find($id);
        if (!$item) return response()->json(['success' => false, 'message' => 'Aula no encontrada'], 404);
        return response()->json(['success' => true, 'data' => $item]);
    }

    public function store(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'nro_aula' => 'required|string|max:20|unique:aula,nro_aula',
            'modulo' => 'required|string|max:10',
        ]);

        if ($v->fails()) return response()->json(['success' => false, 'errors' => $v->errors()], 422);

        $item = $this->service->create($v->validated());
        return response()->json(['success' => true, 'message' => 'Aula creada', 'data' => $item], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $exists = $this->service->find($id);
        if (!$exists) return response()->json(['success' => false, 'message' => 'Aula no encontrada'], 404);

        $v = Validator::make($request->all(), [
            'nro_aula' => 'string|max:20|unique:aula,nro_aula,' . $id . ',id_aula',
            'modulo' => 'string|max:10',
        ]);

        if ($v->fails()) return response()->json(['success' => false, 'errors' => $v->errors()], 422);

        $item = $this->service->update($id, $v->validated());
        return response()->json(['success' => true, 'message' => 'Aula actualizada', 'data' => $item]);
    }

    public function destroy($id): JsonResponse
    {
        // Verificar si el aula tiene horarios asignados
        $aula = $this->service->find($id);
        if (!$aula) return response()->json(['success' => false, 'message' => 'Aula no encontrada'], 404);

        if ($aula->horarios()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el aula porque tiene horarios asignados'
            ], 422);
        }

        $this->service->delete($id);
        return response()->json(['success' => true, 'message' => 'Aula eliminada']);
    }

    // ========== WEB ==========
    
    /**
     * Vista principal de gestión de aulas
     */
    public function vistaAulas(Request $request)
    {
        // Aplicar filtros desde query params
        $query = Aula::query();
        
        // Filtro por número de aula
        if ($request->filled('aula')) {
            $query->where('nro_aula', 'ILIKE', '%' . $request->aula . '%');
        }
        
        // Filtro por módulo
        if ($request->filled('modulo')) {
            $query->where('modulo', 'ILIKE', '%' . $request->modulo . '%');
        }
        
        // Obtener aulas con conteo de horarios
        $aulas = $query->withCount('horarios')->get();
        
        // Agregar información de ocupación para cada aula
        $aulas = $aulas->map(function ($aula) {
            // Contar cuántos horarios tiene asignados
            $horariosCount = $aula->horarios()->count();
            $aula->horarios_count = $horariosCount;
            
            // Determinar disponibilidad (si tiene menos de 30 horarios, está disponible)
            // 6 días x 5 bloques = 30 horarios máximos por semana
            $aula->disponible = $horariosCount < 30;
            
            return $aula;
        });
        
        return view('aulas', compact('aulas'));
    }

    /**
     * Crear aula desde la vista web
     */
    public function storeWeb(Request $request)
    {
        $v = Validator::make($request->all(), [
            'nro_aula' => 'required|string|max:20|unique:aula,nro_aula',
            'modulo' => 'required|string|max:10',
        ], [
            'nro_aula.required' => 'El número de aula es obligatorio.',
            'nro_aula.unique' => 'Ya existe un aula con este número.',
            'nro_aula.max' => 'El número de aula no puede exceder 20 caracteres.',
            'modulo.required' => 'El módulo es obligatorio.',
            'modulo.max' => 'El módulo no puede exceder 10 caracteres.',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        try {
            $aula = $this->service->create($v->validated());

            // Bitácora
            $usuario = Auth::user();
            if ($usuario) {
                $this->bitacora->registrar(
                    "{$usuario->nombre} creó el aula {$aula->nro_aula} - Módulo {$aula->modulo}",
                    $usuario->id_usuario
                );
            }

            return redirect()
                ->route('aulas.vista')
                ->with('success', 'Aula creada exitosamente.');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Error al crear el aula: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Actualizar aula desde la vista web
     */
    public function updateWeb(Request $request, $id)
    {
        $aula = $this->service->find($id);
        if (!$aula) {
            return back()->with('error', 'Aula no encontrada.');
        }

        $v = Validator::make($request->all(), [
            'nro_aula' => 'required|string|max:20|unique:aula,nro_aula,' . $id . ',id_aula',
            'modulo' => 'required|string|max:10',
        ], [
            'nro_aula.required' => 'El número de aula es obligatorio.',
            'nro_aula.unique' => 'Ya existe un aula con este número.',
            'modulo.required' => 'El módulo es obligatorio.',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        try {
            $aulaActualizada = $this->service->update($id, $v->validated());

            // Bitácora
            $usuario = Auth::user();
            if ($usuario) {
                $this->bitacora->registrar(
                    "{$usuario->nombre} actualizó el aula ID:{$id}",
                    $usuario->id_usuario
                );
            }

            return redirect()
                ->route('aulas.vista')
                ->with('success', 'Aula actualizada correctamente.');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Eliminar aula
     */
    public function destroyWeb($id)
    {
        $aula = $this->service->find($id);
        if (!$aula) {
            return back()->with('error', 'Aula no encontrada.');
        }

        // Verificar si tiene horarios asignados
        if ($aula->horarios()->count() > 0) {
            return back()->with('error', 'No se puede eliminar el aula porque tiene horarios asignados.');
        }

        try {
            $nroAula = $aula->nro_aula;
            $this->service->delete($id);

            // Bitácora
            $usuario = Auth::user();
            if ($usuario) {
                $this->bitacora->registrar(
                    "{$usuario->nombre} eliminó el aula {$nroAula}",
                    $usuario->id_usuario
                );
            }

            return back()->with('success', 'Aula eliminada correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    /**
     * API para verificar disponibilidad de un aula
     */
    public function verificarDisponibilidadAPI(Request $request, $id): JsonResponse
    {
        $aula = $this->service->find($id);
        if (!$aula) {
            return response()->json(['success' => false, 'message' => 'Aula no encontrada'], 404);
        }

        $horariosAsignados = $aula->horarios()
            ->with(['carga.docente.usuario', 'carga.grupo.materia'])
            ->orderBy('dia')
            ->orderBy('hora_i')
            ->get();

        $horariosCount = $horariosAsignados->count();
        $disponible = $horariosCount < 30;

        return response()->json([
            'success' => true,
            'aula' => [
                'id_aula' => $aula->id_aula,
                'nro_aula' => $aula->nro_aula,
                'modulo' => $aula->modulo,
                'horarios_asignados' => $horariosCount,
                'disponible' => $disponible,
                'ocupacion_porcentaje' => round(($horariosCount / 30) * 100, 1)
            ],
            'horarios' => $horariosAsignados
        ]);
    }
}
