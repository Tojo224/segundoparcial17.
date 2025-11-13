<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cambiar Contraseña — Sistema de Gestión Académica</title>
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
                <div class="mx-auto -mt-16 mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-yellow-500 shadow-md ring-4 ring-white/60">
                    <!-- Ícono de llave -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-8 w-8 text-white">
                        <path d="M7 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM7 12a6 6 0 0 1 5.5 9M7 12a6 6 0 0 0-5.5 9m11-16.5a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm5.5 2.5a6 6 0 0 1 0 12H7a6 6 0 0 1 0-12h11.5Z"/>
                    </svg>
                </div>

                <div class="text-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Cambiar Contraseña</h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Bienvenido <strong>{{ Auth::user()->nombre }}</strong>. 
                        Por seguridad, debe cambiar su contraseña la primera vez que ingresa.
                    </p>
                </div>

                <!-- Alertas de error -->
                @if ($errors->any())
                    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3">
                        <p class="text-sm font-semibold text-red-800 mb-2">Errores de validación:</p>
                        <ul class="text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Alerta de información -->
                <div class="mb-6 rounded-lg border border-yellow-200 bg-yellow-50 p-3">
                    <p class="text-sm text-yellow-800 flex items-start gap-2">
                        <svg class="h-5 w-5 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span>La contraseña debe tener mínimo 8 caracteres con letras y números.</span>
                    </p>
                </div>

                <form method="POST" action="{{ route('change-password.store') }}" class="space-y-4">
                    @csrf

                    <!-- Contraseña Nueva -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Nueva Contraseña</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required 
                            minlength="8"
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder-gray-500 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
                            placeholder="Ingrese una nueva contraseña"
                        >
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            required 
                            minlength="8"
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder-gray-500 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
                            placeholder="Confirme su nueva contraseña"
                        >
                        @error('password_confirmation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Requisitos -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-xs text-gray-600 space-y-1">
                        <p class="font-semibold text-gray-700">Requisitos:</p>
                        <p class="flex items-center gap-2">
                            <span id="check-length" class="inline-flex h-4 w-4 items-center justify-center rounded border border-gray-300">
                                <svg class="h-3 w-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </span>
                            Mínimo 8 caracteres
                        </p>
                        <p class="flex items-center gap-2">
                            <span id="check-letter" class="inline-flex h-4 w-4 items-center justify-center rounded border border-gray-300">
                                <svg class="h-3 w-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </span>
                            Al menos una letra
                        </p>
                        <p class="flex items-center gap-2">
                            <span id="check-number" class="inline-flex h-4 w-4 items-center justify-center rounded border border-gray-300">
                                <svg class="h-3 w-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </span>
                            Al menos un número
                        </p>
                    </div>

                    <!-- Botón -->
                    <button 
                        type="submit" 
                        class="w-full rounded-lg bg-primary-700 px-4 py-2 font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition"
                    >
                        Cambiar Contraseña
                    </button>
                </form>

                <!-- Información adicional -->
                <div class="mt-4 rounded-lg bg-blue-50 border border-blue-200 p-3 text-xs text-blue-800">
                    <p>🔒 Su contraseña anterior ya no podrá ser utilizada.</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center text-xs text-gray-600">
            <p>© {{ date('Y') }} FICCT - Facultad de Ciencias y Computación</p>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        
        function validatePassword() {
            const password = passwordInput.value;
            const hasLength = password.length >= 8;
            const hasLetter = /[a-zA-Z]/.test(password);
            const hasNumber = /[0-9]/.test(password);

            updateCheck('check-length', hasLength);
            updateCheck('check-letter', hasLetter);
            updateCheck('check-number', hasNumber);
        }

        function updateCheck(elementId, isValid) {
            const element = document.getElementById(elementId);
            if (isValid) {
                element.innerHTML = '<svg class="h-3 w-3 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>';
                element.classList.remove('border-gray-300');
                element.classList.add('border-green-500', 'bg-green-50');
            } else {
                element.innerHTML = '<svg class="h-3 w-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>';
                element.classList.add('border-gray-300');
                element.classList.remove('border-green-500', 'bg-green-50');
            }
        }

        passwordInput.addEventListener('input', validatePassword);
        validatePassword(); // Inicial
    </script>
</body>
</html>
