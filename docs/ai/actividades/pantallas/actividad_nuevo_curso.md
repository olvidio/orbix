---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividades"
titulo: "Actividad Nuevo Curso"
pantalla: "actividades.pantalla.actividad_nuevo_curso"
preguntas: ["Que se puede hacer en Actividad Nuevo Curso?", "Que campos tiene Actividad Nuevo Curso?", "Que acciones hay en Actividad Nuevo Curso?"]
capacidades: ["actividades.actividad_nuevo_curso_ejecutar.gestionar"]
endpoints: ["/src/actividades/actividad_nuevo_curso_ejecutar"]
source: "docs/catalogo/actividades/pantallas/actividad_nuevo_curso.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Actividad Nuevo Curso

## Resumen

Pantalla que crea las actividades para el nuevo curso, copiando las del curso de referencia.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.year`
- `form.year_ref`
- `html.ver_lista`
- `html.year`
- `html.year_ref`
- `post.ok`
- `post.ver_lista`
- `post.year`
- `post.year_ref`

## Acciones Detectadas

- `fnjs_enviar_formulario`

## Capacidades Relacionadas

- `actividades.actividad_nuevo_curso_ejecutar.gestionar`

## Endpoints Relacionados

- `/src/actividades/actividad_nuevo_curso_ejecutar`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
