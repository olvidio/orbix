---
id: "cambios.cambio_usuario_objeto_pref.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Guardar objeto de aviso"
capacidad: "cambios.cambio_usuario_objeto_pref.gestionar"
pantallas_principales: []
fragmentos: ["cambios.pantalla.usuario_avisos_pref"]
acciones: ["guardar"]
endpoints: ["/src/cambios/cambio_usuario_objeto_pref_guardar"]
estado_revision: "revisado"
---

# Flujo - Guardar objeto de aviso

## Objetivo De Usuario

Persistir la parte «objeto + tipo de actividad + fase + flags de aviso» de una preferencia.

## Punto De Entrada

Primer paso de `fnjs_grabar_todo` en `usuario_avisos_pref`.

## Errores Conocidos

- `falta id_usuario`, `id_tipo_activ invalido`, `Hay un error, no se ha guardado`

## Ruta de menú

sin entrada de menú en el índice
