---
id: "pasarela.pantalla.activacion_ajax"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "pasarela"
nombre: "Activacion Ajax"
controller: "frontend/pasarela/controller/activacion_ajax.php"
vistas: []
fragmentos_frontend: ["frontend/pasarela/controller/activacion_ajax.php"]
endpoints: ["/src/pasarela/activacion_default_data", "/src/pasarela/activacion_default_guardar", "/src/pasarela/activacion_excepcion_eliminar", "/src/pasarela/activacion_excepcion_guardar", "/src/pasarela/activacion_lista", "/src/pasarela/tipo_activ_txt_data"]
capacidades: ["pasarela.activacion.gestionar", "pasarela.activacion_default.gestionar", "pasarela.activacion_excepcion.gestionar", "pasarela.tipo_activ_txt.gestionar"]
campos: ["form.activacion", "form.default", "form.extendida", "form.iactividad_val", "form.iasistentes_val", "form.id_tipo_activ", "form.inom_tipo_val", "form.isfsv_val", "post.activacion", "post.default", "post.id_tipo_activ", "post.que", "post.sactividad", "post.sasistentes", "post.snom_tipo"]
acciones: ["fnjs_modificar_activacion", "fnjs_modificar_activacion_default"]
estado_revision: "generado"
---

# Activacion Ajax

Dispatcher AJAX para el parámetro `fecha_activacion`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/pasarela/controller/activacion_ajax.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/pasarela/controller/activacion_ajax.php`

## Endpoints Usados

- `/src/pasarela/activacion_default_data`
- `/src/pasarela/activacion_default_guardar`
- `/src/pasarela/activacion_excepcion_eliminar`
- `/src/pasarela/activacion_excepcion_guardar`
- `/src/pasarela/activacion_lista`
- `/src/pasarela/tipo_activ_txt_data`

## Capacidades Relacionadas

- `pasarela.activacion.gestionar`
- `pasarela.activacion_default.gestionar`
- `pasarela.activacion_excepcion.gestionar`
- `pasarela.tipo_activ_txt.gestionar`

## Campos Detectados

- `form.activacion`
- `form.default`
- `form.extendida`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.id_tipo_activ`
- `form.inom_tipo_val`
- `form.isfsv_val`
- `post.activacion`
- `post.default`
- `post.id_tipo_activ`
- `post.que`
- `post.sactividad`
- `post.sasistentes`
- `post.snom_tipo`

## Acciones Detectadas

- `fnjs_modificar_activacion`
- `fnjs_modificar_activacion_default`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
