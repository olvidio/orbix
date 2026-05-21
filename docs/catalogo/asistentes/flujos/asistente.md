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
estado_revision: "generado"
---

# Flujo - Gestionar Asistente

Propuesta generada automaticamente desde la capacidad `asistentes.asistente.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona Asistente. Crea, edita o mueve un Asistente. Elimina un Asistente y sus matriculas.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

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

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
