---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "notas"
titulo: "Acta Ver"
pantalla: "notas.pantalla.acta_ver"
preguntas: ["Que se puede hacer en Acta Ver?", "Que campos tiene Acta Ver?", "Que acciones hay en Acta Ver?"]
capacidades: ["notas.acta.gestionar", "notas.acta_modificar.gestionar", "notas.acta_ver.gestionar", "notas.asignaturas_search.gestionar", "notas.examinadores_search.gestionar"]
endpoints: ["/src/notas/acta_modificar", "/src/notas/acta_nueva", "/src/notas/acta_ver_form_data", "/src/notas/asignaturas_search", "/src/notas/examinadores_search"]
source: "docs/catalogo/notas/pantallas/acta_ver.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Acta Ver

## Resumen

Esta página muestra un formulario para modificar los datos de un acta.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.acta_pdf`
- `form.search`
- `html.acta`
- `html.acta_pdf`
- `html.examinadores[]`
- `html.id_asignatura`
- `html.refresh`

## Acciones Detectadas

- `fnjs_add_examinador`
- `fnjs_autocomplete_exam`
- `fnjs_cmb_acta`
- `fnjs_eliminar_pdf`
- `fnjs_enviar_formulario`
- `fnjs_guardar_acta`
- `fnjs_nueva_convocatoria`
- `fnjs_upload_pdf`

## Capacidades Relacionadas

- `notas.acta.gestionar`
- `notas.acta_modificar.gestionar`
- `notas.acta_ver.gestionar`
- `notas.asignaturas_search.gestionar`
- `notas.examinadores_search.gestionar`

## Endpoints Relacionados

- `/src/notas/acta_modificar`
- `/src/notas/acta_nueva`
- `/src/notas/acta_ver_form_data`
- `/src/notas/asignaturas_search`
- `/src/notas/examinadores_search`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
