---
id: "actividades.actividad_tipo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Gestionar Actividad Tipo"
capacidad: "actividades.actividad_tipo.gestionar"
pantallas_principales: ["actividades.pantalla.actividad_select_ubi"]
fragmentos: ["actividades.pantalla.actividad_que"]
acciones: ["obtener"]
endpoints: ["/src/actividades/actividad_tipo_get"]
estado_revision: "generado"
---

# Flujo - Gestionar Actividad Tipo

Propuesta generada automaticamente desde la capacidad `actividades.actividad_tipo.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ActividadTipoGetActividad, ActividadTipoGetAsistentes, ActividadTipoGetDlOrg, ActividadTipoGetFiltroLugar, ActividadTipoGetIdTarifa, ActividadTipoGetLugar, ActividadTipoGetNivelStgrDefecto, ActividadTipoGetNomTipo, ActividadTipoGetNomTipoTabla. Endpoint backend que devuelve el payload necesario (datos de desplegable, tabla HTML o valor escalar) segun el parametro POST salida.

## Punto De Entrada

- `actividades.pantalla.actividad_select_ubi`

## Fragmentos O Pantallas Auxiliares

- `actividades.pantalla.actividad_que`

## Escenarios Inferidos

### Obtener

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
- `form.frm_4_nombre_ubi`
- `form.id_tipo_activ`
- `form.id_ubi`
- `form.id_ubi_1`
- `form.isfsv`
- `form.lst_lugar`
- `form.modo`
- `form.nombre_ubi`
- `form.opcion_sel`
- `form.publicado`
- `form.salida`
- `form.selected`
- `form.sfsv`
- `form.tipo`
- `html.b_buscar`
- `post.dl_org`
- `post.empiezamax`
- `post.empiezamin`
- `post.extendida`
- `post.fases_off`
- `post.fases_on`
- `post.filtro_lugar`
- `post.id_tipo_activ`
- `post.id_ubi`
- `post.listar_asistentes`
- `post.modo`
- `post.nom_activ`
- `post.periodo`
- `post.publicado`
- `post.que`
- `post.sactividad`
- `post.sactividad2`
- `post.sasistentes`
- `post.snom_tipo`
- `post.stack`
- `post.status`
- `post.year`

Acciones JavaScript:
- `fnjs_buscar`
- `fnjs_cargar_desplegable`
- `fnjs_construir_desplegable`
- `fnjs_enviar_form`
- `fnjs_lugar`

## Endpoints Del Flujo

- `/src/actividades/actividad_tipo_get`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
