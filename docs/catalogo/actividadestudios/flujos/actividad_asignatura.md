---
id: "actividadestudios.actividad_asignatura.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Actividad Asignatura"
capacidad: "actividadestudios.actividad_asignatura.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.form_asignaturas_de_una_actividad"]
acciones: ["crear", "eliminar"]
endpoints: ["/src/actividadestudios/actividad_asignatura_eliminar", "/src/actividadestudios/actividad_asignatura_nueva"]
estado_revision: "generado"
---

# Flujo - Gestionar Actividad Asignatura

Propuesta generada automaticamente desde la capacidad `actividadestudios.actividad_asignatura.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ActividadAsignatura. Crea una ActividadAsignatura (asignatura impartida en un ca) y abre el dossier 3005 de la actividad. Sustituye al case nuevo del antiguo update_3005.php dispatcher. Elimina una ActividadAsignatura (asignatura impartida en un ca). Sustituye al case eliminar del antiguo update_3005.php dispatcher.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.form_asignaturas_de_una_actividad`

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
- `/src/actividadestudios/actividad_asignatura_eliminar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_activ`
- `form.id_asignatura`
- `form.salida`
- `html.avis_profesor`
- `html.f_fin`
- `html.f_ini`
- `html.guardar`
- `html.mod`
- `html.tipo`
- `post.id_activ`
- `post.id_asignatura`
- `post.id_pau`
- `post.pau`
- `post.sel`

Acciones JavaScript:
- `fnjs_comprobar_fecha`
- `fnjs_construir_desplegable`
- `fnjs_guardar`
- `fnjs_mas_profes`

## Endpoints Del Flujo

- `/src/actividadestudios/actividad_asignatura_eliminar`
- `/src/actividadestudios/actividad_asignatura_nueva`

## Errores Conocidos

- ``faltan claves de la asignatura de actividad``
- ``hay un error, no se ha borrado``
- ``hay un error, no se ha creado``
- ``no encuentro la asignatura``
- ``sólo se puede eliminar una asignatura desde el dossier de la actividad``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
