---
id: "menus.grupmenu.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "menus"
nombre: "Flujo - Gestionar Grupmenu"
capacidad: "menus.grupmenu.gestionar"
pantallas_principales: []
fragmentos: ["menus.pantalla.grupmenu_lista", "menus.pantalla.grupmenu_form"]
acciones: ["eliminar", "guardar", "listar"]
endpoints: ["/src/menus/grupmenu_eliminar", "/src/menus/grupmenu_guardar", "/src/menus/grupmenu_lista", "/src/menus/grupmenu_info"]
estado_revision: "revisado"
---

# Flujo - Gestionar grupos de menú

CRUD de grupos raíz (`aux_grupmenu`) que organizan el árbol por layout.

## Objetivo De Usuario

Alta/edición/baja de grupos de menú.

## Punto De Entrada

Dos caminos:

1. **Menú vivo:** TablaDB InfoGrupMenus (`frontend/shared/controller/tablaDB_lista_ver.php`) — ver módulo shared.
2. **Pantallas Orbix:** `grupmenu_lista` / `grupmenu_form` (sin entrada de menú en el índice).

## Escenarios

### Via pantallas Orbix

1. Listar en `grupmenu_lista` → nuevo/modificar abre `grupmenu_form`.
2. Alta: sin `grupmenu_info`; edición: carga `grupmenu_info` y guarda con `grupmenu_guardar`.
3. Eliminar: `grupmenu_eliminar`.

## Endpoints Del Flujo

- `/src/menus/grupmenu_lista`
- `/src/menus/grupmenu_guardar`
- `/src/menus/grupmenu_eliminar`
- `/src/menus/grupmenu_info`

## Ruta de menú

Entrada de menú (TablaDB, no `grupmenu_lista.php`):

- **Legacy:** sistema > usuarios web > grup menu
- **Pills2:** sistema > usuarios web > grup menu · ADMIN LOCAL > usuarios web > grup menu

Pantallas `grupmenu_*`: sin entrada de menú en el índice.
