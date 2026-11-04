---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Elegir el profesor de una asignatura"
flujo: "actividadestudios.profesores_desplegable.gestionar.flujo"
preguntas: ["Como elegir el profesor de una asignatura en Elegir el profesor de una asignatura?"]
pantallas_principales: ["actividadestudios.pantalla.form_asignaturas_de_una_actividad"]
fragmentos: ["actividadestudios.pantalla.form_asignaturas_de_una_actividad"]
endpoints: ["/src/actividadestudios/profesores_desplegable_data"]
source: "docs/catalogo/actividadestudios/flujos/profesores_desplegable.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir el profesor de una asignatura

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir el profesor de una asignatura`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir el profesor de una asignatura en Elegir el profesor de una asignatura?

## Donde Entrar

- Form Asignaturas De Una Actividad (`actividadestudios.pantalla.form_asignaturas_de_una_actividad`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir el profesor de una asignatura

1. En el formulario de la asignatura de la actividad, elegir o cambiar la asignatura.
2. El desplegable de profesores se actualiza con los que pueden impartirla.
3. Para añadir otro profesor, pulsar **más** y elegirlo en el nuevo desplegable.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/profesores_desplegable_data`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.form_asignaturas_de_una_actividad`
- `actividadestudios.pantalla.form_asignaturas_de_una_actividad`

## Objetivo

Al cambiar la asignatura, o al añadir otro profesor en el formulario de una asignatura impartida, obtener la lista actualizada de profesores posibles para esa asignatura.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
