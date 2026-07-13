---
id: "ubis.pantalla.teleco_editar"
tipo: "pantalla_frontend"
subtipo: "modal"
modulo: "ubis"
nombre: "Teleco Editar"
controller: "frontend/ubis/controller/teleco_editar.php"
vistas: ["frontend/ubis/view/teleco_form.phtml"]
fragmentos_frontend: ["frontend/ubis/controller/teleco_desc_lista_ajax.php"]
endpoints: ["/src/ubis/teleco_editar"]
capacidades: ["ubis.teleco_editar.gestionar"]
campos: ["form.id_desc_teleco", "form.id_tipo_teleco", "form.mod", "form.num_teleco", "form.observ", "html.mod", "html.num_teleco", "html.observ", "post.id_ubi", "post.mod", "post.obj_pau", "post.s_pkey", "post.sel"]
acciones: ["fnjs_actualizar_descripcion", "fnjs_guardar"]
estado_revision: "revisado"
---

# Teleco Editar

Formulario modal de alta o edición de una telecomunicación del ubi.

## Tipo

- Subtipo: `modal`


- Controller: `frontend/ubis/controller/teleco_editar.php`

## Vistas Relacionadas

- `frontend/ubis/view/teleco_form.phtml`

## Fragmentos Frontend Relacionados

- `frontend/ubis/controller/teleco_desc_lista_ajax.php`

## Endpoints Usados

- `/src/ubis/teleco_editar`

## Capacidades Relacionadas

- `ubis.teleco_editar.gestionar`

## Campos Detectados

- `form.id_desc_teleco`
- `form.id_tipo_teleco`
- `form.mod`
- `form.num_teleco`
- `form.observ`
- `html.mod`
- `html.num_teleco`
- `html.observ`
- `post.id_ubi`
- `post.mod`
- `post.obj_pau`
- `post.s_pkey`
- `post.sel`

## Acciones Detectadas

- `fnjs_actualizar_descripcion`
- `fnjs_guardar`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
