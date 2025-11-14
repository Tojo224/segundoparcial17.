# Guía de Deployment - Módulo de Reportes (Fase 4)

## ⚠️ Prerrequisitos

### 1. Verificar Dependencias Instaladas

```bash
# Verificar maatwebsite/excel
composer show maatwebsite/excel
# Esperado: v3.1 o superior

# Verificar barryvdh/laravel-dompdf
composer show barryvdh/laravel-dompdf
# Esperado: v3.1.1 o superior

# Verificar que ambas estén en composer.json
cat composer.json | grep -A 2 "require"
```

Si falta alguna:
```bash
composer require maatwebsite/excel:^3.1
composer require barryvdh/laravel-dompdf:^3.1
composer dump-autoload
```

### 2. Verificar Configuración

```bash
# Verificar que ExcelServiceProvider está registrado
cat config/app.php | grep -i excel

# Verificar que DompdfServiceProvider está registrado
cat config/app.php | grep -i pdf
```

Deben aparecer en `providers[]`:
```php
// config/app.php
'providers' => [
    // ...
    Maatwebsite\Excel\ExcelServiceProvider::class,
    Barryvdh\DomPDF\ServiceProvider::class,
]

'aliases' => [
    // ...
    'Excel' => Maatwebsite\Excel\Facades\Excel::class,
]
```

---

## 📁 Archivos que Cambiaron

### 1. PHP Files
```
app/Modules/ReportesYDashboard/
├── Controllers/
│   └── ReportesController.php           ← MODIFICADO (descarga real)
├── Services/
│   └── ReportesService.php              ← MODIFICADO (columnas exactas)
└── Exports/
    └── ReportesExport.php               ← SIN CAMBIOS (funciona igual)
```

### 2. View Files
```
resources/views/
├── reportes_y_dashboard/
│   └── reportes.blade.php               ← MODIFICADO (JS params)
```

### 3. Route Files
```
routes/
└── api.php                              ← SIN CAMBIOS (rutas OK)
```

---

## ✅ Pasos de Deployment

### Paso 1: Backup de Producción (CRÍTICO)
```bash
# En servidor de producción
# Hacer backup de la BD
mysqldump -u usuario -p base_datos > backup_$(date +%Y%m%d_%H%M%S).sql

# O en PostgreSQL
pg_dump -U usuario base_datos > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Paso 2: Actualizar Archivos

```bash
# Opción A: Git Pull (si estás usando git)
git pull origin main
# Esto descarga los cambios de:
# - ReportesController.php
# - ReportesService.php
# - reportes.blade.php

# Opción B: Copiar archivos manualmente
# 1. Descargar los 3 archivos modificados
# 2. Copiar a su ubicación en servidor
# 3. Mantener permisos: chmod 644 *.php
```

### Paso 3: Limpiar Cache
```bash
# Eliminar cache de Laravel
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# O ejecutar todo junto
php artisan optimize:clear
```

### Paso 4: Verificar Sintaxis
```bash
# Verificar archivos PHP
php -l app/Modules/ReportesYDashboard/Controllers/ReportesController.php
php -l app/Modules/ReportesYDashboard/Services/ReportesService.php

# Esperado: "No syntax errors detected"
```

### Paso 5: Verificar Rutas
```bash
# Listar rutas API
php artisan route:list | grep reportes

# Debe mostrar:
# POST api/reportes/pdf
# POST api/reportes/excel
# POST api/reportes/horarios
# POST api/reportes/asistencia
# POST api/reportes/aulas
# POST api/reportes/carga-horaria
```

### Paso 6: Testing en Producción

```bash
# 1. Acceder a: https://tudominio.com/reportes
# 2. Seleccionar un año y semestre
# 3. Descargar un PDF
# 4. Descargar un Excel
# 5. Verificar en bitácora que queda registrado

# Desde terminal (si tienes acceso)
curl -X POST http://localhost:8000/api/reportes/pdf \
  -H "Content-Type: application/json" \
  -d '{"tipo_reporte":"horarios","id_gestion":1}'
```

---

## 🐛 Troubleshooting en Producción

### Problema: "Class not found - Excel"
**Solución**:
```bash
composer dump-autoload
php artisan config:cache
```

### Problema: "Class not found - Pdf"
**Solución**:
```bash
composer dump-autoload
php artisan config:cache
```

### Problema: "Archivo no descarga"
**Verificar**:
```bash
# 1. Que ReportesController tiene método exportarPDF()
grep -n "exportarPDF" app/Modules/ReportesYDashboard/Controllers/ReportesController.php

# 2. Que ReportesController importa Pdf
grep "use Barryvdh" app/Modules/ReportesYDashboard/Controllers/ReportesController.php

# 3. Que tiene ->download() al final
grep -A 5 "return \$pdf" app/Modules/ReportesYDashboard/Controllers/ReportesController.php
```

### Problema: "Columnas incorrectas en reporte"
**Verificar**:
```bash
# Revisar el método en ReportesService
grep -A 50 "generarReporteHorarios" app/Modules/ReportesYDashboard/Services/ReportesService.php
# Debe tener 10 columnas exactas con los nombres correctos
```

### Problema: "Bitácora no registra"
**Verificar**:
```bash
# Que BitacoraService existe
find . -name "BitacoraService.php" -type f

