---
id: "cambios.cambio_usuario_propiedad_pref_preview.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Preview de condición"
capacidad: "cambios.cambio_usuario_propiedad_pref_preview.gestionar"
pantallas_principales: []
fragmentos: ["cambios.pantalla.usuario_avisos_pref"]
acciones: ["preview"]
endpoints: ["/src/cambios/cambio_usuario_propiedad_pref_preview"]
estado_revision: "revisado"
---

# Flujo - Preview de condición

## Objetivo De Usuario

Ver el texto de la condición y guardar el JSON en la fila de propiedades sin persistir aún en base de
datos (la persistencia ocurre al grabar todo).

## Punto De Entrada

`fnjs_guardar_cond` en el modal de condición.

## Ruta de menú

sin entrada de menú en el índice
