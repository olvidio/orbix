---
id: "pasarela.pantalla.nombre_ajax"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "pasarela"
nombre: "Nombre Ajax"
controller: "frontend/pasarela/controller/nombre_ajax.php"
vistas: []
fragmentos_frontend: ["frontend/pasarela/controller/nombre_ajax.php"]
endpoints: ["/src/pasarela/nombre_excepcion_eliminar", "/src/pasarela/nombre_excepcion_guardar", "/src/pasarela/nombre_lista", "/src/pasarela/tipo_activ_txt_data"]
capacidades: ["pasarela.nombre.gestionar", "pasarela.nombre_excepcion.gestionar", "pasarela.tipo_activ_txt.gestionar"]
campos: ["form.iactividad_val", "form.iasistentes_val", "form.id_tipo_activ", "form.inom_tipo_val", "form.isfsv_val", "form.nombre_actividad", "post.id_tipo_activ", "post.nombre_actividad", "post.que", "post.sactividad", "post.sasistentes", "post.snom_tipo"]
acciones: ["fnjs_modificar"]
estado_revision: "generado"
---

# Nombre Ajax

Dispatcher AJAX para el parámetro `nombre`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/pasarela/controller/nombre_ajax.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/pasarela/controller/nombre_ajax.php`

## Endpoints Usados

- `/src/pasarela/nombre_excepcion_eliminar`
- `/src/pasarela/nombre_excepcion_guardar`
- `/src/pasarela/nombre_lista`
- `/src/pasarela/tipo_activ_txt_data`

## Capacidades Relacionadas

- `pasarela.nombre.gestionar`
- `pasarela.nombre_excepcion.gestionar`
- `pasarela.tipo_activ_txt.gestionar`

## Campos Detectados

- `form.iactividad_val`
- `form.iasistentes_val`
- `form.id_tipo_activ`
- `form.inom_tipo_val`
- `form.isfsv_val`
- `form.nombre_actividad`
- `post.id_tipo_activ`
- `post.nombre_actividad`
- `post.que`
- `post.sactividad`
- `post.sasistentes`
- `post.snom_tipo`

## Acciones Detectadas

- `fnjs_modificar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
