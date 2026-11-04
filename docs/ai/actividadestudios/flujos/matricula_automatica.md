---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Matricular a todos automáticamente"
flujo: "actividadestudios.matricula_automatica.gestionar.flujo"
preguntas: ["Como matricular a todos automáticamente en Matricular a todos automáticamente?"]
pantallas_principales: ["actividadestudios.pantalla.matricular"]
fragmentos: ["actividadestudios.pantalla.matricular"]
endpoints: ["/src/actividadestudios/matricula_automatica"]
source: "docs/catalogo/actividadestudios/flujos/matricula_automatica.md"
estado_revision: "generado"
---

# Ayuda IA - Matricular a todos automáticamente

Usa este documento para responder preguntas de usuario sobre como trabajar con `Matricular a todos automáticamente`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como matricular a todos automáticamente en Matricular a todos automáticamente?

## Donde Entrar

- Matricular (`actividadestudios.pantalla.matricular`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Matricular a todos automáticamente

1. Al pulsar la opción de menú sale primero un aviso: «Se van a matricular automáticamente todas las personas. Si el plan de estudios de una persona no está confirmado, se borrarán sus matrículas anteriores. ¿Quiere continuar?». Con **volver** no se hace nada; con **continuar** se ejecuta la matriculación para todas las personas, sin más confirmación.
2. Para cada persona con ca o cv asignado, el sistema recalcula sus asignaturas (respetando las ya aprobadas y los topes de opcionales) y crea las matrículas; si el plan no está confirmado, borra antes las matrículas previas.
3. La pantalla muestra un mensaje por cada persona con la que no se hizo nada, por ejemplo «no se ha hecho nada con Sánchez Andreu, Enrique: no tiene asignado ca».

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/matricula_automatica`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.matricular`
- `actividadestudios.pantalla.matricular`

## Objetivo

Matricular automáticamente en las asignaturas del ca o la cv a todas las personas que tienen actividad de estudios asignada.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
