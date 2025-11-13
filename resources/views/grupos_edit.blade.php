<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Editar Grupo</title>
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
      <h1 class="text-2xl font-bold text-slate-800">Editar Grupo</h1>
      <p class="text-slate-500">Actualice la información del grupo</p>
    </header>

    <!-- BOTÓN VOLVER -->
    <div>
      <a href="{{ route('grupos.vista') }}"
         class="inline-flex items-center gap-2 bg-slate-200 text-slate-700 px-4 py-2 rounded-md hover:bg-slate-300 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h14a1 1 0 001-1V10"/>
        </svg>
        Volver a Grupos
      </a>
    </div>

    <!-- TARJETA DE FORMULARIO -->
    <div class="bg-white shadow rounded-xl p-6 max-w-2xl">
      @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-4">
          <p class="text-red-800 font-semibold mb-2">Errores de validación:</p>
          <ul class="text-red-700 text-sm space-y-1">
            @foreach ($errors->all() as $error)
              <li>• {{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('grupos.update', $grupo->id_grupo) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
          <label class="text-sm font-medium text-slate-700">Código del Grupo *</label>
          <input type="text" name="codigo" value="{{ old('codigo', $grupo->codigo) }}" required maxlength="10"
                 class="w-full border border-slate-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                 placeholder="Ej: A, B, C... (máx 10 caracteres)">
          @error('codigo')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="text-sm font-medium text-slate-700">Materia Asociada *</label>
          <select name="id_materia" required
                  class="w-full border border-slate-300 rounded-md p-2 bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
            <option value="">Seleccione una materia</option>
            @foreach ($materias as $m)
              <option value="{{ $m->id_materia }}" 
                      {{ old('id_materia', $grupo->id_materia) == $m->id_materia ? 'selected' : '' }}>
                {{ $m->sigla }} - {{ $m->nombre }}
              </option>
            @endforeach
          </select>
          @error('id_materia')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-md">
          <input type="checkbox" name="estado" id="estado" class="h-4 w-4 border-slate-300 rounded"
                 {{ old('estado', $grupo->estado) ? 'checked' : '' }}>
          <label for="estado" class="text-sm font-medium text-slate-700">
            Grupo Activo
          </label>
          <span class="text-xs text-slate-500">(Desactiva el grupo si lo deseas desabilitar temporalmente)</span>
        </div>

        <div class="bg-slate-100 p-3 rounded-md text-xs text-slate-600 space-y-1">
          <p><strong>Información del grupo:</strong></p>
          <p>ID: {{ $grupo->id_grupo }}</p>
          <p>Materia Actual: {{ $grupo->materia->sigla ?? 'N/A' }} - {{ $grupo->materia->nombre ?? 'Sin asignar' }}</p>
          <p>Estado: <span class="font-semibold">{{ $grupo->estado ? 'Activo' : 'Inactivo' }}</span></p>
        </div>

        <div class="flex justify-end gap-2 pt-4 border-t">
          <a href="{{ route('grupos.vista') }}"
             class="px-4 py-2 border border-slate-300 text-slate-700 rounded-md hover:bg-slate-100 transition">
            Cancelar
          </a>
          <button type="submit"
                  class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
            Guardar Cambios
          </button>
        </div>
      </form>
    </div>
  </main>

</body>
</html>
