<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Asignaciones de Grupos</title>
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
      
      <!-- Gestión Académica -->
      <div class="pt-2 pb-1">
        <p class="px-3 text-xs font-semibold text-slate-400 uppercase">Académico</p>
      </div>
      <a href="{{ route('docentes.vista') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Gestionar Docentes</a>
      <a href="{{ route('materias.vista') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Gestionar Materias</a>
      <a href="{{ route('grupos.vista') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Gestionar Grupos</a>
      <a href="{{ route('carga-horaria.asignar') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Asignar Grupos</a>
      <a href="{{ route('carga-horaria.vista') }}" class="block px-3 py-2 rounded-md bg-blue-700">Ver Asignaciones</a>
      
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
      <a href="{{ route('usuarios.vista') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Gestionar Usuarios</a>
      <a href="{{ route('bitacora.vista') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Bitácora</a>

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

  <!-- MAIN -->
  <main class="flex-1 p-6 space-y-6 overflow-x-auto">
    <header class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Asignaciones de Grupos a Docentes</h1>
        <p class="text-slate-500">Vista completa de todas las asignaciones activas</p>
      </div>
      <a href="{{ route('carga-horaria.asignar') }}" 
         class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Nueva Asignación
      </a>
    </header>

    <!-- Mensajes -->
    @if(session('success'))
      <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
        <p class="text-green-700 font-medium">{{ session('success') }}</p>
      </div>
    @endif

    @if(session('error'))
      <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
        <p class="text-red-700 font-medium">{{ session('error') }}</p>
      </div>
    @endif

    <!-- Tabla de asignaciones -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200">
      <div class="p-6 border-b border-slate-200">
        <h2 class="text-lg font-semibold text-slate-800">Lista de Asignaciones</h2>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">ID</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Docente</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Código Docente</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Grupo</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Materia</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Horas</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Acciones</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-slate-200">
            @forelse($asignaciones as $asignacion)
              <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-sm text-slate-600">{{ $asignacion->id_carga }}</td>
                <td class="px-4 py-3 text-sm font-medium text-slate-900">
                  {{ $asignacion->docente->usuario->nombre ?? 'N/A' }}
                </td>
                <td class="px-4 py-3 text-sm text-slate-700">
                  {{ $asignacion->docente->cod_docente ?? 'N/A' }}
                </td>
                <td class="px-4 py-3 text-sm text-slate-700">
                  {{ $asignacion->grupo->codigo ?? 'N/A' }}
                </td>
                <td class="px-4 py-3 text-sm text-slate-700">
                  <div>{{ $asignacion->grupo->materia->nombre ?? 'N/A' }}</div>
                  <div class="text-xs text-slate-500">{{ $asignacion->grupo->materia->sigla ?? '' }}</div>
                </td>
                <td class="px-4 py-3 text-sm text-slate-700">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                    {{ $asignacion->horas_asignadas }} hrs
                  </span>
                </td>
                <td class="px-4 py-3 text-sm">
                  <form method="POST" action="{{ route('carga-horaria.eliminar', $asignacion->id_carga) }}" 
                        onsubmit="return confirm('¿Está seguro de eliminar esta asignación?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                      class="text-red-600 hover:text-red-800 font-medium transition">
                      Eliminar
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                  <div class="flex flex-col items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-slate-300">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                    <p>No hay asignaciones registradas</p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Paginación -->
      @if($asignaciones->hasPages())
        <div class="px-6 py-4 border-t border-slate-200">
          {{ $asignaciones->links() }}
        </div>
      @endif
    </div>

    <!-- Estadísticas rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
        <div class="flex items-center gap-3">
          <div class="bg-blue-100 p-3 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-600">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
            </svg>
          </div>
          <div>
            <p class="text-sm text-slate-500">Total Asignaciones</p>
            <p class="text-2xl font-bold text-slate-800">{{ $asignaciones->total() }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
        <div class="flex items-center gap-3">
          <div class="bg-green-100 p-3 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-green-600">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
            </svg>
          </div>
          <div>
            <p class="text-sm text-slate-500">Docentes con Carga</p>
            <p class="text-2xl font-bold text-slate-800">
              {{ $asignaciones->unique('id_docente')->count() }}
            </p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
        <div class="flex items-center gap-3">
          <div class="bg-purple-100 p-3 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-purple-600">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
            </svg>
          </div>
          <div>
            <p class="text-sm text-slate-500">Grupos Asignados</p>
            <p class="text-2xl font-bold text-slate-800">
              {{ $asignaciones->unique('id_grupo')->count() }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </main>

</body>
</html>
