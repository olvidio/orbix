---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividadestudios"
titulo: "Acta Notas"
pantalla: "actividadestudios.pantalla.acta_notas"
preguntas: ["Que se puede hacer en Acta Notas?", "Que campos tiene Acta Notas?", "Que acciones hay en Acta Notas?"]
capacidades: ["actividadestudios.acta_notas.gestionar", "actividadestudios.acta_notas_definitivas_grabar.gestionar", "actividadestudios.acta_notas_matricula.gestionar"]
endpoints: ["/src/actividadestudios/acta_notas_data", "/src/actividadestudios/acta_notas_definitivas_grabar", "/src/actividadestudios/acta_notas_matricula_guardar"]
source: "docs/catalogo/actividadestudios/pantallas/acta_notas.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Acta Notas

## Resumen

Pantalla del acta de notas de una asignatura concreta dentro de una actividad: incluye el formulario del acta (módulo `notas`) y, debajo, la tabla de alumnos matriculados con nota, preceptor y situación de acta. Sucesor de `apps/actividadestudios/controller/acta_notas.php`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.acta_nota`
- `form.form_preceptor`
- `form.id_nom`
- `form.nota_max`
- `form.nota_num`
- `html.form_preceptor[]`
- `html.id_nom[]`
- `html.que`
- `post.id_activ`
- `post.id_asignatura`
- `post.id_nivel`
- `post.id_pau`
- `post.opcional`
- `post.primary_key_s`
- `post.que`
- `post.refresh`
- `post.scroll_id`
- `post.sel`
- `post.stack`

## Acciones Detectadas

- `fnjs_enviar_formulario`
- `fnjs_guardar_nota`
- `fnjs_guardar_tessera`
- `fnjs_imprimir`
- `fnjs_nota`

## Capacidades Relacionadas

- `actividadestudios.acta_notas.gestionar`
- `actividadestudios.acta_notas_definitivas_grabar.gestionar`
- `actividadestudios.acta_notas_matricula.gestionar`

## Endpoints Relacionados

- `/src/actividadestudios/acta_notas_data`
- `/src/actividadestudios/acta_notas_definitivas_grabar`
- `/src/actividadestudios/acta_notas_matricula_guardar`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
