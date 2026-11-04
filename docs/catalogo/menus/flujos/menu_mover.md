---
id: "menus.menu_mover.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "menus"
nombre: "Flujo - Mover una entrada a otro grupo"
capacidad: "menus.menu_mover.gestionar"
pantallas_principales: ["menus.pantalla.menus_get"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/menus/menu_mover"]
estado_revision: "revisado"
---

# Flujo - Mover ítem de menú

## Objetivo De Usuario

Pasar una entrada de menú a otro grupo.

## Punto De Entrada

Menú **sistema > menus > seleccionar** (Pills2: ADMIN GLOBAL > menus > seleccionar).

## Escenarios Inferidos

### Mover una entrada a otro grupo

Pasos propuestos:
1. Abrir la entrada en el gestor de menús.
2. Elegir el grupo de destino en el desplegable junto a **mover a**.
3. Pulsar **mover a**.

Endpoints asociados:
- `/src/menus/menu_mover`

## Endpoints Del Flujo

- `/src/menus/menu_mover`

## Ruta de menú

- **Legacy:** sistema > menus > seleccionar
- **Pills2:** sistema > menus > seleccionar · ADMIN GLOBAL > menus > seleccionar

(Acción de ficha del gestor; no tiene menú propio.)
