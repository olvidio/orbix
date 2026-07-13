---
id: "asistentes.asistente.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Gestionar Asistente"
capacidad: "asistentes.asistente.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["eliminar", "guardar"]
endpoints: ["/src/asistentes/asistente_eliminar", "/src/asistentes/asistente_guardar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Asistente

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Alta, edición, eliminación y movimiento de asistencia a actividades.


## Punto De Entrada

Pantalla `form_asistentes_a_una_actividad, form_actividades_de_una_persona, asistente_mover` (`frontend/asistentes/controller/`).


## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/asistentes/asistente_eliminar`

### Guardar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/asistentes/asistente_eliminar`
- `/src/asistentes/asistente_guardar`

## Errores Conocidos

- ``falta id_activ_old``
- ``faltan parametros id_activ / id_nom``
- ``hay un error, no se ha eliminado``
- ``hay un error, no se ha guardado``
- ``los datos de asistencia los modifica la dl del asistente``

## Ruta de menú

- sin entrada de menú en el índice (acceso desde dossier actividad/persona, `actividad_que` o navegación embebida).
