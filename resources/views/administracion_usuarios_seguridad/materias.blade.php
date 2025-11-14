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
      <h1 class="text-2xl font-bold text-slate-800">Gestión de Materias</h1>
      <p class="text-slate-500">Administre las materias del sistema académico</p>
    </header>

    <!-- Botón volver -->
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

    <!-- Tarjeta principal -->
    <div class="bg-white shadow rounded-xl p-4">
      <div class="flex flex-col sm:flex-row justify-between items-center mb-3 gap-2">
        <div>
          <h2 class="font-semibold text-lg">Lista de Materias</h2>
          <p class="text-sm text-slate-500">Total: {{ $materias->total() }} registradas</p>
        </div>

        <label for="modalAgregar"
          class="cursor-pointer inline-flex items-center gap-2 bg-slate-900 text-white px-4 py-2 rounded-lg hover:bg-slate-700 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Agregar Materia
        </label>
      </div>

      <!-- Buscador -->
      <form method="GET" class="mb-3">
        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombre de materia..."
          class="w-full sm:w-80 border rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
      </form>

      <!-- Tabla -->
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-100 text-slate-700">
            <tr>
              <th class="p-3 text-left">Sigla</th>
              <th class="p-3 text-left">Nombre</th>
              <th class="p-3 text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($materias as $m)
              <tr class="border-t hover:bg-slate-50">
                <td class="p-3 font-mono font-semibold text-slate-700">{{ $m->sigla }}</td>
                <td class="p-3">{{ $m->nombre }}</td>
                <td class="p-3 text-center flex justify-center gap-2">
                  <!-- Editar -->
                  <label for="modalEditar-{{ $m->id_materia }}" class="text-blue-600 hover:text-blue-800 cursor-pointer" title="Editar materia">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536M9 11l6 6L5 21l2-7 7-7z"/>
                    </svg>
                  </label>

                  <!-- Eliminar -->
                  <form action="{{ route('materias.destroy', $m->id_materia) }}" method="POST"
                        onsubmit="return confirm('¿Seguro que deseas eliminar esta materia?')" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700 transition" title="Eliminar materia">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                      </svg>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center p-4 text-slate-500">No hay materias registradas.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-4">{{ $materias->links('pagination::tailwind') }}</div>
    </div>
  </main>

  <!-- MODAL AGREGAR -->
  <input type="checkbox" id="modalAgregar" class="hidden peer" />
  <label for="modalAgregar" class="fixed inset-0 bg-black/40 hidden peer-checked:block"></label>

  <div class="fixed hidden peer-checked:flex inset-0 items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-lg shadow-lg relative">
      <label for="modalAgregar"
        class="absolute top-3 right-4 text-slate-500 hover:text-slate-700 cursor-pointer text-xl">&times;</label>

      <form method="POST" action="{{ route('materias.store') }}" class="p-6 space-y-4">
        @csrf
        <h2 class="text-xl font-semibold">Agregar Nueva Materia</h2>

        <div>
          <label class="text-sm font-medium">Sigla *</label>
          <input type="text" name="sigla" maxlength="20" value="{{ old('sigla') }}" required
                 class="w-full border rounded-md p-2" placeholder="Ej: BD2, IS1, etc.">
          @error('sigla')
            <span class="text-red-500 text-sm">{{ $message }}</span>
          @enderror
        </div>

        <div>
          <label class="text-sm font-medium">Nombre *</label>
          <input type="text" name="nombre" value="{{ old('nombre') }}" required
                 class="w-full border rounded-md p-2" placeholder="Nombre completo de la materia">
          @error('nombre')
            <span class="text-red-500 text-sm">{{ $message }}</span>
          @enderror
        </div>

        <div class="flex justify-end gap-2 pt-3">
          <button type="submit" class="bg-slate-900 text-white px-4 py-2 rounded-md hover:bg-slate-800">Guardar</button>
          <label for="modalAgregar"
            class="cursor-pointer border border-slate-300 px-4 py-2 rounded-md hover:bg-slate-100">Cancelar</label>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL EDITAR -->
  @foreach ($materias as $m)
  <input type="checkbox" id="modalEditar-{{ $m->id_materia }}" class="hidden peer" />
  <label for="modalEditar-{{ $m->id_materia }}" class="fixed inset-0 bg-black/40 hidden peer-checked:block"></label>

  <div class="fixed hidden peer-checked:flex inset-0 items-center justify-center p-4 z-50">
    <div class="bg-white rounded-xl w-full max-w-lg shadow-lg relative">
      <label for="modalEditar-{{ $m->id_materia }}"
        class="absolute top-3 right-4 text-slate-500 hover:text-slate-700 cursor-pointer text-xl">&times;</label>

      <form method="POST" action="{{ route('materias.update', $m->id_materia) }}" class="p-6 space-y-4">
        @csrf
        @method('PUT')
        <h2 class="text-xl font-semibold">Editar Materia</h2>

        <div>
          <label class="text-sm font-medium">Sigla *</label>
          <input type="text" name="sigla" maxlength="20" value="{{ $m->sigla }}" required 
                 class="w-full border rounded-md p-2">
          @error('sigla')
            <span class="text-red-500 text-sm">{{ $message }}</span>
          @enderror
        </div>

        <div>
          <label class="text-sm font-medium">Nombre *</label>
          <input type="text" name="nombre" value="{{ $m->nombre }}" required 
                 class="w-full border rounded-md p-2">
          @error('nombre')
            <span class="text-red-500 text-sm">{{ $message }}</span>
          @enderror
        </div>

        <div class="flex justify-end gap-2 pt-3">
          <button type="submit" class="bg-slate-900 text-white px-4 py-2 rounded-md hover:bg-slate-800">Actualizar</button>
          <label for="modalEditar-{{ $m->id_materia }}" class="cursor-pointer border border-slate-300 px-4 py-2 rounded-md hover:bg-slate-100">Cancelar</label>
        </div>
      </form>
    </div>
  </div>
  @endforeach

</body>
</html>
