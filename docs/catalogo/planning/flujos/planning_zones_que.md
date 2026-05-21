---
id: "planning.planning_zones_que.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "planning"
nombre: "Flujo - Gestionar Planning Zones Que"
capacidad: "planning.planning_zones_que.gestionar"
pantallas_principales: []
fragmentos: ["planning.pantalla.planning_zones_que"]
acciones: ["obtener_datos"]
endpoints: ["/src/planning/planning_zones_que_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Planning Zones Que

Propuesta generada automaticamente desde la capacidad `planning.planning_zones_que.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona PlanningZonesQue. Opciones de zona + comprobación de permiso para planning_zones_que.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `planning.pantalla.planning_zones_que`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.actividad`
- `form.id_zona`
- `form.trimestre`
- `form.year`
- `html.actividad`
- `html.id_zona`
- `html.trimestre`
- `post.actividad`
- `post.id_zona`
- `post.modo`
- `post.stack`
- `post.trimestre`
- `post.year`

Acciones JavaScript:
- `fnjs_enviar_formulario`
- `fnjs_ver_planning`

## Endpoints Del Flujo

- `/src/planning/planning_zones_que_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
