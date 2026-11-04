---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "tablonanuncios"
titulo: "Anuncios del tablón"
flujo: "tablonanuncios.api_tablon.gestionar.flujo"
preguntas: ["Como anuncios del tablón en Anuncios del tablón?"]
pantallas_principales: ["tablonanuncios.pantalla.api_tablon"]
fragmentos: []
endpoints: ["/src/tablonanuncios/tablon_anuncios_data"]
source: "docs/catalogo/tablonanuncios/flujos/tablonanuncios_api.md"
estado_revision: "generado"
---

# Ayuda IA - Anuncios del tablón

Usa este documento para responder preguntas de usuario sobre como trabajar con `Anuncios del tablón`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como anuncios del tablón en Anuncios del tablón?

## Donde Entrar

- API tablon anuncios (`tablonanuncios.pantalla.api_tablon`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Anuncios del tablón

1. Los anuncios publicados desde otras aplicaciones aparecen en el tablón; no hay que hacer nada en Orbix.

Referencias tecnicas para verificar la respuesta:
- `/src/tablonanuncios/tablon_anuncios_data`

## Pantallas Y Fragmentos Relacionados

- `tablonanuncios.pantalla.api_tablon`

## Objetivo

Publicación y consulta de anuncios desde integraciones externas; no tiene pantalla propia.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
