---
id: "personas.pantalla.traslado_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "personas"
nombre: "Traslado Form"
controller: "frontend/personas/controller/traslado_form.php"
vistas: ["frontend/personas/view/traslado_form.phtml"]
fragmentos_frontend: ["frontend/dossiers/controller/dossiers_ver.php", "frontend/personas/controller/home_persona.php"]
endpoints: ["/src/personas/traslado_form_data", "/src/personas/traslado_update"]
capacidades: ["personas.traslado.gestionar"]
campos: ["form.f_ctr", "form.f_dl", "form.new_ctr", "form.new_dl", "form.situacion", "html.f_ctr", "html.f_dl", "post.cabecera", "post.id_pau", "post.obj_pau", "post.sel"]
acciones: ["fnjs_guardar", "fnjs_update_div"]
estado_revision: "generado"
---

# Traslado Form

Formulario para trasladar una persona de centro y/o delegacion.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/personas/controller/traslado_form.php`

## Vistas Relacionadas

- `frontend/personas/view/traslado_form.phtml`

## Fragmentos Frontend Relacionados

- `frontend/dossiers/controller/dossiers_ver.php`
- `frontend/personas/controller/home_persona.php`

## Endpoints Usados

- `/src/personas/traslado_form_data`
- `/src/personas/traslado_update`

## Capacidades Relacionadas

- `personas.traslado.gestionar`

## Campos Detectados

- `form.f_ctr`
- `form.f_dl`
- `form.new_ctr`
- `form.new_dl`
- `form.situacion`
- `html.f_ctr`
- `html.f_dl`
- `post.cabecera`
- `post.id_pau`
- `post.obj_pau`
- `post.sel`

## Acciones Detectadas

- `fnjs_guardar`
- `fnjs_update_div`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
