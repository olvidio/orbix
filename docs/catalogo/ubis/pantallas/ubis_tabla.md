---
id: "ubis.pantalla.ubis_tabla"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "ubis"
nombre: "Ubis Tabla"
controller: "frontend/ubis/controller/ubis_tabla.php"
vistas: ["frontend/ubis/view/ubis_tabla.phtml"]
fragmentos_frontend: ["frontend/ubis/controller/home_ubis.php", "frontend/ubis/controller/trasladar_ubis.php"]
endpoints: ["/src/ubis/ubis_tabla_data"]
capacidades: ["ubis.ubis_tabla.gestionar"]
campos: ["form.sel", "html.b_mas", "post.stack"]
acciones: ["fnjs_borrar", "fnjs_enviar_formulario", "fnjs_modificar", "fnjs_solo_uno", "fnjs_trasladar", "fnjs_update_div"]
estado_revision: "generado"
---

# Ubis Tabla

Esta página muestra una tabla con los ubis seleccionados.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/ubis/controller/ubis_tabla.php`

## Vistas Relacionadas

- `frontend/ubis/view/ubis_tabla.phtml`

## Fragmentos Frontend Relacionados

- `frontend/ubis/controller/home_ubis.php`
- `frontend/ubis/controller/trasladar_ubis.php`

## Endpoints Usados

- `/src/ubis/ubis_tabla_data`

## Capacidades Relacionadas

- `ubis.ubis_tabla.gestionar`

## Campos Detectados

- `form.sel`
- `html.b_mas`
- `post.stack`

## Acciones Detectadas

- `fnjs_borrar`
- `fnjs_enviar_formulario`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_trasladar`
- `fnjs_update_div`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
