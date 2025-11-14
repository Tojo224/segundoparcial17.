<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gestión de Materias</title>
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
      <a href="{{ route('materias.vista') }}" class="block px-3 py-2 rounded-md bg-blue-700">Gestionar Materias</a>
      <a href="{{ route('grupos.vista') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Gestionar Grupos</a>

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
      <h1 class="text-2xl font-bold text-slate-800">Gestión de Materias</h1>
      <p class="text-slate-500">Administre las materias del sistema académico</p>
    </header>

    <!-- FORMULARIO DE AGREGAR MATERIA (VISIBLE ARRIBA) -->
    <div class="bg-white shadow rounded-xl p-6">
      <h2 class="text-xl font-semibold mb-4 text-slate-800">Agregar Nueva Materia</h2>
      
      @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-red-700 font-semibold mb-2">Errores encontrados:</p>
          <ul class="text-red-600 text-sm space-y-1">
            @foreach ($errors->all() as $error)
              <li>• {{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
          <p class="text-green-700 font-semibold">✓ {{ session('success') }}</p>
        </div>
      @endif

      <form method="POST" action="{{ route('materias.store') }}" class="space-y-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Sigla *</label>
            <input type="text" name="sigla" maxlength="20" value="{{ old('sigla') }}" required
                   class="w-full border border-slate-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" 
                   placeholder="Ej: INF322, MAT103, etc.">
            @error('sigla')
              <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nombre *</label>
            <input type="text" name="nombre" value="{{ old('nombre') }}" required
                   class="w-full border border-slate-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" 
                   placeholder="Nombre completo de la materia">
            @error('nombre')
              <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-2">
          <button type="submit" class="bg-slate-900 text-white px-6 py-2 rounded-md hover:bg-slate-800 transition font-medium">
            Guardar Materia
          </button>
          <button type="reset" class="bg-slate-200 text-slate-700 px-6 py-2 rounded-md hover:bg-slate-300 transition font-medium">
            Limpiar
          </button>
        </div>
      </form>
    </div>

    <!-- LISTA DE MATERIAS -->
    <div class="bg-white shadow rounded-xl p-4">
      <div class="mb-4">
        <h2 class="font-semibold text-lg text-slate-800">Lista de Materias</h2>
        <p class="text-sm text-slate-500">Total: {{ $materias->total() }} registradas</p>
      </div>

      <!-- Buscador -->
      <form method="GET" class="mb-4">
        <input type="text" name="buscar" value="{{ request('buscar') }}" 
               placeholder="Buscar por nombre de materia..."
               class="w-full sm:w-80 border border-slate-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
      </form>

      <!-- Tabla -->
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-100 text-slate-700">
            <tr>
              <th class="p-3 text-left font-semibold">Sigla</th>
              <th class="p-3 text-left font-semibold">Nombre</th>
              <th class="p-3 text-center font-semibold">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($materias as $m)
              <tr class="border-t hover:bg-slate-50 transition">
                <td class="p-3 font-mono font-semibold text-slate-800">{{ $m->sigla }}</td>
                <td class="p-3 text-slate-700">{{ $m->nombre }}</td>
                <td class="p-3 text-center">
                  <div class="flex justify-center gap-3">
                    <!-- Botón Editar -->
                    <button type="button" onclick="openEditModal({{ $m->id_materia }}, '{{ $m->sigla }}', '{{ $m->nombre }}')" 
                            class="text-blue-600 hover:text-blue-800 transition" title="Editar materia">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </button>

                    <!-- Botón Eliminar -->
                    <form action="{{ route('materias.destroy', $m->id_materia) }}" method="POST" style="display:inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="text-red-500 hover:text-red-700 transition" title="Eliminar materia"
                              onclick="return confirm('¿Seguro que deseas eliminar esta materia?')">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center p-8 text-slate-500">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                  </svg>
                  No hay materias registradas aún.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-4">{{ $materias->links('pagination::tailwind') }}</div>
    </div>

    <!-- Botón volver al dashboard -->
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
  </main>

  <!-- MODAL EDITAR (Oculto, se abre con JavaScript) -->
  <div id="modalEditar" class="hidden fixed inset-0 bg-black/40 z-40 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-lg shadow-lg">
      <div class="flex justify-between items-center p-6 border-b border-slate-200">
        <h2 class="text-xl font-semibold text-slate-800">Editar Materia</h2>
        <button type="button" onclick="closeEditModal()" 
                class="text-slate-500 hover:text-slate-700 text-2xl font-light">&times;</button>
      </div>

      <form id="formEditar" method="POST" class="p-6 space-y-4">
        @csrf
        @method('PUT')
        
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Sigla *</label>
          <input type="text" id="editSigla" name="sigla" maxlength="20" required 
                 class="w-full border border-slate-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">Nombre *</label>
          <input type="text" id="editNombre" name="nombre" required 
                 class="w-full border border-slate-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div class="flex justify-end gap-2 pt-4">
          <button type="submit" class="bg-slate-900 text-white px-6 py-2 rounded-md hover:bg-slate-800 transition font-medium">
            Actualizar
          </button>
          <button type="button" onclick="closeEditModal()" 
                  class="bg-slate-200 text-slate-700 px-6 py-2 rounded-md hover:bg-slate-300 transition font-medium">
            Cancelar
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openEditModal(id, sigla, nombre) {
      document.getElementById('editSigla').value = sigla;
      document.getElementById('editNombre').value = nombre;
      document.getElementById('formEditar').action = `/materias/${id}`;
      document.getElementById('modalEditar').classList.remove('hidden');
    }

    function closeEditModal() {
      document.getElementById('modalEditar').classList.add('hidden');
    }

    // Cerrar modal si se hace clic en el fondo
    document.getElementById('modalEditar').addEventListener('click', function(e) {
      if (e.target === this) {
        closeEditModal();
      }
    });
  </script>

</body>
</html>
