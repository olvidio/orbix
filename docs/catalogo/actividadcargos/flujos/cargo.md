---
id: "actividadcargos.cargo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadcargos"
nombre: "Flujo - Gestionar Cargo"
capacidad: "actividadcargos.cargo.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["crear", "eliminar"]
endpoints: ["/src/actividadcargos/cargo_eliminar", "/src/actividadcargos/cargo_nuevo"]
estado_revision: "generado"
---

# Flujo - Gestionar Cargo

Propuesta generada automaticamente desde la capacidad `actividadcargos.cargo.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Ver, añadir y quitar cargos de personas en una actividad (dossier 3102).

Plantilla de redacción revisada en `docs/manual/actividadcargos.md` (sección Cargo).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Crear

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/actividadcargos/cargo_eliminar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadcargos/cargo_eliminar`
- `/src/actividadcargos/cargo_nuevo`

## Errores Conocidos

- ``falta id_item``
- ``faltan parametros id_activ / id_nom / id_cargo``
- ``hay un error, no se ha eliminado``
- ``hay un error, no se ha eliminado el asistente``
- ``hay un error, no se ha guardado el asistente``
- ``no encuentro el cargo``
- ``ya existe este cargo para esta actividad``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
