---
tipo: "relacion_pantallas_api"
modulo: "actividadessacd"
pantallas: 4
endpoints_api: 14
capacidades: 13
estado_revision: "generado"
---

# Relacion Pantallas API - actividadessacd

Indice generado automaticamente para cruzar pantallas frontend, capacidades y endpoints API.

## Por Pantalla

### `actividadessacd.pantalla.activ_sacd`

- Controller: `frontend/actividadessacd/controller/activ_sacd.php`
- Subtipo: `pantalla`

Endpoints directos:
- `/src/actividadessacd/`
- `/src/actividadessacd/lista_actividades_sacd_data`
- `/src/actividadessacd/sacd_asignar`
- `/src/actividadessacd/sacd_eliminar`
- `/src/actividadessacd/sacd_reordenar`
- `/src/actividadessacd/sacds_disponibles_data`
- `/src/actividadessacd/sacds_encargados_data`
- `/src/actividadessacd/solapes_sacd_data`

Capacidades:
- `actividadessacd.lista_actividades_sacd.gestionar`
- `actividadessacd.sacd.gestionar`
- `actividadessacd.sacd_asignar.gestionar`
- `actividadessacd.sacd_reordenar.gestionar`
- `actividadessacd.sacds_disponibles.gestionar`
- `actividadessacd.sacds_encargados.gestionar`
- `actividadessacd.solapes_sacd.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `actividadessacd.pantalla.asignar_sacd_auto`

- Controller: `frontend/actividadessacd/controller/asignar_sacd_auto.php`
- Subtipo: `pantalla`

Endpoints directos:
- `/src/actividadessacd/sacd_asignar_auto`

Capacidades:
- `actividadessacd.sacd_asignar_auto.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `actividadessacd.pantalla.com_sacd_activ_periodo`

- Controller: `frontend/actividadessacd/controller/com_sacd_activ_periodo.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/actividadessacd/com_sacd_activ_periodo_page_data`
- `/src/actividadessacd/comunicacion_activ_sacd_data`
- `/src/actividadessacd/comunicacion_activ_sacd_enviar`

Capacidades:
- `actividadessacd.com_sacd_activ_periodo_page.gestionar`
- `actividadessacd.comunicacion_activ_sacd.gestionar`
- `actividadessacd.comunicacion_activ_sacd_enviar.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `actividadessacd.pantalla.com_sacd_txt`

- Controller: `frontend/actividadessacd/controller/com_sacd_txt.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/actividadessacd/locales_desplegable_data`
- `/src/actividadessacd/texto_comunicacion_data`
- `/src/actividadessacd/texto_comunicacion_guardar`

Capacidades:
- `actividadessacd.locales_desplegable.gestionar`
- `actividadessacd.texto_comunicacion.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

## Por Endpoint API

### `/src/actividadessacd/com_sacd_activ_periodo_page_data`

Pantallas directas:
- `actividadessacd.pantalla.com_sacd_activ_periodo`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadessacd/comunicacion_activ_sacd_data`

Pantallas directas:
- `actividadessacd.pantalla.com_sacd_activ_periodo`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadessacd/comunicacion_activ_sacd_enviar`

Pantallas directas:
- `actividadessacd.pantalla.com_sacd_activ_periodo`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadessacd/lista_actividades_sacd_data`

Pantallas directas:
- `actividadessacd.pantalla.activ_sacd`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadessacd/locales_desplegable_data`

Pantallas directas:
- `actividadessacd.pantalla.com_sacd_txt`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadessacd/sacd_asignar`

Pantallas directas:
- `actividadessacd.pantalla.activ_sacd`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadessacd/sacd_asignar_auto`

Pantallas directas:
- `actividadessacd.pantalla.asignar_sacd_auto`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadessacd/sacd_eliminar`

Pantallas directas:
- `actividadessacd.pantalla.activ_sacd`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadessacd/sacd_reordenar`

Pantallas directas:
- `actividadessacd.pantalla.activ_sacd`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadessacd/sacds_disponibles_data`

Pantallas directas:
- `actividadessacd.pantalla.activ_sacd`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadessacd/sacds_encargados_data`

Pantallas directas:
- `actividadessacd.pantalla.activ_sacd`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadessacd/solapes_sacd_data`

Pantallas directas:
- `actividadessacd.pantalla.activ_sacd`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadessacd/texto_comunicacion_data`

Pantallas directas:
- `actividadessacd.pantalla.com_sacd_txt`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadessacd/texto_comunicacion_guardar`

Pantallas directas:
- `actividadessacd.pantalla.com_sacd_txt`

Pantallas via capacidad:
- Ninguna adicional.

## Alertas De Revision

Endpoints sin pantalla directa detectada:
- Ninguno.

Endpoints sin pantalla directa ni capacidad relacionada:
- Ninguno — los endpoints listados arriba se consumen via AJAX, forms `.phtml`/`.twig` o login; ver `docs/REPASSO_FINAL.md` § B.

## Revision Manual

- Repaso 2026-05-21: huerfanos aceptados como patron normal Orbix (PostRequest / fetch desde vista).

## Revision Manual

- Confirmar si los endpoints sin pantalla directa se usan desde fragmentos AJAX no enlazados.
- Revisar pantallas que dependen de varias capacidades.
- Completar relaciones si hay navegacion generada dinamicamente o desde menus.
