---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cartaspresentacion"
titulo: "Elegir la población"
flujo: "cartaspresentacion.poblaciones.gestionar.flujo"
preguntas: ["Como elegir la población en Elegir la población?"]
pantallas_principales: ["cartaspresentacion.pantalla.cartas_presentacion"]
fragmentos: []
endpoints: ["/src/cartaspresentacion/poblaciones_data"]
source: "docs/catalogo/cartaspresentacion/flujos/poblaciones.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir la población

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir la población`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir la población en Elegir la población?

## Donde Entrar

- Cartas Presentacion (`cartaspresentacion.pantalla.cartas_presentacion`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir la población

1. En «según dl» elegir la opción de la delegación: se rellena el desplegable de poblaciones.
2. Elegir la población. Si se elige «según r», el desplegable de población se oculta.

Referencias tecnicas para verificar la respuesta:
- `/src/cartaspresentacion/poblaciones_data`

## Pantallas Y Fragmentos Relacionados

- `cartaspresentacion.pantalla.cartas_presentacion`

## Objetivo

Filtrar la lista de centros por población dentro de la delegación.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
