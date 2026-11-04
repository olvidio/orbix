---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "menus"
titulo: "Modificar una entrada de menú"
flujo: "menus.menu.gestionar.flujo"
preguntas: ["Como modificar una entrada de menú en Modificar una entrada de menú?"]
pantallas_principales: ["menus.pantalla.menus_get"]
fragmentos: []
endpoints: ["/src/menus/menu_copiar", "/src/menus/menu_eliminar", "/src/menus/menu_guardar"]
source: "docs/catalogo/menus/flujos/menu.md"
estado_revision: "generado"
---

# Ayuda IA - Modificar una entrada de menú

Usa este documento para responder preguntas de usuario sobre como trabajar con `Modificar una entrada de menú`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como modificar una entrada de menú en Modificar una entrada de menú?

## Donde Entrar

- Menus Get (`menus.pantalla.menus_get`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Modificar una entrada de menú

1. Entrar en **seleccionar** y elegir el **grupo** (vsm, scdl, dre, sistema…). Se muestra el árbol de entradas con su orden (por ejemplo 10,50,40), el nombre, «[ok]» y los roles que lo ven entre paréntesis.
2. Pulsar una entrada para editarla: **orden**, **es ok?**, **texto menu**, **meta** (la pantalla a la que lleva), **parametros** y los **permisos** (una casilla por rol; **todos** y **ninguno** marcan o desmarcan todas).
3. Pulsar **guardar**, o **cancelar** para salir.
4. Con **eliminar** se borra la entrada; con **nuevo** se crea otra en el grupo elegido (si no hay grupo avisa «debe seleccionar un grupo de menu para poder añadir un menu»); **mover a** y **copiar en** la pasan o copian a otro grupo.

Referencias tecnicas para verificar la respuesta:
- `/src/menus/menu_copiar`
- `/src/menus/menu_eliminar`
- `/src/menus/menu_guardar`

## Pantallas Y Fragmentos Relacionados

- `menus.pantalla.menus_get`

## Objetivo

Ver, cambiar, copiar, mover, crear o borrar entradas de los menús de un grupo.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
