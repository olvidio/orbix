---
id: "actividadessacd.sacd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadessacd"
nombre: "Flujo - Gestionar Sacd"
capacidad: "actividadessacd.sacd.gestionar"
pantallas_principales: ["actividadessacd.pantalla.activ_sacd"]
fragmentos: []
acciones: ["eliminar"]
endpoints: ["/src/actividadessacd/sacd_eliminar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Sacd

Eliminación de un sacd encargado de una actividad.

## Objetivo De Usuario

El usuario quita un sacd ya asignado a una actividad. El sistema elimina el cargo (`ActividadCargo`)
y, si existe, la fila de asistencia asociada (`Asistencia`).

## Punto De Entrada

Pantalla `activ_sacd` (`frontend/actividadessacd/controller/activ_sacd.php`): al pulsar un sacd
asignado se abre el menú contextual; la opción **borrar** llama a `fnjs_orden(..., 'borrar')`, que
invoca este endpoint.

## Fragmentos O Pantallas Auxiliares

- `actividadessacd.pantalla.activ_sacd`

## Escenarios Inferidos

### Eliminar

Pasos:
1. En una actividad, pulsar un sacd ya asignado.
2. Elegir **borrar** en el menú contextual.
3. El sistema elimina cargo y asistencia (si aplica) y refresca la celda de sacd de la actividad.

Endpoints asociados:
- `/src/actividadessacd/sacd_eliminar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadessacd/sacd_eliminar`

## Errores Conocidos

- ``no se sabe cual borrar``
- ``hay un error, no se ha eliminado el cargo``
- ``hay un error, no se ha eliminado la asistencia``

## Ruta de menú

Se accede desde la pantalla `activ_sacd` (tipo según parámetro `tipo`):

- **Legacy:** dre > propuestas > asignar sacd (variantes por tipo de actividad).
- **Pills2:** ATENCIÓN SACD > Actividades > Asignar sacd a actividades (mismas variantes por `tipo`).
