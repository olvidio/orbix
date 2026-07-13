---
id: "cambios.cambio_usuario_propiedad_pref_guardar_todas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Sincronizar propiedades"
capacidad: "cambios.cambio_usuario_propiedad_pref_guardar_todas.gestionar"
pantallas_principales: []
fragmentos: ["cambios.pantalla.usuario_avisos_pref"]
acciones: ["guardar"]
endpoints: ["/src/cambios/cambio_usuario_propiedad_pref_guardar_todas"]
estado_revision: "revisado"
---

# Flujo - Sincronizar propiedades

## Objetivo De Usuario

Tras guardar el objeto-pref, crear/actualizar/eliminar las `CambioUsuarioPropiedadPref` según los
checkboxes y condiciones del formulario.

## Punto De Entrada

Segundo paso de `fnjs_grabar_todo` en `usuario_avisos_pref`.

## Errores Conocidos

- `faltan parametros`, `Hay un error, no se ha guardado`, `Hay un error, no se ha eliminado`

## Ruta de menú

sin entrada de menú en el índice
