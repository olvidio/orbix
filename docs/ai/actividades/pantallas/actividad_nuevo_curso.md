---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividades"
titulo: "Generar actividades del nuevo curso"
pantalla: "actividades.pantalla.actividad_nuevo_curso"
preguntas: ["Que se puede hacer en Generar actividades del nuevo curso?", "Que campos tiene Generar actividades del nuevo curso?", "Que acciones hay en Generar actividades del nuevo curso?"]
capacidades: ["actividades.actividad_nuevo_curso_ejecutar.gestionar"]
endpoints: ["/src/actividades/actividad_nuevo_curso_ejecutar"]
source: "docs/catalogo/actividades/pantallas/actividad_nuevo_curso.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Generar actividades del nuevo curso

## Resumen

Herramienta de **fin/inicio de curso**: el usuario elige año destino (`year`) y año de referencia (`year_ref`); al confirmar, POST directo a `actividad_nuevo_curso_ejecutar`, que borra actividades en proyecto del nuevo curso, copia las del curso base, opcionalmente centros encargados (`actividadescentro`) y fases (`procesos`). Muestra avisos de duración (~3 min).

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
