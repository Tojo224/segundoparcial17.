<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Asignar Grupos a Docentes</title>
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
      <a href="{{ route('carga-horaria.asignar') }}" class="block px-3 py-2 rounded-md bg-blue-700">Asignar Grupos</a>
      <a href="{{ route('carga-horaria.vista') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Ver Asignaciones</a>
      
      <!-- Aulas y Horarios -->
      <div class="pt-2 pb-1">
        <p class="px-3 text-xs font-semibold text-slate-400 uppercase">Aulas y Horarios</p>
      </div>
      <a href="{{ route('horarios.calendario') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Gestionar Horarios</a>
      
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

<!-- CU10 - Asignar Grupos a Docentes Frontend-->

  <!-- MAIN -->
  <main class="flex-1 p-6 space-y-6 overflow-x-auto">
    <header>
      <h1 class="text-2xl font-bold text-slate-800"> Asignar Grupos a Docentes</h1>
      <p class="text-slate-500">Seleccione un docente y asigne grupos con sus respectivas horas</p>
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

    <!-- Formulario de selección de docente -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
      <h2 class="text-lg font-semibold text-slate-800 mb-4 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
        </svg>
        1. Seleccionar Docente
      </h2>

      <form method="GET" action="{{ route('carga-horaria.asignar') }}" class="flex gap-4 items-end">
        <div class="flex-1">
          <label class="block text-sm font-medium text-slate-700 mb-2">Docente</label>
          <select name="id_docente" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
            <option value="">-- Seleccione un docente --</option>
            @foreach($docentes as $docente)
              <option value="{{ $docente->id_docente }}" {{ $docenteSeleccionado == $docente->id_docente ? 'selected' : '' }}>
                {{ $docente->cod_docente }} - {{ $docente->usuario->nombre ?? 'Sin usuario' }} ({{ $docente->carrera }})
              </option>
            @endforeach
          </select>
        </div>

        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
          Ver Docente
        </button>
      </form>

      @if($docenteInfo)
        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
          <p class="text-sm font-medium text-blue-900">Docente seleccionado:</p>
          <p class="text-blue-700">
            <strong>{{ $docenteInfo->usuario->nombre ?? 'N/A' }}</strong> 
            | CI: {{ $docenteInfo->usuario->ci ?? 'N/A' }}
            | Código: {{ $docenteInfo->cod_docente }}
            | Carrera: {{ $docenteInfo->carrera }}
          </p>
        </div>
      @endif
    </div>

    @if($docenteSeleccionado)
      <!-- Asignaciones actuales del docente -->
      @if(count($asignacionesActuales) > 0)
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
          <h2 class="text-lg font-semibold text-slate-800 mb-4 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
            </svg>
            Grupos Asignados al Docente
          </h2>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
              <thead class="bg-slate-50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Código Grupo</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Materia</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Sigla</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Horas</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Acciones</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-slate-200">
                @foreach($asignacionesActuales as $asignacion)
                  <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $asignacion->grupo->codigo ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-sm text-slate-700">{{ $asignacion->grupo->materia->nombre ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $asignacion->grupo->materia->sigla ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-sm text-slate-700">{{ $asignacion->horas_asignadas }} hrs</td>
                    <td class="px-4 py-3 text-sm">
                      <form method="POST" action="{{ route('carga-horaria.eliminar', $asignacion->id_carga) }}" 
                            onsubmit="return confirm('¿Está seguro de eliminar esta asignación?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Eliminar</button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endif

      <!-- Tabla de grupos disponibles para asignar -->
      <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
        <h2 class="text-lg font-semibold text-slate-800 mb-4 flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          2. Grupos Disponibles para Asignar
        </h2>

        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Código</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Materia</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Sigla</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Estado</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Acción</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
              @foreach($gruposDisponibles as $grupo)
                <tr class="hover:bg-slate-50">
                  <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $grupo->codigo }}</td>
                  <td class="px-4 py-3 text-sm text-slate-700">{{ $grupo->materia->nombre ?? 'N/A' }}</td>
                  <td class="px-4 py-3 text-sm text-slate-600">{{ $grupo->materia->sigla ?? 'N/A' }}</td>
                  <td class="px-4 py-3">
                    @if($grupo->estado)
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
                    @else
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inactivo</span>
                    @endif
                  </td>
                  <td class="px-4 py-3">
                    <button onclick="openModal({{ $grupo->id_grupo }}, '{{ $grupo->codigo }}', '{{ $grupo->materia->nombre ?? '' }}', '{{ $grupo->materia->sigla ?? '' }}')"
                      class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">
                      Asignar
                    </button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @endif
  </main>

  <!-- Modal para confirmar asignación -->
  <div id="modalAsignar" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
      <h3 class="text-xl font-bold text-slate-800 mb-4">Confirmar Asignación de Grupo</h3>

      <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded">
        <p class="text-sm text-slate-700"><strong>Grupo:</strong> <span id="modalGrupoCodigo"></span></p>
        <p class="text-sm text-slate-700"><strong>Materia:</strong> <span id="modalMateriaNombre"></span> (<span id="modalMateriaSigla"></span>)</p>
      </div>

      <form method="POST" action="{{ route('carga-horaria.asignar.store') }}">
        @csrf
        <input type="hidden" name="id_docente" value="{{ $docenteSeleccionado }}">
        <input type="hidden" name="id_grupo" id="modalGrupoId">

        <div class="mb-4">
          <label class="block text-sm font-medium text-slate-700 mb-2">Horas Asignadas *</label>
          <input type="number" name="horas_asignadas" min="1" max="40" required
            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Ej: 4">
          <p class="text-xs text-slate-500 mt-1">Ingrese las horas semanales (1-40)</p>
        </div>

        <div class="flex gap-3">
          <button type="button" onclick="closeModal()"
            class="flex-1 px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition">
            Cancelar
          </button>
          <button type="submit"
            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            Confirmar Asignación
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openModal(idGrupo, codigo, materia, sigla) {
      document.getElementById('modalGrupoId').value = idGrupo;
      document.getElementById('modalGrupoCodigo').textContent = codigo;
      document.getElementById('modalMateriaNombre').textContent = materia;
      document.getElementById('modalMateriaSigla').textContent = sigla;
      document.getElementById('modalAsignar').classList.remove('hidden');
    }

    function closeModal() {
      document.getElementById('modalAsignar').classList.add('hidden');
    }

    // Cerrar modal al hacer clic fuera
    document.getElementById('modalAsignar').addEventListener('click', function(e) {
      if (e.target === this) {
        closeModal();
      }
    });

    // Cerrar modal con tecla ESC
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeModal();
      }
    });
  </script>

</body>
</html>
