---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "menus"
titulo: "Editar un grupo de menús"
flujo: "menus.grupmenu_info.gestionar.flujo"
preguntas: ["Como editar un grupo de menús en Editar un grupo de menús?"]
pantallas_principales: ["menus.pantalla.grupmenu_form"]
fragmentos: ["menus.pantalla.grupmenu_form"]
endpoints: ["/src/menus/grupmenu_info"]
source: "docs/catalogo/menus/flujos/grupmenu_info.md"
estado_revision: "generado"
---

# Ayuda IA - Editar un grupo de menús

Usa este documento para responder preguntas de usuario sobre como trabajar con `Editar un grupo de menús`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como editar un grupo de menús en Editar un grupo de menús?

## Donde Entrar

- Grupmenu Form (`menus.pantalla.grupmenu_form`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Editar un grupo de menús

1. En el gestor de menús, abrir el grupo.
2. Cambiar su nombre y guardar.

Referencias tecnicas para verificar la respuesta:
- `/src/menus/grupmenu_info`

## Pantallas Y Fragmentos Relacionados

- `menus.pantalla.grupmenu_form`
- `menus.pantalla.grupmenu_form`

## Objetivo

Cargar el formulario de edición de un grupo de menús.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
