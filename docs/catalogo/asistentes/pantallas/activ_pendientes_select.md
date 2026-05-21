---
id: "asistentes.pantalla.activ_pendientes_select"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "asistentes"
nombre: "Activ Pendientes Select"
controller: "frontend/asistentes/controller/activ_pendientes_select.php"
vistas: ["frontend/asistentes/view/activ_pendientes.phtml"]
fragmentos_frontend: []
endpoints: ["/src/asistentes/activ_pendientes_select_data"]
capacidades: ["asistentes.activ_pendientes_select.gestionar"]
campos: ["html.any", "html.ok", "html.sactividad", "html.tipo_personas"]
acciones: ["fnjs_enviar", "fnjs_enviar_formulario"]
estado_revision: "generado"
---

# Activ Pendientes Select

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/asistentes/controller/activ_pendientes_select.php`

## Vistas Relacionadas

- `frontend/asistentes/view/activ_pendientes.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/asistentes/activ_pendientes_select_data`

## Capacidades Relacionadas

- `asistentes.activ_pendientes_select.gestionar`

## Campos Detectados

- `html.any`
- `html.ok`
- `html.sactividad`
- `html.tipo_personas`

## Acciones Detectadas

- `fnjs_enviar`
- `fnjs_enviar_formulario`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
