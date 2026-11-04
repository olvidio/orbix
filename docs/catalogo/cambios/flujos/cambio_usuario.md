---
id: "cambios.cambio_usuario.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Eliminar cambio anotado"
capacidad: "cambios.cambio_usuario.gestionar"
pantallas_principales: ["cambios.pantalla.avisos_generar"]
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

1. Marcar una o más filas del listado de avisos.
2. Confirmar borrado.
3. El listado se refresca sin las filas eliminadas.

## Errores Conocidos

- `Hay un error, no se ha eliminado`

## Ruta de menú

(vía pantalla lista de cambios — ver flujo `avisos_generar`)
