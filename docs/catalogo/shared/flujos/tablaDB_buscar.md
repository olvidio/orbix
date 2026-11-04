---
id: "shared.tablaDB_buscar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "shared"
nombre: "Flujo - Buscar antes de listar"
capacidad: "shared.tablaDB_buscar.gestionar"
pantallas_principales: ["shared.pantalla.tablaDB_lista_ver"]
fragmentos: []
acciones: ["mostrar_criterios", "buscar"]
endpoints: ["/src/shared/tablaDB_buscar_datos"]
estado_revision: "revisado"
---

# Flujo - Búsqueda previa al listado

## Objetivo De Usuario

Filtrar los registros antes de ver la tabla, en los mantenimientos que piden criterios de búsqueda.

## Punto De Entrada

Primera pantalla de un mantenimiento de tabla genérico, cuando éste define criterios.

## Escenarios Inferidos

### Buscar antes de listar

Pasos propuestos:
1. Abrir el mantenimiento desde el menú: aparece primero el formulario de criterios.
2. Rellenar los criterios que interesen.
3. Pulsar buscar: se muestra la tabla con los registros que cumplen.

Endpoints asociados:
- `/src/shared/tablaDB_buscar_datos`

## Errores Conocidos

- Ninguno documentado en el builder.

## Ruta de menú

Misma entrada que el listado destino (variante por `clase_info` en `_referencia_menus.md`).
