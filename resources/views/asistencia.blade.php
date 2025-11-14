<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Registrar Asistencia Docente</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-50 flex flex-col lg:flex-row">

  <!-- Sidebar -->
  <aside class="w-full lg:w-64 bg-slate-900 text-white flex-shrink-0">
    <div class="p-4 border-b border-slate-700">
      <div class="flex items-center gap-3">
        <div class="bg-indigo-600 p-2 rounded-lg">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
      <a href="{{ route('asistencia.vista') }}" class="block px-3 py-2 rounded-md bg-indigo-700">Registrar Asistencia</a>
      
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
  <main class="flex-1 p-6 space-y-6">
    <header class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Registro de Asistencia Docente</h1>
        <p class="text-slate-500">Control y seguimiento de asistencias</p>
      </div>
      <button onclick="openModalNuevo()" 
         class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Registrar Asistencia
      </button>
    </header>

    <!-- Mensajes -->
    @if(session('success'))
      <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
        <p class="text-green-700 font-medium">✓ {{ session('success') }}</p>
      </div>
    @endif

    @if(session('error'))
      <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
        <p class="text-red-700 font-medium">✗ {{ session('error') }}</p>
      </div>
    @endif

    @if($errors->any())
      <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
        <ul class="list-disc list-inside text-red-700">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Total de Asistencias -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-500 font-medium">Total Registradas</p>
            <p class="text-3xl font-bold text-slate-800 mt-1">{{ $totalAsistencias }}</p>
          </div>
          <div class="bg-indigo-100 p-3 rounded-lg">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
          </div>
        </div>
      </div>

      <!-- Asistencias de Hoy -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-500 font-medium">Hoy</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">{{ $asistenciasHoy }}</p>
          </div>
          <div class="bg-blue-100 p-3 rounded-lg">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
          </div>
        </div>
      </div>

      <!-- Pendientes -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-500 font-medium">Pendientes Hoy</p>
            <p class="text-3xl font-bold text-orange-600 mt-1">{{ $pendientes }}</p>
          </div>
          <div class="bg-orange-100 p-3 rounded-lg">
            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
      </div>

      <!-- Horarios Programados Hoy -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-500 font-medium">Horarios Hoy</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $horariosHoy->count() }}</p>
          </div>
          <div class="bg-green-100 p-3 rounded-lg">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtros y Calendario -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Filtros -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
        <h3 class="text-lg font-semibold text-slate-800 mb-4">Filtros</h3>
        <form method="GET" action="{{ route('asistencia.vista') }}" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Fecha</label>
            <input type="date" name="fecha" value="{{ request('fecha') }}"
              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Tipo de Asistencia</label>
            <select name="tipo" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
              <option value="">Todos</option>
              @foreach($tiposAsistencia as $tipo)
                <option value="{{ $tipo }}" {{ request('tipo') == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
              @endforeach
            </select>
          </div>

          <div class="flex gap-2">
            <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
              Aplicar Filtros
            </button>
            <a href="{{ route('asistencia.vista') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition">
              Limpiar
            </a>
          </div>
        </form>
      </div>

      <!-- Horarios Pendientes del Día -->
      <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-slate-200">
        <h3 class="text-lg font-semibold text-slate-800 mb-4">Horarios Pendientes de Registro (Hoy)</h3>
        
        @if($horariosSinAsistencia->count() > 0)
          <div class="space-y-3 max-h-80 overflow-y-auto">
            @foreach($horariosSinAsistencia as $horario)
              <div class="flex items-center justify-between p-4 bg-orange-50 border border-orange-200 rounded-lg">
                <div class="flex-1">
                  <p class="font-medium text-slate-800">
                    {{ $horario->carga?->docente?->usuario?->nombre ?? 'Sin docente' }}
                  </p>
                  <p class="text-sm text-slate-600">
                    {{ $horario->carga?->grupo?->materia?->nombre ?? 'Sin materia' }}
                    ({{ $horario->carga?->grupo?->codigo ?? 'Sin grupo' }})
                  </p>
                  <p class="text-xs text-slate-500 mt-1">
                    <span class="inline-flex items-center gap-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      {{ $horario->hora_i }} - {{ $horario->hora_f }}
                    </span>
                    <span class="ml-3 inline-flex items-center gap-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                      </svg>
                      Aula {{ $horario->aula?->nro_aula ?? 'N/A' }} - {{ $horario->aula?->modulo ?? '' }}
                    </span>
                  </p>
                </div>
                <button onclick="registrarRapido({{ $horario->id_horario }}, '{{ $horario->carga?->docente?->usuario?->nombre ?? '' }}')" 
                  class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm">
                  Registrar
                </button>
              </div>
            @endforeach
          </div>
        @else
          <div class="text-center py-8">
            <svg class="w-16 h-16 mx-auto text-green-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-slate-600">Todas las asistencias de hoy han sido registradas</p>
          </div>
        @endif
      </div>
    </div>

    <!-- Tabla de Asistencias -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
      <div class="p-6 border-b border-slate-200">
        <h2 class="text-lg font-semibold text-slate-800">Registro de Asistencias</h2>
        <p class="text-sm text-slate-500 mt-1">Historial completo de asistencias registradas</p>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Fecha</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Docente</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Materia</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Grupo</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Horario</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Aula</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tipo</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Acciones</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-slate-200">
            @forelse($asistencias as $asistencia)
              <tr class="hover:bg-slate-50 transition">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                  #{{ $asistencia->id_asistencia }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                  {{ $asistencia->fecha_registro->format('d/m/Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                  {{ $asistencia->horario?->carga?->docente?->usuario?->nombre ?? 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                  {{ $asistencia->horario?->carga?->grupo?->materia?->nombre ?? 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                  {{ $asistencia->horario?->carga?->grupo?->codigo ?? 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                  {{ $asistencia->horario?->hora_i ?? 'N/A' }} - {{ $asistencia->horario?->hora_f ?? 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                  {{ $asistencia->horario?->aula?->nro_aula ?? 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  @php
                    $badgeColors = [
                      'Presentado' => 'bg-green-100 text-green-800',
                      'Ausente' => 'bg-red-100 text-red-800',
                      'Justificado' => 'bg-blue-100 text-blue-800',
                      'Tardanza' => 'bg-yellow-100 text-yellow-800',
                    ];
                    $color = $badgeColors[$asistencia->tipo] ?? 'bg-slate-100 text-slate-800';
                  @endphp
                  <span class="px-2 py-1 text-xs font-medium rounded-full {{ $color }}">
                    {{ $asistencia->tipo }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <div class="flex items-center gap-2">
                    <button onclick="verDetalle({{ $asistencia->id_asistencia }})" 
                      class="text-blue-600 hover:text-blue-800 transition">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                    </button>
                    <button onclick="openModalEditar({{ $asistencia->id_asistencia }})" 
                      class="text-amber-600 hover:text-amber-800 transition">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </button>
                    <form method="POST" action="{{ route('asistencia.destroy', $asistencia->id_asistencia) }}" 
                      onsubmit="return confirm('¿Está seguro de eliminar esta asistencia?')" class="inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="text-red-600 hover:text-red-800 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="px-6 py-12 text-center">
                  <svg class="w-16 h-16 mx-auto text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                  <p class="text-slate-500 text-lg">No hay asistencias registradas</p>
                  <p class="text-slate-400 text-sm mt-1">Comienza registrando la primera asistencia</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Modal: Registrar/Editar Asistencia -->
  <div id="modalForm" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
      <div class="p-6 border-b border-slate-200">
        <h3 id="modalTitle" class="text-xl font-semibold text-slate-800">Registrar Asistencia</h3>
      </div>

      <form id="formAsistencia" method="POST" action="{{ route('asistencia.store') }}" class="p-6 space-y-4">
        @csrf
        <input type="hidden" id="method" name="_method" value="POST">
        <input type="hidden" id="asistenciaId" name="id_asistencia">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Fecha -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Fecha <span class="text-red-500">*</span></label>
            <input type="date" id="fecha_registro" name="fecha_registro" required
              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
          </div>

          <!-- Tipo de Asistencia -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Tipo <span class="text-red-500">*</span></label>
            <select id="tipo" name="tipo" required
              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
              <option value="">Seleccione...</option>
              @foreach($tiposAsistencia as $tipo)
                <option value="{{ $tipo }}">{{ $tipo }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <!-- Horario -->
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Horario/Clase <span class="text-red-500">*</span></label>
          <select id="id_horario" name="id_horario" required onchange="actualizarInfoHorario()"
            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">Primero seleccione una fecha...</option>
          </select>
          <p class="text-xs text-slate-500 mt-1">Seleccione una fecha para ver los horarios disponibles</p>
        </div>

        <!-- Información del Horario Seleccionado -->
        <div id="infoHorario" class="hidden p-4 bg-slate-50 rounded-lg border border-slate-200">
          <p class="text-sm font-medium text-slate-700 mb-2">Información de la clase:</p>
          <div class="grid grid-cols-2 gap-2 text-sm text-slate-600">
            <div><span class="font-medium">Docente:</span> <span id="infoDocente">-</span></div>
            <div><span class="font-medium">Materia:</span> <span id="infoMateria">-</span></div>
            <div><span class="font-medium">Grupo:</span> <span id="infoGrupo">-</span></div>
            <div><span class="font-medium">Hora:</span> <span id="infoHora">-</span></div>
            <div><span class="font-medium">Aula:</span> <span id="infoAula">-</span></div>
          </div>
        </div>

        <div class="flex gap-3 pt-4">
          <button type="submit" 
            class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
            Guardar Asistencia
          </button>
          <button type="button" onclick="closeModal()" 
            class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition">
            Cancelar
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal: Ver Detalle -->
  <div id="modalDetalle" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full">
      <div class="p-6 border-b border-slate-200">
        <h3 class="text-xl font-semibold text-slate-800">Detalle de Asistencia</h3>
      </div>

      <div id="detalleContent" class="p-6 space-y-4">
        <!-- Se llenará con JavaScript -->
      </div>

      <div class="p-6 border-t border-slate-200">
        <button onclick="closeModalDetalle()" 
          class="w-full px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition">
          Cerrar
        </button>
      </div>
    </div>
  </div>

  <script>
    // Datos de horarios (inyectados desde el servidor)
    let horariosData = @json($horariosHoy);
    let asistenciasData = @json($asistencias);

    // Establecer fecha de hoy por defecto
    document.addEventListener('DOMContentLoaded', function() {
      const fechaInput = document.getElementById('fecha_registro');
      if (fechaInput && !fechaInput.value) {
        fechaInput.value = new Date().toISOString().split('T')[0];
      }

      // Cargar horarios cuando cambia la fecha
      fechaInput.addEventListener('change', cargarHorariosPorFecha);
    });

    // Abrir modal para nueva asistencia
    function openModalNuevo() {
      document.getElementById('modalTitle').textContent = 'Registrar Asistencia';
      document.getElementById('formAsistencia').action = '{{ route("asistencia.store") }}';
      document.getElementById('method').value = 'POST';
      document.getElementById('formAsistencia').reset();
      document.getElementById('fecha_registro').value = new Date().toISOString().split('T')[0];
      document.getElementById('infoHorario').classList.add('hidden');
      cargarHorariosPorFecha();
      document.getElementById('modalForm').classList.remove('hidden');
    }

    // Registrar rápido desde horarios pendientes
    function registrarRapido(idHorario, nombreDocente) {
      document.getElementById('modalTitle').textContent = `Registrar Asistencia - ${nombreDocente}`;
      document.getElementById('formAsistencia').action = '{{ route("asistencia.store") }}';
      document.getElementById('method').value = 'POST';
      document.getElementById('formAsistencia').reset();
      
      const fechaHoy = new Date().toISOString().split('T')[0];
      document.getElementById('fecha_registro').value = fechaHoy;
      
      cargarHorariosPorFecha().then(() => {
        document.getElementById('id_horario').value = idHorario;
        actualizarInfoHorario();
      });
      
      document.getElementById('modalForm').classList.remove('hidden');
    }

    // Abrir modal para editar
    function openModalEditar(id) {
      const asistencia = asistenciasData.find(a => a.id_asistencia === id);
      if (!asistencia) return;

      document.getElementById('modalTitle').textContent = 'Editar Asistencia';
      document.getElementById('formAsistencia').action = `/asistencia/${id}`;
      document.getElementById('method').value = 'PUT';
      
      document.getElementById('fecha_registro').value = asistencia.fecha_registro.split('T')[0];
      document.getElementById('tipo').value = asistencia.tipo;
      
      cargarHorariosPorFecha().then(() => {
        document.getElementById('id_horario').value = asistencia.id_horario;
        actualizarInfoHorario();
      });

      document.getElementById('modalForm').classList.remove('hidden');
    }

    // Cerrar modal
    function closeModal() {
      document.getElementById('modalForm').classList.add('hidden');
    }

    // Cargar horarios por fecha (con AJAX)
    async function cargarHorariosPorFecha() {
      const fecha = document.getElementById('fecha_registro').value;
      if (!fecha) return;

      const select = document.getElementById('id_horario');
      select.innerHTML = '<option value="">Cargando...</option>';

      try {
        const response = await fetch(`/asistencia/horarios-fecha?fecha=${fecha}`, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          }
        });

        if (!response.ok) throw new Error('Error al cargar horarios');

        const data = await response.json();
        
        if (data.success) {
          const horarios = data.data.horarios;
          
          select.innerHTML = '<option value="">Seleccione un horario...</option>';
          
          horarios.forEach(h => {
            const docente = h.carga?.docente?.usuario?.nombre || 'Sin docente';
            const materia = h.carga?.grupo?.materia?.nombre || 'Sin materia';
            const grupo = h.carga?.grupo?.codigo || 'Sin grupo';
            const aula = h.aula?.nro_aula || 'N/A';
            
            const option = document.createElement('option');
            option.value = h.id_horario;
            option.textContent = `${h.hora_i} - ${h.hora_f} | ${docente} | ${materia} (${grupo}) | Aula ${aula}`;
            option.dataset.horario = JSON.stringify(h);
            select.appendChild(option);
          });

          if (horarios.length === 0) {
            select.innerHTML = '<option value="">No hay horarios programados para esta fecha</option>';
          }
        }
      } catch (error) {
        console.error('Error:', error);
        select.innerHTML = '<option value="">Error al cargar horarios</option>';
      }
    }

    // Actualizar información del horario seleccionado
    function actualizarInfoHorario() {
      const select = document.getElementById('id_horario');
      const selectedOption = select.options[select.selectedIndex];
      
      if (!selectedOption || !selectedOption.dataset.horario) {
        document.getElementById('infoHorario').classList.add('hidden');
        return;
      }

      const horario = JSON.parse(selectedOption.dataset.horario);
      
      document.getElementById('infoDocente').textContent = horario.carga?.docente?.usuario?.nombre || 'N/A';
      document.getElementById('infoMateria').textContent = horario.carga?.grupo?.materia?.nombre || 'N/A';
      document.getElementById('infoGrupo').textContent = horario.carga?.grupo?.codigo || 'N/A';
      document.getElementById('infoHora').textContent = `${horario.hora_i} - ${horario.hora_f}`;
      document.getElementById('infoAula').textContent = horario.aula?.nro_aula + ' - ' + horario.aula?.modulo || 'N/A';
      
      document.getElementById('infoHorario').classList.remove('hidden');
    }

    // Ver detalle de asistencia
    function verDetalle(id) {
      const asistencia = asistenciasData.find(a => a.id_asistencia === id);
      if (!asistencia) return;

      const horario = asistencia.horario;
      const docente = horario?.carga?.docente?.usuario?.nombre || 'N/A';
      const materia = horario?.carga?.grupo?.materia?.nombre || 'N/A';
      const grupo = horario?.carga?.grupo?.codigo || 'N/A';
      const aula = horario?.aula?.nro_aula || 'N/A';
      const modulo = horario?.aula?.modulo || '';

      const html = `
        <div class="grid grid-cols-2 gap-4">
          <div>
            <p class="text-sm text-slate-500">ID Asistencia</p>
            <p class="text-lg font-semibold text-slate-800">#${asistencia.id_asistencia}</p>
          </div>
          <div>
            <p class="text-sm text-slate-500">Fecha de Registro</p>
            <p class="text-lg font-semibold text-slate-800">${new Date(asistencia.fecha_registro).toLocaleDateString('es-ES')}</p>
          </div>
          <div>
            <p class="text-sm text-slate-500">Tipo de Asistencia</p>
            <p class="text-lg font-semibold text-slate-800">${asistencia.tipo}</p>
          </div>
          <div>
            <p class="text-sm text-slate-500">Docente</p>
            <p class="text-lg font-semibold text-slate-800">${docente}</p>
          </div>
          <div>
            <p class="text-sm text-slate-500">Materia</p>
            <p class="text-lg font-semibold text-slate-800">${materia}</p>
          </div>
          <div>
            <p class="text-sm text-slate-500">Grupo</p>
            <p class="text-lg font-semibold text-slate-800">${grupo}</p>
          </div>
          <div>
            <p class="text-sm text-slate-500">Horario</p>
            <p class="text-lg font-semibold text-slate-800">${horario?.hora_i || 'N/A'} - ${horario?.hora_f || 'N/A'}</p>
          </div>
          <div>
            <p class="text-sm text-slate-500">Aula</p>
            <p class="text-lg font-semibold text-slate-800">${aula} - ${modulo}</p>
          </div>
        </div>
      `;

      document.getElementById('detalleContent').innerHTML = html;
      document.getElementById('modalDetalle').classList.remove('hidden');
    }

    // Cerrar modal de detalle
    function closeModalDetalle() {
      document.getElementById('modalDetalle').classList.add('hidden');
    }

    // Cerrar modales con ESC
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeModal();
        closeModalDetalle();
      }
    });
  </script>

</body>
</html>
