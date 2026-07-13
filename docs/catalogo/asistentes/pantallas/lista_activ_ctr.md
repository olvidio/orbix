---
id: "asistentes.pantalla.lista_activ_ctr"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "asistentes"
nombre: "Lista Activ Ctr"
controller: "frontend/asistentes/controller/lista_activ_ctr.php"
vistas: ["frontend/asistentes/view/lista_activ_ctr.phtml"]
fragmentos_frontend: []
endpoints: ["/src/asistentes/lista_activ_ctr_data"]
capacidades: ["asistentes.lista_activ_ctr.gestionar"]
campos: []
acciones: []
estado_revision: "revisado"
---

# Lista Activ Ctr

Resultado del filtro `que_ctr_lista`: personas por centro con sus actividades asistidas en el periodo.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/asistentes/controller/lista_activ_ctr.php`

## Vistas Relacionadas

- `frontend/asistentes/view/lista_activ_ctr.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/asistentes/lista_activ_ctr_data`

## Capacidades Relacionadas

- `asistentes.lista_activ_ctr.gestionar`

## Campos Detectados

No se han detectado campos de formulario.

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pantalla revisada contra `frontend/asistentes/`.
## Ruta de menú

- **Legacy:** Destino del submit de que_ctr_lista (`lista=list_activ`)
- **Pills2:** ACTIVIDADES > Listados > Listado de asistentes ca/crt por ctr
