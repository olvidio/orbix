---
id: "actividadestudios.asistente_plan_est_ok.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Asistente Plan Est Ok"
capacidad: "actividadestudios.asistente_plan_est_ok.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/actividadestudios/asistente_plan_est_ok"]
estado_revision: "revisado"
---

# Flujo - Gestionar Asistente Plan Est Ok

Confirmación del plan de estudios de un asistente (`est_ok`).

## Objetivo De Usuario

El usuario marca el plan de estudios de un asistente como confirmado (`est_ok`). Sustituye
al case `plan` de `update_3103.php`.

## Punto De Entrada

Fragmento `select_matriculas_de_una_persona` (dossier 1303): `fnjs_grabar_est` en
`frontend/actividadestudios/view/select_matriculas_de_una_persona.phtml` llama al endpoint
por AJAX.

## Fragmentos O Pantallas Auxiliares

- `frontend/actividadestudios/view/select_matriculas_de_una_persona.phtml` (dossier 1303)

## Escenarios Inferidos

### Ejecutar

Pasos:
1. En el dossier de matrículas de una persona (1303), marcar o confirmar el plan de estudios.
2. Pulsar la acción de confirmar plan (`fnjs_grabar_est`).
3. El sistema actualiza el flag `est_ok` del asistente y refresca el fragmento.

Endpoints asociados:
- `/src/actividadestudios/asistente_plan_est_ok`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado en metadatos del flujo.

Acciones JavaScript:
- `fnjs_grabar_est`

## Endpoints Del Flujo

- `/src/actividadestudios/asistente_plan_est_ok`

## Errores Conocidos

- ``falta id_activ o id_nom``
- ``hay un error, no se ha guardado``
- ``no encuentro al asistente``

## Ruta de menú

Sin entrada de menú en el índice (subflujo desde dossier 1303).

- **Legacy:** vest > buscar persona > n r/dl; stgr > personas > n r/dl.
- **Pills2:** PERSONAS > Numerarios > Buscar n de la r/dl.
