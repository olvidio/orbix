---
id: "configuracion.pantalla.modulos_select"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "configuracion"
nombre: "Modulos Select"
controller: "frontend/configuracion/controller/modulos_select.php"
vistas: ["frontend/configuracion/view/modulos_select.phtml"]
fragmentos_frontend: ["frontend/configuracion/controller/modulos_form.php", "frontend/configuracion/controller/modulos_select.php", "frontend/configuracion/controller/modulos_update.php"]
endpoints: ["/src/configuracion/modulos_select_data"]
capacidades: ["configuracion.modulos_select.gestionar"]
campos: ["html.mod", "html.refresh"]
acciones: ["fnjs_actualizar", "fnjs_eliminar", "fnjs_enviar_formulario", "fnjs_modificar", "fnjs_nuevo", "fnjs_solo_uno"]
estado_revision: "generado"
---

# Modulos Select

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/configuracion/controller/modulos_select.php`

## Vistas Relacionadas

- `frontend/configuracion/view/modulos_select.phtml`

## Fragmentos Frontend Relacionados

- `frontend/configuracion/controller/modulos_form.php`
- `frontend/configuracion/controller/modulos_select.php`
- `frontend/configuracion/controller/modulos_update.php`

## Endpoints Usados

- `/src/configuracion/modulos_select_data`

## Capacidades Relacionadas

- `configuracion.modulos_select.gestionar`

## Campos Detectados

- `html.mod`
- `html.refresh`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_eliminar`
- `fnjs_enviar_formulario`
- `fnjs_modificar`
- `fnjs_nuevo`
- `fnjs_solo_uno`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
