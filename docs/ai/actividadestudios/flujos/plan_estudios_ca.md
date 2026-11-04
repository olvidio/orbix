---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Ver el plan de estudios de una actividad"
flujo: "actividadestudios.plan_estudios_ca.gestionar.flujo"
preguntas: ["Como ver el plan de estudios de una actividad en Ver el plan de estudios de una actividad?"]
pantallas_principales: ["actividadestudios.pantalla.plan_estudios_ca"]
fragmentos: ["actividadestudios.pantalla.plan_estudios_ca"]
endpoints: ["/src/actividadestudios/plan_estudios_ca_data"]
source: "docs/catalogo/actividadestudios/flujos/plan_estudios_ca.md"
estado_revision: "generado"
---

# Ayuda IA - Ver el plan de estudios de una actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver el plan de estudios de una actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver el plan de estudios de una actividad en Ver el plan de estudios de una actividad?

## Donde Entrar

- Plan Estudios Ca (`actividadestudios.pantalla.plan_estudios_ca`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver el plan de estudios de una actividad

1. Marcar una actividad de tipo ca y pulsar **plan estudios**.
2. Se muestra el título de la actividad con **Director de estudios** (para nombrarlo hay que ir al dossier de cargos de la actividad), **Profesores**, **Preceptores** y **Asistentes**.
3. Debajo, cada asistente con su plan; «???» indica que no hay dato.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/plan_estudios_ca_data`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.plan_estudios_ca`
- `actividadestudios.pantalla.plan_estudios_ca`

## Objetivo

Ver el plan de estudios de una actividad (director de estudios, profesores, preceptores y asistentes).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
