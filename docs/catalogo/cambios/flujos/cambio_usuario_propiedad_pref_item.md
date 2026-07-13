---
id: "cambios.cambio_usuario_propiedad_pref_item.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Editar condición de propiedad"
capacidad: "cambios.cambio_usuario_propiedad_pref_item.gestionar"
pantallas_principales: []
fragmentos: ["cambios.pantalla.usuario_avisos_pref_condicion"]
acciones: ["obtener_datos"]
endpoints: ["/src/cambios/cambio_usuario_propiedad_pref_item_data"]
estado_revision: "revisado"
---

# Flujo - Editar condición de propiedad

## Objetivo De Usuario

Abrir el modal para definir operador, valor y alcance de un cambio en una propiedad concreta.

## Punto De Entrada

`fnjs_modificar` en la tabla de propiedades → `usuario_avisos_pref_condicion`.

## Ruta de menú

sin entrada de menú en el índice
