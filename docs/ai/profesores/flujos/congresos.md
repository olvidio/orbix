---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "profesores"
titulo: "Consultar la asistencia a congresos"
flujo: "profesores.congresos.gestionar.flujo"
preguntas: ["Como consultar la asistencia a congresos en Consultar la asistencia a congresos?"]
pantallas_principales: ["profesores.pantalla.congresos"]
fragmentos: []
endpoints: ["/src/profesores/congresos"]
source: "docs/catalogo/profesores/flujos/congresos.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar la asistencia a congresos

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar la asistencia a congresos`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar la asistencia a congresos en Consultar la asistencia a congresos?

## Donde Entrar

- Asistencia a congresos (`profesores.pantalla.congresos`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar la asistencia a congresos

1. Entrar en **asitencia a congresos**.
2. Se muestra una tabla con «apellidos, nombre», «tipo» (claustro, reunión, congreso, cv…), «lugar», «inicio», «fin» y «organiza».
3. Arriba a la derecha de la tabla hay dos iconos (lupa y guardar) para buscar en ella y exportarla.

Referencias tecnicas para verificar la respuesta:
- `/src/profesores/congresos`

## Pantallas Y Fragmentos Relacionados

- `profesores.pantalla.congresos`

## Objetivo

Consultar la asistencia de los profesores a congresos, claustros, reuniones y otros eventos.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
