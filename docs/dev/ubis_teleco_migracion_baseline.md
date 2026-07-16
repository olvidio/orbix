# Baseline migracion `ubis/teleco_*`

## Pantallas y acciones incluidas

- `teleco_tabla.php`: listado de telecomunicaciones por ubi.
- `teleco_editar.php`: formulario de alta/edicion.
- `teleco_update.php`: persistencia (guardar/eliminar).
- `teleco_ajax.php`: carga de descripciones por tipo.

## Parametros clave

- `obj_pau`: tipo de ubi (`CentroDl`, `CentroEx`, `CasaDl`, `CasaEx`, ...).
- `id_ubi`: identificador del ubi.
- `mod`: `nuevo`, `editar`, `teleco`, `eliminar_teleco`.
- `sel[]` / `s_pkey`: PK codificada para seleccionar registro.
- `id_tipo_teleco`: para refrescar combo de descripciones.

## Comportamiento actual

- Lista con botones de nuevo/modificar/eliminar segun permisos.
- Form con combos dependientes (tipo -> descripcion).
- Guardado y eliminado via llamadas AJAX.
- Refresco de ficha tras cambios.

## Riesgos a vigilar

- Resolucion dinamica de repositorios segun `obj_pau`.
- PK compuesta codificada en base64 url-safe.
- Compatibilidad con permisos `scdl` y alcance DL/EX.
