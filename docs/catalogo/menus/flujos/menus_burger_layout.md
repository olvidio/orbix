---
id: "menus.menus_burger_layout.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "menus"
nombre: "Flujo - Ver el menú Burger o Pills"
capacidad: "menus.menus_burger_layout.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["obtener_datos"]
endpoints: ["/src/menus/menus_burger_layout_data"]
estado_revision: "revisado"
---

# Flujo - Render menú Pills2/Burger

## Objetivo De Usuario

Cargar el árbol de menús de los diseños modernos (Burger, Pills, Pills 2).

## Punto De Entrada

Automático al iniciar sesión con esos layouts.

## Escenarios Inferidos

### Ver el menú Burger o Pills

Pasos propuestos:
1. Elegir el layout en **preferencias**.
2. Al entrar, el menú aparece anidado según el rol del usuario.

Endpoints asociados:
- `/src/menus/menus_burger_layout_data`

## Endpoints Del Flujo

- `/src/menus/menus_burger_layout_data`
- `/src/menus/grupmenu_coleccion`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
