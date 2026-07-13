---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "profesores"
titulo: "Ver docencia global"
flujo: "profesores.docencia.gestionar.flujo"
preguntas: ["Como consultar en Ver docencia global?"]
pantallas_principales: ["profesores.pantalla.docencia"]
fragmentos: []
endpoints: ["/src/profesores/docencia"]
source: "docs/catalogo/profesores/flujos/docencia.md"
estado_revision: "generado"
---

# Ayuda IA - Ver docencia global

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver docencia global`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar en Ver docencia global?

## Donde Entrar

- Ver docencia (`profesores.pantalla.docencia`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar

1. Abrir **ver docencia** desde el menú `stgr2`.
2. Revisar la tabla `tabla_docencia`.

Referencias tecnicas para verificar la respuesta:
- `/src/profesores/docencia`

## Pantallas Y Fragmentos Relacionados

- `profesores.pantalla.docencia`

## Objetivo

Revisar qué docencia consta registrada por profesor, curso, asignatura y acta.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
