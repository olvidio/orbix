---
id: "actividadestudios.asistente_observ.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Guardar las observaciones de un asistente"
capacidad: "actividadestudios.asistente_observ.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/actividadestudios/asistente_observ"]
estado_revision: "revisado"
---

# Flujo - Gestionar Asistente Observ

Guardado del campo `observ` de un asistente en una actividad de estudios.

## Objetivo De Usuario

Guardar el texto de observaciones generales de un asistente en una actividad.

## Punto De Entrada

Ahora mismo no hay ningún botón que lo use en la pantalla actual: la acción existe en el sistema, pero no está enlazada desde el dossier de matrículas de la actividad.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados en `frontend/` actual.

## Escenarios Inferidos

### Guardar las observaciones de un asistente

Pasos propuestos:
1. Esta acción no se puede lanzar desde las pantallas actuales.
2. Las observaciones de un asistente se editan desde su ficha de asistencia (ver el apartado de asistentes).

Endpoints asociados:
- `/src/actividadestudios/asistente_observ`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado en frontend actual.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadestudios/asistente_observ`

## Errores Conocidos

- ``falta id_activ o id_nom``
- ``hay un error, no se ha guardado``
- ``no encuentro al asistente``

## Ruta de menú

Sin entrada de menú en el índice. Subflujo previsto desde dossier 3103 (matrículas de una
actividad), accesible vía búsqueda de actividad CA.

- **Legacy:** vsm > ca > buscar ca.
- **Pills2:** ACTIVIDADES > Buscar actividad > ca n.
