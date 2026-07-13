---
id: "ubis.pantalla.list_ctr"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "ubis"
nombre: "List Ctr"
controller: "frontend/ubis/controller/list_ctr.php"
vistas: ["frontend/ubis/view/list_ctr.phtml"]
fragmentos_frontend: ["frontend/ubis/controller/delegacion_que.php", "frontend/ubis/controller/home_ubis.php", "frontend/ubis/controller/list_ctr.php", "frontend/ubis/controller/trasladar_ubis.php"]
endpoints: ["/src/ubis/list_ctr_data"]
capacidades: ["ubis.list_ctr.gestionar"]
campos: ["form.loc", "form.que_lista", "form.sel", "post.loc", "post.que_lista", "post.stack"]
acciones: ["fnjs_actualizar", "fnjs_cerrar", "fnjs_enviar_formulario", "fnjs_left_side_hide", "fnjs_limpiar", "fnjs_modificar", "fnjs_solo_uno", "fnjs_trasladar", "fnjs_update_div", "fnjs_ver_dl"]
estado_revision: "revisado"
---

# List Ctr

Pantalla principal de listado de centros y casas con filtros por delegación y tipo.

## Tipo

- Subtipo: `pantalla_principal`


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

## Ruta de menú

- **Legacy:** scdl > direcciones > listados
- **Pills2:** Calendario > centros y casas > listados
