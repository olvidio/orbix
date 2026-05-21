---
id: "planning.planning_persona_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "planning"
nombre: "Flujo - Gestionar Planning Persona Ver"
capacidad: "planning.planning_persona_ver.gestionar"
pantallas_principales: []
fragmentos: ["planning.pantalla.planning_persona_ver"]
acciones: ["obtener_datos"]
endpoints: ["/src/planning/planning_persona_ver_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Planning Persona Ver

Propuesta generada automaticamente desde la capacidad `planning.planning_persona_ver.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona PlanningPersonaVer. Actividades por persona (vista plana) para planning_persona_ver.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `planning.pantalla.planning_persona_ver`

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
- `post.obj_pau`
- `post.periodo`
- `post.year`

Acciones JavaScript:
- `fnjs_exportar`

## Endpoints Del Flujo

- `/src/planning/planning_persona_ver_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
