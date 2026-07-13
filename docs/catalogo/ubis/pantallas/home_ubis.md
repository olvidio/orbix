---
id: "ubis.pantalla.home_ubis"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "ubis"
nombre: "Home Ubis"
controller: "frontend/ubis/controller/home_ubis.php"
vistas: ["frontend/ubis/view/home_ubis.phtml"]
fragmentos_frontend: ["frontend/dossiers/controller/dossiers_ver.php", "frontend/dossiers/controller/lista_dossiers.php", "frontend/ubis/controller/direcciones_editar.php", "frontend/ubis/controller/home_ubis.php", "frontend/ubis/controller/teleco_tabla.php", "frontend/ubis/controller/ubis_editar.php"]
endpoints: ["/src/ubis/home_ubis_data"]
capacidades: ["ubis.home_ubis.gestionar"]
campos: ["post.bloque", "post.id_ubi", "post.sel", "post.stack"]
acciones: ["fnjs_left_side_show", "fnjs_update_div"]
estado_revision: "revisado"
---

# Home Ubis

Ficha resumen de un ubi con enlaces a edición, direcciones, telecomunicaciones y dossiers.

## Tipo

- Subtipo: `fragmento_ajax`


- Controller: `frontend/ubis/controller/home_ubis.php`

## Vistas Relacionadas

- `frontend/ubis/view/home_ubis.phtml`

## Fragmentos Frontend Relacionados

- `frontend/dossiers/controller/dossiers_ver.php`
- `frontend/dossiers/controller/lista_dossiers.php`
- `frontend/ubis/controller/direcciones_editar.php`
- `frontend/ubis/controller/home_ubis.php`
- `frontend/ubis/controller/teleco_tabla.php`
- `frontend/ubis/controller/ubis_editar.php`

## Endpoints Usados

- `/src/ubis/home_ubis_data`

## Capacidades Relacionadas

- `ubis.home_ubis.gestionar`

## Campos Detectados

- `post.bloque`
- `post.id_ubi`
- `post.sel`
- `post.stack`

## Acciones Detectadas

- `fnjs_left_side_show`
- `fnjs_update_div`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
