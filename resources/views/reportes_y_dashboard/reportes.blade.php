@extends('administracion_usuarios_seguridad.dashboard')

@section('content')
<div class="px-4 py-6 sm:px-6 lg:px-8">
    <!-- Encabezado -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-100">Centro de Reportes</h1>
        <p class="text-slate-400 mt-1">Genere y exporte reportes del sistema</p>
    </div>

    <!-- Selector de Gestión Académica -->
    <div class="bg-app-panel border border-white/10 rounded-lg p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Año Académico</label>
                <select id="filtro-anio" class="w-full bg-slate-700 text-slate-100 px-4 py-2 rounded border border-slate-600 hover:border-slate-500 transition">
                    <option value="">-- Seleccione Año --</option>
                    @if(isset($gestiones) && $gestiones->isNotEmpty())
                        @foreach($gestiones->groupBy('anio') as $anio => $grupo)
                            <option value="{{ $anio }}">{{ $anio }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Semestre</label>
                <select id="filtro-semestre" class="w-full bg-slate-700 text-slate-100 px-4 py-2 rounded border border-slate-600 hover:border-slate-500 transition" disabled>
                    <option value="">-- Seleccione Semestre --</option>
                </select>
            </div>
        </div>
        <input type="hidden" id="filtro-id-gestion" value="">
    </div>

    <!-- Reportes Disponibles -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Reporte 1: Horarios -->
        <div class="bg-app-panel border border-white/10 rounded-lg p-6 hover:border-blue-500/30 transition">
            <div class="flex items-start gap-4 mb-4">
                <div class="inline-flex h-10 w-10 rounded-lg bg-blue-500/20 grid place-items-center">
                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-slate-100">Reporte de Horarios</h3>
                    <p class="text-sm text-slate-400 mt-1">Horarios semanales por docente, materia o grupo</p>
                </div>
            </div>
            <p class="text-sm text-slate-400 mb-4">Formatos disponibles:</p>
            <div class="flex gap-3">
                <button onclick="descargarReporte('horarios', 'pdf')" class="flex-1 flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition font-medium">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    PDF
                </button>
                <button onclick="descargarReporte('horarios', 'excel')" class="flex-1 flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition font-medium">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Excel
                </button>
            </div>
        </div>

        <!-- Reporte 2: Asistencia -->
        <div class="bg-app-panel border border-white/10 rounded-lg p-6 hover:border-green-500/30 transition">
            <div class="flex items-start gap-4 mb-4">
                <div class="inline-flex h-10 w-10 rounded-lg bg-green-500/20 grid place-items-center">
                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-slate-100">Reporte de Asistencia</h3>
                    <p class="text-sm text-slate-400 mt-1">Asistencia por docente, grupo y período</p>
                </div>
            </div>
            <p class="text-sm text-slate-400 mb-4">Formatos disponibles:</p>
            <div class="flex gap-3">
                <button onclick="descargarReporte('asistencia', 'pdf')" class="flex-1 flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition font-medium">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    PDF
                </button>
                <button onclick="descargarReporte('asistencia', 'excel')" class="flex-1 flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition font-medium">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Excel
                </button>
            </div>
        </div>

        <!-- Reporte 3: Disponibilidad de Aulas -->
        <div class="bg-app-panel border border-white/10 rounded-lg p-6 hover:border-purple-500/30 transition">
            <div class="flex items-start gap-4 mb-4">
                <div class="inline-flex h-10 w-10 rounded-lg bg-purple-500/20 grid place-items-center">
                    <svg class="h-5 w-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-slate-100">Disponibilidad de Aulas</h3>
                    <p class="text-sm text-slate-400 mt-1">Ocupación y disponibilidad de espacios</p>
                </div>
            </div>
            <p class="text-sm text-slate-400 mb-4">Formatos disponibles:</p>
            <div class="flex gap-3">
                <button onclick="descargarReporte('aulas', 'pdf')" class="flex-1 flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition font-medium">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    PDF
                </button>
                <button onclick="descargarReporte('aulas', 'excel')" class="flex-1 flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition font-medium">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Excel
                </button>
            </div>
        </div>

        <!-- Reporte 4: Carga Horaria Docente -->
        <div class="bg-app-panel border border-white/10 rounded-lg p-6 hover:border-orange-500/30 transition">
            <div class="flex items-start gap-4 mb-4">
                <div class="inline-flex h-10 w-10 rounded-lg bg-orange-500/20 grid place-items-center">
                    <svg class="h-5 w-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-slate-100">Carga Horaria Docente</h3>
                    <p class="text-sm text-slate-400 mt-1">Distribución de horas por docente</p>
                </div>
            </div>
            <p class="text-sm text-slate-400 mb-4">Formatos disponibles:</p>
            <div class="flex gap-3">
                <button onclick="descargarReporte('carga_horaria', 'pdf')" class="flex-1 flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition font-medium">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    PDF
                </button>
                <button onclick="descargarReporte('carga_horaria', 'excel')" class="flex-1 flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition font-medium">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Excel
                </button>
            </div>
        </div>
    </div>

    <!-- Mensajes de Notificación -->
    <div id="mensaje-error" class="hidden mt-6 bg-red-900/30 border border-red-500 text-red-300 px-4 py-3 rounded-lg">
        <p id="texto-error"></p>
    </div>
    <div id="mensaje-exito" class="hidden mt-6 bg-green-900/30 border border-green-500 text-green-300 px-4 py-3 rounded-lg">
        <p id="texto-exito"></p>
    </div>
</div>

<script>
    const gestiones = @json(isset($gestiones) ? $gestiones : []);
    
    // Actualizar semestres cuando cambia el año
    document.getElementById('filtro-anio').addEventListener('change', function() {
        const anio = this.value;
        const semestreSelect = document.getElementById('filtro-semestre');
        semestreSelect.innerHTML = '<option value="">-- Seleccione Semestre --</option>';
        semestreSelect.disabled = true;
        
        if (anio) {
            const semestres = gestiones.filter(g => g.anio == anio);
            if (semestres.length) {
                semestreSelect.disabled = false;
                semestres.forEach(g => {
                    const option = document.createElement('option');
                    option.value = g.id_gestion;
                    option.textContent = `Semestre ${g.semestre}`;
                    semestreSelect.appendChild(option);
                });
            }
        }
    });

    // Guardar id_gestion cuando cambia semestre
    document.getElementById('filtro-semestre').addEventListener('change', function() {
        document.getElementById('filtro-id-gestion').value = this.value;
    });

    async function descargarReporte(tipo, formato) {
        const idGestion = document.getElementById('filtro-id-gestion').value;
        const mensajeError = document.getElementById('mensaje-error');
        const mensajeExito = document.getElementById('mensaje-exito');

        // Validar que se haya seleccionado año y semestre
        if (!idGestion) {
            mostrarError('Por favor seleccione un año académico y semestre');
            return;
        }

        try {
            mensajeError.classList.add('hidden');
            mensajeExito.classList.add('hidden');

            const url = formato === 'pdf' ? '/api/reportes/pdf' : '/api/reportes/excel';
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    tipo_reporte: tipo,
                    id_gestion: idGestion
                })
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.mensaje || 'Error en la descarga');
            }

            // La respuesta es un archivo, descargarlo
            const blob = await response.blob();
            const urlDescarga = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = urlDescarga;
            link.download = `Reporte_${tipo}_${new Date().getTime()}.${formato === 'pdf' ? 'pdf' : 'xlsx'}`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(urlDescarga);

            mostrarExito(`Reporte descargado exitosamente. Se registró en bitácora.`);

        } catch (error) {
            console.error('Error:', error);
            mostrarError(`Error al descargar reporte: ${error.message}`);
        }
    }

    function mostrarError(mensaje) {
        document.getElementById('texto-error').textContent = mensaje;
        document.getElementById('mensaje-error').classList.remove('hidden');
        setTimeout(() => {
            document.getElementById('mensaje-error').classList.add('hidden');
        }, 5000);
    }

    function mostrarExito(mensaje) {
        document.getElementById('texto-exito').textContent = mensaje;
        document.getElementById('mensaje-exito').classList.remove('hidden');
        setTimeout(() => {
            document.getElementById('mensaje-exito').classList.add('hidden');
        }, 5000);
    }
</script>

@endsection
