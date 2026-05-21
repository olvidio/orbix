---
id: "actividadplazas.pantalla.incorporar_peticion"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "actividadplazas"
nombre: "Incorporar Peticion"
controller: "frontend/actividadplazas/controller/incorporar_peticion.php"
vistas: ["frontend/actividadplazas/view/incorporar_peticion.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividadplazas/peticiones_incorporar"]
capacidades: ["actividadplazas.peticiones_incorporar.gestionar"]
campos: ["form.sactividad", "form.sasistentes", "post.sactividad", "post.sasistentes"]
acciones: ["fnjs_incorporar_peticiones", "fnjs_left_side_hide"]
estado_revision: "generado"
---

# Incorporar Peticion

Pantalla que dispara la incorporacion de las primeras peticiones como asistencia (accion contra `/src/actividadplazas/peticiones_incorporar`).

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/actividadplazas/controller/incorporar_peticion.php`

## Vistas Relacionadas

- `frontend/actividadplazas/view/incorporar_peticion.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadplazas/peticiones_incorporar`

## Capacidades Relacionadas

- `actividadplazas.peticiones_incorporar.gestionar`

## Campos Detectados

- `form.sactividad`
- `form.sasistentes`
- `post.sactividad`
- `post.sasistentes`

## Acciones Detectadas

- `fnjs_incorporar_peticiones`
- `fnjs_left_side_hide`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
