---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "menus"
titulo: "Importar una plantilla de menús"
flujo: "menus.menus_importar.gestionar.flujo"
preguntas: ["Como importar una plantilla de menús en Importar una plantilla de menús?"]
pantallas_principales: ["menus.pantalla.menus_importar_form"]
fragmentos: ["menus.pantalla.menus_importar_form"]
endpoints: ["/src/menus/menus_importar"]
source: "docs/catalogo/menus/flujos/menus_importar.md"
estado_revision: "generado"
---

# Ayuda IA - Importar una plantilla de menús

Usa este documento para responder preguntas de usuario sobre como trabajar con `Importar una plantilla de menús`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como importar una plantilla de menús en Importar una plantilla de menús?

## Donde Entrar

- Menus Importar Form (`menus.pantalla.menus_importar_form`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Importar una plantilla de menús

1. Entrar en **importar**: «Escoger menus de referencia».
2. Elegir la plantilla en el desplegable (por ejemplo Aes, Ceb, Aut-sep-2025…).
3. Pulsar **importar**. Los menús locales se sustituyen por los de la plantilla y se pierden los cambios locales.

Referencias tecnicas para verificar la respuesta:
- `/src/menus/menus_importar`

## Pantallas Y Fragmentos Relacionados

- `menus.pantalla.menus_importar_form`
- `menus.pantalla.menus_importar_form`

## Objetivo

Sustituir los menús locales por los de una plantilla de referencia.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
