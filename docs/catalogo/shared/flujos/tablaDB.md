---
id: "shared.tablaDB.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "shared"
nombre: "Flujo - Guardar o eliminar un registro de una tabla"
capacidad: "shared.tablaDB.gestionar"
pantallas_principales: ["shared.pantalla.tablaDB_formulario_ver"]
fragmentos: ["shared.pantalla.tablaDB_formulario_ver", "shared.pantalla.tablaDB_lista_ver"]
acciones: ["nuevo", "editar", "eliminar"]
endpoints: ["/src/shared/tablaDB_update"]
estado_revision: "revisado"
---

# Flujo - Persistir registro tabla genérica

## Objetivo De Usuario

Dar de alta, modificar o eliminar un registro de cualquier tabla mantenida con la pantalla genérica de mantenimiento.

## Punto De Entrada

Botón de guardar del formulario genérico, o botón **eliminar** de la tabla.

## Escenarios Inferidos

### Guardar o eliminar un registro de una tabla

Pasos propuestos:
1. En el formulario, pulsar guardar: se graba el registro nuevo o los cambios del existente.
2. En la tabla, seleccionar una o varias filas y pulsar **eliminar** para borrarlas.
3. Algunas tablas tienen reglas propias; por ejemplo, al activar un módulo se crean sus tablas.

Endpoints asociados:
- `/src/shared/tablaDB_update`

## Errores Conocidos

- `no se ha ejecutado la acción`
- Errores de repositorio y validación de módulos (ver API `tablaDB_update`).

## Ruta de menú

sin entrada directa; mutación desde listado/formulario cuya ruta depende de `clase_info`.
