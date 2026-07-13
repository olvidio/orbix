---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "menus"
titulo: "Grupmenu"
flujo: "menus.grupmenu.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["menus.pantalla.grupmenu_lista", "menus.pantalla.menus_get", "menus.pantalla.menus_que"]
endpoints: ["/src/menus/grupmenu_eliminar", "/src/menus/grupmenu_guardar", "/src/menus/grupmenu_lista"]
source: "docs/catalogo/menus/flujos/grupmenu.md"
estado_revision: "generado"
---

# Ayuda IA - Grupmenu

Usa este documento para responder preguntas de usuario sobre como trabajar con `Grupmenu`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `menus.pantalla.grupmenu_lista`
- `menus.pantalla.menus_get`
- `menus.pantalla.menus_que`

## Objetivo

CRUD de grupos raíz (`aux_grupmenu`) que organizan el árbol por layout.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