# Que se está inyectando en controller
grep -n "BitacoraService" app/Modules/ReportesYDashboard/Controllers/ReportesController.php

# Que se está llamando en exportarPDF()
grep -B 2 -A 2 "bitacora->registrar" app/Modules/ReportesYDashboard/Controllers/ReportesController.php
```

---

## 📊 Monitoreo en Producción

### Verificar Logs
```bash
# Ver últimas líneas del log
tail -f storage/logs/laravel.log

# Buscar errores de reportes
grep -i "reportes\|excel\|pdf" storage/logs/laravel.log

# Ver en tiempo real
tail -f storage/logs/laravel.log | grep -i reportes
```

### Verificar Base de Datos
```bash
# Verificar tabla de bitácora
SELECT * FROM bitacora 
WHERE descripcion LIKE 'Exportó%' 
ORDER BY fecha DESC LIMIT 10;

# Contar descargas por tipo
SELECT descripcion, COUNT(*) 
FROM bitacora 
WHERE descripcion LIKE 'Exportó reporte%' 
GROUP BY descripcion;
```

---

## 🔄 Rollback (Si algo falla)

```bash
# Restaurar versión anterior desde git
git checkout HEAD^ -- app/Modules/ReportesYDashboard/
git checkout HEAD^ -- resources/views/reportes_y_dashboard/reportes.blade.php

# O restaurar archivo manualmente
# 1. Descargar la versión anterior
# 2. Copiar archivos originales
# 3. Limpiar cache
php artisan optimize:clear

# O restaurar BD desde backup (si es necesario)
mysql -u usuario -p base_datos < backup_20240115_143000.sql
```

---

## 📋 Checklist Pre-Deployment

- [ ] Backup de BD realizado
- [ ] Dependencias instaladas (composer show)
- [ ] Archivos descargados/copiados
- [ ] Permisos correctos (644 para .php)
- [ ] Cache limpiado
- [ ] Sintaxis verificada (php -l)
- [ ] Rutas listadas (route:list)
- [ ] Testing manual realizado
- [ ] Bitácora registra descarga
- [ ] Archivos descargables (PDF/Excel)
- [ ] Headers correctos (año/semestre/fecha)
- [ ] Sin errores en logs
- [ ] Documentación notificada al equipo

---

## 📞 Mantenimiento Futuro

### Actualizaciones Mensuales
```bash
# Revisar logs
tail -100 storage/logs/laravel.log | grep -i reportes

# Revisar bitácora
SELECT COUNT(*) FROM bitacora WHERE fecha > NOW() - INTERVAL 30 DAY;

# Limpiar archivos temporales (si aplica)
find storage/app -name "*.xlsx" -mtime +30 -delete
find storage/app -name "*.pdf" -mtime +30 -delete
```

### Optimización
```bash
# Si reportes grandes son lentos
# Agregar índices a BD:
ALTER TABLE horario ADD INDEX idx_gestion (id_gestion);
ALTER TABLE asistencia ADD INDEX idx_gestion (id_gestion);
ALTER TABLE carga_horaria ADD INDEX idx_gestion (id_gestion);

# Verificar query plans
EXPLAIN SELECT * FROM horario WHERE id_gestion = 1;
```

---

## 🚨 Problemas Comunes

| Problema | Causa | Solución |
|----------|-------|----------|
| Class not found (Excel) | AutoLoader desactualizado | `composer dump-autoload` |
| Headers already sent | Espacio antes de `<?php` | Verificar archivo |
| File not found (storage) | Permisos insuficientes | `chmod 755 storage` |
| Query slow | Sin índices en BD | Agregar índices |
| Out of memory PDF | Dataset muy grande | Paginar reportes |
| Bitácora vacía | Auth::id() falla | Verificar autenticación |

---

## 📞 Soporte

Si encuentras problemas:

1. **Revisar logs**: `tail -f storage/logs/laravel.log`
2. **Verificar sintaxis**: `php -l archivo.php`
3. **Limpiar cache**: `php artisan optimize:clear`
4. **Consultar documentación**: Ver CAMBIOS_FASE_4.md
5. **Testing manual**: Ver TESTING_REPORTES.md

---

## ✅ Validación Final

Después de deployment, confirmar:

```bash
# 1. Servicios corriendo
ps aux | grep php
ps aux | grep apache/nginx

# 2. Conexión a BD
php artisan tinker
>>> DB::connection()->getPdo()

# 3. Rutas OK
php artisan route:list | grep reportes

# 4. Archivo se descarga
curl -o test.pdf -X POST http://localhost:8000/api/reportes/pdf \
  -H "Content-Type: application/json" \
  -d '{"tipo_reporte":"horarios","id_gestion":1}'
file test.pdf  # Debe mostrar: PDF document

# 5. Excel se descarga
curl -o test.xlsx -X POST http://localhost:8000/api/reportes/excel \
  -H "Content-Type: application/json" \
  -d '{"tipo_reporte":"horarios","id_gestion":1}'
file test.xlsx  # Debe mostrar: Microsoft Excel Workbook
```

---

**Versión del Documento**: 1.0
**Fecha**: 15 de Enero de 2024
**Aplicable a**: Fase 4 del Módulo de Reportes
**Estado**: Listo para Producción
