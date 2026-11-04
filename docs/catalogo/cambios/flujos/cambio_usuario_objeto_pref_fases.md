---
id: "cambios.cambio_usuario_objeto_pref_fases.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Elegir la fase de un aviso"
capacidad: "cambios.cambio_usuario_objeto_pref_fases.gestionar"
pantallas_principales: ["cambios.pantalla.usuario_avisos_pref_fases"]
fragmentos: ["cambios.pantalla.usuario_avisos_pref_fases"]
acciones: ["obtener_datos"]
endpoints: ["/src/cambios/cambio_usuario_objeto_pref_fases_data"]
estado_revision: "revisado"
---

# Flujo - Actualizar fases de referencia

## Objetivo De Usuario

Actualizar el desplegable de fase/estado al cambiar el objeto o el tipo de actividad.

## Punto De Entrada

Menú de usuario > **preferencias** > **gestionar avisos en cambios actividades**.

## Escenarios Inferidos

### Elegir la fase de un aviso

Pasos propuestos:
1. Cambiar el objeto o el tipo de actividad.
2. El desplegable de fase se actualiza con las fases posibles.

Endpoints asociados:
- `/src/cambios/cambio_usuario_objeto_pref_fases_data`

## Errores Conocidos

- `primero debe elegir un objeto sobre el que mirar los cambios`

## Ruta de menú

sin entrada de menú en el índice
