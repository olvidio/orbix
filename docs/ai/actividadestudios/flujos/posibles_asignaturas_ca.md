---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Ver las posibles asignaturas de una actividad"
flujo: "actividadestudios.posibles_asignaturas_ca.gestionar.flujo"
preguntas: ["Como ver las posibles asignaturas de una actividad en Ver las posibles asignaturas de una actividad?"]
pantallas_principales: ["actividadestudios.pantalla.posibles_asignaturas_ca"]
fragmentos: ["actividadestudios.pantalla.posibles_asignaturas_ca"]
endpoints: ["/src/actividadestudios/posibles_asignaturas_ca_data"]
source: "docs/catalogo/actividadestudios/flujos/posibles_asignaturas_ca.md"
estado_revision: "generado"
---

# Ayuda IA - Ver las posibles asignaturas de una actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver las posibles asignaturas de una actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver las posibles asignaturas de una actividad en Ver las posibles asignaturas de una actividad?

## Donde Entrar

- Posibles Asignaturas Ca (`actividadestudios.pantalla.posibles_asignaturas_ca`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver las posibles asignaturas de una actividad

1. Marcar una actividad de tipo ca y pulsar **posibles asignaturas**.
2. Se muestra una tabla «asignatura / posibles alumnos / nombres» y, debajo, los «alumnos a los que faltan 4 o menos asignaturas para terminar el cuadrienio».

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/posibles_asignaturas_ca_data`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.posibles_asignaturas_ca`
- `actividadestudios.pantalla.posibles_asignaturas_ca`

## Objetivo

Ver qué asignaturas podrían cursar los asistentes de la actividad.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
