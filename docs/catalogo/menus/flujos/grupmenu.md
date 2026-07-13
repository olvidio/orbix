---
id: "menus.grupmenu.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "menus"
nombre: "Flujo - Gestionar Grupmenu"
capacidad: "menus.grupmenu.gestionar"
pantallas_principales: []
fragmentos: ["menus.pantalla.grupmenu_lista", "menus.pantalla.menus_get", "menus.pantalla.menus_que"]
acciones: ["eliminar", "guardar", "listar"]
endpoints: ["/src/menus/grupmenu_eliminar", "/src/menus/grupmenu_guardar", "/src/menus/grupmenu_lista"]
estado_revision: "revisado"
---

# Flujo - Gestionar grupos de menú

## Objetivo De Usuario

CRUD de grupos raíz (`aux_grupmenu`) que organizan el árbol por layout.

## Punto De Entrada

`grupmenu_lista` o TablaDB InfoGrupMenus.

## Endpoints Del Flujo

- `/src/menus/grupmenu_lista`
- `/src/menus/grupmenu_guardar`
- `/src/menus/grupmenu_eliminar`
- `/src/menus/grupmenu_info`

## Ruta de menú

- **Legacy:** sistema > usuarios web > grup menu
- **Pills2:** ADMIN LOCAL > usuarios web > grup menu
