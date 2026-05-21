---
id: "dossiers.tipo_dossier.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dossiers"
nombre: "Flujo - Gestionar Tipo Dossier"
capacidad: "dossiers.tipo_dossier.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["eliminar", "guardar"]
endpoints: ["/src/dossiers/tipo_dossier_eliminar", "/src/dossiers/tipo_dossier_guardar"]
estado_revision: "generado"
---

# Flujo - Gestionar Tipo Dossier

Propuesta generada automaticamente desde la capacidad `dossiers.tipo_dossier.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona TipoDossier. Elimina un TipoDossier. Guarda los cambios a un TipoDossier.

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
- `/src/dossiers/tipo_dossier_eliminar`

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

- `/src/dossiers/tipo_dossier_eliminar`
- `/src/dossiers/tipo_dossier_guardar`

## Errores Conocidos

- ``Hay un error, no se ha eliminado.``
- ``Hay un error, no se ha guardado.``
- ``falta id_tipo_dossier``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
