<!doctype html>
<html lang="es" x-data="{ sidebarOpen:false }">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sistema de Gestión Académica — Dashboard</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            // Fondo oscuro general
            app: {
              bg: '#0b1324',     // fondo de app
              panel: '#0f1a33',  // panel/menú
              soft: '#111c38',   // variantes
              line: '#0b1224'
            },
            // Paleta por módulo (inspirada en la imagen 2)
            mod: {
              gray:  { base:'#374151', hover:'#4b5563', pill:'#6b7280' },   // Dashboard
              red:   { base:'#b91c1c', hover:'#dc2626', pill:'#ef4444' },   // Seguridad
              blue:  { base:'#1d4ed8', hover:'#2563eb', pill:'#60a5fa' },   // Académico
              sky:   { base:'#0ea5e9', hover:'#38bdf8', pill:'#7dd3fc' },   // Horarios
              indigo:{ base:'#4338ca', hover:'#4f46e5', pill:'#a5b4fc' },   // Asistencia
              slate: { base:'#334155', hover:'#475569', pill:'#94a3b8' },   // Reportes
            }
          },
          boxShadow: {
            soft: '0 10px 25px -15px rgba(0,0,0,0.6)'
          }
        }
      }
    }
  </script>
</head>
<body class="min-h-screen bg-app-bg text-slate-100">

