---
id: "shared.tablaDB_formulario.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "shared"
nombre: "Flujo - Formulario tabla genérica"
capacidad: "shared.tablaDB_formulario.gestionar"
pantallas_principales: []
fragmentos: ["shared.pantalla.tablaDB_formulario_ver"]
acciones: ["cargar", "guardar", "cancelar"]
endpoints: ["/src/shared/tablaDB_formulario_datos", "/src/shared/tablaDB_update"]
estado_revision: "revisado"
---

# Flujo - Formulario tabla genérica

## Objetivo De Usuario

Crear o modificar un registro en el mantenimiento genérico de tablas.

## Punto De Entrada

`tablaDB_formulario_ver.php` desde listado (`mod`, `sel`/`s_pkey`) o dossier (`obj_pau`).

## Escenarios

### Cargar formulario

1. POST con `clase_info`, `mod`, `a_pkey` (edición) o vacío (alta).
2. `tablaDB_formulario_datos` devuelve `fields` y metadatos.
3. Render de campos según tipo.

### Guardar

1. Validar fecha en cliente (`fnjs_comprobar_fecha`).
2. `fnjs_grabar` → `tablaDB_update` con todos los campos + `go_to`.
3. Éxito → navegación atrás al listado/dossier.

## Errores Conocidos

- Mensajes de `tablaDB_update` en `alert` vía `json.mensaje`.

## Ruta de menú

sin entrada de menú en el índice.
