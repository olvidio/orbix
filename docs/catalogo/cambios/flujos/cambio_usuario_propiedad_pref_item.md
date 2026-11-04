---
id: "cambios.cambio_usuario_propiedad_pref_item.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Poner condición a un campo"
capacidad: "cambios.cambio_usuario_propiedad_pref_item.gestionar"
pantallas_principales: ["cambios.pantalla.usuario_avisos_pref_condicion"]
fragmentos: ["cambios.pantalla.usuario_avisos_pref_condicion"]
acciones: ["obtener_datos"]
endpoints: ["/src/cambios/cambio_usuario_propiedad_pref_item_data"]
estado_revision: "revisado"
---

# Flujo - Editar condición de propiedad

## Objetivo De Usuario

Abrir la ventana para definir operador, valor y alcance de un campo vigilado.

## Punto De Entrada

Menú de usuario > **preferencias** > **gestionar avisos en cambios actividades**.

## Escenarios Inferidos

### Poner condición a un campo

Pasos propuestos:
1. En la tabla de campos, pulsar modificar en el campo.
2. Elegir el operador y el valor y el alcance, y guardar la condición.

Endpoints asociados:
- `/src/cambios/cambio_usuario_propiedad_pref_item_data`

## Ruta de menú

sin entrada de menú en el índice
