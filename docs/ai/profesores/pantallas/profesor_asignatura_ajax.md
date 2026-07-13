---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "profesores"
titulo: "Tabla profesores por asignatura"
pantalla: "profesores.pantalla.profesor_asignatura_ajax"
preguntas: ["Que se puede hacer en Tabla profesores por asignatura?", "Que campos tiene Tabla profesores por asignatura?", "Que acciones hay en Tabla profesores por asignatura?"]
capacidades: ["profesores.profesor_asignatura_ajax.gestionar"]
endpoints: ["/src/profesores/profesor_asignatura_ajax"]
source: "docs/catalogo/profesores/pantallas/profesor_asignatura_ajax.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Tabla profesores por asignatura

## Resumen

Fragmento AJAX: devuelve HTML de tabla `Lista` con profesores del departamento y de ampliación para la asignatura indicada.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.id_asignatura`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `profesores.profesor_asignatura_ajax.gestionar`

## Endpoints Relacionados

- `/src/profesores/profesor_asignatura_ajax`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
