---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "profesores"
titulo: "Consultar congresos"
flujo: "profesores.congresos.gestionar.flujo"
preguntas: ["Como consultar en Consultar congresos?"]
pantallas_principales: ["profesores.pantalla.congresos"]
fragmentos: []
endpoints: ["/src/profesores/congresos"]
source: "docs/catalogo/profesores/flujos/congresos.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar congresos

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar congresos`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar en Consultar congresos?

## Donde Entrar

- Asistencia a congresos (`profesores.pantalla.congresos`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar

1. Abrir **asistencia a congresos** desde el menú `stgr2`.
2. Revisar la tabla `tabla_congreso`.

Referencias tecnicas para verificar la respuesta:
- `/src/profesores/congresos`

## Pantallas Y Fragmentos Relacionados

- `profesores.pantalla.congresos`

## Objetivo

Revisar congresos registrados por profesor (tipo, lugar, fechas, organizador).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
