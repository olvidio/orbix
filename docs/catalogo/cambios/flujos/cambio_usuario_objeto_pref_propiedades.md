---
id: "cambios.cambio_usuario_objeto_pref_propiedades.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Cargar propiedades vigilables"
capacidad: "cambios.cambio_usuario_objeto_pref_propiedades.gestionar"
pantallas_principales: []
fragmentos: ["cambios.pantalla.usuario_avisos_pref_propiedades"]
acciones: ["obtener_datos"]
endpoints: ["/src/cambios/cambio_usuario_objeto_pref_propiedades_data"]
estado_revision: "revisado"
---

# Flujo - Cargar propiedades vigilables

## Objetivo De Usuario

Mostrar la tabla de campos del objeto que pueden vigilarse, con el estado guardado preseleccionado.

## Punto De Entrada

`fnjs_actualizar_propiedades` tras elegir objeto o al cargar edición.

## Errores Conocidos

- `Usuario no encontrado`, `Usuario sin rol asignado`, `objeto %s no encontrado`

## Ruta de menú

sin entrada de menú en el índice
