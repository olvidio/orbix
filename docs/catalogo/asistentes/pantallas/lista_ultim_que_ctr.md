---
id: "asistentes.pantalla.lista_ultim_que_ctr"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "asistentes"
nombre: "Lista Ultim Que Ctr"
controller: "frontend/asistentes/controller/lista_ultim_que_ctr.php"
vistas: ["frontend/asistentes/view/lista_ultim_que_ctr.phtml"]
fragmentos_frontend: []
endpoints: ["/src/asistentes/lista_ultim_que_ctr_data"]
capacidades: ["asistentes.lista_ultim_que_ctr.gestionar"]
campos: []
acciones: ["fnjs_enviar_formulario"]
estado_revision: "revisado"
---

# Lista Ultim Que Ctr

Selector de centro antes del informe de última asistencia (personas s sin crt/cv reciente).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/asistentes/controller/lista_ultim_que_ctr.php`

## Vistas Relacionadas

- `frontend/asistentes/view/lista_ultim_que_ctr.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/asistentes/lista_ultim_que_ctr_data`

## Capacidades Relacionadas

- `asistentes.lista_ultim_que_ctr.gestionar`

## Campos Detectados

No se han detectado campos de formulario.

## Acciones Detectadas

- `fnjs_enviar_formulario`

## Manual De Usuario

Pantalla revisada contra `frontend/asistentes/`.
## Ruta de menú

- **Legacy:** vsg > crt/cv > informes de seguimiento
- **Pills2:** vsg > crt/cv > s que no han ido / corresponde ir
