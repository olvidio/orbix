---
id: "notas.pantalla.asignaturas_pendientes"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "notas"
nombre: "Asignaturas Pendientes"
controller: "frontend/notas/controller/asignaturas_pendientes.php"
vistas: []
fragmentos_frontend: ["frontend/notas/controller/asignaturas_pendientes.php"]
endpoints: ["/src/notas/asignaturas_pendientes_data"]
capacidades: ["notas.asignaturas_pendientes.gestionar"]
campos: ["form.dl", "post.dl"]
acciones: ["fnjs_left_side_hide"]
estado_revision: "generado"
---

# Asignaturas Pendientes

Cuadro "alumnos x asignaturas": genera una tabla con las asignaturas pendientes de todos los alumnos, filtrando por delegacion (`ambito = dl`) o por las delegaciones seleccionadas de la region stgr (`ambito = rstgr`).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/notas/controller/asignaturas_pendientes.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/notas/controller/asignaturas_pendientes.php`

## Endpoints Usados

- `/src/notas/asignaturas_pendientes_data`

## Capacidades Relacionadas

- `notas.asignaturas_pendientes.gestionar`

## Campos Detectados

- `form.dl`
- `post.dl`

## Acciones Detectadas

- `fnjs_left_side_hide`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
