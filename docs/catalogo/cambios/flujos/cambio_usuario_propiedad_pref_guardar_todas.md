---
id: "cambios.cambio_usuario_propiedad_pref_guardar_todas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Guardar los avisos"
capacidad: "cambios.cambio_usuario_propiedad_pref_guardar_todas.gestionar"
pantallas_principales: ["cambios.pantalla.usuario_avisos_pref"]
fragmentos: ["cambios.pantalla.usuario_avisos_pref"]
acciones: ["guardar"]
endpoints: ["/src/cambios/cambio_usuario_propiedad_pref_guardar_todas"]
estado_revision: "revisado"
---

# Flujo - Sincronizar propiedades

## Objetivo De Usuario

Crear, actualizar o quitar los campos vigilados según las casillas y condiciones marcadas.

## Punto De Entrada

Menú de usuario > **preferencias** > **gestionar avisos en cambios actividades**.

## Escenarios Inferidos

### Guardar los avisos

Pasos propuestos:
1. Marcar y condicionar los campos.
2. Pulsar grabar todo: se guardan el aviso y todos sus campos vigilados.

Endpoints asociados:
- `/src/cambios/cambio_usuario_propiedad_pref_guardar_todas`

## Errores Conocidos

- `faltan parametros`, `Hay un error, no se ha guardado`, `Hay un error, no se ha eliminado`

## Ruta de menú

sin entrada de menú en el índice
