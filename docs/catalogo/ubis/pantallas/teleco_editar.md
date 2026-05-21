---
id: "ubis.pantalla.teleco_editar"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "ubis"
nombre: "Teleco Editar"
controller: "frontend/ubis/controller/teleco_editar.php"
vistas: ["frontend/ubis/view/teleco_form.phtml"]
fragmentos_frontend: ["frontend/ubis/controller/teleco_desc_lista_ajax.php"]
endpoints: ["/src/ubis/teleco_editar"]
capacidades: ["ubis.teleco_editar.gestionar"]
campos: ["form.id_desc_teleco", "form.id_tipo_teleco", "form.mod", "form.num_teleco", "form.observ", "html.mod", "html.num_teleco", "html.observ", "post.id_ubi", "post.mod", "post.obj_pau", "post.s_pkey", "post.sel"]
acciones: ["fnjs_actualizar_descripcion", "fnjs_guardar"]
estado_revision: "generado"
---

# Teleco Editar

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
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

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
