<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gestión de Usuarios</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 flex flex-col lg:flex-row">

  <!-- Sidebar -->
  <aside class="w-full lg:w-64 bg-slate-900 text-white flex-shrink-0">
    <div class="p-4 border-b border-slate-700">
      <div class="flex items-center gap-3">
        <div class="bg-blue-600 p-2 rounded-lg">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 4v16m8-8H4"/>
          </svg>
        </div>
        <div>
          <div class="text-sm text-slate-400">Sistema</div>
          <div class="font-semibold">Gestión Académica</div>
        </div>
      </div>
    </div>
    <nav class="p-4 space-y-2">
      <a href="/" class="block px-3 py-2 rounded-md hover:bg-slate-800">Dashboard</a>
      
      <!-- Gestión Académica -->
      <div class="pt-2 pb-1">
        <p class="px-3 text-xs font-semibold text-slate-400 uppercase">Académico</p>
      </div>
      <a href="{{ route('docentes.vista') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Gestionar Docentes</a>
      <a href="{{ route('materias.vista') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Gestionar Materias</a>
      <a href="{{ route('grupos.vista') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Gestionar Grupos</a>
      <a href="{{ route('carga-horaria.asignar') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Asignar Grupos</a>
      <a href="{{ route('carga-horaria.vista') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Ver Asignaciones</a>
      
      <!-- Aulas y Horarios -->
      <div class="pt-2 pb-1">
        <p class="px-3 text-xs font-semibold text-slate-400 uppercase">Aulas y Horarios</p>
      </div>
      <a href="{{ route('aulas.vista') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Gestionar Aulas</a>
      <a href="{{ route('horarios.calendario') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Gestionar Horarios</a>
      <a href="{{ route('reservas.vista') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Reservas de Aulas</a>
      
      <!-- Control de Asistencia -->
      <div class="pt-2 pb-1">
        <p class="px-3 text-xs font-semibold text-slate-400 uppercase">Asistencia</p>
      </div>
      <a href="{{ route('asistencia.vista') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Registrar Asistencia</a>
      
      <!-- Administración -->
      <div class="pt-2 pb-1">
        <p class="px-3 text-xs font-semibold text-slate-400 uppercase">Administración</p>
      </div>
      <a href="{{ route('usuarios.vista') }}" class="block px-3 py-2 rounded-md bg-blue-700">Gestionar Usuarios</a>
      <a href="{{ route('bitacora.vista') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Bitácora</a>
      
    <!-- Botón real de Cerrar Sesión -->
    <form method="POST" action="{{ route('logout') }}" class="mt-4">
    @csrf
    <button type="submit"
              class="flex items-center gap-2 w-full px-3 py-2 rounded-md text-red-500 hover:bg-red-100 transition">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
        <path stroke-linecap="round" stroke-linejoin="round"
                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
        </svg>
        Cerrar Sesión
    </button>
