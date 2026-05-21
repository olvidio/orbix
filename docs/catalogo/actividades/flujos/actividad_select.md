---
id: "actividades.actividad_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Gestionar Actividad Select"
capacidad: "actividades.actividad_select.gestionar"
pantallas_principales: []
fragmentos: ["actividades.pantalla.actividad_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/actividad_select_datos"]
estado_revision: "generado"
---

# Flujo - Gestionar Actividad Select

Propuesta generada automaticamente desde la capacidad `actividades.actividad_select.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ActividadSelectListado. JSON del listado para actividad_select: filtros POST → {.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividades.pantalla.actividad_select`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_dossier`
- `form.mod`
- `form.queSel`
- `html.b_buscar`
- `html.id_dossier`
- `html.mod`
- `html.queSel`
- `post.Gstack`
- `post.continuar`
- `post.dl_org`
- `post.empiezamax`
- `post.empiezamin`
- `post.fases_off`
- `post.fases_on`
- `post.filtro_lugar`
- `post.id_tipo_activ`
- `post.id_ubi`
- `post.modo`
- `post.nom_activ`
- `post.periodo`
- `post.publicado`
- `post.sactividad`
- `post.sactividad2`
- `post.sasistentes`
- `post.scroll_id`
- `post.sel`
- `post.ssfsv`
- `post.stack`
- `post.status`
- `post.year`

Acciones JavaScript:
- `button:. _(`
- `fnjs_borrar`
- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_solo_uno`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/actividades/actividad_select_datos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
