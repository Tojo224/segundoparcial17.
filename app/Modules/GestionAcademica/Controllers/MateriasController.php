<?php

namespace App\Modules\GestionAcademica\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\GestionAcademica\Services\MateriasService;
use App\Modules\AdministracionUsuariosSeguridad\Services\BitacoraService;
use App\Modules\GestionAcademica\Models\Materia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MateriasController extends Controller
{
    protected MateriasService $service;
    protected BitacoraService $bitacora;

    public function __construct(MateriasService $service, BitacoraService $bitacora)
    {
        $this->service = $service;
        $this->bitacora = $bitacora;
    }

    // === API ===
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['sigla', 'nombre']);
        $items = $this->service->paginate($request->integer('per_page', 15), $filters);
        return response()->json(['success' => true, 'data' => $items]);
    }

    public function show($id): JsonResponse
    {
        $item = $this->service->find($id);
        if (!$item) return response()->json(['success' => false, 'message' => 'Materia no encontrada'], 404);
        return response()->json(['success' => true, 'data' => $item]);
    }

    public function store(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'sigla' => 'required|string|max:20|unique:materia,sigla',
            'nombre' => 'required|string|max:255|unique:materia,nombre',
        ]);
        if ($v->fails()) return response()->json(['success' => false, 'errors' => $v->errors()], 422);

        $item = $this->service->create($v->validated());

        // 🔹 Bitácora
        $usuario = auth()->user();
        if ($usuario) {
            $this->bitacora->registrar(
                "{$usuario->nombre} registró la materia {$item->sigla} - {$item->nombre}",
                $usuario->id_usuario
            );
        }

        return response()->json(['success' => true, 'message' => 'Materia creada', 'data' => $item], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $exists = $this->service->find($id);
        if (!$exists) return response()->json(['success' => false, 'message' => 'Materia no encontrada'], 404);

        $v = Validator::make($request->all(), [
            'sigla' => 'string|max:20|unique:materia,sigla,' . $id . ',id_materia',
            'nombre' => 'string|max:255|unique:materia,nombre,' . $id . ',id_materia',
        ]);
        if ($v->fails()) return response()->json(['success' => false, 'errors' => $v->errors()], 422);

        $item = $this->service->update($id, $v->validated());

        // 🔹 Bitácora
        $usuario = auth()->user();
        if ($usuario) {
            $this->bitacora->registrar(
                "{$usuario->nombre} actualizó la materia {$item->sigla} - {$item->nombre}",
                $usuario->id_usuario
            );
        }

        return response()->json(['success' => true, 'message' => 'Materia actualizada', 'data' => $item]);
    }

    public function destroy($id): JsonResponse
    {
        $materia = $this->service->find($id);
        if (!$materia) return response()->json(['success' => false, 'message' => 'Materia no encontrada'], 404);

        $this->service->delete($id);

        // 🔹 Bitácora
        $usuario = auth()->user();
        if ($usuario) {
            $this->bitacora->registrar(
                "{$usuario->nombre} eliminó la materia {$materia->sigla} - {$materia->nombre}",
                $usuario->id_usuario
            );
        }

        return response()->json(['success' => true, 'message' => 'Materia eliminada']);
    }

    // === WEB ===
    public function vistaMaterias(Request $request)
    {
        $buscar = $request->get('buscar');
        $filters = [];

        if ($buscar) {
            $filters['nombre'] = $buscar;
        }

        $materias = $this->service->paginate(10, $filters);
        return view('materias', compact('materias'));
    }

    public function storeWeb(Request $request)
    {
        $v = Validator::make($request->all(), [
            'sigla' => 'required|string|max:20|unique:materia,sigla',
            'nombre' => 'required|string|max:255|unique:materia,nombre',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        $materia = $this->service->create($v->validated());

        // 🔹 Bitácora
        $usuario = auth()->user();
        if ($usuario) {
            $this->bitacora->registrar(
                "{$usuario->nombre} registró la materia {$materia->sigla} - {$materia->nombre}",
                $usuario->id_usuario
            );
        }

        return redirect()->route('materias.vista')->with('success', 'Materia registrada correctamente.');
    }

    public function updateWeb(Request $request, $id)
    {
        $materia = $this->service->find($id);
        if (!$materia) {
            return redirect()->route('materias.vista')->with('error', 'Materia no encontrada.');
        }

        $v = Validator::make($request->all(), [
            'sigla' => 'required|string|max:20|unique:materia,sigla,' . $id . ',id_materia',
            'nombre' => 'required|string|max:255|unique:materia,nombre,' . $id . ',id_materia',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        $materia = $this->service->update($id, $v->validated());

        // 🔹 Bitácora
        $usuario = auth()->user();
        if ($usuario) {
            $this->bitacora->registrar(
                "{$usuario->nombre} actualizó la materia {$materia->sigla} - {$materia->nombre}",
                $usuario->id_usuario
            );
        }

        return redirect()->route('materias.vista')->with('success', 'Materia actualizada correctamente.');
    }

    public function destroyWeb($id)
    {
        $materia = $this->service->find($id);
        if (!$materia) {
            return redirect()->route('materias.vista')->with('error', 'Materia no encontrada.');
        }

        try {
            $sigla = $materia->sigla;
            $nombre = $materia->nombre;

            $this->service->delete($id);

            // 🔹 Bitácora
            $usuario = auth()->user();
            if ($usuario) {
                $this->bitacora->registrar(
                    "{$usuario->nombre} eliminó la materia {$sigla} - {$nombre}",
                    $usuario->id_usuario
                );
            }

            return redirect()->route('materias.vista')->with('success', 'Materia eliminada correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('materias.vista')->with('error', 'Error al eliminar la materia: ' . $e->getMessage());
        }
    }
}
