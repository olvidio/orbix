---
id: "menus.menus_get_page.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "menus"
nombre: "Flujo - Gestionar Menus Get Page"
capacidad: "menus.menus_get_page.gestionar"
pantallas_principales: []
fragmentos: ["menus.pantalla.menus_get"]
acciones: ["obtener_datos"]
endpoints: ["/src/menus/menus_get_page_data"]
estado_revision: "revisado"
---

# Flujo - Cargar listado/ficha menú

## Objetivo De Usuario

Builder AJAX lista vs edición en gestor de menús.

## Punto De Entrada

`menus_get` fragmento.

## Endpoints Del Flujo

- `/src/menus/menus_get_page_data`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
