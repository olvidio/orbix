---
id: "asistentes.pantalla.que_ctr_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "asistentes"
nombre: "Que Ctr Lista"
controller: "frontend/asistentes/controller/que_ctr_lista.php"
vistas: ["frontend/asistentes/view/que_ctr_lista.phtml"]
fragmentos_frontend: []
endpoints: ["/src/asistentes/que_ctr_lista_data"]
capacidades: ["asistentes.que_ctr.gestionar"]
campos: ["html.btn_ok", "html.n_agd"]
acciones: ["fnjs_buscar", "fnjs_comprobar_fecha", "fnjs_enviar_formulario", "fnjs_otro"]
estado_revision: "revisado"
---

# Que Ctr Lista

Formulario intermedio: elige tipo de persona (n/agd/…), centro y periodo antes de abrir el listado por centros.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/asistentes/controller/que_ctr_lista.php`

## Vistas Relacionadas

- `frontend/asistentes/view/que_ctr_lista.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/asistentes/que_ctr_lista_data`

## Capacidades Relacionadas

- `asistentes.que_ctr.gestionar`

## Campos Detectados

- `html.btn_ok`
- `html.n_agd`

## Acciones Detectadas

- `fnjs_buscar`
- `fnjs_comprobar_fecha`
- `fnjs_enviar_formulario`
- `fnjs_otro`

## Manual De Usuario

Pantalla revisada contra `frontend/asistentes/`.
## Ruta de menú

- **Legacy:** Varias entradas según `lista`/`sactividad` (vsm/vest/dagd > crt/ca/cv > list por ctr; dre > personas > pendientes)
- **Pills2:** ACTIVIDADES > Listados > Listado de asistentes ca/crt por ctr, Mejores ca para n/agd, Listado de personas sin ca/crt
