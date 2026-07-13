---
id: "ubis.pantalla.centros_form_num"
tipo: "pantalla_frontend"
subtipo: "modal"
modulo: "ubis"
nombre: "Centros Form Num"
controller: "frontend/ubis/controller/centros_form_num.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/ubis/centros_form_num", "/src/ubis/centros_update"]
capacidades: ["ubis.centros.gestionar", "ubis.centros_form_num.gestionar"]
campos: ["form.n_buzon", "form.num_cartas", "form.num_pi", "get.id_ubi", "post.id_ubi"]
acciones: ["fnjs_cerrar", "fnjs_guardar"]
estado_revision: "revisado"
---

# Centros Form Num

Formulario modal para editar buzón, pi y cartas de un centro DL.

## Tipo

- Subtipo: `modal`


- Controller: `frontend/ubis/controller/centros_form_num.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/ubis/centros_form_num`
- `/src/ubis/centros_update`

## Capacidades Relacionadas

- `ubis.centros.gestionar`
- `ubis.centros_form_num.gestionar`

## Campos Detectados

- `form.n_buzon`
- `form.num_cartas`
- `form.num_pi`
- `get.id_ubi`
- `post.id_ubi`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
