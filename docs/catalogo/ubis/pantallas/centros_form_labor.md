---
id: "ubis.pantalla.centros_form_labor"
tipo: "pantalla_frontend"
subtipo: "modal"
modulo: "ubis"
nombre: "Centros Form Labor"
controller: "frontend/ubis/controller/centros_form_labor.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/ubis/centros_form_labor", "/src/ubis/centros_update"]
capacidades: ["ubis.centros.gestionar", "ubis.centros_form_labor.gestionar"]
campos: ["form.tipo_ctr", "form.tipo_labor", "get.id_ubi", "post.id_ubi"]
acciones: ["fnjs_cerrar", "fnjs_guardar"]
estado_revision: "revisado"
---

# Centros Form Labor

Formulario modal para editar tipo de centro y tipo de labor de un centro DL.

## Tipo

- Subtipo: `modal`


- Controller: `frontend/ubis/controller/centros_form_labor.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/ubis/centros_form_labor`
- `/src/ubis/centros_update`

## Capacidades Relacionadas

- `ubis.centros.gestionar`
- `ubis.centros_form_labor.gestionar`

## Campos Detectados

- `form.tipo_ctr`
- `form.tipo_labor`
- `get.id_ubi`
- `post.id_ubi`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
