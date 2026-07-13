---
id: "personas.pantalla.traslado_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "personas"
nombre: "Traslado de persona"
controller: "frontend/personas/controller/traslado_form.php"
vistas: ["frontend/personas/view/traslado_form.phtml"]
fragmentos_frontend: []
endpoints: ["/src/personas/traslado_form_data", "/src/personas/traslado_update"]
capacidades: ["personas.traslado.gestionar"]
campos: ["form.new_ctr", "form.f_ctr", "form.new_dl", "form.f_dl", "form.situacion", "post.id_pau", "post.obj_pau", "post.cabecera"]
acciones: ["fnjs_guardar", "fnjs_update_div"]
estado_revision: "revisado"
---

# Traslado de persona

Formulario para cambiar centro (`new_ctr`+`f_ctr`) y/o delegación (`new_dl`+`f_dl`+`situacion`).
No aplica a personas de paso (`PersonaPub`).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/personas/controller/traslado_form.php`

## Endpoints Usados

- `/src/personas/traslado_form_data`
- `/src/personas/traslado_update`

## Manual De Usuario

Pantalla revisada contra `frontend/personas/`. Acceso desde ficha edición (`ir_a_traslado`) o
botón «cambio de ctr» en listado (`sm`).

## Ruta de menú

- sin entrada de menú en el índice (desde ficha o listado personas).
