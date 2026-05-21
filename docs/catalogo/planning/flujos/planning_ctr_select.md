---
id: "planning.planning_ctr_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "planning"
nombre: "Flujo - Gestionar Planning Ctr Select"
capacidad: "planning.planning_ctr_select.gestionar"
pantallas_principales: []
fragmentos: ["planning.pantalla.planning_ctr_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/planning/planning_ctr_select_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Planning Ctr Select

Propuesta generada automaticamente desde la capacidad `planning.planning_ctr_select.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona PlanningCtrSelect. Personas + actividades agrupadas por centro para planning_ctr_select.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `planning.pantalla.planning_ctr_select`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.ctr`
- `post.empiezamax`
- `post.empiezamin`
- `post.modelo`
- `post.periodo`
- `post.sacd`
- `post.tipo`
- `post.todos_agd`
- `post.todos_n`
- `post.todos_s`
- `post.year`

Acciones JavaScript:
- `fnjs_exportar`

## Endpoints Del Flujo

- `/src/planning/planning_ctr_select_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
