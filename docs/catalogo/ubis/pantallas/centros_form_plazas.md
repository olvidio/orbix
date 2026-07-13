---
id: "ubis.pantalla.centros_form_plazas"
tipo: "pantalla_frontend"
subtipo: "modal"
modulo: "ubis"
nombre: "Centros Form Plazas"
controller: "frontend/ubis/controller/centros_form_plazas.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/ubis/centros_form_plazas", "/src/ubis/centros_update"]
capacidades: ["ubis.centros.gestionar", "ubis.centros_form_plazas.gestionar"]
campos: ["form.num_habit_indiv", "form.plazas", "get.id_ubi", "post.id_ubi"]
acciones: ["fnjs_cerrar", "fnjs_guardar"]
estado_revision: "revisado"
---

# Centros Form Plazas

Formulario modal para editar plazas, habitaciones y sede de un centro DL.

## Tipo

- Subtipo: `modal`


- Controller: `frontend/ubis/controller/centros_form_plazas.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/ubis/centros_form_plazas`
- `/src/ubis/centros_update`

## Capacidades Relacionadas

- `ubis.centros.gestionar`
- `ubis.centros_form_plazas.gestionar`

## Campos Detectados

- `form.num_habit_indiv`
- `form.plazas`
- `get.id_ubi`
- `post.id_ubi`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
