---
id: "shared.tablaDB_buscar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "shared"
nombre: "Flujo - Búsqueda previa al listado"
capacidad: "shared.tablaDB_buscar.gestionar"
pantallas_principales: ["shared.pantalla.tablaDB_lista_ver"]
fragmentos: []
acciones: ["mostrar_criterios", "buscar"]
endpoints: ["/src/shared/tablaDB_buscar_datos"]
estado_revision: "revisado"
---

# Flujo - Búsqueda previa al listado

## Objetivo De Usuario

Filtrar registros antes de mostrar la tabla en mantenimientos que definen criterios de búsqueda.

## Punto De Entrada

Primera carga de `tablaDB_lista_ver.php` (sin `k_buscar` ni `aSerieBuscar`).

## Escenarios

### Mostrar criterios

1. `tablaDB_buscar_datos` con `clase_info` y contexto padre.
2. Vista `tablaDB_busqueda.phtml` o `buscar_view` custom del `Info*`.

### Buscar

1. Usuario rellena `k_buscar` (y campos extra del `Info*`).
2. Submit → misma URL con criterios → fase listado (`tablaDB_lista_datos`).

## Errores Conocidos

- Ninguno documentado en el builder.

## Ruta de menú

Misma entrada que el listado destino (variante por `clase_info` en `_referencia_menus.md`).
