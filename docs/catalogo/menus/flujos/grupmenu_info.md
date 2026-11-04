---
id: "menus.grupmenu_info.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "menus"
nombre: "Flujo - Editar un grupo de menús"
capacidad: "menus.grupmenu_info.gestionar"
pantallas_principales: ["menus.pantalla.grupmenu_form"]
fragmentos: ["menus.pantalla.grupmenu_form"]
acciones: ["ejecutar"]
endpoints: ["/src/menus/grupmenu_info"]
estado_revision: "revisado"
---

# Flujo - Cargar datos grupmenu

## Objetivo De Usuario

Cargar el formulario de edición de un grupo de menús.

## Punto De Entrada

Gestor de menús, editar grupo.

## Escenarios Inferidos

### Editar un grupo de menús

Pasos propuestos:
1. En el gestor de menús, abrir el grupo.
2. Cambiar su nombre y guardar.

Endpoints asociados:
- `/src/menus/grupmenu_info`

## Endpoints Del Flujo

- `/src/menus/grupmenu_info`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
