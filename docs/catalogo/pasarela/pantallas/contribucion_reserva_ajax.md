---
id: "pasarela.pantalla.contribucion_reserva_ajax"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "pasarela"
nombre: "Contribucion Reserva Ajax"
controller: "frontend/pasarela/controller/contribucion_reserva_ajax.php"
vistas: []
fragmentos_frontend: ["frontend/pasarela/controller/contribucion_reserva_ajax.php"]
endpoints: ["/src/pasarela/contribucion_reserva_default_data", "/src/pasarela/contribucion_reserva_default_guardar", "/src/pasarela/contribucion_reserva_excepcion_eliminar", "/src/pasarela/contribucion_reserva_excepcion_guardar", "/src/pasarela/contribucion_reserva_lista", "/src/pasarela/tipo_activ_txt_data"]
capacidades: ["pasarela.contribucion_reserva.gestionar", "pasarela.contribucion_reserva_default.gestionar", "pasarela.contribucion_reserva_excepcion.gestionar", "pasarela.tipo_activ_txt.gestionar"]
campos: ["form.contribucion", "form.default", "form.iactividad_val", "form.iasistentes_val", "form.id_tipo_activ", "form.inom_tipo_val", "form.isfsv_val", "post.contribucion", "post.default", "post.id_tipo_activ", "post.que", "post.sactividad", "post.sasistentes", "post.snom_tipo"]
acciones: ["fnjs_modificar", "fnjs_modificar_default"]
estado_revision: "generado"
---

# Contribucion Reserva Ajax

Dispatcher AJAX para el parámetro `contribucion_reserva`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/pasarela/controller/contribucion_reserva_ajax.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/pasarela/controller/contribucion_reserva_ajax.php`

## Endpoints Usados

- `/src/pasarela/contribucion_reserva_default_data`
- `/src/pasarela/contribucion_reserva_default_guardar`
- `/src/pasarela/contribucion_reserva_excepcion_eliminar`
- `/src/pasarela/contribucion_reserva_excepcion_guardar`
- `/src/pasarela/contribucion_reserva_lista`
- `/src/pasarela/tipo_activ_txt_data`

## Capacidades Relacionadas

- `pasarela.contribucion_reserva.gestionar`
- `pasarela.contribucion_reserva_default.gestionar`
- `pasarela.contribucion_reserva_excepcion.gestionar`
- `pasarela.tipo_activ_txt.gestionar`

## Campos Detectados

- `form.contribucion`
- `form.default`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.id_tipo_activ`
- `form.inom_tipo_val`
- `form.isfsv_val`
- `post.contribucion`
- `post.default`
- `post.id_tipo_activ`
- `post.que`
- `post.sactividad`
- `post.sasistentes`
- `post.snom_tipo`

## Acciones Detectadas

- `fnjs_modificar`
- `fnjs_modificar_default`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
