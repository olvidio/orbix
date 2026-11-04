---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "menus"
titulo: "Exportar los menús"
flujo: "menus.menus_exportar.gestionar.flujo"
preguntas: ["Como exportar los menús en Exportar los menús?"]
pantallas_principales: ["menus.pantalla.menus_exportar_form"]
fragmentos: ["menus.pantalla.menus_exportar_form"]
endpoints: ["/src/menus/menus_exportar"]
source: "docs/catalogo/menus/flujos/menus_exportar.md"
estado_revision: "generado"
---

# Ayuda IA - Exportar los menús

Usa este documento para responder preguntas de usuario sobre como trabajar con `Exportar los menús`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como exportar los menús en Exportar los menús?

## Donde Entrar

- Menus Exportar Form (`menus.pantalla.menus_exportar_form`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Exportar los menús

1. Entrar en **Exportar** (menú **sistema > menus > Exportar**). Se abre un formulario con el campo **Guardar como** y el botón **guardar**.
2. Escribir el nombre de la plantilla y pulsar **guardar**: el menú local pasa a ser la plantilla de referencia que otras delegaciones pueden importar.
3. Si ya existe una plantilla con ese nombre, la pantalla pregunta si se quiere reemplazar.

Referencias tecnicas para verificar la respuesta:
- `/src/menus/menus_exportar`

## Pantallas Y Fragmentos Relacionados

- `menus.pantalla.menus_exportar_form`
- `menus.pantalla.menus_exportar_form`

## Objetivo

Guardar el menú actual como plantilla de referencia en la base pública.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
