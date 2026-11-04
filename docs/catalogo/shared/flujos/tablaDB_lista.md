---
id: "shared.tablaDB_lista.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "shared"
nombre: "Flujo - Buscar y listar los registros"
capacidad: "shared.tablaDB_lista.gestionar"
pantallas_principales: ["shared.pantalla.tablaDB_lista_ver"]
fragmentos: ["shared.pantalla.tablaDB_formulario_ver"]
acciones: ["buscar", "listar", "nuevo", "editar", "eliminar"]
endpoints: ["/src/shared/tablaDB_buscar_datos", "/src/shared/tablaDB_lista_datos", "/src/shared/tablaDB_update"]
estado_revision: "revisado"
---

# Flujo - Listar y mantener tabla genérica

## Objetivo De Usuario

Consultar y mantener los registros de tablas de configuración que se abren desde el menú (asignaturas, ubicaciones, inventario, procesos…) con la misma pantalla genérica.

## Punto De Entrada

Distintas entradas de menú abren esta misma pantalla, cada una con su tabla.

## Escenarios Inferidos

### Buscar y listar los registros

Pasos propuestos:
1. Abrir la tabla desde el menú.
2. Si el mantenimiento pide criterios, rellenarlos y pulsar buscar.
3. Aparece la tabla; se puede ordenar por varias columnas.

Endpoints asociados:
- `/src/shared/tablaDB_buscar_datos`
- `/src/shared/tablaDB_lista_datos`
- `/src/shared/tablaDB_update`

### Añadir, modificar o eliminar un registro

Pasos propuestos:
1. Pulsar **nuevo** para añadir, o seleccionar una fila y pulsar **modificar** (ver «Crear o modificar un registro»).
2. Para eliminar, seleccionar la fila, pulsar **eliminar** y confirmar.

Endpoints asociados:
- `/src/shared/tablaDB_buscar_datos`
- `/src/shared/tablaDB_lista_datos`
- `/src/shared/tablaDB_update`

## Errores Conocidos

- Errores de `tablaDB_update` (ver ficha API).
- Sin errores propios en builders de lista.

## Ruta de menú

Variante según `clase_info` — ver `docs/guias/_referencia_menus.md` (entradas `tablaDB_lista_ver.php`).
Ejemplos: `global > estudios > asignaturas`, `sistema > menus > meta menus`, `dre > zonas > zonas`.
