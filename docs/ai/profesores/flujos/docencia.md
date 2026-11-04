---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "profesores"
titulo: "Consultar la docencia"
flujo: "profesores.docencia.gestionar.flujo"
preguntas: ["Como consultar la docencia en Consultar la docencia?"]
pantallas_principales: ["profesores.pantalla.docencia"]
fragmentos: []
endpoints: ["/src/profesores/docencia"]
source: "docs/catalogo/profesores/flujos/docencia.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar la docencia

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar la docencia`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar la docencia en Consultar la docencia?

## Donde Entrar

- Ver docencia (`profesores.pantalla.docencia`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar la docencia

1. Entrar en **ver docencia**.
2. Se muestra una tabla con «apellidos, nombre», «inicio curso», «asignatura», «modo» (por ejemplo «sem. invierno» o «ca/cv») y «acta».
3. Arriba a la derecha de la tabla hay dos iconos (lupa y guardar) para buscar en ella y exportarla.

Referencias tecnicas para verificar la respuesta:
- `/src/profesores/docencia`

## Pantallas Y Fragmentos Relacionados

- `profesores.pantalla.docencia`

## Objetivo

Consultar la docencia impartida por los profesores: curso, asignatura, modo y acta.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
