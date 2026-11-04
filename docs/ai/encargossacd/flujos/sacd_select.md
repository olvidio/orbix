---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Elegir un sacerdote"
flujo: "encargossacd.sacd_select.gestionar.flujo"
preguntas: ["Como elegir un sacerdote en Elegir un sacerdote?"]
pantallas_principales: ["encargossacd.pantalla.sacd_ficha_ajax"]
fragmentos: ["encargossacd.pantalla.sacd_ficha_ajax"]
endpoints: ["/src/encargossacd/sacd_select_data"]
source: "docs/catalogo/encargossacd/flujos/sacd_select.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir un sacerdote

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir un sacerdote`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir un sacerdote en Elegir un sacerdote?

## Donde Entrar

- Sacd Ficha Ajax (`encargossacd.pantalla.sacd_ficha_ajax`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir un sacerdote

1. En **ficha sacd** o **ausencias**, elegir el **tipo**.
2. El desplegable **sacd** se rellena con los sacerdotes de ese tipo; elegir uno.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/sacd_select_data`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.sacd_ficha_ajax`
- `encargossacd.pantalla.sacd_ficha_ajax`

## Objetivo

Rellenar el desplegable de sacerdotes según el tipo elegido.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
