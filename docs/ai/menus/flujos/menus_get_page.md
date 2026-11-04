---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "menus"
titulo: "Ver el listado y la ficha de un menú"
flujo: "menus.menus_get_page.gestionar.flujo"
preguntas: ["Como ver el listado y la ficha de un menú en Ver el listado y la ficha de un menú?"]
pantallas_principales: ["menus.pantalla.menus_get"]
fragmentos: ["menus.pantalla.menus_get"]
endpoints: ["/src/menus/menus_get_page_data"]
source: "docs/catalogo/menus/flujos/menus_get_page.md"
estado_revision: "generado"
---

# Ayuda IA - Ver el listado y la ficha de un menú

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver el listado y la ficha de un menú`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver el listado y la ficha de un menú en Ver el listado y la ficha de un menú?

## Donde Entrar

- Menus Get (`menus.pantalla.menus_get`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver el listado y la ficha de un menú

1. En el gestor de menús, elegir el grupo.
2. El listado se carga en la misma pantalla; pulsar una entrada para abrir su ficha.

Referencias tecnicas para verificar la respuesta:
- `/src/menus/menus_get_page_data`

## Pantallas Y Fragmentos Relacionados

- `menus.pantalla.menus_get`
- `menus.pantalla.menus_get`

## Objetivo

Mostrar, dentro del gestor de menús, el listado de un grupo o la ficha de una entrada.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
