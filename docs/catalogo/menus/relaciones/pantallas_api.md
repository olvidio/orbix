---
tipo: "relacion_pantallas_api"
modulo: "menus"
pantallas: 7
endpoints_api: 19
capacidades: 15
estado_revision: "generado"
---

# Relacion Pantallas API - menus

Indice generado automaticamente para cruzar pantallas frontend, capacidades y endpoints API.

## Por Pantalla

### `menus.pantalla.grupmenu_form`

- Controller: `frontend/menus/controller/grupmenu_form.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/menus/grupmenu_info`

Capacidades:
- `menus.grupmenu_info.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `menus.pantalla.grupmenu_lista`

- Controller: `frontend/menus/controller/grupmenu_lista.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/menus/grupmenu_eliminar`
- `/src/menus/grupmenu_lista`

Capacidades:
- `menus.grupmenu.gestionar`

Endpoints aportados por capacidades:
- `/src/menus/grupmenu_guardar`

### `menus.pantalla.menus_exportar_form`

- Controller: `frontend/menus/controller/menus_exportar_form.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/menus/menus_exportar`

Capacidades:
- `menus.menus_exportar.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `menus.pantalla.menus_get`

- Controller: `frontend/menus/controller/menus_get.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/menus/grupmenu_lista`
- `/src/menus/lista_meta_menus`
- `/src/menus/menus_get_page_data`

Capacidades:
- `menus.grupmenu.gestionar`
- `menus.lista_meta_menus.gestionar`
- `menus.menus_get_page.gestionar`

Endpoints aportados por capacidades:
- `/src/menus/grupmenu_eliminar`
- `/src/menus/grupmenu_guardar`

### `menus.pantalla.menus_importar_de_ficheros_a_ref`

- Controller: `frontend/menus/controller/menus_importar_de_ficheros_a_ref.php`
- Subtipo: `pantalla`

Endpoints directos:
- Ninguno detectado.

Capacidades:
- Ninguna detectada.

Endpoints aportados por capacidades:
- Ninguno adicional.

### `menus.pantalla.menus_importar_form`

- Controller: `frontend/menus/controller/menus_importar_form.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/menus/lista_templates`
- `/src/menus/menus_importar`

Capacidades:
- `menus.lista_templates.gestionar`
- `menus.menus_importar.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `menus.pantalla.menus_que`

- Controller: `frontend/menus/controller/menus_que.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/menus/grupmenu_lista`

Capacidades:
- `menus.grupmenu.gestionar`

Endpoints aportados por capacidades:
- `/src/menus/grupmenu_eliminar`
- `/src/menus/grupmenu_guardar`

## Por Endpoint API

### `/src/menus/grupmenu_coleccion`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/menus/grupmenu_eliminar`

Pantallas directas:
- `menus.pantalla.grupmenu_lista`

Pantallas via capacidad:
- `menus.pantalla.menus_get`
- `menus.pantalla.menus_que`

### `/src/menus/grupmenu_guardar`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- `menus.pantalla.grupmenu_lista`
- `menus.pantalla.menus_get`
- `menus.pantalla.menus_que`

### `/src/menus/grupmenu_info`

Pantallas directas:
- `menus.pantalla.grupmenu_form`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/menus/grupmenu_lista`

Pantallas directas:
- `menus.pantalla.grupmenu_lista`
- `menus.pantalla.menus_get`
- `menus.pantalla.menus_que`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/menus/lista_meta_menus`

Pantallas directas:
- `menus.pantalla.menus_get`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/menus/lista_templates`

Pantallas directas:
- `menus.pantalla.menus_importar_form`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/menus/menu_copiar`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/menus/menu_eliminar`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/menus/menu_guardar`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/menus/menu_mover`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/menus/menus_burger_layout_data`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/menus/menus_exportar`

Pantallas directas:
- `menus.pantalla.menus_exportar_form`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/menus/menus_exportar_ref_a_ficheros`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/menus/menus_generar_txt`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/menus/menus_get_page_data`

Pantallas directas:
- `menus.pantalla.menus_get`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/menus/menus_importar`

Pantallas directas:
- `menus.pantalla.menus_importar_form`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/menus/menus_importar_de_ficheros_a_ref`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/menus/menus_legacy_layout_items_data`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

## Alertas De Revision

Endpoints sin pantalla directa detectada:
- `/src/menus/grupmenu_coleccion`
- `/src/menus/grupmenu_guardar`
- `/src/menus/menu_copiar`
- `/src/menus/menu_eliminar`
- `/src/menus/menu_guardar`
- `/src/menus/menu_mover`
- `/src/menus/menus_burger_layout_data`
- `/src/menus/menus_exportar_ref_a_ficheros`
- `/src/menus/menus_generar_txt`
- `/src/menus/menus_importar_de_ficheros_a_ref`
- `/src/menus/menus_legacy_layout_items_data`

Endpoints sin pantalla directa ni capacidad relacionada:
- Ninguno — los endpoints listados arriba se consumen via AJAX, forms `.phtml`/`.twig` o login; ver `docs/REPASSO_FINAL.md` § B.

## Revision Manual

- Repaso 2026-05-21: huerfanos aceptados como patron normal Orbix (PostRequest / fetch desde vista).

## Revision Manual

- Confirmar si los endpoints sin pantalla directa se usan desde fragmentos AJAX no enlazados.
- Revisar pantallas que dependen de varias capacidades.
- Completar relaciones si hay navegacion generada dinamicamente o desde menus.
