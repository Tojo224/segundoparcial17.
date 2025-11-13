<?php

namespace App\Modules\GestionAcademica\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\GestionAcademica\Services\DocentesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Modules\AdministracionUsuariosSeguridad\Models\Usuario;
use App\Modules\AdministracionUsuariosSeguridad\Services\BitacoraService;
use Illuminate\Support\Facades\Auth;

class DocentesController extends Controller
{
    protected BitacoraService $bitacoraService;

    public function __construct(protected DocentesService $service, BitacoraService $bitacoraService)
    {
        $this->bitacoraService = $bitacoraService;
    }

    public function index(Request $request): JsonResponse
    {
        $items = $this->service->paginate($request->integer('per_page', 15), $request->only(['cod_docente','carrera']));
        return response()->json(['success'=>true,'data'=>$items]);
    }

    public function show($id): JsonResponse
    {
        $item = $this->service->find($id);
        if (!$item) return response()->json(['success'=>false,'message'=>'Docente no encontrado'],404);
        return response()->json(['success'=>true,'data'=>$item]);
    }

    public function store(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'cod_docente' => 'required|string|max:20|unique:docente,cod_docente',
            'nit' => 'nullable|string|max:30',
            'maestria' => 'nullable|string|max:100',
            'carrera' => 'required|string|max:100',
        ]);
        if ($v->fails()) return response()->json(['success'=>false,'errors'=>$v->errors()],422);
        $item = $this->service->create($v->validated());
        return response()->json(['success'=>true,'message'=>'Docente registrado','data'=>$item],201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $exists = $this->service->find($id);
        if (!$exists) return response()->json(['success'=>false,'message'=>'Docente no encontrado'],404);
        $v = Validator::make($request->all(), [
            'cod_docente' => 'string|max:20|unique:docente,cod_docente,'.$id.',id_docente',
            'nit' => 'nullable|string|max:30',
            'maestria' => 'nullable|string|max:100',
            'carrera' => 'string|max:100',
            'id_usuario' => 'exists:usuario,id_usuario',
        ]);
        if ($v->fails()) return response()->json(['success'=>false,'errors'=>$v->errors()],422);
        $item = $this->service->update($id, $v->validated());
        return response()->json(['success'=>true,'message'=>'Docente actualizado','data'=>$item]);
    }

    public function destroy($id): JsonResponse
    {
        if (!$this->service->delete($id)) return response()->json(['success'=>false,'message'=>'Docente no encontrado'],404);
        return response()->json(['success'=>true,'message'=>'Docente eliminado']);
    }
    
    public function vistaDocentes(Request $request)
    {
     $buscar = $request->get('buscar');
     $filters = [];

        if ($buscar) {
            $filters['cod_docente'] = $buscar;
            $filters['carrera'] = $buscar;
        }

        $docentes = $this->service->paginate(10, $filters);

        return view('docentes', compact('docentes'));
    }

   public function storeWeb(Request $request)
    {
        // Validación de campos combinados (usuario + docente)
        $v = Validator::make($request->all(), [
            'cod_docente' => 'required|string|max:20|unique:docente,cod_docente',
            'nit' => 'nullable|string|max:30',
            'maestria' => 'nullable|string|max:100',
            'carrera' => 'required|string|max:100',

            // Campos de usuario
            'CI' => 'required|string|max:20|unique:usuario,ci',
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'correo' => 'required|email|unique:usuario,correo',
            'sexo' => 'required|in:M,F',
            'estado_civil' => 'required|string|max:20',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        $data = $v->validated();

        try {
            // Crear usuario automáticamente
            $usuario = \App\Modules\AdministracionUsuariosSeguridad\Models\Usuario::create([
                'ci' => $data['CI'],
                'nombre' => $data['nombre'],
                'telefono' => $data['telefono'] ?? null,
                'direccion' => $data['direccion'] ?? null,
                'correo' => $data['correo'],
                'sexo' => $data['sexo'],
                'estado_civil' => $data['estado_civil'],
                'estado' => true,
                'contraseña' => bcrypt($data['CI']), // Contraseña = CI (autogenerada)
                'id_rol' => 3 // por ejemplo, rol docente
            ]);

            // Crear docente vinculado al usuario
            $docente = $this->service->create([
                'cod_docente' => $data['cod_docente'],
                'nit' => $data['nit'] ?? null,
                'maestria' => $data['maestria'] ?? null,
                'carrera' => $data['carrera'],
                'id_usuario' => $usuario->id_usuario
            ]);
            
            // Registrar en bitácora
            $usuarioActual = Auth::user();
            if ($usuarioActual) {
                $this->bitacoraService->registrar(
                    "{$usuarioActual->nombre} registró al docente {$usuario->nombre} ({$docente->cod_docente})",
                    $usuarioActual->id_usuario
                );
            }
            
            return redirect()
                ->route('docentes.vista')
                ->with('success', 'Docente y usuario creados correctamente.');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Error al guardar: ' . $e->getMessage()])
                ->withInput();
        }
    }

}

