---
id: "menus.menus_legacy_layout_items.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "menus"
nombre: "Flujo - Ver el menú Legacy"
capacidad: "menus.menus_legacy_layout_items.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["obtener_datos"]
endpoints: ["/src/menus/menus_legacy_layout_items_data"]
estado_revision: "revisado"
---

# Flujo - Render menú Legacy

## Objetivo De Usuario

Cargar los ítems visibles del menú del diseño Legacy.

## Punto De Entrada

Automático al iniciar sesión con el layout Legacy.

## Escenarios Inferidos

### Ver el menú Legacy

Pasos propuestos:
1. Iniciar sesión con layout Legacy (se elige en **preferencias**).
2. La barra lateral muestra los grupos permitidos para el usuario.

Endpoints asociados:
- `/src/menus/menus_legacy_layout_items_data`

## Endpoints Del Flujo

- `/src/menus/menus_legacy_layout_items_data`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
