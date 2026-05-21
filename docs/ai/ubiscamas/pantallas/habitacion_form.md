---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "ubiscamas"
titulo: "Habitacion Form"
pantalla: "ubiscamas.pantalla.habitacion_form"
preguntas: ["Que se puede hacer en Habitacion Form?", "Que campos tiene Habitacion Form?", "Que acciones hay en Habitacion Form?"]
capacidades: ["ubiscamas.habitacion.gestionar"]
endpoints: ["/src/ubiscamas/habitacion_form_data"]
source: "docs/catalogo/ubiscamas/pantallas/habitacion_form.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Habitacion Form

## Resumen

Descripcion funcional pendiente de revisar.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `html.adaptada`
- `html.despacho`
- `html.new_camas_desc[${rowIdx}]`
- `html.new_camas_larga[${rowIdx}]`
- `html.new_camas_vip[${rowIdx}]`
- `html.nombre`
- `html.numero_camas`
- `html.numero_camas_vip`
- `html.observaciones`
- `html.orden`
- `html.planta`
- `html.refresh`
- `html.sillon`
- `html.tipoLavabo`
- `post.refresh`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_anadir_cama_dinamica`
- `fnjs_cancelar`
- `fnjs_editar_cama`
- `fnjs_eliminar_cama`
- `fnjs_enviar_formulario`
- `fnjs_guardar`
- `fnjs_nueva_cama`
- `fnjs_update_div`

## Capacidades Relacionadas

- `ubiscamas.habitacion.gestionar`

## Endpoints Relacionados

- `/src/ubiscamas/habitacion_form_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
