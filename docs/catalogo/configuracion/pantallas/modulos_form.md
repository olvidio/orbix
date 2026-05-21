---
id: "configuracion.pantalla.modulos_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "configuracion"
nombre: "Modulos Form"
controller: "frontend/configuracion/controller/modulos_form.php"
vistas: ["frontend/configuracion/view/modulos_form.phtml"]
fragmentos_frontend: ["frontend/configuracion/controller/modulos_form.php", "frontend/configuracion/controller/modulos_update.php"]
endpoints: ["/src/configuracion/modulos_form_data"]
capacidades: ["configuracion.modulos.gestionar"]
campos: ["html.descripcion", "html.nom", "html.refresh", "html.sel_apps[]", "html.sel_mods[]", "post.refresh"]
acciones: ["fnjs_actualizar", "fnjs_cambio", "fnjs_enviar_formulario", "fnjs_guardar"]
estado_revision: "generado"
---

# Modulos Form

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/configuracion/controller/modulos_form.php`

## Vistas Relacionadas

- `frontend/configuracion/view/modulos_form.phtml`

## Fragmentos Frontend Relacionados

- `frontend/configuracion/controller/modulos_form.php`
- `frontend/configuracion/controller/modulos_update.php`

## Endpoints Usados

- `/src/configuracion/modulos_form_data`

## Capacidades Relacionadas

- `configuracion.modulos.gestionar`

## Campos Detectados

- `html.descripcion`
- `html.nom`
- `html.refresh`
- `html.sel_apps[]`
- `html.sel_mods[]`
- `post.refresh`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_cambio`
- `fnjs_enviar_formulario`
- `fnjs_guardar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
