---
id: "menus.grupmenu_coleccion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "menus"
nombre: "Flujo - Ver mi menú lateral"
capacidad: "menus.grupmenu_coleccion.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/menus/grupmenu_coleccion"]
estado_revision: "revisado"
---

# Flujo - Grupmenu visibles (portada)

## Objetivo De Usuario

Cargar los grupos e ítems de menú que puede ver el usuario en el menú lateral.

## Punto De Entrada

Automático al iniciar sesión.

## Escenarios Inferidos

### Ver mi menú lateral

Pasos propuestos:
1. Iniciar sesión.
2. El menú lateral muestra solo los grupos y opciones autorizados para el rol del usuario.

Endpoints asociados:
- `/src/menus/grupmenu_coleccion`

## Endpoints Del Flujo

- `/src/menus/grupmenu_coleccion`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
