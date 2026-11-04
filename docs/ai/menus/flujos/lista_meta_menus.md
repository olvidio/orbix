---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "menus"
titulo: "Elegir el destino de un menú"
flujo: "menus.lista_meta_menus.gestionar.flujo"
preguntas: ["Como elegir el destino de un menú en Elegir el destino de un menú?"]
pantallas_principales: ["menus.pantalla.menus_get"]
fragmentos: ["menus.pantalla.menus_get"]
endpoints: ["/src/menus/lista_meta_menus"]
source: "docs/catalogo/menus/flujos/lista_meta_menus.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir el destino de un menú

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir el destino de un menú`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir el destino de un menú en Elegir el destino de un menú?

## Donde Entrar

- Menus Get (`menus.pantalla.menus_get`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir el destino de un menú

1. En la ficha de la entrada, abrir el desplegable de destino.
2. Elegir la pantalla o módulo al que debe llevar.

Referencias tecnicas para verificar la respuesta:
- `/src/menus/lista_meta_menus`

## Pantallas Y Fragmentos Relacionados

- `menus.pantalla.menus_get`
- `menus.pantalla.menus_get`

## Objetivo

Ofrecer los destinos posibles (módulo o URL) al editar una entrada de menú.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
