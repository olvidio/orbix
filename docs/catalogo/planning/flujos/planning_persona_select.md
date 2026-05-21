---
id: "planning.planning_persona_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "planning"
nombre: "Flujo - Gestionar Planning Persona Select"
capacidad: "planning.planning_persona_select.gestionar"
pantallas_principales: []
fragmentos: ["planning.pantalla.planning_persona_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/planning/planning_persona_select_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Planning Persona Select

Propuesta generada automaticamente desde la capacidad `planning.planning_persona_select.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona PlanningPersonaSelect. Listado de personas para planning_persona_select.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `planning.pantalla.planning_persona_select`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.id_dossier`
- `html.modelo`
- `html.que`
- `post.apellido1`
- `post.apellido2`
- `post.centro`
- `post.empiezamax`
- `post.empiezamin`
- `post.id_sel`
- `post.na`
- `post.nombre`
- `post.obj_pau`
- `post.periodo`
- `post.scroll_id`
- `post.stack`
- `post.year`

Acciones JavaScript:
- `fnjs_actividades`
- `fnjs_enviar_formulario`
- `fnjs_planning_print`
- `fnjs_solo_uno`
- `fnjs_ver_planning`

## Endpoints Del Flujo

- `/src/planning/planning_persona_select_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
