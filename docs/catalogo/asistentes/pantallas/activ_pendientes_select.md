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
estado_revision: "revisado"
---

# Activ Pendientes Select

Listado de personas de la delegación (y otras DL) sin asistencia propia a ca o crt en el curso elegido.

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

Pantalla revisada contra `frontend/asistentes/`.
## Ruta de menú

- **Legacy:** vsm/vest/dagd/dre > pendientes según `sactividad` y `tipo_personas`
- **Pills2:** ACTIVIDADES > Listados > Listado de personas sin ca/crt
