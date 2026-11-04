---
id: "menus.grupmenu.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "menus"
nombre: "Flujo - Gestionar Grupmenu"
capacidad: "menus.grupmenu.gestionar"
pantallas_principales: ["menus.pantalla.grupmenu_form", "menus.pantalla.grupmenu_lista", "menus.pantalla.menus_get", "menus.pantalla.menus_que"]
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

1. En la lista de grupos de menú, pulsar **nuevo** o seleccionar uno y pulsar **modificar**: se abre el formulario.
2. En un alta el formulario sale vacío; al modificar sale con los datos del grupo. Pulsar **guardar**.
3. Para eliminar un grupo, seleccionarlo y pulsar **eliminar**.

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
