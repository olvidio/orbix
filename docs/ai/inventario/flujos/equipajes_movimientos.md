---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Consultar los movimientos de un equipaje"
flujo: "inventario.equipajes_movimientos.gestionar.flujo"
preguntas: ["Como consultar los movimientos de un equipaje en Consultar los movimientos de un equipaje?"]
pantallas_principales: ["inventario.pantalla.equipajes_movimientos"]
fragmentos: ["inventario.pantalla.equipajes_movimientos"]
endpoints: ["/src/inventario/equipajes_movimientos"]
source: "docs/catalogo/inventario/flujos/equipajes_movimientos.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar los movimientos de un equipaje

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar los movimientos de un equipaje`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar los movimientos de un equipaje en Consultar los movimientos de un equipaje?

## Donde Entrar

- Movimientos maletas — resultado (`inventario.pantalla.equipajes_movimientos`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar los movimientos de un equipaje

1. Abrir **movimientos maletas**.
2. Marcar el equipaje y pulsar **seleccionar**.
3. Para cada valija se listan los documentos **añadir** y **quitar**.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/equipajes_movimientos`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.equipajes_movimientos`
- `inventario.pantalla.equipajes_movimientos`

## Objetivo

Ver qué documentos se han añadido o quitado de las valijas de un equipaje.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
