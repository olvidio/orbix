---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "asistentes"
titulo: "Tabla Peticiones"
flujo: "asistentes.tabla_peticiones.gestionar.flujo"
preguntas: ["Como obtener datos en Tabla Peticiones?"]
pantallas_principales: []
fragmentos: ["asistentes.pantalla.tabla_peticiones"]
endpoints: ["/src/asistentes/tabla_peticiones_data"]
source: "docs/catalogo/asistentes/flujos/tabla_peticiones.md"
estado_revision: "generado"
---

# Ayuda IA - Tabla Peticiones

Usa este documento para responder preguntas de usuario sobre como trabajar con `Tabla Peticiones`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Tabla Peticiones?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `asistentes.pantalla.tabla_peticiones`

## Objetivo

Ver peticiones de plaza y mover asistente a actividad preferida.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
