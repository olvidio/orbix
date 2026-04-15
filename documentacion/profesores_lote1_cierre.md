# Cierre lote 1 migracion `apps/profesores`

## Arquitectura final del lote

1. **`src/profesores`**: casos de uso en `application/`, respuesta HTTP JSON desde `infrastructure/ui/http/controllers/` y registro en `config/routes.php` (rutas `/src/profesores/...`).
2. **`frontend/profesores`**: controladores que llaman al backend con `PostRequest::getDataFromUrl`, montan `web\Lista` y renderizan vistas `.phtml` en `frontend/profesores/view/` (patron `frontend/usuarios/controller/usuario_lista.php`).
3. **`apps/profesores/controller/*`**: wrappers de compatibilidad que solo hacen `require` al equivalente en `frontend/`. **Deprecados para enlaces y menus nuevos.**

## URLs canonicas

Ver tabla en `documentacion/profesores_migracion_baseline.md`. Resumen:

- `frontend/profesores/controller/congresos.php`
- `frontend/profesores/controller/docencia.php`
- `frontend/profesores/controller/profesor_asignatura_que.php`
- `frontend/profesores/controller/profesor_asignatura_ajax.php`

Referencias actualizadas en documentacion y plantillas de menu:

- `documentacion/Documentacion_Obix/menus.csv`
- `proves/aux_metamenus.csv`
- `log/menus/comun.sql`
- `documentacion/Documentacion_Obix/12. Estudios y STGR.md`

**Bases de datos ya desplegadas:** si los menus estan en tablas con URLs antiguas `apps/profesores/...`, conviene un script SQL de actualizacion puntual (mismo mapeo que en `menus.csv`).

## Checklist de no-regresion ejecutado

- `php -l` en rutas, application, controllers `src`, controllers `frontend`, wrappers `apps`.
- Mismos ids de tabla y columnas que el baseline.

## Siguiente lote sugerido

- `lista_por_departamentos.php` y despues `ficha_profesor_stgr.php` (mayor acoplamiento).
- Aplicar los mismos criterios descritos en [`refactor.md`](../refactor.md) en la raiz del repositorio.
