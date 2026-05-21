---
tipo: "relacion_pantallas_api"
modulo: "cambios"
pantallas: 6
endpoints_api: 12
capacidades: 11
estado_revision: "generado"
---

# Relacion Pantallas API - cambios

Indice generado automaticamente para cruzar pantallas frontend, capacidades y endpoints API.

## Por Pantalla

### `cambios.pantalla.avisos_generar`

- Controller: `frontend/cambios/controller/avisos_generar.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/cambios/avisos_generar_lista_data`

Capacidades:
- `cambios.avisos_generar.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `cambios.pantalla.usuario_avisos_pref`

- Controller: `frontend/cambios/controller/usuario_avisos_pref.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/cambios/usuario_avisos_pref_form_data`

Capacidades:
- `cambios.usuario_avisos_pref.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `cambios.pantalla.usuario_avisos_pref_condicion`

- Controller: `frontend/cambios/controller/usuario_avisos_pref_condicion.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/cambios/cambio_usuario_propiedad_pref_item_data`

Capacidades:
- `cambios.cambio_usuario_propiedad_pref_item.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `cambios.pantalla.usuario_avisos_pref_fases`

- Controller: `frontend/cambios/controller/usuario_avisos_pref_fases.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/cambios/cambio_usuario_objeto_pref_fases_data`

Capacidades:
- `cambios.cambio_usuario_objeto_pref_fases.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `cambios.pantalla.usuario_avisos_pref_propiedades`

- Controller: `frontend/cambios/controller/usuario_avisos_pref_propiedades.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/cambios/cambio_usuario_objeto_pref_propiedades_data`

Capacidades:
- `cambios.cambio_usuario_objeto_pref_propiedades.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `cambios.pantalla.usuario_form_avisos`

- Controller: `frontend/cambios/controller/usuario_form_avisos.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/cambios/usuario_form_avisos_data`

Capacidades:
- `cambios.usuario_form_avisos.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

## Por Endpoint API

### `/src/cambios/avisos_generar_lista_data`

Pantallas directas:
- `cambios.pantalla.avisos_generar`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/cambios/cambio_usuario_eliminar`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/cambios/cambio_usuario_eliminar_hasta_fecha`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/cambios/cambio_usuario_objeto_pref_eliminar`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/cambios/cambio_usuario_objeto_pref_fases_data`

Pantallas directas:
- `cambios.pantalla.usuario_avisos_pref_fases`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/cambios/cambio_usuario_objeto_pref_guardar`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/cambios/cambio_usuario_objeto_pref_propiedades_data`

Pantallas directas:
- `cambios.pantalla.usuario_avisos_pref_propiedades`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/cambios/cambio_usuario_propiedad_pref_guardar_todas`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/cambios/cambio_usuario_propiedad_pref_item_data`

Pantallas directas:
- `cambios.pantalla.usuario_avisos_pref_condicion`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/cambios/cambio_usuario_propiedad_pref_preview`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/cambios/usuario_avisos_pref_form_data`

Pantallas directas:
- `cambios.pantalla.usuario_avisos_pref`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/cambios/usuario_form_avisos_data`

Pantallas directas:
- `cambios.pantalla.usuario_form_avisos`

Pantallas via capacidad:
- Ninguna adicional.

## Alertas De Revision

Endpoints sin pantalla directa detectada (AJAX desde formularios):
- `/src/cambios/cambio_usuario_eliminar`, `cambio_usuario_eliminar_hasta_fecha` — `avisos_generar.php`
- `/src/cambios/cambio_usuario_objeto_pref_guardar`, `_eliminar` — `UsuarioAvisosPrefFormRender` + pref fragmentos
- `/src/cambios/cambio_usuario_propiedad_pref_preview`, `guardar_todas` — formulario preferencias

Endpoints sin pantalla directa ni capacidad relacionada:
- Ninguno — los endpoints listados arriba se consumen via AJAX, forms `.phtml`/`.twig` o login; ver `docs/REPASSO_FINAL.md` § B.

## Revision Manual

- Repaso 2026-05-21: huerfanos aceptados como patron normal Orbix (PostRequest / fetch desde vista).

## Revision Manual

- Confirmar si los endpoints sin pantalla directa se usan desde fragmentos AJAX no enlazados.
- Revisar pantallas que dependen de varias capacidades.
- Completar relaciones si hay navegacion generada dinamicamente o desde menus.
