---
tipo: "relacion_pantallas_api"
modulo: "actividadescentro"
pantallas: 1
endpoints_api: 7
capacidades: 7
estado_revision: "generado"
---

# Relacion Pantallas API - actividadescentro

Indice generado automaticamente para cruzar pantallas frontend, capacidades y endpoints API.

## Por Pantalla

### `actividadescentro.pantalla.activ_ctr`

- Controller: `frontend/actividadescentro/controller/activ_ctr.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/actividadescentro/`
- `/src/actividadescentro/activ_ctr_shell_data`

Capacidades:
- `actividadescentro.activ_ctr_shell.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

## Por Endpoint API

### `/src/actividadescentro/activ_ctr_shell_data`

Pantallas directas:
- `actividadescentro.pantalla.activ_ctr`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadescentro/centro_encargado_asignar`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadescentro/centro_encargado_eliminar`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadescentro/centro_encargado_reordenar`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadescentro/centros_disponibles_data`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadescentro/centros_encargados_data`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/actividadescentro/lista_actividades_ctr_data`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

## Alertas De Revision

Endpoints sin pantalla directa detectada:
- `/src/actividadescentro/centro_encargado_asignar`
- `/src/actividadescentro/centro_encargado_eliminar`
- `/src/actividadescentro/centro_encargado_reordenar`
- `/src/actividadescentro/centros_disponibles_data`
- `/src/actividadescentro/centros_encargados_data`
- `/src/actividadescentro/lista_actividades_ctr_data`

Endpoints sin pantalla directa ni capacidad relacionada:
- Ninguno — los endpoints listados arriba se consumen via AJAX, forms `.phtml`/`.twig` o login; ver `docs/REPASSO_FINAL.md` § B.

## Revision Manual

- Repaso 2026-05-21: huerfanos aceptados como patron normal Orbix (PostRequest / fetch desde vista).

## Revision Manual

- Confirmar si los endpoints sin pantalla directa se usan desde fragmentos AJAX no enlazados.
- Revisar pantallas que dependen de varias capacidades.
- Completar relaciones si hay navegacion generada dinamicamente o desde menus.
