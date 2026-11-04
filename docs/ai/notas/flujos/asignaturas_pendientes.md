---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Consultar el cuadro alumnos-asignaturas"
flujo: "notas.asignaturas_pendientes.gestionar.flujo"
preguntas: ["Como consultar el cuadro alumnos-asignaturas en Consultar el cuadro alumnos-asignaturas?"]
pantallas_principales: ["notas.pantalla.asignaturas_pendientes"]
fragmentos: ["notas.pantalla.asignaturas_pendientes"]
endpoints: ["/src/notas/asignaturas_pendientes_data"]
source: "docs/catalogo/notas/flujos/asignaturas_pendientes.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar el cuadro alumnos-asignaturas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar el cuadro alumnos-asignaturas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar el cuadro alumnos-asignaturas en Consultar el cuadro alumnos-asignaturas?

## Donde Entrar

- Asignaturas Pendientes (`notas.pantalla.asignaturas_pendientes`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar el cuadro alumnos-asignaturas

1. Entrar en **tabla alumnos-asignaturas**.
2. Se muestra una fila por alumno y una columna por asignatura, con lo que le falta.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/asignaturas_pendientes_data`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.asignaturas_pendientes`
- `notas.pantalla.asignaturas_pendientes`

## Objetivo

Ver el cuadro de alumnos y asignaturas pendientes de cada uno.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
