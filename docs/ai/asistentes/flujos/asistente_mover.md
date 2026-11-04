---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "asistentes"
titulo: "Mover un asistente a otra actividad"
flujo: "asistentes.asistente_mover.gestionar.flujo"
preguntas: ["Como mover un asistente a otra actividad en Mover un asistente a otra actividad?"]
pantallas_principales: ["asistentes.pantalla.asistente_mover"]
fragmentos: ["asistentes.pantalla.asistente_mover"]
endpoints: ["/src/asistentes/asistente_mover_data"]
source: "docs/catalogo/asistentes/flujos/asistente_mover.md"
estado_revision: "generado"
---

# Ayuda IA - Mover un asistente a otra actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Mover un asistente a otra actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como mover un asistente a otra actividad en Mover un asistente a otra actividad?

## Donde Entrar

- Asistente Mover (`asistentes.pantalla.asistente_mover`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Mover un asistente a otra actividad

1. Seleccionar al asistente en la lista y elegir mover.
2. En **mover a** elegir la actividad de destino (del mismo tipo) y, si se quiere, escribir **observaciones**.
3. Pulsar **guardar** (o **cancelar**).

Referencias tecnicas para verificar la respuesta:
- `/src/asistentes/asistente_mover_data`

## Pantallas Y Fragmentos Relacionados

- `asistentes.pantalla.asistente_mover`
- `asistentes.pantalla.asistente_mover`

## Objetivo

Pasar un asistente de una actividad a otra del mismo tipo.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
