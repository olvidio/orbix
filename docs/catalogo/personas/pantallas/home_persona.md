---
id: "personas.pantalla.home_persona"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "personas"
nombre: "Home Persona"
controller: "frontend/personas/controller/home_persona.php"
vistas: ["frontend/personas/view/home_persona.phtml"]
fragmentos_frontend: ["frontend/dossiers/controller/dossiers_ver.php", "frontend/dossiers/controller/lista_dossiers.php", "frontend/personas/controller/home_persona.php", "frontend/personas/controller/personas_editar.php"]
endpoints: ["/src/personas/home_persona_data"]
capacidades: ["personas.home_persona.gestionar"]
campos: ["post.id_nom", "post.id_tabla", "post.obj_pau", "post.sel"]
acciones: ["fnjs_update_div"]
estado_revision: "generado"
---

# Home Persona

Pantalla de cabecera de una persona (datos basicos + acceso a dossiers y ficha).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/personas/controller/home_persona.php`

## Vistas Relacionadas

- `frontend/personas/view/home_persona.phtml`

## Fragmentos Frontend Relacionados

- `frontend/dossiers/controller/dossiers_ver.php`
- `frontend/dossiers/controller/lista_dossiers.php`
- `frontend/personas/controller/home_persona.php`
- `frontend/personas/controller/personas_editar.php`

## Endpoints Usados

- `/src/personas/home_persona_data`

## Capacidades Relacionadas

- `personas.home_persona.gestionar`

## Campos Detectados

- `post.id_nom`
- `post.id_tabla`
- `post.obj_pau`
- `post.sel`

## Acciones Detectadas

- `fnjs_update_div`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
