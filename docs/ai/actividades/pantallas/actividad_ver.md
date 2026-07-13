---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividades"
titulo: "Ficha de actividad (ver/editar/nueva/cambiar tipo)"
pantalla: "actividades.pantalla.actividad_ver"
preguntas: ["Que se puede hacer en Ficha de actividad (ver/editar/nueva/cambiar tipo)?", "Que campos tiene Ficha de actividad (ver/editar/nueva/cambiar tipo)?", "Que acciones hay en Ficha de actividad (ver/editar/nueva/cambiar tipo)?"]
capacidades: ["actividades.actividad_ver.gestionar", "actividades.actividad_permiso_crear.gestionar", "actividades.actividad_status_labels.gestionar", "actividades.actividad_nivel_stgr_default.gestionar", "actividades.actividad_que.gestionar"]
endpoints: ["/src/actividades/actividad_ver_datos", "/src/actividades/actividad_status_labels_datos", "/src/actividades/actividad_nivel_stgr_default_datos", "/src/actividades/actividad_permiso_crear_datos", "/src/actividades/actividad_que_datos", "/src/actividades/actividad_fases_completadas_datos", "/src/actividades/actividad_nuevo", "/src/actividades/actividad_editar", "/src/actividades/actividad_cambiar_tipo", "/src/actividades/actividad_tipo_get"]
source: "docs/catalogo/actividades/pantallas/actividad_ver.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Ficha de actividad (ver/editar/nueva/cambiar tipo)

## Resumen

Formulario de la **ficha de actividad**, con tres modos segun `mod` / `id_activ`:

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.id_activ`
- `post.mod`
- `post.obj_pau`
- `post.refresh`
- `post.id_tipo_activ`
- `post.sasistentes`
- `post.sactividad`
- `post.sel`
- `form.nom_activ`
- `form.f_ini`
- `form.h_ini`
- `form.f_fin`
- `form.h_fin`
- `form.dl_org`
- `form.plazas`
- `form.id_ubi`
- `form.lugar_esp`
- `form.id_tarifa`
- `form.precio`
- `form.observ`
- `form.id_repeticion`
- `form.nivel_stgr`
- `form.publicado`
- `form.idioma`
- `form.status`

## Acciones Detectadas

- `fnjs_guardar`
- `fnjs_cambiar_ubi`
- `fnjs_generarNomActiv`
- `fnjs_asistentes`
- `fnjs_actividad`
- `fnjs_nom_tipo`
- `fnjs_act_id_activ`
- `fnjs_actualizar_nivel_stgr`

## Capacidades Relacionadas

- `actividades.actividad_ver.gestionar`
- `actividades.actividad_permiso_crear.gestionar`
- `actividades.actividad_status_labels.gestionar`
- `actividades.actividad_nivel_stgr_default.gestionar`
- `actividades.actividad_que.gestionar`

## Endpoints Relacionados

- `/src/actividades/actividad_ver_datos`
- `/src/actividades/actividad_status_labels_datos`
- `/src/actividades/actividad_nivel_stgr_default_datos`
- `/src/actividades/actividad_permiso_crear_datos`
- `/src/actividades/actividad_que_datos`
- `/src/actividades/actividad_fases_completadas_datos`
- `/src/actividades/actividad_nuevo`
- `/src/actividades/actividad_editar`
- `/src/actividades/actividad_cambiar_tipo`
- `/src/actividades/actividad_tipo_get`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
