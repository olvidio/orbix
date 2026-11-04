---
id: "menus.menus_exportar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "menus"
nombre: "Flujo - Exportar los menús"
capacidad: "menus.menus_exportar.gestionar"
pantallas_principales: ["menus.pantalla.menus_exportar_form"]
fragmentos: ["menus.pantalla.menus_exportar_form"]
acciones: ["ejecutar"]
endpoints: ["/src/menus/menus_exportar"]
estado_revision: "revisado"
---

# Flujo - Exportar esquema a plantilla

## Objetivo De Usuario

Guardar el menú actual como plantilla de referencia en la base pública.

## Punto De Entrada

Menú **sistema > menus > Exportar** (Pills2: ADMIN LOCAL > Exportar menús).

## Escenarios Inferidos

### Exportar los menús

Pasos propuestos:
1. Entrar en **Exportar** (menú **sistema > menus > Exportar**). Se abre un formulario con el campo **Guardar como** y el botón **guardar**.
2. Escribir el nombre de la plantilla y pulsar **guardar**: el menú local pasa a ser la plantilla de referencia que otras delegaciones pueden importar.
3. Si ya existe una plantilla con ese nombre, la pantalla pregunta si se quiere reemplazar.

Endpoints asociados:
- `/src/menus/menus_exportar`

## Endpoints Del Flujo

- `/src/menus/menus_exportar`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** ADMIN LOCAL > Exportar menús · sistema > menus > Exportar · ADMIN GLOBAL > menus > Exportar

## Casos particulares

- Si la plantilla ya existe, la UI pide confirmación y reenvía con `sobreescribir=true`.
