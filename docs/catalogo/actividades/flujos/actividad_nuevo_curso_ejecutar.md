---
id: "actividades.actividad_nuevo_curso_ejecutar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Gestionar Actividad Nuevo Curso Ejecutar"
capacidad: "actividades.actividad_nuevo_curso_ejecutar.gestionar"
pantallas_principales: []
fragmentos: ["actividades.pantalla.actividad_nuevo_curso"]
acciones: ["ejecutar"]
endpoints: ["/src/actividades/actividad_nuevo_curso_ejecutar"]
estado_revision: "generado"
---

# Flujo - Gestionar Actividad Nuevo Curso Ejecutar

Propuesta generada automaticamente desde la capacidad `actividades.actividad_nuevo_curso_ejecutar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ActividadNuevoCursoEjecutar. Endpoint backend para actividad_nuevo_curso (ejecucion).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividades.pantalla.actividad_nuevo_curso`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.year`
- `form.year_ref`
- `html.ver_lista`
- `html.year`
- `html.year_ref`
- `post.ok`
- `post.ver_lista`
- `post.year`
- `post.year_ref`

Acciones JavaScript:
- `fnjs_enviar_formulario`

## Endpoints Del Flujo

- `/src/actividades/actividad_nuevo_curso_ejecutar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
