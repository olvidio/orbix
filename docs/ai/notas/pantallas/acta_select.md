---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "notas"
titulo: "Acta Select"
pantalla: "notas.pantalla.acta_select"
preguntas: ["Que se puede hacer en Acta Select?", "Que campos tiene Acta Select?", "Que acciones hay en Acta Select?"]
capacidades: ["notas.acta.gestionar", "notas.acta_select.gestionar"]
endpoints: ["/src/notas/acta_eliminar", "/src/notas/acta_select_data"]
source: "docs/catalogo/notas/pantallas/acta_select.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Acta Select

## Resumen

Esta página muestra una tabla con las actas.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.acta`
- `form.mod`
- `form.sel`
- `html.acta`
- `html.btn_ok`
- `html.mod`
- `html.refresh`
- `post.acta`
- `post.refresh`
- `post.stack`
- `post.titulo`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_descargar_pdf`
- `fnjs_eliminar`
- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_imprimir`
- `fnjs_left_side_hide`
- `fnjs_modificar`
- `fnjs_nuevo`
- `fnjs_solo_uno`

## Capacidades Relacionadas

- `notas.acta.gestionar`
- `notas.acta_select.gestionar`

## Endpoints Relacionados

- `/src/notas/acta_eliminar`
- `/src/notas/acta_select_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
