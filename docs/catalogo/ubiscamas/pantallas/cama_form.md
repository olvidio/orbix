---
id: "ubiscamas.pantalla.cama_form"
tipo: "pantalla_frontend"
subtipo: "modal"
modulo: "ubiscamas"
nombre: "Cama Form"
controller: "frontend/ubiscamas/controller/cama_form.php"
vistas: ["frontend/ubiscamas/view/cama_form.phtml"]
fragmentos_frontend: []
endpoints: ["/src/ubiscamas/cama_form_data"]
capacidades: ["ubiscamas.cama.gestionar"]
campos: ["html.descripcion", "html.larga", "html.vip"]
acciones: ["fnjs_cancelar", "fnjs_guardar"]
estado_revision: "revisado"
---

# Cama Form

Formulario modal para editar descripción, larga y VIP de una cama.

## Tipo

- Subtipo: `modal`

- Controller: `frontend/ubiscamas/controller/cama_form.php`

## Vistas Relacionadas

- `frontend/ubiscamas/view/cama_form.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/ubiscamas/cama_form_data`

## Capacidades Relacionadas

- `ubiscamas.cama.gestionar`

## Campos Detectados

- `html.descripcion`
- `html.larga`
- `html.vip`

## Acciones Detectadas

- `fnjs_cancelar`
- `fnjs_guardar`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
