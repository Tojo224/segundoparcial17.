<?php

namespace App\Modules\GestionAcademica\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\GestionAcademica\Services\GruposService;
use App\Modules\AdministracionUsuariosSeguridad\Services\BitacoraService;
use App\Modules\GestionAcademica\Models\Materia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GruposController extends Controller
{
    protected GruposService $service;
    protected BitacoraService $bitacora;

    public function __construct(GruposService $service, BitacoraService $bitacora)
    {
        $this->service = $service;
        $this->bitacora = $bitacora;
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['codigo','estado','id_materia']);
        $items = $this->service->paginate($request->integer('per_page', 15), $filters);
        return response()->json(['success'=>true,'data'=>$items]);
    }

    public function show($id): JsonResponse
    {
        $item = $this->service->find($id);
        if (!$item) return response()->json(['success'=>false,'message'=>'Grupo no encontrado'],404);
        return response()->json(['success'=>true,'data'=>$item]);
    }

    // -------- WEB --------
    public function vistaGrupos(Request $request)
    {
        $buscar = $request->get('buscar');
        $filters = [];

        // Si se proporciona búsqueda, buscar por nombre o sigla de materia
        if ($buscar) {
            $filters['buscar_materia'] = $buscar;
        }

        // Mostrar todos los grupos (activos e inactivos)
        $grupos = $this->service->paginate(10, $filters);
        $materias = Materia::all();
        return view('grupos', compact('grupos', 'materias'));
    }

    public function storeWeb(Request $request)
    {
        $v = Validator::make($request->all(), [
            'codigo' => 'required|string|max:10',
            'estado' => 'boolean',
            'id_materia' => 'required|exists:materia,id_materia',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        // Validar que la combinación (codigo, id_materia) sea única
        $exists = \App\Modules\GestionAcademica\Models\Grupo::where('codigo', $request->codigo)
                                                             ->where('id_materia', $request->id_materia)
                                                             ->exists();
        if ($exists) {
            return back()
                ->withErrors(['codigo' => 'El código ya existe para esta materia.'])
                ->withInput();
        }

        try {
            $data = $v->validated();
            $data['estado'] = $request->has('estado');

            \Log::info('Datos a crear grupo:', $data);

            $grupo = $this->service->create($data);
            
            \Log::info('Grupo creado con ID:', ['id_grupo' => $grupo->id_grupo]);
            
            // Recargar con la relación de materia
            $grupo = $this->service->find($grupo->id_grupo);

            // Registrar en bitácora
            $usuario = auth()->user();
            if ($usuario) {
                $this->bitacora->registrar(
                    "{$usuario->nombre} registró el grupo {$grupo->codigo} para la materia {$grupo->materia->nombre}",
                    $usuario->id_usuario
                );
            }

            return redirect()->route('grupos.vista')->with('success', 'Grupo registrado correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error al crear grupo:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withErrors(['error' => 'Error al registrar el grupo: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $exists = $this->service->find($id);
        if (!$exists) return response()->json(['success'=>false,'message'=>'Grupo no encontrado'],404);

        $v = Validator::make($request->all(), [
            'codigo' => 'string|max:10|unique:grupo,codigo,'.$id.',id_grupo',
            'estado' => 'boolean',
            'id_materia' => 'exists:materia,id_materia',
        ]);

        if ($v->fails()) return response()->json(['success'=>false,'errors'=>$v->errors()],422);

        $item = $this->service->update($id, $v->validated());

        // Bitácora
        $usuario = auth()->user();
        if ($usuario) {
            $this->bitacora->registrar(
                "{$usuario->nombre} actualizó el grupo {$item->codigo} ({$item->materia->nombre})",
                $usuario->id_usuario
            );
        }

        return response()->json(['success'=>true,'message'=>'Grupo actualizado','data'=>$item]);
    }

    public function destroy($id): JsonResponse
    {
        $grupo = $this->service->find($id);
        if (!$grupo) return response()->json(['success'=>false,'message'=>'Grupo no encontrado'],404);

        $this->service->delete($id);

        // Bitácora
        $usuario = auth()->user();
        if ($usuario) {
            $this->bitacora->registrar(
                "{$usuario->nombre} eliminó el grupo {$grupo->codigo} ({$grupo->materia->nombre})",
                $usuario->id_usuario
            );
        }

        return response()->json(['success'=>true,'message'=>'Grupo eliminado']);
    }
    public function updateWeb(Request $request, $id)
    {
        $exists = $this->service->find($id);
        if (!$exists) {
            return redirect()->route('grupos.vista')->with('error', 'Grupo no encontrado.');
        }

        $v = Validator::make($request->all(), [
            'codigo' => 'required|string|max:10',
            'id_materia' => 'required|exists:materia,id_materia',
            'estado' => 'boolean',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        // Validar que la combinación (codigo, id_materia) sea única
        // pero permitir el grupo actual
        $duplicado = \App\Modules\GestionAcademica\Models\Grupo::where('codigo', $request->codigo)
                                                                ->where('id_materia', $request->id_materia)
                                                                ->where('id_grupo', '!=', $id)
                                                                ->exists();
        if ($duplicado) {
            return back()
                ->withErrors(['codigo' => 'El código ya existe para esta materia.'])
                ->withInput();
        }

        try {
            $data = $v->validated();
            $data['estado'] = $request->has('estado');

            $grupo = $this->service->update($id, $data);

            // 🔹 Bitácora
            $usuario = auth()->user();
            if ($usuario) {
                $this->bitacora->registrar(
                    "{$usuario->nombre} actualizó el grupo {$grupo->codigo} ({$grupo->materia->nombre})",
                    $usuario->id_usuario
                );
            }

            return redirect()->route('grupos.vista')->with('success', 'Grupo actualizado correctamente.');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroyWeb($id)
    {
        $grupo = $this->service->find($id);
        if (!$grupo) {
            return redirect()->route('grupos.vista')->with('error', 'Grupo no encontrado.');
        }

        try {
            $codigo = $grupo->codigo;
            $materia_nombre = $grupo->materia->nombre ?? 'Sin materia';

            // Desactivar el grupo (eliminado lógico)
            $grupo->estado = false;
            $grupo->save();

            // 🔹 Bitácora
            $usuario = auth()->user();
            if ($usuario) {
                $this->bitacora->registrar(
                    "{$usuario->nombre} desactivó el grupo {$codigo} ({$materia_nombre})",
                    $usuario->id_usuario
                );
            }

            return redirect()->route('grupos.vista')->with('success', 'Grupo desactivado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('grupos.vista')->with('error', 'Error al desactivar: ' . $e->getMessage());
        }
    }

    // Método para mostrar formulario de edición
    public function edit($id)
    {
        $grupo = $this->service->find($id);
        if (!$grupo) {
            return redirect()->route('grupos.vista')->with('error', 'Grupo no encontrado.');
        }
        $materias = Materia::all();
        return view('grupos_edit', compact('grupo', 'materias'));
    }
}
