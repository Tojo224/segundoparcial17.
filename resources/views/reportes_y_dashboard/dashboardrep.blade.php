@extends('administracion_usuarios_seguridad.dashboard')

@section('content')
<div class="px-4 py-6 sm:px-6 lg:px-8">

    <!-- ENCABEZADO -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-slate-100">Dashboard de Reportes</h1>
            <p class="text-slate-400 mt-1">Métricas principales del sistema de reportes</p>
        </div>

        <!-- SELECTOR DE PERIODO GLOBAL -->
        <div>
            <select id="filtroPeriodo"
                class="bg-slate-800 text-slate-200 border border-slate-600 rounded-lg px-4 py-2">
                <option value="semana">Esta Semana</option>
                <option value="mes">Este Mes</option>
                <option value="semestre">Este Semestre</option>
                <option value="gestion">Gestión Académica</option>
            </select>
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <div class="bg-app-panel border border-white/10 rounded-lg p-6">
            <p class="text-sm text-slate-400 uppercase">Ocupación de Aulas</p>
            <p id="kpi-ocupacion" class="text-4xl text-slate-100 font-bold mt-3">--</p>
            <span class="text-xs text-slate-500">Total aulas ocupadas</span>
        </div>

        <div class="bg-app-panel border border-white/10 rounded-lg p-6">
            <p class="text-sm text-slate-400 uppercase">Asistencia Docentes</p>
            <p id="kpi-asistencia" class="text-4xl text-slate-100 font-bold mt-3">--%</p>
            <span class="text-xs text-slate-500">Según periodo</span>
        </div>

        <div class="bg-app-panel border border-white/10 rounded-lg p-6">
            <p class="text-sm text-slate-400 uppercase">Carga Horaria Promedio</p>
            <p id="kpi-carga-promedio" class="text-4xl text-slate-100 font-bold mt-3">--</p>
            <span class="text-xs text-slate-500">Horas por docente</span>
        </div>

    </div>

    <!-- ACTIVIDAD Y DOCENTES ACTIVOS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        <div class="lg:col-span-2 bg-app-panel border border-white/10 rounded-lg p-6">
            <h3 class="text-lg text-slate-100 font-semibold mb-6">
                Actividad de Asistencias
            </h3>
            <div class="relative h-64">
                <canvas id="chartActividad"></canvas>
            </div>
        </div>

        <div class="bg-app-panel border border-white/10 rounded-lg p-6">
            <h3 class="text-sm text-slate-400 uppercase">Docentes Activos</h3>
            <p id="kpi-docentes-activos" class="text-4xl text-slate-100 font-bold mt-3">--</p>
            <span class="text-xs text-slate-500">Con carga asignada</span>
        </div>

    </div>

    <!-- DISTRIBUCION -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        <div class="bg-app-panel border border-white/10 rounded-lg p-6">
            <h3 class="text-lg text-slate-100 font-semibold mb-6">Distribución de Asistencias</h3>
            <div class="relative h-64">
                <canvas id="chartDistribucion"></canvas>
            </div>
        </div>

        <div class="bg-app-panel border border-white/10 rounded-lg p-6">
            <h3 class="text-lg text-slate-100 font-semibold mb-6">Desglose por Tipo</h3>

            <div class="space-y-3">
                <div class="flex justify-between border-b border-white/10 pb-2">
                    <span class="text-slate-300">Presente</span>
                    <span id="tipo-presente" class="text-slate-100">--</span>
                </div>

                <div class="flex justify-between border-b border-white/10 pb-2">
                    <span class="text-slate-300">Ausente</span>
                    <span id="tipo-ausente" class="text-slate-100">--</span>
                </div>

                <div class="flex justify-between border-b border-white/10 pb-2">
                    <span class="text-slate-300">Retraso</span>
                    <span id="tipo-retraso" class="text-slate-100">--</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-slate-300">Justificada</span>
                    <span id="tipo-justificada" class="text-slate-100">--</span>
                </div>
            </div>

        </div>

    </div>

    <!-- CARGA HORARIA (CON SU FILTRO PROPIO) -->
    <div class="bg-app-panel border border-white/10 rounded-lg p-6 mb-8">

        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg text-slate-100 font-semibold">
                Carga Horaria por Materia
            </h3>

            <!-- SELECTOR PROPIO SOLO PARA ESTE GRÁFICO -->
            <select id="selectGestionCarga"
                class="bg-slate-800 text-slate-200 px-4 py-2 rounded-md border border-white/10 hover:border-white/20">
                <option value="">Cargando...</option>
            </select>
        </div>

        <!-- TABLA DE CARGA HORARIA -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left py-3 px-4 text-slate-300 font-semibold">Materia</th>
                        <th class="text-right py-3 px-4 text-slate-300 font-semibold">Horas Asignadas</th>
                        <th class="text-center py-3 px-4 text-slate-300 font-semibold">Visualización</th>
                    </tr>
                </thead>
                <tbody id="tablaCargaHoraria">
                    <tr class="border-b border-white/10">
                        <td colspan="3" class="text-center py-8 text-slate-400">Cargando datos...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- CHARTJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let chartActividad, chartDistribucion;

