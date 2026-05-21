---
id: "planning.planning_zones_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "planning"
nombre: "Flujo - Gestionar Planning Zones Select"
capacidad: "planning.planning_zones_select.gestionar"
pantallas_principales: []
fragmentos: ["planning.pantalla.planning_zones_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/planning/planning_zones_select_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Planning Zones Select

Propuesta generada automaticamente desde la capacidad `planning.planning_zones_select.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona PlanningZonesSelect. Dataset para {.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `planning.pantalla.planning_zones_select`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.actividad`
- `post.id_zona`
- `post.modelo`
- `post.propuesta`
- `post.trimestre`
- `post.year`

Acciones JavaScript:
- `fnjs_exportar`

## Endpoints Del Flujo

- `/src/planning/planning_zones_select_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
