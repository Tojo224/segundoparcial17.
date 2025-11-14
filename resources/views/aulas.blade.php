<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Gestionar Aulas</title>
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
      <a href="{{ route('aulas.vista') }}" class="block px-3 py-2 rounded-md bg-blue-700">Gestionar Aulas</a>
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
<!-- CU13 -->
  <!-- MAIN -->
  <main class="flex-1 p-6 space-y-6">
    <header class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Gestionar Aulas</h1>
        <p class="text-slate-500">Administración de aulas y control de disponibilidad</p>
      </div>
      <button onclick="openModalNuevo()" 
         class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Nueva Aula
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
      <form method="GET" action="{{ route('aulas.vista') }}" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
          <label for="filtro_aula" class="block text-sm font-medium text-slate-700 mb-1">
            Buscar por Aula
          </label>
          <input type="text" 
                 id="filtro_aula" 
                 name="aula" 
                 value="{{ request('aula') }}"
                 placeholder="Ej: 101, A1, Lab..."
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <div class="flex-1">
          <label for="filtro_modulo" class="block text-sm font-medium text-slate-700 mb-1">
            Buscar por Módulo
          </label>
          <input type="text" 
                 id="filtro_modulo" 
                 name="modulo" 
                 value="{{ request('modulo') }}"
                 placeholder="Ej: A, B, Central..."
                 class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="flex items-end gap-2">
          <button type="submit" 
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            Filtrar
          </button>
          
          @if(request('aula') || request('modulo'))
            <a href="{{ route('aulas.vista') }}" 
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
            <p class="text-sm text-slate-500">Total Aulas</p>
            <p class="text-2xl font-bold text-slate-800">{{ $aulas->count() }}</p>
          </div>
          <div class="bg-blue-100 p-3 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-600">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-500">Disponibles</p>
            <p class="text-2xl font-bold text-green-600">{{ $aulas->where('disponible', true)->count() }}</p>
          </div>
          <div class="bg-green-100 p-3 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-green-600">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-slate-500">Horarios Totales</p>
            <p class="text-2xl font-bold text-slate-800">{{ $aulas->sum('horarios_count') }}</p>
          </div>
          <div class="bg-purple-100 p-3 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-purple-600">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabla de Aulas -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
      <div class="p-6 border-b border-slate-200">
        <h2 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
          </svg>
          Lista de Aulas
        </h2>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                Aula
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                Módulo
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                Horarios Asignados
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                Estado
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">
                Acciones
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-slate-200">
            @forelse($aulas as $aula)
              <tr class="hover:bg-slate-50 transition">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-blue-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819" />
                      </svg>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-slate-900">Aula {{ $aula->nro_aula }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 py-1 text-xs font-medium bg-slate-100 text-slate-800 rounded">
                    Módulo {{ $aula->modulo }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-purple-600">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-semibold text-slate-900">{{ $aula->horarios_count }}</span>
                    <span class="text-xs text-slate-500">horarios</span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  @if($aula->disponible)
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      Disponible
                    </span>
                  @else
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                      </svg>
                      Ocupada
                    </span>
                  @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex gap-2 justify-end">
                    <button onclick="verDetalles({{ $aula->id_aula }})" 
                      class="text-blue-600 hover:text-blue-900 transition"
                      title="Ver detalles">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                    </button>
                    <button onclick="openModalEditar({{ $aula->id_aula }}, '{{ $aula->nro_aula }}', '{{ $aula->modulo }}')" 
                      class="text-yellow-600 hover:text-yellow-900 transition"
                      title="Editar">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                      </svg>
                    </button>
                    <form method="POST" action="{{ route('aulas.destroy', $aula->id_aula) }}" 
                          onsubmit="return confirm('¿Está seguro de eliminar esta aula?');"
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
                <td colspan="5" class="px-6 py-12 text-center">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mx-auto text-slate-300 mb-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                  </svg>
                  <p class="text-slate-500">No hay aulas registradas</p>
                  <button onclick="openModalNuevo()" 
                    class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Crear Primera Aula
                  </button>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Modal Nueva/Editar Aula -->
  <div id="modalAula" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
      <div class="p-6 border-b border-slate-200">
        <h3 id="modalTitulo" class="text-xl font-bold text-slate-800">Nueva Aula</h3>
      </div>

      <form id="formAula" method="POST" action="{{ route('aulas.store') }}" class="p-6">
        @csrf
        <input type="hidden" id="modalMethod" name="_method" value="POST">
        <input type="hidden" id="modalId" name="id_aula">

        <div class="space-y-4">
          <!-- Número de Aula -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Número de Aula *</label>
            <input type="text" name="nro_aula" id="modalNroAula" required maxlength="20"
              placeholder="Ej: 101, A1, Lab1"
              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
          </div>

          <!-- Módulo -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Módulo *</label>
            <input type="text" name="modulo" id="modalModulo" required maxlength="10"
              placeholder="Ej: A, B, C, 1, 2"
              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
          </div>
        </div>

        <div class="flex gap-3 mt-6">
          <button type="button" onclick="closeModal()"
            class="flex-1 px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition">
            Cancelar
          </button>
          <button type="submit"
            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            Guardar
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Detalles de Aula -->
  <div id="modalDetalles" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
      <div class="p-6 border-b border-slate-200">
        <h3 class="text-xl font-bold text-slate-800">Detalles del Aula</h3>
      </div>

      <div id="detallesContent" class="p-6">
        <!-- Contenido dinámico -->
      </div>

      <div class="p-6 border-t border-slate-200">
        <button onclick="closeModalDetalles()"
          class="w-full px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition">
          Cerrar
        </button>
      </div>
    </div>
  </div>

  <script>
    // Abrir modal para nueva aula
    function openModalNuevo() {
      document.getElementById('modalTitulo').textContent = 'Nueva Aula';
      document.getElementById('formAula').action = '{{ route("aulas.store") }}';
      document.getElementById('modalMethod').value = 'POST';
      document.getElementById('modalId').value = '';
      document.getElementById('formAula').reset();
      document.getElementById('modalAula').classList.remove('hidden');
    }

    // Abrir modal para editar
    function openModalEditar(id, nroAula, modulo) {
      document.getElementById('modalTitulo').textContent = 'Editar Aula';
      document.getElementById('formAula').action = `/aulas/${id}`;
      document.getElementById('modalMethod').value = 'PUT';
      document.getElementById('modalId').value = id;
      document.getElementById('modalNroAula').value = nroAula;
      document.getElementById('modalModulo').value = modulo;
      document.getElementById('modalAula').classList.remove('hidden');
    }

    // Cerrar modal
    function closeModal() {
      document.getElementById('modalAula').classList.add('hidden');
    }

    // Ver detalles del aula
    async function verDetalles(idAula) {
      const content = document.getElementById('detallesContent');
      content.innerHTML = '<p class="text-center py-8">Cargando...</p>';
      document.getElementById('modalDetalles').classList.remove('hidden');

      try {
        const response = await fetch(`/aulas/${idAula}/disponibilidad`);
        const data = await response.json();

        if (data.success) {
          const aula = data.aula;
          const horarios = data.horarios;

          let horariosHTML = '';
          if (horarios.length > 0) {
            horariosHTML = horarios.map(h => `
              <div class="bg-slate-50 p-3 rounded-lg">
                <div class="flex justify-between items-start">
                  <div>
                    <p class="font-medium text-slate-800">${h.carga?.grupo?.materia?.nombre || 'N/A'}</p>
                    <p class="text-sm text-slate-600">Grupo: ${h.carga?.grupo?.codigo || 'N/A'}</p>
                    <p class="text-sm text-slate-600">Docente: ${h.carga?.docente?.usuario?.nombre || 'N/A'}</p>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-medium text-blue-600">${h.dia}</p>
                    <p class="text-xs text-slate-500">${h.hora_i.substring(0,5)} - ${h.hora_f.substring(0,5)}</p>
                  </div>
                </div>
              </div>
            `).join('');
          } else {
            horariosHTML = '<p class="text-center text-slate-500 py-4">Sin horarios asignados</p>';
          }

          content.innerHTML = `
            <div class="space-y-4">
              <div class="grid grid-cols-2 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                  <p class="text-sm text-blue-600 font-medium">Aula</p>
                  <p class="text-2xl font-bold text-blue-900">${aula.nro_aula}</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                  <p class="text-sm text-purple-600 font-medium">Módulo</p>
                  <p class="text-2xl font-bold text-purple-900">${aula.modulo}</p>
                </div>
              </div>

              <div class="bg-slate-50 p-4 rounded-lg">
                <div class="flex justify-between items-center mb-2">
                  <p class="text-sm font-medium text-slate-700">Ocupación</p>
                  <p class="text-sm font-bold ${aula.disponible ? 'text-green-600' : 'text-orange-600'}">
                    ${aula.ocupacion_porcentaje}%
                  </p>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-3">
                  <div class="${aula.ocupacion_porcentaje < 50 ? 'bg-green-500' : (aula.ocupacion_porcentaje < 80 ? 'bg-yellow-500' : 'bg-red-500')} h-3 rounded-full" 
                       style="width: ${aula.ocupacion_porcentaje}%"></div>
                </div>
                <p class="text-xs text-slate-500 mt-1">${aula.horarios_asignados} de 30 horarios asignados</p>
              </div>

              <div>
                <h4 class="font-semibold text-slate-800 mb-3">Horarios Asignados (${horarios.length})</h4>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                  ${horariosHTML}
                </div>
              </div>
            </div>
          `;
        } else {
          content.innerHTML = '<p class="text-center text-red-500 py-8">Error al cargar detalles</p>';
        }
      } catch (error) {
        console.error('Error:', error);
        content.innerHTML = '<p class="text-center text-red-500 py-8">Error al cargar detalles</p>';
      }
    }

    // Cerrar modal detalles
    function closeModalDetalles() {
      document.getElementById('modalDetalles').classList.add('hidden');
    }

    // Cerrar modales con ESC
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeModal();
        closeModalDetalles();
      }
    });

    // Cerrar modal al hacer clic fuera
    document.getElementById('modalAula').addEventListener('click', function(e) {
      if (e.target === this) closeModal();
    });

    document.getElementById('modalDetalles').addEventListener('click', function(e) {
      if (e.target === this) closeModalDetalles();
    });
  </script>

</body>
</html>