// -------------------------------
// GESTIONES SOLO PARA CARGA HORARIA
// -------------------------------
async function cargarGestionesCarga() {
    try {
        console.log('Iniciando carga de gestiones...');
        
        const res = await fetch('/api/dashboard/gestiones');
        const data = await res.json();
        
        console.log('Gestiones obtenidas:', data);
        
        if (!data.success || !data.datos) {
            console.error('Error en respuesta de gestiones:', data);
            return;
        }

        const select = document.getElementById("selectGestionCarga");
        select.innerHTML = "";

        if (data.datos.length === 0) {
            console.warn('No hay gestiones disponibles');
            select.innerHTML = '<option value="">No hay gestiones</option>';
            return;
        }

        data.datos.forEach(g => {
            const opt = document.createElement("option");
            opt.value = g.id_gestion;
            opt.textContent = `${g.anio} - Semestre ${g.semestre}`;
            select.appendChild(opt);
            console.log(`Gestión agregada: ${g.anio} - ${g.semestre} (id: ${g.id_gestion})`);
        });

        // Cargar datos de la primera gestión automáticamente
        if (data.datos.length > 0) {
            select.value = data.datos[0].id_gestion;
            console.log('Gestión por defecto seleccionada:', data.datos[0].id_gestion);
            await cargarCargaMateria();
        }

    } catch (error) {
        console.error('Error al cargar gestiones:', error);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    cargarGestionesCarga();

    // filtro global
    const selector = document.getElementById('filtroPeriodo');
    selector.addEventListener('change', () => cargarTodo(selector.value));

    // filtro para carga horaria
    const selectGestionCarga = document.getElementById("selectGestionCarga");
    if (selectGestionCarga) {
        selectGestionCarga.addEventListener("change", cargarCargaMateria);
    }

    cargarTodo(selector.value);
});

// -------------------------------
// KPIs
// -------------------------------
async function cargarKPIs(periodo) {
    const res = await fetch(`/api/dashboard/kpis?periodo=${periodo}`);
    const d = await res.json().then(r => r.datos);

    document.getElementById('kpi-ocupacion').textContent = d.ocupacion_aulas.valor;
    document.getElementById('kpi-asistencia').textContent = d.asistencia_docentes.porcentaje + "%";
    document.getElementById('kpi-carga-promedio').textContent = d.carga_horaria.valor;
    document.getElementById('kpi-docentes-activos').textContent = d.docentes_activos.valor;
}

// -------------------------------
// ACTIVIDAD
// -------------------------------
async function cargarActividad(periodo) {
    const res = await fetch(`/api/dashboard/actividad?periodo=${periodo}`);
    const arr = await res.json().then(r => r.datos);

    const labels = arr.map(x => x.fecha);
    const valores = arr.map(x => x.cantidad);

    if (chartActividad) chartActividad.destroy();

    chartActividad = new Chart(document.getElementById('chartActividad'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: "Asistencias Registradas",
                data: valores,
                borderColor: "#3b82f6",
                backgroundColor: "rgba(59,130,246,0.2)",
                tension: .3,
                fill: true
            }]
        }
    });
}

// -------------------------------
// DISTRIBUCION
// -------------------------------
async function cargarDistribucion(periodo) {
    const res = await fetch(`/api/dashboard/distribucion?periodo=${periodo}`);
    const dist = await res.json().then(r => r.datos);

    dist.forEach(x => {
        document.getElementById(`tipo-${x.tipo.toLowerCase()}`).textContent = x.cantidad;
    });

    const labels = dist.map(x => x.tipo);
    const valores = dist.map(x => x.cantidad);

    if (chartDistribucion) chartDistribucion.destroy();

    chartDistribucion = new Chart(document.getElementById('chartDistribucion'), {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: valores,
                backgroundColor: ['#3b82f6', '#ef4444', '#f59e0b', '#10b981']
            }]
        }
    });
}

