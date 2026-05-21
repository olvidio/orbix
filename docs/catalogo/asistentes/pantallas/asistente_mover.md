---
id: "asistentes.pantalla.asistente_mover"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "asistentes"
nombre: "Asistente Mover"
controller: "frontend/asistentes/controller/asistente_mover.php"
vistas: ["frontend/asistentes/view/asistente_mover.phtml"]
fragmentos_frontend: []
endpoints: ["/src/asistentes/asistente_mover_data"]
capacidades: ["asistentes.asistente_mover.gestionar"]
campos: ["html.guardar", "html.observ"]
acciones: ["fnjs_mover_cerrar", "fnjs_mover_guardar"]
estado_revision: "generado"
---

# Asistente Mover

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/asistentes/controller/asistente_mover.php`

## Vistas Relacionadas

- `frontend/asistentes/view/asistente_mover.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/asistentes/asistente_mover_data`

## Capacidades Relacionadas

- `asistentes.asistente_mover.gestionar`

## Campos Detectados

- `html.guardar`
- `html.observ`

## Acciones Detectadas

- `fnjs_mover_cerrar`
- `fnjs_mover_guardar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
