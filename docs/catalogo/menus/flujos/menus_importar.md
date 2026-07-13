---
id: "menus.menus_importar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "menus"
nombre: "Flujo - Gestionar Menus Importar"
capacidad: "menus.menus_importar.gestionar"
pantallas_principales: []
fragmentos: ["menus.pantalla.menus_importar_form"]
acciones: ["ejecutar"]
endpoints: ["/src/menus/menus_importar"]
estado_revision: "revisado"
---

# Flujo - Importar plantilla al esquema

## Objetivo De Usuario

Sustituye menús locales por una plantilla seleccionada.

## Punto De Entrada

`menus_importar_form`.

## Endpoints Del Flujo

- `/src/menus/menus_importar`

## Ruta de menú

- **Legacy:** sistema > menus > importar
- **Pills2:** ADMIN LOCAL > Importar menús
