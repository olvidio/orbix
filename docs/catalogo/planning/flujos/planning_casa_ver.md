---
id: "planning.planning_casa_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "planning"
nombre: "Flujo - Gestionar Planning Casa Ver"
capacidad: "planning.planning_casa_ver.gestionar"
pantallas_principales: []
fragmentos: ["planning.pantalla.planning_casa_ver"]
acciones: ["obtener_datos"]
endpoints: ["/src/planning/planning_casa_ver_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Planning Casa Ver

Propuesta generada automaticamente desde la capacidad `planning.planning_casa_ver.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona PlanningCasaVer. Dataset para {.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `planning.pantalla.planning_casa_ver`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.empiezamax`
- `post.empiezamin`
- `post.modelo`
- `post.periodo`
- `post.propuesta_calendario`
- `post.year`

Acciones JavaScript:
- `fnjs_exportar`

## Endpoints Del Flujo

- `/src/planning/planning_casa_ver_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
