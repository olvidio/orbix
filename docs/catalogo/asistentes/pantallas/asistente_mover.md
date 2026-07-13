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
estado_revision: "revisado"
---

# Asistente Mover

Modal para mover un asistente a otra actividad del mismo tipo en el curso.

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

Pantalla revisada contra `frontend/asistentes/`.
## Ruta de menú

- sin entrada de menú en el índice (acceso desde dossier actividad/persona, `actividad_que` o navegación embebida).
