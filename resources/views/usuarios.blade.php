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
      <a href="{{ route('usuarios.vista') }}" class="block px-3 py-2 rounded-md bg-blue-700">Gestionar Usuarios</a>
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
        <div class="text-sm text-slate-500">Auxiliares</div>
        <div class="text-3xl font-bold text-indigo-600">
          {{ $usuarios->where('id_rol', 3)->count() }}
        </div>
      </div>
    </div>

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
    <dialog id="crearModal" class="rounded-xl p-6 w-[95%] max-w-md">
      <form method="POST" action="{{ route('usuarios.store') }}" class="space-y-3">
        @csrf
        <h2 class="text-lg font-bold">Registrar Usuario</h2>
        <input name="nombre" required class="border rounded-md w-full p-2" placeholder="Nombre completo">
        <input name="correo" required type="email" class="border rounded-md w-full p-2" placeholder="Correo">
        <input name="telefono" class="border rounded-md w-full p-2" placeholder="Teléfono">
        <input name="direccion" class="border rounded-md w-full p-2" placeholder="Dirección">
        <input name="contraseña" required type="password" class="border rounded-md w-full p-2" placeholder="Contraseña">
        <select name="id_rol" class="border rounded-md w-full p-2">
          <option value="">Seleccione rol</option>
          <option value="1">Administrador</option>
          <option value="2">Docente</option>
          <option value="3">Auxiliar</option>
        </select>
        <div class="flex justify-end gap-2 mt-4">
          <button type="button" onclick="document.getElementById('crearModal').close()" class="px-3 py-2 bg-slate-200 rounded-md">Cancelar</button>
          <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Guardar</button>
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
