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
estado_revision: "generado"
---

# Flujo - Gestionar Asistente Plan Est Ok

Propuesta generada automaticamente desde la capacidad `actividadestudios.asistente_plan_est_ok.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona AsistentePlanEstOk. Marca el flag est_ok (plan de estudios confirmado) de un Asistente. Sustituye al case plan de update_3103.php.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Ejecutar

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

- `/src/actividadestudios/asistente_plan_est_ok`

## Errores Conocidos

- ``falta id_activ o id_nom``
- ``hay un error, no se ha guardado``
- ``no encuentro al asistente``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