</form>

    </nav>
  </aside>

  <!-- Contenido principal -->
  <main class="flex-1 p-6 space-y-6 overflow-x-auto">
    <header>
      <h1 class="text-2xl font-bold text-slate-800">Gestión de Usuarios</h1>
      <p class="text-slate-500">Administre cuentas y permisos de usuario</p>
    </header>

    <!-- Resumen -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
      <div class="bg-white shadow rounded-xl p-4 text-center">
        <div class="text-sm text-slate-500">Total Usuarios</div>
        <div class="text-3xl font-bold text-slate-800">{{ $usuarios->count() }}</div>
      </div>
      <div class="bg-white shadow rounded-xl p-4 text-center">
        <div class="text-sm text-slate-500">Activos</div>
        <div class="text-3xl font-bold text-green-600">
          {{ $usuarios->where('estado', true)->count() }}
        </div>
      </div>
      <div class="bg-white shadow rounded-xl p-4 text-center">
        <div class="text-sm text-slate-500">Docentes</div>
        <div class="text-3xl font-bold text-blue-600">
          {{ $usuarios->where('id_rol', 2)->count() }}
        </div>
      </div>
      <div class="bg-white shadow rounded-xl p-4 text-center">
        <div class="text-sm text-slate-500">Administradores</div>
        <div class="text-3xl font-bold text-red-600">
          {{ $usuarios->where('id_rol', 1)->count() }}
        </div>
      </div>
      <div class="bg-white shadow rounded-xl p-4 text-center">
        <div class="text-sm text-slate-500">Coordinadores</div>
        <div class="text-3xl font-bold text-indigo-600">
          {{ $usuarios->where('id_rol', 5)->count() }}
        </div>
      </div>
    </div>

    <!-- Mensajes de éxito/error -->
    @if (session('success'))
      <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
        <p class="text-green-700 font-semibold">✓ {{ session('success') }}</p>
      </div>
    @endif

    @if (session('error'))
      <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-red-700 font-semibold">✗ {{ session('error') }}</p>
      </div>
    @endif

    <!-- Botones superiores -->
    <div class="flex flex-wrap gap-3 items-center">
    <a href="{{ route('home') }}" 
         class="flex items-center gap-2 bg-slate-200 text-slate-700 px-4 py-2 rounded-md hover:bg-slate-300 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h14a1 1 0 001-1V10"/>
        </svg>
        Volver al Menu principal
    </a>

    <button onclick="document.getElementById('crearModal').showModal()"
              class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
        Registrar Usuario
    </button>
    </div>


    <!-- Tabla -->
    <div class="bg-white shadow rounded-xl overflow-x-auto">
      <table class="min-w-full text-sm text-slate-700">
        <thead class="text-xs uppercase bg-slate-100">
          <tr>
            <th class="py-3 px-4 text-left">Nombre</th>
            <th class="py-3 px-4 text-left">Correo</th>
            <th class="py-3 px-4 text-left">Rol</th>
            <th class="py-3 px-4 text-left">Estado</th>
            <th class="py-3 px-4 text-left">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($usuarios as $user)
            <tr class="border-t hover:bg-slate-50">
              <td class="py-3 px-4">{{ $user->nombre }}</td>
              <td class="py-3 px-4">{{ $user->correo }}</td>
              <td class="py-3 px-4">
                <span class="bg-slate-200 text-slate-700 px-2 py-1 rounded-md text-xs">
                  {{ $user->rol->nombre ?? 'Sin rol' }}
                </span>
              </td>
              <td class="py-3 px-4">
                <span class="px-2 py-1 text-xs rounded-md {{ $user->estado ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                  {{ $user->estado ? 'Activo' : 'Inactivo' }}
                </span>
              </td>
              <td class="py-3 px-4 flex gap-2">
                <form action="{{ route('usuarios.destroy', $user->id_usuario) }}" method="POST" onsubmit="return confirm('¿Desactivar usuario?')" class="inline">
                  @csrf @method('DELETE')
                  <button class="text-red-600 hover:underline">Eliminar</button>
                </form>
                <button onclick="editarUsuario({{ $user->id_usuario }}, '{{ $user->nombre }}', '{{ $user->correo }}')" class="text-blue-600 hover:underline">
                  Editar
                </button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- Modal Crear Usuario -->
    <dialog id="crearModal" class="rounded-xl p-6 w-[95%] max-w-2xl max-h-[90vh] overflow-y-auto">
      <form method="POST" action="{{ route('usuarios.store') }}" class="space-y-4">
        @csrf
        <h2 class="text-lg font-bold">Registrar Nuevo Usuario</h2>
        
        @if ($errors->any())
          <div class="p-3 bg-red-50 border border-red-200 rounded-md">
            <p class="text-red-700 font-semibold text-sm mb-2">Errores encontrados:</p>
            <ul class="text-red-600 text-xs space-y-1">
              @foreach ($errors->all() as $error)
                <li>• {{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <!-- CI (Cédula de Identidad) -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">CI *</label>
            <input type="text" name="CI" value="{{ old('CI') }}" required 
                   class="border border-slate-300 rounded-md w-full p-2 text-sm" 
                   placeholder="Ej: 12345678">
            @error('CI')
              <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror
          </div>

          <!-- Nombre -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nombre Completo *</label>
            <input type="text" name="nombre" value="{{ old('nombre') }}" required 
                   class="border border-slate-300 rounded-md w-full p-2 text-sm" 
                   placeholder="Nombre completo">
            @error('nombre')
              <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror
          </div>

          <!-- Correo -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Correo Electrónico *</label>
            <input type="email" name="correo" value="{{ old('correo') }}" required 
                   class="border border-slate-300 rounded-md w-full p-2 text-sm" 
                   placeholder="correo@ejemplo.com">
            @error('correo')
              <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror
          </div>

          <!-- Teléfono -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Teléfono</label>
            <input type="text" name="telefono" value="{{ old('telefono') }}" 
                   class="border border-slate-300 rounded-md w-full p-2 text-sm" 
                   placeholder="Teléfono">
            @error('telefono')
              <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror
          </div>

          <!-- Dirección -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1">Dirección</label>
            <input type="text" name="direccion" value="{{ old('direccion') }}" 
                   class="border border-slate-300 rounded-md w-full p-2 text-sm" 
                   placeholder="Dirección">
            @error('direccion')
              <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror
          </div>

          <!-- Sexo -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Sexo *</label>
            <select name="sexo" required class="border border-slate-300 rounded-md w-full p-2 text-sm">
              <option value="">Seleccione</option>
              <option value="M" {{ old('sexo') === 'M' ? 'selected' : '' }}>Masculino</option>
              <option value="F" {{ old('sexo') === 'F' ? 'selected' : '' }}>Femenino</option>
            </select>
            @error('sexo')
              <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror
          </div>

          <!-- Estado Civil -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Estado Civil *</label>
            <select name="estado_civil" required class="border border-slate-300 rounded-md w-full p-2 text-sm">
              <option value="">Seleccione</option>
              <option value="Soltero" {{ old('estado_civil') === 'Soltero' ? 'selected' : '' }}>Soltero</option>
              <option value="Casado" {{ old('estado_civil') === 'Casado' ? 'selected' : '' }}>Casado</option>
              <option value="Divorciado" {{ old('estado_civil') === 'Divorciado' ? 'selected' : '' }}>Divorciado</option>
              <option value="Viudo" {{ old('estado_civil') === 'Viudo' ? 'selected' : '' }}>Viudo</option>
            </select>
            @error('estado_civil')
              <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror
          </div>

          <!-- Contraseña -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Contraseña *</label>
            <input type="password" name="contraseña" required 
                   class="border border-slate-300 rounded-md w-full p-2 text-sm" 
                   placeholder="Mínimo 6 caracteres">
            @error('contraseña')
              <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror
          </div>

          <!-- Rol -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Rol *</label>
            <select name="id_rol" required class="border border-slate-300 rounded-md w-full p-2 text-sm">
              <option value="">Seleccione rol</option>
              <option value="1" {{ old('id_rol') === '1' ? 'selected' : '' }}>Administrador</option>
              <option value="2" {{ old('id_rol') === '2' ? 'selected' : '' }}>Docente</option>
              <option value="3" {{ old('id_rol') === '3' ? 'selected' : '' }}>Auxiliar</option>
              <option value="4" {{ old('id_rol') === '4' ? 'selected' : '' }}>Autoridad</option>
              <option value="5" {{ old('id_rol') === '5' ? 'selected' : '' }}>Coordinador</option>
            </select>
            @error('id_rol')
              <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror
          </div>

          <!-- Estado -->
          <div class="flex items-center gap-2 pt-2">
            <input type="checkbox" id="estado" name="estado" value="1" 
                   {{ old('estado', '1') === '1' || old('estado') === 1 ? 'checked' : '' }} 
                   class="w-4 h-4 rounded">
            <label for="estado" class="text-sm font-medium text-slate-700">Usuario Activo</label>
            @error('estado')
              <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror
          </div>
        </div>

        <div class="flex justify-end gap-2 mt-4 pt-4 border-t">
          <button type="button" onclick="document.getElementById('crearModal').close()" 
                  class="px-4 py-2 bg-slate-200 text-slate-700 rounded-md hover:bg-slate-300 text-sm font-medium">
            Cancelar
          </button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium">
            Registrar Usuario
          </button>
        </div>
      </form>
    </dialog>

    <!-- Modal Editar -->
    <dialog id="editarModal" class="rounded-xl p-6 w-[95%] max-w-md">
      <form method="POST" id="editarForm" class="space-y-3">
        @csrf @method('PUT')
        <h2 class="text-lg font-bold">Editar Usuario</h2>
        <input id="edit_nombre" name="nombre" required class="border rounded-md w-full p-2">
        <input id="edit_correo" name="correo" required type="email" class="border rounded-md w-full p-2">
        <div class="flex justify-end gap-2 mt-4">
          <button type="button" onclick="document.getElementById('editarModal').close()" class="px-3 py-2 bg-slate-200 rounded-md">Cancelar</button>
          <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Actualizar</button>
        </div>
      </form>
    </dialog>

  </main>

  <script>
    function editarUsuario(id, nombre, correo) {
      const modal = document.getElementById('editarModal');
      document.getElementById('edit_nombre').value = nombre;
      document.getElementById('edit_correo').value = correo;
      const form = document.getElementById('editarForm');
      form.action = `/usuarios/${id}`;
      modal.showModal();
    }
  </script>
</body>
</html>
