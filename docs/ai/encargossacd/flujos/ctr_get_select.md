---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Elegir un centro"
flujo: "encargossacd.ctr_get_select.gestionar.flujo"
preguntas: ["Como elegir un centro en Elegir un centro?"]
pantallas_principales: ["encargossacd.pantalla.ctr_ficha"]
fragmentos: ["encargossacd.pantalla.ctr_ficha", "encargossacd.pantalla.encargo_ver"]
endpoints: ["/src/encargossacd/ctr_get_select_data"]
source: "docs/catalogo/encargossacd/flujos/ctr_get_select.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir un centro

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir un centro`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir un centro en Elegir un centro?

## Donde Entrar

- Ctr Ficha (`encargossacd.pantalla.ctr_ficha`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir un centro

1. En **ficha ctr**, elegir el **grupo de ctrs**.
2. El desplegable **centro** se rellena con los centros de ese grupo; elegir uno.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/ctr_get_select_data`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.ctr_ficha`
- `encargossacd.pantalla.ctr_ficha`
- `encargossacd.pantalla.encargo_ver`

## Objetivo

Rellenar el desplegable de centros según el grupo elegido.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
