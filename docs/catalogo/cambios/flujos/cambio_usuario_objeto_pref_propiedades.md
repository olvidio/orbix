---
id: "cambios.cambio_usuario_objeto_pref_propiedades.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Elegir qué campos vigilar"
capacidad: "cambios.cambio_usuario_objeto_pref_propiedades.gestionar"
pantallas_principales: ["cambios.pantalla.usuario_avisos_pref_propiedades"]
fragmentos: ["cambios.pantalla.usuario_avisos_pref_propiedades"]
acciones: ["obtener_datos"]
endpoints: ["/src/cambios/cambio_usuario_objeto_pref_propiedades_data"]
estado_revision: "revisado"
---

# Flujo - Cargar propiedades vigilables

## Objetivo De Usuario

Mostrar los campos del objeto que se pueden vigilar, con el estado guardado.

## Punto De Entrada

Menú de usuario > **preferencias** > **gestionar avisos en cambios actividades**.

## Escenarios Inferidos

### Elegir qué campos vigilar

Pasos propuestos:
1. Elegir el objeto.
2. Aparece la tabla de campos; marcar los que se quieren vigilar.

Endpoints asociados:
- `/src/cambios/cambio_usuario_objeto_pref_propiedades_data`

## Errores Conocidos

- `Usuario no encontrado`, `Usuario sin rol asignado`, `objeto %s no encontrado`

## Ruta de menú

sin entrada de menú en el índice
