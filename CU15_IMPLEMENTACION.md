# CU15 - Registrar Asistencia Docente

## Implementación Completa ✅

### Estructura Creada

#### 1. Modelos (Models)
- **Asistencia.php** (`app/Modules/ControlAsistencia/Models/`)
  - Conexión con tabla `asistencia` existente
  - Relación con modelo `Horario`
  - Constantes para tipos de asistencia: Presentado, Ausente, Justificado, Tardanza
  - Scopes útiles: fecha, rangoFecha, tipo, porHorario, recientes

#### 2. Servicios (Services)
- **AsistenciaService.php** (`app/Modules/ControlAsistencia/Services/`)
  - `all()`: Obtener asistencias con filtros avanzados
  - `paginate()`: Paginación de registros
  - `find()`: Buscar por ID
  - `create()`: Crear nueva asistencia
  - `update()`: Actualizar asistencia
  - `delete()`: Eliminar asistencia
  - `existeAsistencia()`: Verificar duplicados
  - `getHorariosPorFecha()`: Obtener horarios programados por día
  - `getHorariosSinAsistencia()`: Identificar horarios pendientes
  - `getEstadisticas()`: Cálculo de métricas

#### 3. Controladores (Controllers)
- **AsistenciaController.php** (`app/Modules/ControlAsistencia/Controllers/`)
  
  **API Endpoints:**
  - GET `/api/asistencia` - Listar con paginación
  - POST `/api/asistencia` - Crear
  - PUT `/api/asistencia/{id}` - Actualizar
  - DELETE `/api/asistencia/{id}` - Eliminar
  - GET `/api/asistencia/horarios-fecha` - Horarios por fecha
  - GET `/api/asistencia/estadisticas` - Obtener estadísticas
  
  **Web Methods:**
  - `vistaAsistencia()` - Vista principal
  - `storeWeb()` - Guardar desde formulario
  - `updateWeb()` - Actualizar desde formulario
  - `destroyWeb()` - Eliminar desde web

#### 4. Vistas (Views)
- **asistencia.blade.php** (`resources/views/`)
  
  **Componentes incluidos:**
  - Sidebar de navegación con todas las secciones
  - Tarjetas de estadísticas (Total, Hoy, Pendientes, Horarios programados)
  - Panel de filtros (Fecha, Tipo de asistencia)
  - Lista de horarios pendientes con botón de registro rápido
  - Tabla de asistencias registradas con acciones CRUD
  - Modal para registrar/editar asistencia
  - Modal de detalle de asistencia
  - JavaScript para carga dinámica de horarios por fecha
  - Diseño responsive con Tailwind CSS

#### 5. Rutas (Routes)
- **web.php** actualizado con:
  ```php
  Route::get('/asistencia', [AsistenciaController::class, 'vistaAsistencia'])
  Route::post('/asistencia', [AsistenciaController::class, 'storeWeb'])
  Route::put('/asistencia/{id}', [AsistenciaController::class, 'updateWeb'])
  Route::delete('/asistencia/{id}', [AsistenciaController::class, 'destroyWeb'])
  Route::get('/asistencia/horarios-fecha', [AsistenciaController::class, 'getHorariosPorFecha'])
  Route::get('/asistencia/estadisticas', [AsistenciaController::class, 'getEstadisticas'])
  ```

### Navegación Actualizada

Se agregó el enlace "Registrar Asistencia" en todas las vistas:
- ✅ reservas.blade.php
- ✅ horarios_calendario.blade.php
- ✅ aulas.blade.php
- ✅ docentes.blade.php
- ✅ usuarios.blade.php
- ✅ bitacora.blade.php
- ✅ grupos.blade.php
- ✅ asignar_grupos.blade.php
- ✅ asignaciones_grupos.blade.php
- ✅ asistencia.blade.php (nueva)

### Características Implementadas

1. **Registro de Asistencia**
   - Selección de fecha
   - Selección de horario/clase
   - Tipos: Presentado, Ausente, Justificado, Tardanza
   - Información completa de la clase (docente, materia, grupo, aula)

2. **Registro Rápido**
   - Lista de horarios pendientes del día
   - Botón de registro rápido por cada horario

3. **Validaciones**
   - No permite duplicar asistencia para el mismo horario en la misma fecha
   - Validación de tipos de asistencia
   - Validación de existencia de horarios

4. **Filtros**
   - Por fecha
   - Por tipo de asistencia
   - Por docente (en el servicio)

5. **Estadísticas**
   - Total de asistencias registradas
   - Asistencias del día
   - Horarios pendientes de registro
   - Total de horarios programados

6. **Bitácora**
   - Registro automático de todas las operaciones CRUD
   - Información detallada de las acciones

### Base de Datos

Tabla existente utilizada: **asistencia**
- id_asistencia (PK)
- fecha_registro (date)
- tipo (varchar)
- id_horario (FK → horario)

Relaciones:
- asistencia → horario
- horario → carga_horaria
- carga_horaria → docente → usuario
- carga_horaria → grupo → materia

### Diseño

- Color principal: Indigo (#4338ca, #4f46e5)
- Responsive design con Tailwind CSS
- Iconos SVG consistentes con el resto del sistema
- Modales para formularios y detalles
- Badges de colores por tipo de asistencia:
  - Verde: Presentado
  - Rojo: Ausente
  - Azul: Justificado
  - Amarillo: Tardanza

### Funcionalidades JavaScript

- Carga dinámica de horarios según fecha seleccionada
- Actualización automática de información del horario
- Modales interactivos
- Registro rápido desde horarios pendientes
- Validación en cliente

## Acceso

URL: `/asistencia`
Ruta nombrada: `asistencia.vista`

## Estado: COMPLETO ✅

Todos los componentes del CU15 han sido implementados y están listos para uso.
