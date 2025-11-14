<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>CU11 - Gestionar Horarios</title>
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
      <a href="{{ route('horarios.calendario') }}" class="block px-3 py-2 rounded-md bg-blue-700">Gestionar Horarios</a>
      
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
        <h1 class="text-2xl font-bold text-slate-800">CU11 - Gestionar Horarios</h1>
        <p class="text-slate-500">Calendario semanal de clases con gestión de horarios</p>
      </div>
      <button onclick="openModalNuevo()" 
         class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Nuevo Horario
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

    <!-- Leyenda -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
      <h3 class="text-sm font-semibold text-slate-700 mb-3">Leyenda</h3>
      <div class="flex flex-wrap gap-4 text-sm">
        <div class="flex items-center gap-2">
          <div class="w-4 h-4 bg-blue-100 border-2 border-blue-500 rounded"></div>
          <span class="text-slate-700">Clase programada</span>
        </div>
        <div class="flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-slate-600">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
          </svg>
          <span class="text-slate-700">Click para editar</span>
        </div>
        <div class="flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-red-600">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
          </svg>
          <span class="text-slate-700">Click para eliminar</span>
        </div>
      </div>
    </div>

    <!-- Calendario Semanal -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
      <div class="p-6 border-b border-slate-200">
        <h2 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
          </svg>
          Calendario Semanal
        </h2>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full">
          <thead class="bg-slate-50">
            <tr>
              @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $dia)
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase border-r border-slate-200">
                  {{ $dia }}
                </th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            <tr>
              @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $dia)
                <td class="align-top p-4 border-r border-slate-200 bg-slate-50" style="min-height: 400px;">
                  <div class="space-y-2">
                    @php
                      $horariosDia = $horariosSemanal[$dia] ?? collect();
                    @endphp

                    @forelse($horariosDia as $horario)
                      <div class="bg-white border-2 border-blue-500 rounded-lg p-3 shadow-sm hover:shadow-md transition cursor-pointer group">
                        <!-- Horario -->
                        <div class="flex items-center gap-2 mb-2">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-blue-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                          <span class="text-sm font-bold text-blue-700">
                            {{ substr($horario->hora_inicio, 0, 5) }} - {{ substr($horario->hora_fin, 0, 5) }}
                          </span>
                        </div>

                        <!-- Materia -->
                        <div class="text-sm font-semibold text-slate-800 mb-1">
                          {{ $horario->carga->grupo->materia->nombre ?? 'Sin materia' }}
                        </div>

                        <!-- Grupo -->
                        <div class="text-xs text-slate-600 mb-1">
                          Grupo: {{ $horario->carga->grupo->codigo ?? 'N/A' }}
                        </div>

                        <!-- Docente -->
                        <div class="flex items-center gap-1 text-xs text-slate-600 mb-2">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                          </svg>
                          {{ $horario->carga->docente->usuario->nombre ?? 'Sin docente' }}
                        </div>

                        <!-- Aula -->
                        <div class="flex items-center gap-1 text-xs text-slate-600 mb-3">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                          </svg>
                          Aula {{ $horario->aula->nro_aula ?? 'N/A' }} - Módulo {{ $horario->aula->modulo ?? 'N/A' }}
                        </div>

                        <!-- Acciones -->
                        <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition">
                          <button onclick="openModalEditar({{ $horario->id_horario }}, '{{ $horario->dia }}', '{{ $horario->hora_i }}', '{{ $horario->hora_f }}', {{ $horario->id_carga }}, {{ $horario->id_aula }})"
                            class="flex-1 px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition">
                            Editar
                          </button>
                          <form method="POST" action="{{ route('horarios.destroy', $horario->id_horario) }}" 
                                onsubmit="return confirm('¿Eliminar este horario?');" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                              class="w-full px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition">
                              Eliminar
                            </button>
                          </form>
                        </div>
                      </div>
                    @empty
                      <div class="text-center py-8 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 mx-auto mb-2 opacity-50">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        <p class="text-xs">Sin clases</p>
                      </div>
                    @endforelse
                  </div>
                </td>
              @endforeach
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Modal Nuevo/Editar Horario -->
  <div id="modalHorario" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
      <div class="p-6 border-b border-slate-200">
        <h3 id="modalTitulo" class="text-xl font-bold text-slate-800">Nuevo Horario</h3>
      </div>

      <form id="formHorario" method="POST" action="{{ route('horarios.store') }}" class="p-6">
        @csrf
        <input type="hidden" id="modalMethod" name="_method" value="POST">
        <input type="hidden" id="modalId" name="id_horario">

        <!-- Alerta de conflictos (oculta por defecto) -->
        <div id="alertaConflictos" class="hidden mb-4 bg-orange-50 border-l-4 border-orange-500 p-4 rounded">
          <p class="text-orange-800 font-bold mb-2">⚠️ Conflictos Detectados:</p>
          <ul id="listaConflictos" class="list-disc list-inside text-orange-700 text-sm"></ul>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Día -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Día de la Semana *</label>
            <select name="dia" id="modalDia" required
              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">-- Seleccione --</option>
              <option value="Lunes">Lunes</option>
              <option value="Martes">Martes</option>
              <option value="Miércoles">Miércoles</option>
              <option value="Jueves">Jueves</option>
              <option value="Viernes">Viernes</option>
              <option value="Sábado">Sábado</option>
            </select>
          </div>

          <!-- Hora Inicio -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Hora Inicio *</label>
            <input type="time" name="hora_i" id="modalHoraInicio" required
              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
          </div>

          <!-- Hora Fin -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Hora Fin *</label>
            <input type="time" name="hora_f" id="modalHoraFin" required
              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
          </div>

          <!-- Asignación Docente-Grupo -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Asignación (Docente-Grupo) *</label>
            <select name="id_carga" id="modalCarga" required
              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">-- Seleccione --</option>
              @foreach($cargas as $carga)
                <option value="{{ $carga->id_carga }}">
                  {{ $carga->docente->usuario->nombre ?? 'N/A' }} - 
                  {{ $carga->grupo->materia->nombre ?? 'N/A' }} 
                  (Grupo {{ $carga->grupo->codigo ?? 'N/A' }})
                </option>
              @endforeach
            </select>
          </div>

          <!-- Aula -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-2">Aula *</label>
            <select name="id_aula" id="modalAula" required
              class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">-- Seleccione --</option>
              @foreach($aulas as $aula)
                <option value="{{ $aula->id_aula }}">
                  Aula {{ $aula->nro_aula }} - Módulo {{ $aula->modulo }}
                </option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="flex gap-3 mt-6">
          <button type="button" onclick="closeModal()"
            class="flex-1 px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition">
            Cancelar
          </button>
          <button type="button" onclick="verificarConflictos()"
            class="flex-1 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
            Verificar Conflictos
          </button>
          <button type="submit"
            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            Guardar
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Abrir modal para nuevo horario
    function openModalNuevo() {
      document.getElementById('modalTitulo').textContent = 'Nuevo Horario';
      document.getElementById('formHorario').action = '{{ route("horarios.store") }}';
      document.getElementById('modalMethod').value = 'POST';
      document.getElementById('modalId').value = '';
      document.getElementById('formHorario').reset();
      document.getElementById('alertaConflictos').classList.add('hidden');
      document.getElementById('modalHorario').classList.remove('hidden');
    }

    // Abrir modal para editar
    function openModalEditar(id, dia, horaInicio, horaFin, idCarga, idAula) {
      document.getElementById('modalTitulo').textContent = 'Editar Horario';
      document.getElementById('formHorario').action = `/horarios/${id}`;
      document.getElementById('modalMethod').value = 'PUT';
      document.getElementById('modalId').value = id;
      document.getElementById('modalDia').value = dia;
      document.getElementById('modalHoraInicio').value = horaInicio.substring(0, 5);
      document.getElementById('modalHoraFin').value = horaFin.substring(0, 5);
      document.getElementById('modalCarga').value = idCarga;
      document.getElementById('modalAula').value = idAula;
      document.getElementById('alertaConflictos').classList.add('hidden');
      document.getElementById('modalHorario').classList.remove('hidden');
    }

    // Cerrar modal
    function closeModal() {
      document.getElementById('modalHorario').classList.add('hidden');
    }

    // Verificar conflictos vía AJAX
    async function verificarConflictos() {
      const dia = document.getElementById('modalDia').value;
      const horaInicio = document.getElementById('modalHoraInicio').value;
      const horaFin = document.getElementById('modalHoraFin').value;
      const idCarga = document.getElementById('modalCarga').value;
      const idAula = document.getElementById('modalAula').value;
      const idHorario = document.getElementById('modalId').value;

      if (!dia || !horaInicio || !horaFin || !idCarga || !idAula) {
        alert('Por favor complete todos los campos obligatorios');
        return;
      }

      try {
        const response = await fetch('{{ route("horarios.verificar-conflictos") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
          dia: dia,
          hora_i: horaInicio,
          hora_f: horaFin,
          id_carga: idCarga,
          id_aula: idAula,
          id_horario_excluir: idHorario || null
        })
      });        const data = await response.json();
        const alertaDiv = document.getElementById('alertaConflictos');
        const listaUl = document.getElementById('listaConflictos');

        if (data.tiene_conflictos) {
          listaUl.innerHTML = '';
          data.conflictos.forEach(conf => {
            const li = document.createElement('li');
            li.textContent = conf.mensaje;
            listaUl.appendChild(li);
          });
          alertaDiv.classList.remove('hidden');
        } else {
          alertaDiv.classList.add('hidden');
          alert('✓ No se detectaron conflictos. Puede guardar el horario.');
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Error al verificar conflictos');
      }
    }

    // Cerrar modal con ESC
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeModal();
      }
    });

    // Cerrar modal al hacer clic fuera
    document.getElementById('modalHorario').addEventListener('click', function(e) {
      if (e.target === this) {
        closeModal();
      }
    });
  </script>

</body>
</html>
