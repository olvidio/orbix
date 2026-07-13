---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Profesores Desplegable"
flujo: "actividadestudios.profesores_desplegable.gestionar.flujo"
preguntas: ["Como obtener datos en Profesores Desplegable?"]
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.form_asignaturas_de_una_actividad"]
endpoints: ["/src/actividadestudios/profesores_desplegable_data"]
source: "docs/catalogo/actividadestudios/flujos/profesores_desplegable.md"
estado_revision: "generado"
---

# Ayuda IA - Profesores Desplegable

Usa este documento para responder preguntas de usuario sobre como trabajar con `Profesores Desplegable`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Profesores Desplegable?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. En el formulario de asignatura impartida, cambiar la asignatura del desplegable.
2. Se dispara `fnjs_mas_profes('asignatura')` o reconstrucción del desplegable.
3. El sistema consulta `profesores_desplegable_data` con `id_activ`, `id_asignatura` y `salida`.
4. Se actualiza el desplegable de profesores en pantalla.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/profesores_desplegable_data`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.form_asignaturas_de_una_actividad`

## Objetivo

Al cambiar la asignatura o añadir un profesor en el formulario de asignatura impartida, el usuario obtiene la lista actualizada de profesores candidatos para esa asignatura en la actividad.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
