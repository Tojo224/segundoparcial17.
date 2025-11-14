# Guía de Testing - Módulo de Reportes

## 1. Requisitos Previos

1. **Base de datos poblada** con:
   - Al menos 1 gestión académica (año + semestre)
   - Docentes creados
   - Materias creadas
   - Grupos creados
   - Horarios asignados
   - Carga horaria asignada

2. **Laravel corriendo**:
   ```bash
   php artisan serve
   ```

3. **Usuario autenticado** en el sistema

---

## 2. Caso de Prueba 1: Listar Gestiones Académicas

### Objetivo
Verificar que el selector de año/semestre se llena correctamente

### Pasos
1. Ir a: `http://localhost:8000/reportes`
2. Observar el dropdown "Año Académico"

### Resultado Esperado
- Dropdown muestra todos los años disponibles
- Al seleccionar un año, semestre se habilita
- Al seleccionar semestre, se guarda id_gestion en input oculto

### Validación
```javascript
// En consola del navegador
console.log(document.getElementById('filtro-id-gestion').value);
// Debe mostrar un número (id_gestion)
```

---

## 3. Caso de Prueba 2: Descargar Reporte Horarios (PDF)

### Objetivo
Verificar que la descarga de PDF funciona correctamente

### Pasos
1. Seleccionar Año: 2024
2. Seleccionar Semestre: 1
3. Hacer clic en botón "PDF" del Reporte de Horarios
4. Esperar a que el archivo descargue

### Resultado Esperado
- ✅ Se descarga archivo: `Reporte_horarios_[timestamp].pdf`
- ✅ PDF contiene:
  - Encabezado: "Reporte de Horarios"
  - Año: 2024 y Semestre: 1
  - Fecha de generación
  - Tabla con 10 columnas exactas
  - Todos los registros

### Columnas a Verificar (Reporte Horarios)
1. Docente
2. Materia
3. Grupo
4. Dia
5. Hora_Inicio
6. Hora_Fin
7. Aula
8. Modulo
9. Anio (debe ser 2024 en todos)
10. Semestre (debe ser 1 en todos)

### Validar en Navegador
```javascript
// Ver logs en consola
console.log('PDF descargado exitosamente');
```

---

## 4. Caso de Prueba 3: Descargar Reporte Horarios (Excel)

### Objetivo
Verificar que la descarga de Excel funciona correctamente

### Pasos
1. Seleccionar Año: 2024
2. Seleccionar Semestre: 1
3. Hacer clic en botón "Excel" del Reporte de Horarios
4. Esperar a que el archivo descargue
5. Abrir con LibreOffice Calc o Excel

### Resultado Esperado
- ✅ Se descarga archivo: `Reporte_horarios_[timestamp].xlsx`
- ✅ Excel contiene:
  - Encabezado con colores (fondo oscuro, texto blanco)
  - 10 columnas exactas
  - Datos sin formatear adicional (datos puros)
  - Columna de Año con valor 2024 en todas las filas
  - Columna de Semestre con valor 1 en todas las filas

---

## 5. Caso de Prueba 4: Descargar Reporte Asistencia (PDF)

### Objetivo
Verificar que el reporte de asistencia incluye estadísticas

### Pasos
1. Seleccionar Año: 2024
2. Seleccionar Semestre: 1
3. Hacer clic en "PDF" del Reporte de Asistencia
4. Descargar y abrir

### Resultado Esperado
- ✅ PDF descargado: `Reporte_asistencia_[timestamp].pdf`
- ✅ Tabla contiene 10 columnas:
  1. Fecha_Registro
  2. Docente
  3. Materia
  4. Grupo
  5. Dia
  6. Hora_Inicio
  7. Hora_Fin
  8. Tipo (Presente/Ausente/Retardo/Justificado)
  9. Anio (2024)
  10. Semestre (1)

### Estadísticas Esperadas
- Total de registros
- Presentes
- Ausentes
- Retrasos
- Justificadas

---

## 6. Caso de Prueba 5: Descargar Reporte Disponibilidad Aulas

### Objetivo
Verificar que el reporte muestra 2 secciones: Ocupadas + Disponibles

### Pasos
1. Seleccionar Año: 2024
2. Seleccionar Semestre: 1
3. Hacer clic en "PDF" del Reporte de Disponibilidad Aulas
4. Descargar y abrir

### Resultado Esperado
- ✅ PDF con 2 secciones claramente separadas:

#### Sección 1: AULAS OCUPADAS (9 columnas)
1. Aula
2. Modulo
3. Dia
4. Hora_Inicio
5. Hora_Fin
6. Docente
7. Materia
8. Grupo
9. Anio (2024)
10. Semestre (1)

Muestra TODOS los horarios asignados en esta gestión

#### Sección 2: AULAS DISPONIBLES (5 columnas)
1. Aula
2. Modulo
3. Intervalos_Disponibles (valor: "Toda la semana")
4. Anio (2024)
5. Semestre (1)

Muestra aulas que NO tienen horarios en esta gestión

---

## 7. Caso de Prueba 6: Descargar Reporte Carga Horaria (Excel)

### Objetivo
Verificar que agrupa correctamente por docente

### Pasos
1. Seleccionar Año: 2024
2. Seleccionar Semestre: 1
3. Hacer clic en "Excel" del Reporte de Carga Horaria
4. Descargar y abrir