<!-- Shell -->
<div class="min-h-screen lg:grid lg:grid-cols-[18rem_1fr]">

  <!-- Drawer backdrop (mobile) -->
  <div
    class="fixed inset-0 bg-black/50 z-40 lg:hidden"
    x-show="sidebarOpen"
    x-transition.opacity
    @click="sidebarOpen=false"
    aria-hidden="true"></div>

  <!-- SIDEBAR -->
  <aside
    class="fixed inset-y-0 left-0 z-50 w-72 bg-app-panel border-r border-app.line shadow-soft 
           flex flex-col lg:sticky lg:top-0 lg:h-screen"
    :class="{'-translate-x-full lg:translate-x-0': !sidebarOpen}"
    x-transition:enter="transition-transform duration-200"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition-transform duration-200"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full">

    <!-- Header del menú (logo + sistema) -->
    <div class="px-4 pt-4 pb-3 border-b border-white/10">
      <div class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-xl bg-mod.blue.base grid place-items-center shadow-soft">
          <!-- ícono birrete -->
          <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M11.7 2.04a1 1 0 0 1 .6 0l9 3a1 1 0 0 1 0 1.92l-3.2 1.07v3.02a2 2 0 0 1-1.2 1.84l-4 1.78a2 2 0 0 1-1.6 0l-4-1.78a2 2 0 0 1-1.2-1.84V8.03L1.7 6.96a1 1 0 0 1 0-1.92l9-3Z"/>
          </svg>
        </div>
        <div class="leading-tight">
          <div class="text-xs text-slate-400">FICCT</div>
          <div class="font-semibold">Sistema Académico</div>
        </div>
      </div>

      <!-- Usuario / Rol (estilo imagen 2) -->
      <div class="mt-3 flex items-center gap-3 rounded-xl bg-white/5 px-3 py-2">
        <div class="h-8 w-8 rounded-full bg-white/10 grid place-items-center">A</div>
        <div class="text-sm">
          <div class="font-medium">Administrador FICCT</div>
          <div class="text-slate-400 -mt-0.5">Admin</div>
        </div>
        <!-- selector de rol para filtrar el menú (persistente) -->
        <select id="roleSelect"
                class="ml-auto rounded-md bg-app.soft/60 text-xs border border-white/10 focus:border-mod.blue.pill focus:ring-0">
          <option>Administrador</option>
          <option>Coordinador</option>
          <option>Docente</option>
          <option>Autoridad</option>
          <option>Auxiliar</option>
        </select>
      </div>
    </div>

    <!-- NAVEGACIÓN -->
    <nav id="sidebarModules" class="flex-1 overflow-y-auto px-3 py-4 space-y-3">

      <!-- DASHBOARD -->
      <section x-data="{open:true}" class="rounded-xl overflow-hidden">
        <div class="flex items-center justify-between">
          <span class="text-xs tracking-wide uppercase text-slate-400 px-2">Dashboard</span>
        </div>
        <div class="mt-1 rounded-xl bg-white/5 ring-1 ring-white/5">
          <button @click="open=!open"
                  class="w-full flex items-center justify-between px-3 py-2 text-left
                         bg-mod.gray.base/30 hover:bg-mod.gray.hover/30">
            <span class="flex items-center gap-2">
              <span class="inline-flex h-6 w-6 rounded-lg bg-mod.gray.pill/20 grid place-items-center">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M3 3.75A.75.75 0 0 1 3.75 3h7.5a.75.75 0 0 1 .75.75v16.5a.75.75 0 0 1-.75.75h-7.5a.75.75 0 0 1-.75-.75V3.75Zm9.75 6A.75.75 0 0 1 13.5 9h6.75a.75.75 0 0 1 .75.75v11.5a.75.75 0 0 1-.75.75H13.5a.75.75 0 0 1-.75-.75V9.75Z"/></svg>
              </span>
              <span class="font-medium">Reportes y Dashboard</span>
            </span>
            <svg :class="{'rotate-180': open}" class="h-4 w-4 text-slate-300 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>
          </button>
          <ul x-show="open" x-collapse class="py-1">
            <li><a href="#" data-role="CU16" class="flex items-center gap-2 px-4 py-2 rounded-md hover:bg-white/5"><svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18"/></svg>Generar reportes académicos</a></li>
            <li><a href="#" data-role="CU17" class="flex items-center gap-2 px-4 py-2 rounded-md hover:bg-white/5"><svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m4-4H8"/></svg>Exportar reportes en Excel y PDF</a></li>
            <li><a href="#" data-role="CU18" class="flex items-center gap-2 px-4 py-2 rounded-md hover:bg-white/5"><svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3v18M3 12h18"/></svg>Generar dashboard</a></li>
          </ul>
        </div>
      </section>

      <!-- SEGURIDAD -->
      <section x-data="{open:true}" data-module="seguridad" class="rounded-xl overflow-hidden">
        <div class="text-xs tracking-wide uppercase text-slate-400 px-2">Seguridad</div>
        <div class="mt-1 rounded-xl ring-1 ring-white/5">
          <button @click="open=!open"
                  class="w-full flex items-center justify-between px-3 py-2 text-left
                         bg-mod.red.base/25 hover:bg-mod.red.hover/25">
            <span class="flex items-center gap-2">
              <span class="inline-flex h-6 w-6 rounded-lg bg-mod.red.pill/25 grid place-items-center">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1.75a4.75 4.75 0 0 0-4.75 4.75v2.25H6A2.75 2.75 0 0 0 3.25 11.5v7.75A2.75 2.75 0 0 0 6 22h12a2.75 2.75 0 0 0 2.75-2.75V11.5A2.75 2.75 0 0 0 18 8.75h-1.25V6.5A4.75 4.75 0 0 0 12 1.75Z"/></svg>
              </span>
              <span class="font-medium">Administración de Usuarios y Seguridad</span>
            </span>
            <svg :class="{'rotate-180': open}" class="h-4 w-4 text-slate-300 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>
          </button>
          <ul x-show="open" x-collapse class="py-1">
           <li><a href="{{ route('usuarios.vista') }}" class="flex items-center gap-2 px-4 py-2 rounded-md hover:bg-sidebar-hover">
  <svg class="h-5 w-5 text-slate-300" xmlns="http://www.w3.org/2000/svg" fill="none"
       viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M5.121 17.804A4 4 0 0 1 8 16h8a4 4 0 0 1 2.879 1.804M15 11a3 3 0 1 0-6 0 3 3 0 0 0 6 0Z"/>
  </svg>
  Gestionar usuarios
