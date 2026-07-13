---
id: "cambios.cambio_usuario_objeto_pref_fases.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Actualizar fases de referencia"
capacidad: "cambios.cambio_usuario_objeto_pref_fases.gestionar"
pantallas_principales: []
fragmentos: ["cambios.pantalla.usuario_avisos_pref_fases"]
acciones: ["obtener_datos"]
endpoints: ["/src/cambios/cambio_usuario_objeto_pref_fases_data"]
estado_revision: "revisado"
---

# Flujo - Actualizar fases de referencia

## Objetivo De Usuario

Refrescar el desplegable de fase/estado al cambiar objeto o tipo de actividad en el formulario de aviso.

## Punto De Entrada

`fnjs_actualizar_fases` → fragmento `usuario_avisos_pref_fases`.

## Errores Conocidos

- `primero debe elegir un objeto sobre el que mirar los cambios`

## Ruta de menú

sin entrada de menú en el índice
