---
id: "menus.menu.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "menus"
nombre: "Flujo - Modificar una entrada de menú"
capacidad: "menus.menu.gestionar"
pantallas_principales: ["menus.pantalla.menus_get"]
fragmentos: []
acciones: ["copiar", "eliminar", "guardar"]
endpoints: ["/src/menus/menu_copiar", "/src/menus/menu_eliminar", "/src/menus/menu_guardar"]
estado_revision: "revisado"
---

# Flujo - Gestionar ítem de menú

## Objetivo De Usuario

Ver, cambiar, copiar, mover, crear o borrar entradas de los menús de un grupo.

## Punto De Entrada

Menú **sistema > menus > seleccionar** (Pills2: ADMIN GLOBAL > menus > seleccionar).

## Escenarios Inferidos

### Modificar una entrada de menú

Pasos propuestos:
1. Entrar en **seleccionar** y elegir el **grupo** (vsm, scdl, dre, sistema…). Se muestra el árbol de entradas con su orden (por ejemplo 10,50,40), el nombre, «[ok]» y los roles que lo ven entre paréntesis.
2. Pulsar una entrada para editarla: **orden**, **es ok?**, **texto menu**, **meta** (la pantalla a la que lleva), **parametros** y los **permisos** (una casilla por rol; **todos** y **ninguno** marcan o desmarcan todas).
3. Pulsar **guardar**, o **cancelar** para salir.
4. Con **eliminar** se borra la entrada; con **nuevo** se crea otra en el grupo elegido (si no hay grupo avisa «debe seleccionar un grupo de menu para poder añadir un menu»); **mover a** y **copiar en** la pasan o copian a otro grupo.

Endpoints asociados:
- `/src/menus/menu_copiar`
- `/src/menus/menu_eliminar`
- `/src/menus/menu_guardar`

## Endpoints Del Flujo

- `/src/menus/menu_guardar`
- `/src/menus/menu_eliminar`
- `/src/menus/menu_copiar`
- `/src/menus/menu_mover`
- `/src/menus/menus_get_page_data`
- `/src/menus/lista_meta_menus`

## Ruta de menú

- **Legacy:** sistema > menus > seleccionar
- **Pills2:** sistema > menus > seleccionar · ADMIN GLOBAL > menus > seleccionar
