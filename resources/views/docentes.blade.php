<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gestión de Docentes</title>
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
      <a href="{{ route('usuarios.vista') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800">Gestionar Usuarios</a>
      <a href="{{ route('docentes.vista') }}" class="block px-3 py-2 rounded-md bg-blue-700">Gestionar Docentes</a>

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
    <header>
      <h1 class="text-2xl font-bold text-slate-800">Gestión de Docentes</h1>
      <p class="text-slate-500">Administre la información y carga horaria de los docentes</p>
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

    <!-- Botón Volver -->
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

    <!-- Tarjeta -->
    <div class="bg-white shadow rounded-xl p-4">
      <div class="flex flex-col sm:flex-row justify-between items-center mb-3 gap-2">
        <div>
          <h2 class="font-semibold text-lg">Lista de Docentes</h2>
          <p class="text-sm text-slate-500">Total: {{ $docentes->total() }} registrados</p>
        </div>

        <!-- Abrir modal -->
        <label for="modalAgregar"
          class="cursor-pointer inline-flex items-center gap-2 bg-slate-900 text-white px-4 py-2 rounded-lg hover:bg-slate-700 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Agregar Docente
        </label>
      </div>

      <!-- Buscador -->
      <form method="GET" class="mb-3">
        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar docente..."
          class="w-full sm:w-64 border rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
      </form>

      <!-- Tabla -->
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-100 text-slate-700">
            <tr>
              <th class="p-3 text-left">Código</th>
              <th class="p-3 text-left">Nombre</th>
              <th class="p-3 text-left">Correo</th>
              <th class="p-3 text-left">Carrera</th>
              <th class="p-3 text-left">Maestría</th>
              <th class="p-3 text-left">NIT</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($docentes as $doc)
              <tr class="border-t hover:bg-slate-50">
                <td class="p-3 font-mono">{{ $doc->cod_docente }}</td>
                <td class="p-3 font-medium">{{ $doc->usuario->nombre ?? 'Sin usuario' }}</td>
                <td class="p-3 text-slate-600">{{ $doc->usuario->correo ?? '—' }}</td>
                <td class="p-3">{{ $doc->carrera ?? '—' }}</td>
                <td class="p-3">{{ $doc->maestria ?? '—' }}</td>
                <td class="p-3">{{ $doc->nit ?? '—' }}</td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center p-4 text-slate-500">No hay docentes registrados.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-4">{{ $docentes->links('pagination::tailwind') }}</div>
    </div>
  </main>

  <!-- MODAL -->
  <input type="checkbox" id="modalAgregar" class="hidden peer" />
  <label for="modalAgregar" class="fixed inset-0 bg-black/40 hidden peer-checked:block"></label>

  <div class="fixed hidden peer-checked:flex inset-0 items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl w-full max-w-lg shadow-lg relative max-h-[90vh] overflow-y-auto">
        <label for="modalAgregar"
         class="absolute top-3 right-4 text-slate-500 hover:text-slate-700 cursor-pointer text-xl">&times;</label>

      <!-- FORMULARIO -->
      <form method="POST" action="{{ route('docentes.store') }}" class="p-6 space-y-4">
        @csrf
        <h2 class="text-xl font-semibold">Agregar Nuevo Docente</h2>
        <p class="text-slate-500 text-sm">Complete la información del docente</p>

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
          <label class="text-sm font-medium">Código Docente *</label>
          <input type="text" name="cod_docente" value="{{ old('cod_docente') }}" required
                 class="w-full border rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div>
          <label class="text-sm font-medium">NIT</label>
          <input type="text" name="nit" value="{{ old('nit') }}"
                 class="w-full border rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div>
          <label class="text-sm font-medium">Nombre Completo *</label>
          <input type="text" name="nombre" value="{{ old('nombre') }}" required
                 class="w-full border rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div>
          <label class="text-sm font-medium">Cédula de Identidad (CI) *</label>
          <input type="text" name="CI" value="{{ old('CI') }}" required
                 class="w-full border rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div>
          <label class="text-sm font-medium">Correo Electrónico *</label>
          <input type="email" name="correo" value="{{ old('correo') }}" required
                 class="w-full border rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div>
          <label class="text-sm font-medium">Teléfono</label>
          <input type="text" name="telefono" value="{{ old('telefono') }}"
                 class="w-full border rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div>
          <label class="text-sm font-medium">Dirección</label>
          <input type="text" name="direccion" value="{{ old('direccion') }}"
                 class="w-full border rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div>
          <label class="text-sm font-medium">Sexo *</label>
          <select name="sexo" required
                  class="w-full border rounded-md p-2 bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
            <option value="">Seleccione</option>
            <option value="M" {{ old('sexo') == 'M' ? 'selected' : '' }}>Masculino</option>
            <option value="F" {{ old('sexo') == 'F' ? 'selected' : '' }}>Femenino</option>
          </select>
        </div>

        <div>
          <label class="text-sm font-medium">Estado Civil *</label>
          <input type="text" name="estado_civil" value="{{ old('estado_civil') }}" required
                 class="w-full border rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div>
          <label class="text-sm font-medium">Carrera *</label>
          <select name="carrera" required
                  class="w-full border rounded-md p-2 bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
            <option value="">Seleccione una carrera</option>
            <option value="Ingeniería en Sistemas">Ingeniería en Sistemas</option>
            <option value="Ingeniería en Redes y Telecomunicaciones">Ingeniería en Redes y Telecomunicaciones</option>
            <option value="Ingeniería Informática">Ingeniería Informática</option>
            <option value="Ingeniería Robótica">Ingeniería Robótica</option>
          </select>
        </div>

        <div>
          <label class="text-sm font-medium">Maestría</label>
          <input type="text" name="maestria" value="{{ old('maestria') }}"
                 class="w-full border rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div class="flex justify-end gap-2 pt-3">
          <button type="submit"
                  class="bg-slate-900 text-white px-4 py-2 rounded-md hover:bg-slate-800 transition">
            Guardar
          </button>
          <label for="modalAgregar"
                 class="cursor-pointer border border-slate-300 px-4 py-2 rounded-md hover:bg-slate-100">
            Cancelar
          </label>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
