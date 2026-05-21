---
id: "actividadestudios.matricula_editar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Matricula Editar"
capacidad: "actividadestudios.matricula_editar.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.form_matriculas_de_una_persona"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadestudios/matricula_editar"]
estado_revision: "generado"
---

# Flujo - Gestionar Matricula Editar

Propuesta generada automaticamente desde la capacidad `actividadestudios.matricula_editar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona MatriculaEditar. Edita una matricula.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.form_matriculas_de_una_persona`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_nom`
- `html.id_asignatura`
- `html.preceptor`
- `post.id_activ`
- `post.id_asignatura`
- `post.id_nivel`
- `post.id_pau`
- `post.sel`

Acciones JavaScript:
- `fnjs_cmb_opcional`
- `fnjs_cmb_preceptor`
- `fnjs_construir_desplegable`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/actividadestudios/matricula_editar`

## Errores Conocidos

- ``faltan claves de la matricula``
- ``hay un error, no se ha guardado``
- ``no encuentro la matricula``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
