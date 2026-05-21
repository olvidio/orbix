---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "pasarela"
titulo: "Nombre Ajax"
pantalla: "pasarela.pantalla.nombre_ajax"
preguntas: ["Que se puede hacer en Nombre Ajax?", "Que campos tiene Nombre Ajax?", "Que acciones hay en Nombre Ajax?"]
capacidades: ["pasarela.nombre.gestionar", "pasarela.nombre_excepcion.gestionar", "pasarela.tipo_activ_txt.gestionar"]
endpoints: ["/src/pasarela/nombre_excepcion_eliminar", "/src/pasarela/nombre_excepcion_guardar", "/src/pasarela/nombre_lista", "/src/pasarela/tipo_activ_txt_data"]
source: "docs/catalogo/pasarela/pantallas/nombre_ajax.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Nombre Ajax

## Resumen

Dispatcher AJAX para el parámetro `nombre`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

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

## Capacidades Relacionadas

- `pasarela.nombre.gestionar`
- `pasarela.nombre_excepcion.gestionar`
- `pasarela.tipo_activ_txt.gestionar`

## Endpoints Relacionados

- `/src/pasarela/nombre_excepcion_eliminar`
- `/src/pasarela/nombre_excepcion_guardar`
- `/src/pasarela/nombre_lista`
- `/src/pasarela/tipo_activ_txt_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
