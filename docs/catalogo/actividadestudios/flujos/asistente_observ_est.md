---
id: "actividadestudios.asistente_observ_est.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Guardar las observaciones del plan de estudios"
capacidad: "actividadestudios.asistente_observ_est.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/actividadestudios/asistente_observ_est"]
estado_revision: "revisado"
---

# Flujo - Gestionar Asistente Observ Est

Guardado del campo `observ_est` (observaciones de plan de estudios) de un asistente.

## Objetivo De Usuario

Guardar las observaciones sobre el plan de estudios de un asistente en su actividad.

## Punto De Entrada

Dossier **Matrículas** de una persona: botón para grabar las observaciones de estudios.

## Fragmentos O Pantallas Auxiliares

- `frontend/actividadestudios/view/select_matriculas_de_una_persona.phtml` (dossier 1303)

## Escenarios Inferidos

### Guardar las observaciones del plan de estudios

Pasos propuestos:
1. En el dossier de matrículas de una persona, escribir el texto en las observaciones del plan de estudios.
2. Pulsar el botón para grabar las observaciones.
3. Si se guarda bien, la lista se refresca con el texto nuevo.

Endpoints asociados:
- `/src/actividadestudios/asistente_observ_est`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado en metadatos del flujo (campo `observ_est` en el fragmento dossier).

Acciones JavaScript:
- `fnjs_grabar_observ`

## Endpoints Del Flujo

- `/src/actividadestudios/asistente_observ_est`

## Errores Conocidos

- ``falta id_activ o id_nom``
- ``hay un error, no se ha guardado``
- ``no encuentro al asistente``

## Ruta de menú

Sin entrada de menú en el índice (subflujo desde dossier 1303 «matrículas de una persona»).

- **Legacy:** vest > buscar persona > n r/dl; stgr > personas > n r/dl.
- **Pills2:** PERSONAS > Numerarios > Buscar n de la r/dl; PERSONAS > Agregados > Buscar agd de la r/dl.
