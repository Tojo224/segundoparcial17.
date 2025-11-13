<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gestión de Grupos</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-50 flex flex-col lg:flex-row">

  <!-- SIDEBAR -->
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
      <a href="{{ route('docentes.vista') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Gestionar Docentes</a>
      <a href="{{ route('materias.vista') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Gestionar Materias</a>
      <a href="{{ route('grupos.vista') }}" class="block px-3 py-2 rounded-md bg-blue-700">Gestionar Grupos</a>

      <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <button type="submit" class="flex items-center gap-2 w-full px-3 py-2 rounded-md text-red-500 hover:bg-red-100 transition">
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
    <header>
      <h1 class="text-2xl font-bold text-slate-800">Gestión de Grupos</h1>
      <p class="text-slate-500">Administre los grupos por materia</p>
    </header>

    @if (session('success'))
      <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <div>
          <h3 class="font-semibold text-green-800">¡Éxito!</h3>
          <p class="text-green-700">{{ session('success') }}</p>
        </div>
      </div>
    @endif

    @if (session('error'))
      <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <div>
          <h3 class="font-semibold text-red-800">Error</h3>
          <p class="text-red-700">{{ session('error') }}</p>
        </div>
      </div>
    @endif

    <!-- BOTÓN VOLVER -->
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

    <!-- TARJETA -->
    <div class="bg-white shadow rounded-xl p-4">
      <div class="flex flex-col sm:flex-row justify-between items-center mb-3 gap-2">
        <div>
          <h2 class="font-semibold text-lg">Lista de Grupos</h2>
          <p class="text-sm text-slate-500">Total: {{ $grupos->total() }} registrados</p>
        </div>

        <!-- Abrir modal -->
        <label for="modalAgregar"
          class="cursor-pointer inline-flex items-center gap-2 bg-slate-900 text-white px-4 py-2 rounded-lg hover:bg-slate-700 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Agregar Grupo
        </label>
      </div>

      <!-- BUSCADOR POR MATERIA -->
      <form method="GET" class="mb-3">
        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por materia (ej: Base, Sistemas, SI-...)..."
          class="w-full sm:w-96 border rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
      </form>

      <!-- TABLA -->
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-100 text-slate-700">
            <tr>
              <th class="p-3 text-left">Código</th>
              <th class="p-3 text-left">Materia (Sigla - Nombre)</th>
              <th class="p-3 text-center">Estado</th>
              <th class="p-3 text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($grupos as $g)
              <tr class="border-t hover:bg-slate-50">
                <td class="p-3 font-mono font-bold">{{ $g->codigo }}</td>
                <td class="p-3">
                  @if ($g->materia)
                    <span class="font-semibold text-slate-900">{{ $g->materia->sigla }}</span> - {{ $g->materia->nombre }}
                  @else
                    <span class="text-slate-500 italic">Sin materia asignada</span>
                  @endif
                </td>
                <td class="p-3 text-center">
                  @if ($g->estado)
                    <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">Activo</span>
                  @else
                    <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-700 rounded-full">Inactivo</span>
                  @endif
                </td>
                <td class="p-3 text-center flex justify-center gap-2">
                  <!-- BOTÓN EDITAR -->
                  <a href="{{ route('grupos.edit', $g->id_grupo) }}" class="inline-flex items-center gap-1 bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700 transition text-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 9l-12.231 12.231" />
                    </svg>
                    Editar
                  </a>

                  <!-- BOTÓN DESACTIVAR -->
                  <form action="{{ route('grupos.destroy', $g->id_grupo) }}" method="POST"
                        onsubmit="return confirm('¿Desactivar este grupo?')" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1 bg-red-600 text-white px-3 py-1 rounded-md hover:bg-red-700 transition text-xs" title="Desactivar grupo">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                      Desactivar
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center p-4 text-slate-500">No hay grupos registrados.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-4">{{ $grupos->links('pagination::tailwind') }}</div>
    </div>
  </main>

  <!-- MODAL AGREGAR -->
  <input type="checkbox" id="modalAgregar" class="hidden peer" />
  <label for="modalAgregar" class="fixed inset-0 bg-black/40 hidden peer-checked:block z-40"></label>

  <div class="fixed hidden peer-checked:flex inset-0 items-center justify-center p-4 z-50">
    <div class="bg-white rounded-xl w-full max-w-lg shadow-lg relative">
      <label for="modalAgregar"
        class="absolute top-3 right-4 text-slate-500 hover:text-slate-700 cursor-pointer text-xl">&times;</label>

      <form method="POST" action="{{ route('grupos.store') }}" class="p-6 space-y-4">
        @csrf
        <h2 class="text-xl font-semibold">Agregar Nuevo Grupo</h2>
        <p class="text-slate-500 text-sm">Complete la información del grupo</p>

        @if ($errors->any())
          <div class="bg-red-50 border border-red-200 rounded-md p-3">
            <p class="text-red-800 font-semibold text-sm mb-2">Errores de validación:</p>
            <ul class="text-red-700 text-sm space-y-1">
              @foreach ($errors->all() as $error)
                <li>• {{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div>
          <label class="text-sm font-medium">Código *</label>
          <input type="text" name="codigo" value="{{ old('codigo') }}" required maxlength="10" class="w-full border rounded-md p-2" placeholder="Ej: A, B, C... (máx 10 caracteres)">
          @error('codigo')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="text-sm font-medium">Materia *</label>
          <select name="id_materia" required class="w-full border rounded-md p-2 bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
            <option value="">Seleccione una materia</option>
            @foreach ($materias as $m)
              <option value="{{ $m->id_materia }}" {{ old('id_materia') == $m->id_materia ? 'selected' : '' }}>
                {{ $m->sigla }} - {{ $m->nombre }}
              </option>
            @endforeach
          </select>
          @error('id_materia')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center gap-2">
          <input type="checkbox" name="estado" id="estado" class="h-4 w-4 border rounded" checked>
          <label for="estado" class="text-sm font-medium">Activo</label>
        </div>

        <div class="flex justify-end gap-2 pt-3">
          <button type="submit" class="bg-slate-900 text-white px-4 py-2 rounded-md hover:bg-slate-800">Guardar</button>
          <label for="modalAgregar"
            class="cursor-pointer border border-slate-300 px-4 py-2 rounded-md hover:bg-slate-100">Cancelar</label>
        </div>
      </form>
    </div>
  </div>

</body>
</html>
