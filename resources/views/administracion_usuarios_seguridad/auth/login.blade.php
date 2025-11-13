<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión — Sistema de Gestión Académica</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-blue-100 to-blue-200 flex items-center justify-center p-6">
    <div class="w-full max-w-md">
        <div class="relative mx-auto bg-white/90 backdrop-blur rounded-2xl shadow-xl ring-1 ring-white/50">
            <div class="px-8 pt-10 pb-8">
                <div class="mx-auto -mt-16 mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-primary-700 shadow-md ring-4 ring-white/60">
                    <!-- Ícono birrete (graduation cap) -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-8 w-8 text-white">
                        <path d="M11.7 2.04a1 1 0 0 1 .6 0l9 3a1 1 0 0 1 0 1.92l-3.2 1.07v3.02a2 2 0 0 1-1.2 1.84l-4 1.78a2 2 0 0 1-1.6 0l-4-1.78a2 2 0 0 1-1.2-1.84V8.03L1.7 6.96a1 1 0 0 1 0-1.92l9-3ZM6.5 8.7l5.1 2.26a1 1 0 0 0 .8 0l5.1-2.26-5.5-1.84-5.5 1.84Zm13.2 5.62a.75.75 0 0 1 1.05.07c.38.43.75.95.75 1.61 0 1.26-.9 2.36-2.11 3.09-1.13.69-2.64 1.12-4.29 1.12s-3.16-.43-4.29-1.12C9.1 19.36 8.2 18.26 8.2 17c0-.66.37-1.18.75-1.61a.75.75 0 0 1 1.12.98c-.15.17-.37.44-.37.63 0 .6.54 1.34 1.57 1.96.93.57 2.22.93 3.65.93s2.72-.36 3.65-.93c1.03-.62 1.57-1.36 1.57-1.96 0-.19-.22-.46-.37-.63a.75.75 0 0 1 .07-1.05Z"/>
                    </svg>
                </div>

                <div class="text-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Sistema de Gestión Académica</h1>
                    <p class="mt-1 text-sm text-gray-600">FICCT - Facultad de Ciencias y Computación</p>
                </div>

                @if (session('success'))
                    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ url('/login') }}" class="space-y-4">
                    @csrf

                    <!-- Correo -->
                    <div>
                        <label for="correo" class="mb-1 block text-sm font-medium text-gray-700">Correo Electrónico</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <!-- Ícono sobre -->
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                    <path d="M1.5 7.125C1.5 6.06 2.36 5.2 3.425 5.2h17.15c1.065 0 1.925.86 1.925 1.925v9.95c0 1.065-.86 1.925-1.925 1.925H3.425A1.925 1.925 0 0 1 1.5 17.075v-9.95Zm2.5-.125a.5.5 0 0 0-.5.5v.264l7.827 4.477a2.5 2.5 0 0 0 2.346 0L21 7.764V7.5a.5.5 0 0 0-.5-.5H4Z"/>
                                    <path d="M21 9.235l-6.827 3.91a4.5 4.5 0 0 1-4.346 0L3 9.235v7.84a.5.5 0 0 0 .5.5h17a.5.5 0 0 0 .5-.5V9.235Z"/>
                                </svg>
                            </span>
                            <input id="correo" name="correo" type="email" value="{{ old('correo') }}" required autocomplete="email"
                                   class="block w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-11 pr-3 text-gray-900 placeholder-gray-400 shadow-sm focus:border-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="nombre@correo.com">
                        </div>
                    </div>

                    <!-- Contraseña -->
                    <div>
                        <label for="password" class="mb-1 block text-sm font-medium text-gray-700">Contraseña</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <!-- Ícono candado -->
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                    <path d="M12 1.75a4.75 4.75 0 0 0-4.75 4.75v2.25H6A2.75 2.75 0 0 0 3.25 11.5v7.75A2.75 2.75 0 0 0 6 22h12a2.75 2.75 0 0 0 2.75-2.75V11.5A2.75 2.75 0 0 0 18 8.75h-1.25V6.5A4.75 4.75 0 0 0 12 1.75Zm-3.25 6V6.5a3.25 3.25 0 1 1 6.5 0v1.25h-6.5Z"/>
                                </svg>
                            </span>
                            <input id="password" name="password" type="password" required autocomplete="current-password"
                                   class="block w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-11 pr-3 text-gray-900 placeholder-gray-400 shadow-sm focus:border-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="#" class="text-sm text-primary-700 hover:text-primary-800 hover:underline">¿Olvidó su contraseña?</a>
                    </div>

                    <button type="submit"
                            class="mt-1 inline-flex w-full items-center justify-center rounded-lg bg-primary-700 px-4 py-2.5 font-semibold text-white shadow-sm transition hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        Iniciar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

