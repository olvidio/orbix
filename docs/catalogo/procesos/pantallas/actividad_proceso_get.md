---
id: "procesos.pantalla.actividad_proceso_get"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "procesos"
nombre: "Actividad Proceso Get"
controller: "frontend/procesos/controller/actividad_proceso_get.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/procesos/actividad_proceso_get"]
capacidades: ["procesos.actividad_proceso.gestionar"]
campos: ["html.b_guardar", "html.completado", "html.observ"]
acciones: ["fnjs_guardar"]
estado_revision: "generado"
---

# Actividad Proceso Get

Renderer frontend de la tabla de fases del proceso de una actividad.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/procesos/controller/actividad_proceso_get.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/procesos/actividad_proceso_get`

## Capacidades Relacionadas

- `procesos.actividad_proceso.gestionar`

## Campos Detectados

- `html.b_guardar`
- `html.completado`
- `html.observ`

## Acciones Detectadas

- `fnjs_guardar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
