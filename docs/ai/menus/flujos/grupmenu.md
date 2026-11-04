---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "menus"
titulo: "Grupmenu"
flujo: "menus.grupmenu.gestionar.flujo"
preguntas: []
pantallas_principales: ["menus.pantalla.grupmenu_form", "menus.pantalla.grupmenu_lista", "menus.pantalla.menus_get", "menus.pantalla.menus_que"]
fragmentos: ["menus.pantalla.grupmenu_lista", "menus.pantalla.grupmenu_form"]
endpoints: ["/src/menus/grupmenu_eliminar", "/src/menus/grupmenu_guardar", "/src/menus/grupmenu_lista", "/src/menus/grupmenu_info"]
source: "docs/catalogo/menus/flujos/grupmenu.md"
estado_revision: "generado"
---

# Ayuda IA - Grupmenu

Usa este documento para responder preguntas de usuario sobre como trabajar con `Grupmenu`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Grupmenu Form (`menus.pantalla.grupmenu_form`)
- Grupmenu Lista (`menus.pantalla.grupmenu_lista`)
- Menus Get (`menus.pantalla.menus_get`)
- Menus Que (`menus.pantalla.menus_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `menus.pantalla.grupmenu_form`
- `menus.pantalla.grupmenu_lista`
- `menus.pantalla.menus_get`
- `menus.pantalla.menus_que`
- `menus.pantalla.grupmenu_lista`
- `menus.pantalla.grupmenu_form`

## Objetivo

Alta/edición/baja de grupos de menú.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