// -------------------------------
// CARGA HORARIA - TABLA VISUAL
// -------------------------------
async function cargarCargaMateria() {
    try {
        const idGestionSelect = document.getElementById("selectGestionCarga");
        const id_gestion = idGestionSelect ? idGestionSelect.value : null;

        console.log('🔍 selectGestionCarga existe:', !!idGestionSelect);
        console.log('🔍 Valor del selector (tipo):', typeof id_gestion);
        console.log('🔍 Valor del selector (contenido):', id_gestion);
        console.log('🔍 Selector HTML:', idGestionSelect?.outerHTML);

        if (!id_gestion) {
            console.warn('⚠️ Sin gestión seleccionada');
            document.getElementById('tablaCargaHoraria').innerHTML = `
                <tr class="border-b border-white/10">
                    <td colspan="3" class="text-center py-8 text-slate-400">
                        Por favor seleccione una gestión académica
                    </td>
                </tr>
            `;
            return;
        }

        const url = `/api/dashboard/estadisticas-carga-horaria?id_gestion=${id_gestion}`;
        console.log('📡 URL completa:', url);
        console.log('📡 Enviando GET request...');

        const response = await fetch(url);
        
        console.log('📨 Respuesta HTTP status:', response.status);
        console.log('📨 Respuesta HTTP statusText:', response.statusText);
        console.log('📨 Headers:', response.headers);

        if (!response.ok) {
            console.error('❌ HTTP Error:', response.status);
            const errorText = await response.text();
            console.error('❌ Response body:', errorText);
            document.getElementById('tablaCargaHoraria').innerHTML = `
                <tr class="border-b border-white/10">
                    <td colspan="3" class="text-center py-8 text-red-400">
                        Error al cargar datos (HTTP ${response.status})
                    </td>
                </tr>
            `;
            return;
        }

        const result = await response.json();
        console.log('📦 Datos recibidos (JSON completo):', result);
        console.log('📦 result.success:', result.success);
        console.log('📦 result.datos:', result.datos);
        console.log('📦 result.datos tipo:', typeof result.datos);

        if (!result.success) {
            console.error('❌ result.success es false:', result);
            document.getElementById('tablaCargaHoraria').innerHTML = `
                <tr class="border-b border-white/10">
                    <td colspan="3" class="text-center py-8 text-red-400">
                        ${result.mensaje || 'Error desconocido'}
                    </td>
                </tr>
            `;
            return;
        }

        const datos = result.datos || [];
        console.log('✅ Datos extraídos:', datos);
        console.log('📊 Cantidad de registros:', datos.length);
        console.log('📊 Es array:', Array.isArray(datos));

        if (!Array.isArray(datos)) {
            console.error('❌ datos no es un array:', typeof datos);
            return;
        }

        if (datos.length === 0) {
            console.warn('⚠️ No hay datos para esta gestión (array vacío)');
            document.getElementById('tablaCargaHoraria').innerHTML = `
                <tr class="border-b border-white/10">
                    <td colspan="3" class="text-center py-8 text-slate-400">
                        No hay datos para esta gestión académica
                    </td>
                </tr>
            `;
            return;
        }

        // Obtener el máximo de horas para la barra de visualización
        const maxHoras = Math.max(...datos.map(d => {
            const h = parseInt(d.Total_Horas) || 0;
            console.log(`📌 ${d.Materia}: ${h} horas`);
            return h;
        }));

        console.log('📊 Máximo de horas:', maxHoras);

        // Generar HTML de la tabla
        let html = '';
        datos.forEach(item => {
            const materia = item.Materia || 'Sin nombre';
            const horas = parseInt(item.Total_Horas) || 0;
            const porcentaje = maxHoras > 0 ? (horas / maxHoras) * 100 : 0;

            html += `
                <tr class="border-b border-white/10 hover:bg-white/5 transition">
                    <td class="py-4 px-4 text-slate-100">${materia}</td>
                    <td class="text-right py-4 px-4 text-slate-100 font-semibold">${horas}h</td>
                    <td class="py-4 px-4">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 bg-slate-700 rounded-full overflow-hidden h-2">
                                <div class="bg-purple-500 h-full transition-all" style="width: ${porcentaje}%"></div>
                            </div>
                            <span class="text-xs text-slate-400 w-8 text-right">${Math.round(porcentaje)}%</span>
                        </div>
                    </td>
                </tr>
            `;
        });

        document.getElementById('tablaCargaHoraria').innerHTML = html;
        console.log('✅ Tabla cargada exitosamente con', datos.length, 'registros');

    } catch (error) {
        console.error('❌ Excepción en cargarCargaMateria:', error);
        console.error('📋 Nombre del error:', error.name);
        console.error('📋 Mensaje del error:', error.message);
        console.error('📋 Stack:', error.stack);
        document.getElementById('tablaCargaHoraria').innerHTML = `
            <tr class="border-b border-white/10">
                <td colspan="3" class="text-center py-8 text-red-400">
                    Error: ${error.message}
                </td>
            </tr>
        `;
    }
}

// -------------------------------
async function cargarTodo(periodo) {
    await cargarKPIs(periodo);
    await cargarActividad(periodo);
    await cargarDistribucion(periodo);
}

</script>

@endsection