</a>
</li>

                <li><a href="{{ route('bitacora.vista') }}" data-role="CU5"
    class="flex items-center gap-2 px-4 py-2 rounded-md hover:bg-white/5">
    <svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M7 7h10M7 11h10M7 15h6"/>
    </svg>
    Gestionar bitácora
    </a>
    </li>
          </ul>
        </div>
      </section>

      <!-- ACADÉMICO -->
      <section x-data="{open:true}" data-module="academica" class="rounded-xl overflow-hidden">
        <div class="text-xs tracking-wide uppercase text-slate-400 px-2">Académico</div>
        <div class="mt-1 rounded-xl ring-1 ring-white/5">
          <button @click="open=!open"
                  class="w-full flex items-center justify-between px-3 py-2 text-left
                         bg-mod.blue.base/25 hover:bg-mod.blue.hover/25">
            <span class="flex items-center gap-2">
              <span class="inline-flex h-6 w-6 rounded-lg bg-mod.blue.pill/25 grid place-items-center">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M3 4.75A1.75 1.75 0 0 1 4.75 3h6.5A1.75 1.75 0 0 1 13 4.75v6.5A1.75 1.75 0 0 1 11.25 13h-6.5A1.75 1.75 0 0 1 3 11.25v-6.5Z"/><path d="M14.5 4.75A1.75 1.75 0 0 1 16.25 3h3A1.75 1.75 0 0 1 21 4.75v3A1.75 1.75 0 0 1 19.25 9h-3A1.75 1.75 0 0 1 14.5 7.25v-2.5Z"/><path d="M14.5 14.5A1.5 1.5 0 0 1 16 13h3a2 2 0 0 1 2 2v3.25A2.75 2.75 0 0 1 18.25 21H16a1.5 1.5 0 0 1-1.5-1.5v-5Z"/></svg>
              </span>
              <span class="font-medium">Gestión Académica</span>
            </span>
            <svg :class="{'rotate-180': open}" class="h-4 w-4 text-slate-300 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>
          </button>
          <ul x-show="open" x-collapse class="py-1">
            <li>
              <a href="{{ route('docentes.vista') }}" data-role="CU6"
                class="flex items-center gap-2 px-4 py-2 rounded-md hover:bg-white/5">
                <svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v12m6-6H6"/>
                </svg>
                Registrar docentes
              </a>
            </li>

            <li>
              <a href="{{ route('materias.vista') }}"
                data-role="CU7"
                class="flex items-center gap-2 px-4 py-2 rounded-md hover:bg-white/5
                      {{ request()->routeIs('materias.vista') ? 'bg-white/5' : '' }}">
                <svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 10h16M4 14h10M4 18h10"/>
                </svg>
                Gestionar materias
              </a>
            </li>
            <li>
              <a href="{{ route('grupos.vista') }}" data-role="CU8"
                class="flex items-center gap-2 px-4 py-2 rounded-md hover:bg-white/5">
                <svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 10h16M4 14h10M4 18h10"/>
                </svg>
                Gestionar grupos
              </a>
            </li>

            <li><a href="#" data-role="CU9" class="flex items-center gap-2 px-4 py-2 rounded-md hover:bg-white/5"><svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="currentColor"><path d="M4 6.75A1.75 1.75 0 0 1 5.75 5h12.5A1.75 1.75 0 0 1 20 6.75v10.5A1.75 1.75 0 0 1 18.25 19H5.75A1.75 1.75 0 0 1 4 17.25V6.75Zm3 2.5h10v6.5H7v-6.5Z"/></svg>Gestionar carga horaria</a></li>
            <li><a href="#" data-role="CU10" class="flex items-center gap-2 px-4 py-2 rounded-md hover:bg-white/5"><svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Asignar grupos a docentes</a></li>
          </ul>
        </div>
      </section>

      <!-- AULAS Y HORARIOS -->
      <section x-data="{open:true}" data-module="aulas_horarios" class="rounded-xl overflow-hidden">
        <div class="text-xs tracking-wide uppercase text-slate-400 px-2">Horarios</div>
        <div class="mt-1 rounded-xl ring-1 ring-white/5">
          <button @click="open=!open"
                  class="w-full flex items-center justify-between px-3 py-2 text-left
                         bg-mod.sky.base/20 hover:bg-mod.sky.hover/20">
            <span class="flex items-center gap-2">
              <span class="inline-flex h-6 w-6 rounded-lg bg-mod.sky.pill/25 grid place-items-center">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M3.5 6.75A1.75 1.75 0 0 1 5.25 5h13.5A1.75 1.75 0 0 1 20.5 6.75v10.5A1.75 1.75 0 0 1 18.75 19H5.25A1.75 1.75 0 0 1 3.5 17.25V6.75Z"/></svg>
              </span>
              <span class="font-medium">Aulas y Horarios</span>
            </span>
            <svg :class="{'rotate-180': open}" class="h-4 w-4 text-slate-300 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>
          </button>
          <ul x-show="open" x-collapse class="py-1">
            <li><a href="#" data-role="CU11" class="flex items-center gap-2 px-4 py-2 rounded-md hover:bg-white/5"><svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 21h14a2 2 0 0 0 2-2v-9H3v9a2 2 0 0 0 2 2Z"/></svg>Asignar horarios manualmente</a></li>
            <li><a href="#" data-role="CU12_Horarios" class="flex items-center gap-2 px-4 py-2 rounded-md hover:bg-white/5"><svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 10 10A10.011 10.011 0 0 0 12 2Zm.75 5.25a.75.75 0 0 0-1.5 0V12a.75.75 0 0 0 .22.53l3 3a.75.75 0 1 0 1.06-1.06l-2.78-2.78Z"/></svg>Gestionar horarios</a></li>
            <li><a href="#" data-role="CU12_Aulas" class="flex items-center gap-2 px-4 py-2 rounded-md hover:bg-white/5"><svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9-4 9 4-9 4-9-4z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 17l9 4 9-4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9 4 9-4"/></svg>Gestionar aulas</a></li>
            <li><a href="#" data-role="CU13" class="flex items-center gap-2 px-4 py-2 rounded-md hover:bg-white/5"><svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 21h14M3 11h18"/></svg>Gestionar reserva de aula</a></li>
          </ul>
        </div>
      </section>

      <!-- ASISTENCIA -->
      <section x-data="{open:true}" data-module="asistencia" class="rounded-xl overflow-hidden">
        <div class="text-xs tracking-wide uppercase text-slate-400 px-2">Asistencia</div>
        <div class="mt-1 rounded-xl ring-1 ring-white/5">
          <button @click="open=!open"
                  class="w-full flex items-center justify-between px-3 py-2 text-left
                         bg-mod.indigo.base/25 hover:bg-mod.indigo.hover/25">
            <span class="flex items-center gap-2">
              <span class="inline-flex h-6 w-6 rounded-lg bg-mod.indigo.pill/25 grid place-items-center">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 10 10A10.011 10.011 0 0 0 12 2Zm3 10.75H9a.75.75 0 0 1 0-1.5h6a.75.75 0 0 1 0 1.5Z"/></svg>
              </span>
              <span class="font-medium">Control de Asistencia</span>
            </span>
            <svg :class="{'rotate-180': open}" class="h-4 w-4 text-slate-300 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>
          </button>
          <ul x-show="open" x-collapse class="py-1">
            <li><a href="#" data-role="CU14" class="flex items-center gap-2 px-4 py-2 rounded-md hover:bg-white/5"><svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Registrar asistencia docente</a></li>
            <li><a href="#" data-role="CU15" class="flex items-center gap-2 px-4 py-2 rounded-md hover:bg-white/5"><svg class="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6h6m-6 6h6m-6 6h6M8 6h.01M8 12h.01M8 18h.01"/></svg>Consultar asistencia</a></li>
          </ul>
        </div>
      </section>

    </nav>

    <!-- Cerrar sesión funcional -->
    <div class="p-3 border-t border-white/10">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
             class="w-full inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2
                       bg-red-600 hover:bg-red-500 text-white font-medium transition">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 12H3m12 0-4 4m4-4-4-4m6 8V8a2 2 0 0 0-2-2h-3"/>
        </svg>
        Cerrar sesión
        </button>
    </form>
    </div>

  </aside>

  <!-- MAIN -->
  <div class="min-h-screen flex flex-col">

    <!-- Topbar (móvil) -->
    <header class="sticky top-0 z-30 bg-app-panel/70 backdrop-blur border-b border-white/10 lg:hidden">
      <div class="px-4 h-14 flex items-center justify-between">
        <button class="p-2 rounded-lg bg-white/5" @click="sidebarOpen=true" aria-label="Abrir menú">
          <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
        <div class="flex items-center gap-2">
          <div class="h-8 w-8 rounded-lg bg-mod.blue.base grid place-items-center">
            <svg class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M11.7 2.04a1 1 0 0 1 .6 0l9 3a1 1 0 0 1 0 1.92l-3.2 1.07v3.02a2 2 0 0 1-1.2 1.84l-4 1.78a2 2 0 0 1-1.6 0l-4-1.78a2 2 0 0 1-1.2-1.84V8.03L1.7 6.96a1 1 0 0 1 0-1.92l9-3Z"/></svg>
          </div>
          <div class="font-semibold">Gestión Académica</div>
        </div>
        <div class="w-10"></div>
      </div>
    </header>

    <!-- Contenido -->
    <main class="flex-1">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-6">
          <h1 class="text-xl font-semibold">Bienvenido/a</h1>
          <p class="mt-1 text-slate-300">
            Usa el selector de rol en el panel lateral para simular los permisos. El menú mostrará únicamente
            los casos de uso habilitados para el rol seleccionado.
          </p>

          <div class="mt-6 grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl border border-white/10 p-4">
              <div class="font-medium mb-1">Módulos</div>
              <ul class="list-disc list-inside text-slate-300/90 text-sm">
                <li>Administración de Usuarios y Seguridad</li>
                <li>Gestión Académica</li>
                <li>Aulas y Horarios</li>
                <li>Control de Asistencia</li>
                <li>Reportes y Dashboard</li>
              </ul>
            </div>
            <div class="rounded-xl border border-white/10 p-4">
              <div class="font-medium mb-1">Nota</div>
              <p class="text-slate-300/90 text-sm">
                Esta UI es una base. Enlaza cada caso de uso a tus rutas reales editando los <code>href</code> del sidebar.
              </p>
            </div>
          </div>
        </div>
      </div>
    </main>

    <footer class="py-4 text-center text-xs text-slate-400">FICCT — Sistema de Gestión Académica</footer>
  </div>
