---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "profesores"
titulo: "Profesor para asignatura"
pantalla: "profesores.pantalla.profesor_asignatura_que"
preguntas: ["Que se puede hacer en Profesor para asignatura?", "Que campos tiene Profesor para asignatura?", "Que acciones hay en Profesor para asignatura?"]
capacidades: ["profesores.profesor_asignatura_que.gestionar"]
endpoints: ["/src/profesores/profesor_asignatura_que", "/src/profesores/profesor_asignatura_ajax"]
source: "docs/catalogo/profesores/pantallas/profesor_asignatura_que.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Profesor para asignatura

## Resumen

Formulario con desplegable de asignaturas; al elegir una, carga por AJAX la tabla de profesores habilitados (departamento y ampliación) con contacto y docencia previa.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_asignatura`

## Acciones Detectadas

- `fnjs_left_side_hide`
- `fnjs_profes`

## Capacidades Relacionadas

- `profesores.profesor_asignatura_que.gestionar`

## Endpoints Relacionados

- `/src/profesores/profesor_asignatura_que`
- `/src/profesores/profesor_asignatura_ajax`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
