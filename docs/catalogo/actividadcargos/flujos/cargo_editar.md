---
id: "actividadcargos.cargo_editar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadcargos"
nombre: "Flujo - Gestionar Cargo Editar"
capacidad: "actividadcargos.cargo_editar.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/actividadcargos/cargo_editar"]
estado_revision: "generado"
---

# Flujo - Gestionar Cargo Editar

Propuesta generada automaticamente desde la capacidad `actividadcargos.cargo_editar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Guardar cambios en un cargo existente (tipo, AGD, observaciones y sincronización de asistente).

Plantilla de redacción revisada en `docs/manual/actividadcargos.md` (sección Cargo Editar).

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

- `/src/actividadcargos/cargo_editar`

## Errores Conocidos

- ``faltan parametros id_activ / id_nom / id_cargo``
- ``hay un error, no se ha eliminado el asistente``
- ``hay un error, no se ha guardado``
- ``hay un error, no se ha guardado el asistente``
- ``no encuentro el cargo``
- ``ya existe este cargo para esta actividad``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
