<?php

namespace App\Modules\GestionAcademica\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\GestionAcademica\Services\CargaHorariaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CargaHorariaController extends Controller
{
    public function __construct(protected CargaHorariaService $service) {}

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
            'id_gestion' => 'required|exists:gestion_academica,id_gestion',
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
            'id_gestion' => 'exists:gestion_academica,id_gestion',
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
}

