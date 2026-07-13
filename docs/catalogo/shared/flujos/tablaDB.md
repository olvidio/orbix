---
id: "shared.tablaDB.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "shared"
nombre: "Flujo - Persistir registro tabla genérica"
capacidad: "shared.tablaDB.gestionar"
pantallas_principales: []
fragmentos: ["shared.pantalla.tablaDB_formulario_ver", "shared.pantalla.tablaDB_lista_ver"]
acciones: ["nuevo", "editar", "eliminar"]
endpoints: ["/src/shared/tablaDB_update"]
estado_revision: "revisado"
---

# Flujo - Persistir registro tabla genérica

## Objetivo De Usuario

Dar de alta, modificar o eliminar un registro en cualquier tabla mantenida con el patrón `Info*` +
repositorio CRUD.

## Punto De Entrada

- Formulario: `fnjs_grabar` (`mod` nuevo/editar).
- Listado: acción eliminar con `mod=eliminar` y `sel[]`.

## Escenarios

### Nuevo / editar

1. Formulario envía `$_POST` completo a `tablaDB_update` con `mod` y `clase_info`.
2. `DatosUpdateRepo` mapea campos (check, fecha, decimal) y llama `Guardar`.
3. Casos especiales: `ModuloInstalado` (tablas al activar), `ProfesorLatin` (id fijo).

### Eliminar

1. `mod=eliminar` con `s_pkey` o `sel[0]`.
2. `DatosUpdateRepo::eliminar`; en módulos instalados puede ejecutar `dropTables`.

## Errores Conocidos

- `no se ha ejecutado la acción`
- Errores de repositorio y validación de módulos (ver API `tablaDB_update`).

## Ruta de menú

sin entrada directa; mutación desde listado/formulario cuya ruta depende de `clase_info`.
