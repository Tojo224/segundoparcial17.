<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Gestionar Reservas de Aulas</title>
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
      <a href="{{ route('carga-horaria.vista') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Ver Asignaciones</a>
      
      <!-- Aulas y Horarios -->
      <div class="pt-2 pb-1">
        <p class="px-3 text-xs font-semibold text-slate-400 uppercase">Aulas y Horarios</p>
      </div>
      <a href="{{ route('aulas.vista') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Gestionar Aulas</a>
      <a href="{{ route('horarios.calendario') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Gestionar Horarios</a>
      <a href="{{ route('reservas.vista') }}" class="block px-3 py-2 rounded-md bg-blue-700">Reservas de Aulas</a>
      
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
 <!-- CU14 - Gestionar Reservas de Aulas -->
  <!-- MAIN -->
  <main class="flex-1 p-6 space-y-6">
    <header class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Reservas de Aulas</h1>
        <p class="text-slate-500">Gestión y calendario de reservas de espacios</p>
      </div>
      <button onclick="openModalNuevo()" 
         class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Nueva Reserva
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

    @if($errors->has('conflictos'))
      <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded">
        <p class="text-orange-800 font-bold mb-2">⚠️ Conflictos de Horario Detectados:</p>
        <ul class="list-disc list-inside text-orange-700">
          @foreach($errors->get('conflictos') as $conflicto)
            <li>{{ $conflicto }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if($errors->any() && !$errors->has('conflictos'))
      <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
        <p class="text-red-700 font-medium">Errores:</p>
        <ul class="list-disc list-inside text-red-600">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
      <form method="GET" action="{{ route('reservas.vista') }}" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
          <label for="filtro_fecha" class="block text-sm font-medium text-slate-700 mb-1">
            Filtrar por Fecha
          </label>
          <input type="date" 
                 id="filtro_fecha" 
                 name="fecha" 
                 value="{{ request('fecha') }}"
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <div class="flex-1">
          <label for="filtro_aula" class="block text-sm font-medium text-slate-700 mb-1">
            Filtrar por Aula
          </label>
          <select id="filtro_aula" 
                  name="aula"
                  class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">-- Todas las aulas --</option>
            @foreach($aulas as $aula)
              <option value="{{ $aula->id_aula }}" {{ request('aula') == $aula->id_aula ? 'selected' : '' }}>
                Aula {{ $aula->nro_aula }} - Módulo {{ $aula->modulo }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="flex items-end gap-2">
          <button type="submit" 
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            Filtrar
          </button>
          
          @if(request('fecha') || request('aula'))
            <a href="{{ route('reservas.vista') }}" 
               class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition flex items-center gap-2">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
              Limpiar
            </a>
          @endif
        </div>
      </form>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-500">Total Reservas</p>
            <p class="text-2xl font-bold text-slate-800">{{ $totalReservas }}</p>
          </div>
          <div class="bg-blue-100 p-3 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-600">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-500">Reservas Hoy</p>
            <p class="text-2xl font-bold text-green-600">{{ $reservasHoy }}</p>
          </div>
          <div class="bg-green-100 p-3 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-green-600">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-500">Próximas</p>
            <p class="text-2xl font-bold text-purple-600">{{ $proximasReservas }}</p>
          </div>
          <div class="bg-purple-100 p-3 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-purple-600">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Lista de Reservas -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
      <div class="p-6 border-b border-slate-200">
        <h2 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
          </svg>
          Lista de Reservas
        </h2>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                Fecha
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                Horario
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                Aula
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                Usuario
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                Motivo
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">
                Acciones
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-slate-200">
            @forelse($reservas as $reserva)
              <tr class="hover:bg-slate-50 transition">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-blue-600">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                    <div>
                      <div class="text-sm font-medium text-slate-900">{{ $reserva->fecha->format('d/m/Y') }}</div>
                      <div class="text-xs text-slate-500">{{ $reserva->dia }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-green-600">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium text-slate-900">
                      {{ substr($reserva->hora_i, 0, 5) }} - {{ substr($reserva->hora_f, 0, 5) }}
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                    Aula {{ $reserva->aula->nro_aula }} - Módulo {{ $reserva->aula->modulo }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                  {{ $reserva->usuario->nombre ?? 'N/A' }}
                </td>
                <td class="px-6 py-4 text-sm text-slate-700">
                  <div class="max-w-xs truncate" title="{{ $reserva->motivo }}">
                    {{ $reserva->motivo }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex gap-2 justify-end">
                    <button onclick="verDetalles({{ $reserva->id_reserva }})" 
                      class="text-blue-600 hover:text-blue-900 transition"
                      title="Ver detalles">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                    </button>
                    <button onclick="openModalEditar({{ $reserva->id_reserva }}, '{{ $reserva->fecha->format('Y-m-d') }}', '{{ substr($reserva->hora_i, 0, 5) }}', '{{ substr($reserva->hora_f, 0, 5) }}', {{ $reserva->id_aula }}, '{{ addslashes($reserva->motivo) }}')" 
                      class="text-yellow-600 hover:text-yellow-900 transition"
                      title="Editar">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                      </svg>
                    </button>
                    <form method="POST" action="{{ route('reservas.destroy', $reserva->id_reserva) }}" 
                          onsubmit="return confirm('¿Está seguro de eliminar esta reserva?');"
                          class="inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" 
                        class="text-red-600 hover:text-red-900 transition"
                        title="Eliminar">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-6 py-12 text-center">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mx-auto text-slate-300 mb-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                  </svg>
                  <p class="text-slate-500">No hay reservas registradas</p>
                  <button onclick="openModalNuevo()" 
                    class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Crear Primera Reserva
                  </button>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Modal Nueva/Editar Reserva -->
  <div id="modalReserva" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
      <div class="p-6 border-b border-slate-200">
        <h3 id="modalTitulo" class="text-xl font-bold text-slate-800">Nueva Reserva</h3>
      </div>

      <form id="formReserva" method="POST" action="{{ route('reservas.store') }}" class="p-6 space-y-4">
        @csrf
        <input type="hidden" id="modalMethod" name="_method" value="POST">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Aula -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-2">
              Aula *
            </label>
            <select name="id_aula" id="modalAula" required
              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">-- Seleccione un aula --</option>
              @foreach($aulas as $aula)
                <option value="{{ $aula->id_aula }}">
                  Aula {{ $aula->nro_aula }} - Módulo {{ $aula->modulo }}
                </option>
              @endforeach
            </select>
          </div>

          <!-- Fecha -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">
              Fecha *
            </label>
            <input type="date" name="fecha" id="modalFecha" required
              min="{{ date('Y-m-d') }}"
              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
          </div>

          <!-- Hora Inicio -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">
              Hora Inicio *
            </label>
            <input type="time" name="hora_i" id="modalHoraI" required
              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
          </div>

          <!-- Hora Fin -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-2">
              Hora Fin *
            </label>
            <input type="time" name="hora_f" id="modalHoraF" required
              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
          </div>

          <!-- Motivo -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-2">
              Motivo de la Reserva *
            </label>
            <textarea name="motivo" id="modalMotivo" rows="3" required
              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Ej: Reunión de equipo, Presentación de proyecto, etc."></textarea>
          </div>
        </div>

        <div class="flex gap-3 pt-4">
          <button type="button" onclick="closeModal()"
            class="flex-1 px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition">
            Cancelar
          </button>
          <button type="submit"
            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            Guardar Reserva
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Detalles -->
  <div id="modalDetalles" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg">
      <div class="p-6 border-b border-slate-200">
        <h3 class="text-xl font-bold text-slate-800">Detalles de la Reserva</h3>
      </div>

      <div id="detallesContenido" class="p-6 space-y-3">
        <!-- Contenido dinámico -->
      </div>

      <div class="p-6 border-t border-slate-200">
        <button onclick="document.getElementById('modalDetalles').classList.add('hidden')"
          class="w-full px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition">
          Cerrar
        </button>
      </div>
    </div>
  </div>

  <script>
    function openModalNuevo() {
      document.getElementById('modalTitulo').textContent = 'Nueva Reserva';
      document.getElementById('formReserva').action = '{{ route('reservas.store') }}';
      document.getElementById('modalMethod').value = 'POST';
      
      // Limpiar formulario
      document.getElementById('modalAula').value = '';
      document.getElementById('modalFecha').value = '';
      document.getElementById('modalHoraI').value = '';
      document.getElementById('modalHoraF').value = '';
      document.getElementById('modalMotivo').value = '';
      
      document.getElementById('modalReserva').classList.remove('hidden');
    }

    function openModalEditar(id, fecha, horaI, horaF, idAula, motivo) {
      document.getElementById('modalTitulo').textContent = 'Editar Reserva';
      document.getElementById('formReserva').action = '/reservas/' + id;
      document.getElementById('modalMethod').value = 'PUT';
      
      document.getElementById('modalAula').value = idAula;
      document.getElementById('modalFecha').value = fecha;
      document.getElementById('modalHoraI').value = horaI;
      document.getElementById('modalHoraF').value = horaF;
      document.getElementById('modalMotivo').value = motivo;
      
      document.getElementById('modalReserva').classList.remove('hidden');
    }

    function closeModal() {
      document.getElementById('modalReserva').classList.add('hidden');
    }

    function verDetalles(id) {
      // Buscar la reserva en los datos actuales
      const reservas = @json($reservas);
      const reserva = reservas.find(r => r.id_reserva === id);
      
      if (!reserva) return;

      const contenido = `
        <div class="space-y-3">
          <div class="flex items-start gap-3 p-3 bg-blue-50 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-blue-600 mt-0.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
            </svg>
            <div>
              <p class="text-sm font-medium text-slate-700">Fecha y Día</p>
              <p class="text-sm text-slate-900">${new Date(reserva.fecha).toLocaleDateString('es-BO')} (${reserva.dia})</p>
            </div>
          </div>

          <div class="flex items-start gap-3 p-3 bg-green-50 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-green-600 mt-0.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
              <p class="text-sm font-medium text-slate-700">Horario</p>
              <p class="text-sm text-slate-900">${reserva.hora_i.substring(0, 5)} - ${reserva.hora_f.substring(0, 5)}</p>
            </div>
          </div>

          <div class="flex items-start gap-3 p-3 bg-purple-50 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-purple-600 mt-0.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
            </svg>
            <div>
              <p class="text-sm font-medium text-slate-700">Aula</p>
              <p class="text-sm text-slate-900">Aula ${reserva.aula.nro_aula} - Módulo ${reserva.aula.modulo}</p>
            </div>
          </div>

          <div class="flex items-start gap-3 p-3 bg-orange-50 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-orange-600 mt-0.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
            </svg>
            <div>
              <p class="text-sm font-medium text-slate-700">Usuario</p>
              <p class="text-sm text-slate-900">${reserva.usuario.nombre}</p>
            </div>
          </div>

          <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-slate-600 mt-0.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            <div>
              <p class="text-sm font-medium text-slate-700">Motivo</p>
              <p class="text-sm text-slate-900">${reserva.motivo}</p>
            </div>
          </div>
        </div>
      `;

      document.getElementById('detallesContenido').innerHTML = contenido;
      document.getElementById('modalDetalles').classList.remove('hidden');
    }

    // Cerrar modales al hacer clic fuera
    document.getElementById('modalReserva').addEventListener('click', function(e) {
      if (e.target === this) closeModal();
    });

    document.getElementById('modalDetalles').addEventListener('click', function(e) {
      if (e.target === this) this.classList.add('hidden');
    });

    // Cerrar modales con tecla ESC
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeModal();
        document.getElementById('modalDetalles').classList.add('hidden');
      }
    });
  </script>

</body>
</html>
