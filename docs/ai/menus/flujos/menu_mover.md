---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "menus"
titulo: "Mover una entrada a otro grupo"
flujo: "menus.menu_mover.gestionar.flujo"
preguntas: ["Como mover una entrada a otro grupo en Mover una entrada a otro grupo?"]
pantallas_principales: ["menus.pantalla.menus_get"]
fragmentos: []
endpoints: ["/src/menus/menu_mover"]
source: "docs/catalogo/menus/flujos/menu_mover.md"
estado_revision: "generado"
---

# Ayuda IA - Mover una entrada a otro grupo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Mover una entrada a otro grupo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como mover una entrada a otro grupo en Mover una entrada a otro grupo?

## Donde Entrar

- Menus Get (`menus.pantalla.menus_get`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Mover una entrada a otro grupo

1. Abrir la entrada en el gestor de menús.
2. Elegir el grupo de destino en el desplegable junto a **mover a**.
3. Pulsar **mover a**.

Referencias tecnicas para verificar la respuesta:
- `/src/menus/menu_mover`

## Pantallas Y Fragmentos Relacionados

- `menus.pantalla.menus_get`

## Objetivo

Pasar una entrada de menú a otro grupo.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
