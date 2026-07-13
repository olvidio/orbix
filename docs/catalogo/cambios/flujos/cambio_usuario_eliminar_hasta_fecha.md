---
id: "cambios.cambio_usuario_eliminar_hasta_fecha.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Purgar cambios hasta fecha"
capacidad: "cambios.cambio_usuario_eliminar_hasta_fecha.gestionar"
pantallas_principales: []
fragmentos: ["cambios.pantalla.avisos_generar"]
acciones: ["eliminar_hasta_fecha"]
endpoints: ["/src/cambios/cambio_usuario_eliminar_hasta_fecha"]
estado_revision: "revisado"
---

# Flujo - Purgar cambios hasta fecha

## Objetivo De Usuario

Eliminar en bloque todos los cambios anotados con fecha anterior o igual a la indicada.

## Punto De Entrada

Pantalla `avisos_generar` → `fnjs_borrar_hasta_fecha`.

## Escenarios

### Eliminar hasta fecha

1. Indicar fecha límite (`f_fin`).
2. Ejecutar purga.
3. Refrescar listado.

## Errores Conocidos

- `debe indicar la fecha`
- `Hay un error al eliminar los cambios hasta la fecha indicada`

## Ruta de menú

(vía pantalla lista de cambios)
