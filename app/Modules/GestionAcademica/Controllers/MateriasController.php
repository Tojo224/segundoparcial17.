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

    // === API ===
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

    public function store(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'codigo' => 'required|string|max:10|unique:grupo,codigo',
            'estado' => 'boolean',
            'id_materia' => 'required|exists:materia,id_materia',
        ]);
        if ($v->fails()) return response()->json(['success'=>false,'errors'=>$v->errors()],422);

        $item = $this->service->create($v->validated());

        // 🔹 Bitácora
        $usuario = auth()->user();
        if ($usuario) {
            $this->bitacora->registrar(
                "{$usuario->nombre} creó el grupo {$item->codigo} ({$item->materia->nombre})",
                $usuario->id_usuario
            );
        }

        return response()->json(['success'=>true,'message'=>'Grupo creado','data'=>$item],201);
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

        // 🔹 Bitácora
        $usuario = auth()->user();
        if ($usuario) {
            $this->bitacora->registrar(
                "{$usuario->nombre} actualizó el grupo {$item->codigo} ({$item->materia->nombre})",
                $usuario->id_usuario
            );
        }

        return response()->json(['success'=>true,'message'=>'Grupo actualizado','data'=>$item]);
    }

    // === Eliminado lógico ===
    public function destroy($id): JsonResponse
    {
        $grupo = $this->service->find($id);
        if (!$grupo) return response()->json(['success'=>false,'message'=>'Grupo no encontrado'],404);

        // Eliminado lógico: cambiar estado a inactivo
        $grupo->estado = false;
        $grupo->save();

        // 🔹 Bitácora
        $usuario = auth()->user();
        if ($usuario) {
            $this->bitacora->registrar(
                "{$usuario->nombre} desactivó el grupo {$grupo->codigo} ({$grupo->materia->nombre})",
                $usuario->id_usuario
            );
        }

        return response()->json(['success'=>true,'message'=>'Grupo desactivado']);
    }

    // === WEB ===
    public function vistaGrupos(Request $request)
    {
        $buscar = $request->get('buscar');
        $filters = [];

        if ($buscar) {
            $filters['codigo'] = $buscar;
        }

        $grupos = $this->service->paginate(10, $filters);
        $materias = Materia::all();
        return view('grupos', compact('grupos', 'materias'));
    }

    public function storeWeb(Request $request)
    {
        $v = Validator::make($request->all(), [
            'codigo' => 'required|string|max:10|unique:grupo,codigo',
            'estado' => 'boolean',
            'id_materia' => 'required|exists:materia,id_materia',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        $data = $v->validated();
        $data['estado'] = $request->has('estado');

        $grupo = $this->service->create($data);

        // 🔹 Bitácora
        $usuario = auth()->user();
        if ($usuario) {
            $this->bitacora->registrar(
                "{$usuario->nombre} registró el grupo {$grupo->codigo} ({$grupo->materia->nombre})",
                $usuario->id_usuario
            );
        }

        return redirect()->route('grupos.vista')->with('success', 'Grupo registrado correctamente.');
    }

    public function destroyWeb($id)
    {
        $grupo = $this->service->find($id);
        if (!$grupo) {
            return redirect()->route('grupos.vista')->with('error', 'Grupo no encontrado.');
        }

        try {
            // Eliminado lógico
            $grupo->estado = false;
            $grupo->save();

            // 🔹 Bitácora
            $usuario = auth()->user();
            if ($usuario) {
                $this->bitacora->registrar(
                    "{$usuario->nombre} desactivó el grupo {$grupo->codigo} ({$grupo->materia->nombre})",
                    $usuario->id_usuario
                );
            }

            return redirect()->route('grupos.vista')->with('success', 'Grupo desactivado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('grupos.vista')->with('error', 'Error al desactivar el grupo: ' . $e->getMessage());
        }
    }
}
