---
id: "menus.menus_get_page.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "menus"
nombre: "Flujo - Ver el listado y la ficha de un menú"
capacidad: "menus.menus_get_page.gestionar"
pantallas_principales: ["menus.pantalla.menus_get"]
fragmentos: ["menus.pantalla.menus_get"]
acciones: ["obtener_datos"]
endpoints: ["/src/menus/menus_get_page_data"]
estado_revision: "revisado"
---

# Flujo - Cargar listado/ficha menú

## Objetivo De Usuario

Mostrar, dentro del gestor de menús, el listado de un grupo o la ficha de una entrada.

## Punto De Entrada

Menú **sistema > menus > seleccionar** (Pills2: ADMIN GLOBAL > menus > seleccionar).

## Escenarios Inferidos

### Ver el listado y la ficha de un menú

Pasos propuestos:
1. En el gestor de menús, elegir el grupo.
2. El listado se carga en la misma pantalla; pulsar una entrada para abrir su ficha.

Endpoints asociados:
- `/src/menus/menus_get_page_data`

## Endpoints Del Flujo

- `/src/menus/menus_get_page_data`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
