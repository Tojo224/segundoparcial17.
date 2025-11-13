<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bitácora del Sistema</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-50 flex flex-col lg:flex-row">

  <!-- Sidebar -->
  <aside class="w-full lg:w-64 bg-slate-900 text-white flex-shrink-0">
    <div class="p-4 border-b border-slate-700">
      <div class="flex items-center gap-3">
        <div class="bg-blue-600 p-2 rounded-lg">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
        </div>
        <div>
          <div class="text-sm text-slate-400">Sistema</div>
          <div class="font-semibold">Gestión Académica</div>
        </div>
      </div>
    </div>
    <nav class="p-4 space-y-2">
      <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Dashboard</a>
      <a href="{{ route('usuarios.vista') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Gestionar Usuarios</a>
      <a href="{{ route('bitacora.vista') }}" class="block px-3 py-2 rounded-md bg-blue-700">Gestionar Bitácora</a>

      <!-- Cerrar sesión -->
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
    <header class="space-y-2">
      <h1 class="text-2xl font-bold text-slate-800">Bitácora del Sistema</h1>
      <p class="text-slate-500">Registro de actividades y auditoría</p>
    </header>

    <!-- Botón Volver al Dashboard -->
    <div>
      <a href="{{ route('home') }}"
         class="inline-flex items-center gap-2 bg-slate-200 text-slate-700 px-4 py-2 rounded-md hover:bg-slate-300 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h14a1 1 0 001-1V10"/>
        </svg>
        Volver al Dashboard
      </a>
    </div>

    <!-- Filtros -->
    <form method="GET" class="flex flex-wrap gap-3 items-center">
      <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar..."
             class="border rounded-md p-2 w-full sm:w-64" />
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
        Filtrar
      </button>
    </form>

    <!-- Tabla -->
    <div class="bg-white shadow rounded-xl overflow-x-auto">
      <table class="min-w-full text-sm text-slate-700">
        <thead class="bg-slate-100 text-xs uppercase">
          <tr>
            <th class="py-3 px-4 text-left">Fecha / Hora</th>
            <th class="py-3 px-4 text-left">Usuario</th>
            <th class="py-3 px-4 text-left">Acción</th>
            <th class="py-3 px-4 text-left">IP</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($bitacoras as $log)
            <tr class="border-t hover:bg-slate-50">
              <td class="py-3 px-4">{{ $log->fecha }} {{ $log->hora }}</td>
              <td class="py-3 px-4">{{ $log->usuario->correo ?? 'Desconocido' }}</td>
              <td class="py-3 px-4">{{ $log->accion }}</td>
              <td class="py-3 px-4">{{ $log->ip }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center py-4 text-slate-500">No hay registros disponibles.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
      {{ $bitacoras->links('pagination::tailwind') }}
    </div>
  </main>
</body>
</html>
