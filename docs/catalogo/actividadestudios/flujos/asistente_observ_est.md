---
id: "actividadestudios.asistente_observ_est.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Asistente Observ Est"
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

El usuario guarda las observaciones de plan de estudios (`observ_est`) de un asistente
en su actividad vigente. Sustituye al case `observ_est` de `update_3103.php`.

## Punto De Entrada

Fragmento `select_matriculas_de_una_persona` (dossier 1303): `fnjs_grabar_observ` en
`frontend/actividadestudios/view/select_matriculas_de_una_persona.phtml` llama al endpoint
por AJAX al grabar observaciones de estudios.

## Fragmentos O Pantallas Auxiliares

- `frontend/actividadestudios/view/select_matriculas_de_una_persona.phtml` (dossier 1303)

## Escenarios Inferidos

### Ejecutar

Pasos:
1. En el dossier de matrículas de una persona (1303), editar el campo de observaciones de
   plan de estudios de un asistente.
2. Pulsar grabar observaciones (`fnjs_grabar_observ`).
3. El sistema envía el formulario serializado al endpoint y refresca el fragmento si tiene éxito.

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
