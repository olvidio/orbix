---
id: "planning.planning_casa_que.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "planning"
nombre: "Flujo - Gestionar Planning Casa Que"
capacidad: "planning.planning_casa_que.gestionar"
pantallas_principales: []
fragmentos: ["planning.pantalla.planning_casa_que"]
acciones: ["obtener_datos"]
endpoints: ["/src/planning/planning_casa_que_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Planning Casa Que

Propuesta generada automaticamente desde la capacidad `planning.planning_casa_que.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona PlanningCasaQue. Dataset para montar CasasQue en {.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `planning.pantalla.planning_casa_que`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.cdc_sel`
- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.id_cdc_mas`
- `form.id_cdc_num`
- `form.modelo`
- `form.periodo`
- `form.sin_activ`
- `form.year`
- `html.modelo`
- `html.sin_activ`
- `post.cdc_sel`
- `post.empiezamax`
- `post.empiezamin`
- `post.periodo`
- `post.propuesta_calendario`
- `post.sSeleccionados`
- `post.sin_activ`
- `post.stack`
- `post.year`

Acciones JavaScript:
- `fnjs_comprobar_fecha`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_ver_planning`

## Endpoints Del Flujo

- `/src/planning/planning_casa_que_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
