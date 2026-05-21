---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "pasarela"
titulo: "Activacion Ajax"
pantalla: "pasarela.pantalla.activacion_ajax"
preguntas: ["Que se puede hacer en Activacion Ajax?", "Que campos tiene Activacion Ajax?", "Que acciones hay en Activacion Ajax?"]
capacidades: ["pasarela.activacion.gestionar", "pasarela.activacion_default.gestionar", "pasarela.activacion_excepcion.gestionar", "pasarela.tipo_activ_txt.gestionar"]
endpoints: ["/src/pasarela/activacion_default_data", "/src/pasarela/activacion_default_guardar", "/src/pasarela/activacion_excepcion_eliminar", "/src/pasarela/activacion_excepcion_guardar", "/src/pasarela/activacion_lista", "/src/pasarela/tipo_activ_txt_data"]
source: "docs/catalogo/pasarela/pantallas/activacion_ajax.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Activacion Ajax

## Resumen

Dispatcher AJAX para el parámetro `fecha_activacion`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

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

## Capacidades Relacionadas

- `pasarela.activacion.gestionar`
- `pasarela.activacion_default.gestionar`
- `pasarela.activacion_excepcion.gestionar`
- `pasarela.tipo_activ_txt.gestionar`

## Endpoints Relacionados

- `/src/pasarela/activacion_default_data`
- `/src/pasarela/activacion_default_guardar`
- `/src/pasarela/activacion_excepcion_eliminar`
- `/src/pasarela/activacion_excepcion_guardar`
- `/src/pasarela/activacion_lista`
- `/src/pasarela/tipo_activ_txt_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