### Resultado Esperado
- ✅ Excel descargado: `Reporte_carga_horaria_[timestamp].xlsx`
- ✅ 6 columnas exactas:
  1. Docente (ejemplo: "Juan Pérez")
  2. Materia (ejemplo: "Matemática")
  3. Grupo (ejemplo: "101")
  4. Horas_Asignadas (número entero)
  5. Anio (2024 en todos)
  6. Semestre (1 en todos)

### Validación de Datos
- Cada fila = una materia/grupo por docente
- Horas_Asignadas ≥ 0
- No hay valores NULL o vacíos en columnas requeridas

---

## 8. Caso de Prueba 7: Validación de Bitácora

### Objetivo
Verificar que cada descarga registra en bitácora

### Pasos
1. Ir a: `http://localhost:8000/bitacora`
2. Descargar un reporte (cualquiera, cualquier formato)
3. Volver a bitácora y actualizar página
4. Buscar entrada de descarga

### Resultado Esperado
- ✅ Aparece entrada con:
  - Acción: "Exportó reporte de horarios a PDF" (ejemplo)
  - Usuario: Tu usuario actual
  - Fecha/Hora: Coincide con descarga
  - IP: Tu IP local

---

## 9. Caso de Prueba 8: Error - Sin Seleccionar Gestión

### Objetivo
Verificar que la validación frontend funciona

### Pasos
1. NO seleccionar año ni semestre
2. Hacer clic en cualquier botón "Descargar"

### Resultado Esperado
- ✅ Mensaje de error: "Por favor seleccione un año académico y semestre"
- ✅ NO se realiza ninguna solicitud HTTP
- ✅ NO se registra en bitácora

---

## 10. Caso de Prueba 9: Error - Gestión Inválida

### Objetivo
Verificar que el servidor valida id_gestion

### Pasos (Consola del Navegador)
```javascript
fetch('/api/reportes/pdf', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        tipo_reporte: 'horarios',
        id_gestion: 99999  // ID que no existe
    })
})
.then(r => r.json())
.then(console.log)
```

### Resultado Esperado
- ✅ Respuesta 500 con error
- ✅ Mensaje: "Error al generar reporte"

---

## 11. Caso de Prueba 10: Error - Sin Datos

### Objetivo
Verificar mensaje cuando gestión no tiene datos

### Pasos
1. Seleccionar gestión que no tiene horarios/asistencia
2. Intentar descargar reporte

### Resultado Esperado
- ✅ Mensaje de error: "No hay datos para exportar"
- ✅ No se descarga archivo

---

## 12. Verificación de Headers en Archivos

### PDF
**Abrir con editor de texto y buscar**:
```
Año: 2024 | Semestre: 1
Fecha de Generación: 2024-01-15 14:30:00
```

### Excel
**Columna A (encabezado)** debe tener estilos:
- Fondo color oscuro (#1F2937)
- Texto blanco
- Letras en negrita
- Ancho automático

---

## 13. API Endpoints para Testing Manual

### Generar Reporte (JSON)
```bash
curl -X POST http://localhost:8000/api/reportes/pdf \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: [token]" \
  -d '{"tipo_reporte":"horarios","id_gestion":5}'
```

### Descargar PDF
```bash
curl -X POST http://localhost:8000/api/reportes/pdf \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: [token]" \
  -d '{"tipo_reporte":"horarios","id_gestion":5}' \
  --output Reporte_horarios.pdf
```

### Descargar Excel
```bash
curl -X POST http://localhost:8000/api/reportes/excel \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: [token]" \
  -d '{"tipo_reporte":"horarios","id_gestion":5}' \
  --output Reporte_horarios.xlsx
```

---

## 14. Checklist de Testing

- [ ] Selector de año/semestre funciona
- [ ] Descarga PDF - Horarios con 10 columnas
- [ ] Descarga Excel - Horarios con 10 columnas
- [ ] Año y Semestre aparecen en TODAS las filas
- [ ] Descarga PDF - Asistencia con estadísticas
- [ ] Descarga Excel - Asistencia con datos
- [ ] Descarga PDF - Aulas (2 secciones)
- [ ] Descarga Excel - Aulas (2 secciones)
- [ ] Descarga PDF - Carga Horaria
- [ ] Descarga Excel - Carga Horaria
- [ ] Headers profesionales en archivos
- [ ] Bitácora registra cada descarga
- [ ] Error al no seleccionar gestión
- [ ] Error al usar id_gestion inválido
- [ ] Error cuando no hay datos
- [ ] Mensajes de éxito/error en UI
- [ ] Nombres de archivo incluyen timestamp
- [ ] Formatos PDF y Excel son válidos

---

## 15. Troubleshooting

### El archivo no descarga
- ✅ Verificar: `exportarPDF()` y `exportarExcel()` tienen `->download()` al final
- ✅ Verificar: Headers Content-Type correctos (application/pdf, application/xlsx)
- ✅ Verificar: No hay redirecciones después del download

### Las columnas están mal
- ✅ Verificar: En `generarHTMLReporte()` - obtiene keys del primer objeto
- ✅ Verificar: En `ReportesExport` - usa encabezados pasados del controller

### Año/Semestre no aparecen
- ✅ Verificar: `obtenerInfoGestion()` retorna correctamente
- ✅ Verificar: Response incluye `'info'` con año, semestre
- ✅ Verificar: En `generarHTMLReporte()` se usa `$info['anio']` y `$info['semestre']`

### Bitácora no registra
- ✅ Verificar: `$this->bitacora->registrar()` se llama ANTES del download
- ✅ Verificar: Usuario está autenticado (`Auth::id()`)
- ✅ Verificar: BitacoraService no lanza excepciones

---

**Última Actualización**: Fase 4 - 2024-01-15
