---
id: "menus.menu.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "menus"
nombre: "Flujo - Gestionar Menu"
capacidad: "menus.menu.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["copiar", "eliminar", "guardar"]
endpoints: ["/src/menus/menu_copiar", "/src/menus/menu_eliminar", "/src/menus/menu_guardar"]
estado_revision: "revisado"
---

# Flujo - Gestionar ítem de menú

## Objetivo De Usuario

Alta, edición, copia, movimiento y borrado de entradas del árbol (`aux_menus`) enlazadas a un metamenu.

## Punto De Entrada

`menus_que` → elegir grupo → listado/ficha en `menus_get`.

## Endpoints Del Flujo

- `/src/menus/menu_guardar`
- `/src/menus/menu_eliminar`
- `/src/menus/menu_copiar`
- `/src/menus/menu_mover`
- `/src/menus/menus_get_page_data`
- `/src/menus/lista_meta_menus`

## Ruta de menú

- **Legacy:** sistema > menus > seleccionar
- **Pills2:** ADMIN GLOBAL > menus > seleccionar
