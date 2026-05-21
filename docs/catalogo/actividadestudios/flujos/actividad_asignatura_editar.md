---
id: "actividadestudios.actividad_asignatura_editar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Actividad Asignatura Editar"
capacidad: "actividadestudios.actividad_asignatura_editar.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.form_asignaturas_de_una_actividad"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadestudios/actividad_asignatura_editar"]
estado_revision: "generado"
---

# Flujo - Gestionar Actividad Asignatura Editar

Propuesta generada automaticamente desde la capacidad `actividadestudios.actividad_asignatura_editar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ActividadAsignaturaEditar. Edita una ActividadAsignatura existente. Sustituye al case editar del antiguo update_3005.php dispatcher.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.form_asignaturas_de_una_actividad`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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

- `/src/actividadestudios/actividad_asignatura_editar`

## Errores Conocidos

- ``faltan claves de la asignatura de actividad``
- ``hay un error, no se ha guardado``
- ``no encuentro la asignatura``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
