---
id: "personas.pantalla.personas_editar"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "personas"
nombre: "Personas Editar"
controller: "frontend/personas/controller/personas_editar.php"
vistas: []
fragmentos_frontend: ["frontend/dossiers/controller/dossiers_ver.php", "frontend/personas/controller/home_persona.php", "frontend/personas/controller/traslado_form.php"]
endpoints: ["/src/personas/personas_editar_data"]
capacidades: ["personas.personas_editar.gestionar"]
campos: ["post.apellido1", "post.id_nom", "post.nuevo", "post.obj_pau", "post.sel", "post.stack", "post.tabla"]
acciones: ["fnjs_act_ctr"]
estado_revision: "generado"
---

# Personas Editar

Ficha de una persona: edicion (o alta si `$Qnuevo === 1`).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/personas/controller/personas_editar.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/dossiers/controller/dossiers_ver.php`
- `frontend/personas/controller/home_persona.php`
- `frontend/personas/controller/traslado_form.php`

## Endpoints Usados

- `/src/personas/personas_editar_data`

## Capacidades Relacionadas

- `personas.personas_editar.gestionar`

## Campos Detectados

- `post.apellido1`
- `post.id_nom`
- `post.nuevo`
- `post.obj_pau`
- `post.sel`
- `post.stack`
- `post.tabla`

## Acciones Detectadas

- `fnjs_act_ctr`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
