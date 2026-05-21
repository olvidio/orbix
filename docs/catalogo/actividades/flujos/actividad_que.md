---
id: "actividades.actividad_que.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Gestionar Actividad Que"
capacidad: "actividades.actividad_que.gestionar"
pantallas_principales: []
fragmentos: ["actividades.pantalla.actividad_que", "actividades.pantalla.actividad_ver", "actividades.pantalla.planning_casa_modificar", "actividades.pantalla.planning_casa_nueva"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/actividad_que_datos"]
estado_revision: "generado"
---

# Flujo - Gestionar Actividad Que

Propuesta generada automaticamente desde la capacidad `actividades.actividad_que.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ActividadQueDatos. HTML del bloque tipo de actividad (desplegables) para actividad_que.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividades.pantalla.actividad_que`
- `actividades.pantalla.actividad_ver`
- `actividades.pantalla.planning_casa_modificar`
- `actividades.pantalla.planning_casa_nueva`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.dl_org`
- `form.dl_propia`
- `form.entrada`
- `form.extendida`
- `form.filtro_lugar`
- `form.id_tipo_activ`
- `form.id_ubi`
- `form.isfsv`
- `form.modo`
- `form.opcion_sel`
- `form.publicado`
- `form.salida`
- `form.selected`
- `form.sfsv`
- `form.ssfsv`
- `post.dl_org`
- `post.empiezamax`
- `post.empiezamin`
- `post.extendida`
- `post.fases_off`
- `post.fases_on`
- `post.filtro_lugar`
- `post.id_activ`
- `post.id_tipo_activ`
- `post.id_ubi`
- `post.listar_asistentes`
- `post.mod`
- `post.modo`
- `post.nom_activ`
- `post.obj_pau`
- `post.periodo`
- `post.publicado`
- `post.que`
- `post.refresh`
- `post.sactividad`
- `post.sactividad2`
- `post.sasistentes`
- `post.sel`
- `post.snom_tipo`
- `post.stack`
- `post.status`
- `post.year`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividades/actividad_que_datos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