</div>

<!-- Lógica de permisos por rol -->
<script>
  const permisos = {
    Administrador: ['CU1','CU2','CU3','CU4','CU5','CU6','CU7','CU8','CU9','CU12_Aulas','CU13'],
    Coordinador:   ['CU1','CU2','CU3','CU6','CU7','CU8','CU9','CU10','CU11','CU12_Horarios','CU13','CU15','CU16','CU17','CU18'],
    Docente:       ['CU1','CU2','CU3','CU14'],
    Autoridad:     ['CU1','CU2','CU3','CU15','CU16','CU17','CU18'],
    Auxiliar:      ['CU1','CU2','CU3','CU12_Aulas','CU13']
  };

  function updateSidebarByRole(role) {
    const allowed = permisos[role] || [];
    document.querySelectorAll('[data-role]').forEach(el => {
      el.classList.toggle('hidden', !allowed.includes(el.getAttribute('data-role')));
    });
    // Atenuar secciones sin items visibles
    document.querySelectorAll('[data-module]').forEach(section => {
      const visible = section.querySelectorAll('[data-role]:not(.hidden)').length;
      section.classList.toggle('opacity-40', visible === 0);
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    const select = document.getElementById('roleSelect');
    const saved = localStorage.getItem('ui.role');
    if (saved && permisos[saved]) select.value = saved;
    updateSidebarByRole(select.value);
    select.addEventListener('change', () => {
      localStorage.setItem('ui.role', select.value);
      updateSidebarByRole(select.value);
    });
  });
</script>
</body>
</html>
