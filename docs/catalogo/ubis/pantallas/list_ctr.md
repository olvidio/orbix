---
id: "ubis.pantalla.list_ctr"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "ubis"
nombre: "List Ctr"
controller: "frontend/ubis/controller/list_ctr.php"
vistas: ["frontend/ubis/view/list_ctr.phtml"]
fragmentos_frontend: ["frontend/ubis/controller/delegacion_que.php", "frontend/ubis/controller/home_ubis.php", "frontend/ubis/controller/list_ctr.php", "frontend/ubis/controller/trasladar_ubis.php"]
endpoints: ["/src/ubis/list_ctr_data"]
capacidades: ["ubis.list_ctr.gestionar"]
campos: ["form.loc", "form.que_lista", "form.sel", "post.loc", "post.que_lista", "post.stack"]
acciones: ["fnjs_actualizar", "fnjs_cerrar", "fnjs_enviar_formulario", "fnjs_left_side_hide", "fnjs_limpiar", "fnjs_modificar", "fnjs_solo_uno", "fnjs_trasladar", "fnjs_update_div", "fnjs_ver_dl"]
estado_revision: "generado"
---

# List Ctr

Página para realizar algunos listados standard de ubis Llegamos desde menú: "centros y casas" y submenú "listados" Las funciones que podré hacer con los ubis son idénticas a las que realizamos en submenú "buscar" Se tiene en cuenta si es una vuelta de un go_to

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/ubis/controller/list_ctr.php`

## Vistas Relacionadas

- `frontend/ubis/view/list_ctr.phtml`

## Fragmentos Frontend Relacionados

- `frontend/ubis/controller/delegacion_que.php`
- `frontend/ubis/controller/home_ubis.php`
- `frontend/ubis/controller/list_ctr.php`
- `frontend/ubis/controller/trasladar_ubis.php`

## Endpoints Usados

- `/src/ubis/list_ctr_data`

## Capacidades Relacionadas

- `ubis.list_ctr.gestionar`

## Campos Detectados

- `form.loc`
- `form.que_lista`
- `form.sel`
- `post.loc`
- `post.que_lista`
- `post.stack`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_cerrar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_limpiar`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_trasladar`
- `fnjs_update_div`
- `fnjs_ver_dl`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
