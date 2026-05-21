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
estado_revision: "generado"
---

# Home Ubis

Descripcion funcional pendiente de revisar.

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

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
