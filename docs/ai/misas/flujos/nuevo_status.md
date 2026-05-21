---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Nuevo Status"
flujo: "misas.nuevo_status.gestionar.flujo"
preguntas: ["Como ejecutar en Nuevo Status?"]
pantallas_principales: []
fragmentos: ["misas.pantalla.cambiar_status"]
endpoints: ["/src/misas/nuevo_status"]
source: "docs/catalogo/misas/flujos/nuevo_status.md"
estado_revision: "generado"
---

# Ayuda IA - Nuevo Status

Usa este documento para responder preguntas de usuario sobre como trabajar con `Nuevo Status`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Nuevo Status?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.cambiar_status`

## Objetivo

Gestiona NuevoStatusPeriodo. Actualiza status de todos los EncargoDia de encargos 8100+ de la zona en el rango.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
