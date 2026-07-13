---
id: "cambios.cambio_usuario.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Eliminar cambio anotado"
capacidad: "cambios.cambio_usuario.gestionar"
pantallas_principales: []
fragmentos: ["cambios.pantalla.avisos_generar"]
acciones: ["eliminar"]
endpoints: ["/src/cambios/cambio_usuario_eliminar"]
estado_revision: "revisado"
---

# Flujo - Eliminar cambio anotado

## Objetivo De Usuario

Quitar de la cola de avisos un `CambioUsuario` concreto seleccionado en la lista de cambios.

## Punto De Entrada

Pantalla `avisos_generar` → acción `fnjs_borrar`.

## Escenarios

### Eliminar

1. Seleccionar una o más filas (`sel[]` = `id_item_cambio#id_usuario#sfsv#aviso_tipo`).
2. Confirmar borrado.
3. El listado se refresca sin las filas eliminadas.

## Errores Conocidos

- `Hay un error, no se ha eliminado`

## Ruta de menú

(vía pantalla lista de cambios — ver flujo `avisos_generar`)
