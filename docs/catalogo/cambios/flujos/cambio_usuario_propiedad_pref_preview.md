---
id: "cambios.cambio_usuario_propiedad_pref_preview.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Previsualizar una condición"
capacidad: "cambios.cambio_usuario_propiedad_pref_preview.gestionar"
pantallas_principales: ["cambios.pantalla.usuario_avisos_pref"]
fragmentos: ["cambios.pantalla.usuario_avisos_pref"]
acciones: ["preview"]
endpoints: ["/src/cambios/cambio_usuario_propiedad_pref_preview"]
estado_revision: "revisado"
---

# Flujo - Preview de condición

## Objetivo De Usuario

Ver el texto de la condición y dejarla en la tabla sin guardar todavía en la base de datos.

## Punto De Entrada

Menú de usuario > **preferencias** > **gestionar avisos en cambios actividades**.

## Escenarios Inferidos

### Previsualizar una condición

Pasos propuestos:
1. En la ventana de condición, pulsar guardar.
2. Se muestra el texto de la condición en la fila; no se guarda definitivamente hasta grabar todo.

Endpoints asociados:
- `/src/cambios/cambio_usuario_propiedad_pref_preview`

## Ruta de menú

sin entrada de menú en el índice
