---
id: "ubis.pantalla.centros_form_num"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "ubis"
nombre: "Centros Form Num"
controller: "frontend/ubis/controller/centros_form_num.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/ubis/centros_form_num", "/src/ubis/centros_update"]
capacidades: ["ubis.centros.gestionar", "ubis.centros_form_num.gestionar"]
campos: ["form.n_buzon", "form.num_cartas", "form.num_pi", "get.id_ubi", "post.id_ubi"]
acciones: ["fnjs_cerrar", "fnjs_guardar"]
estado_revision: "generado"
---

# Centros Form Num

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
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

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
