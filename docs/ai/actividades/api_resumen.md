---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "actividades"
endpoints: 32
estado_revision: "revisado_parcial"
---

# Resumen API Para Ayuda IA - actividades

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## Notas de revision (tanda 1, jun 2026)

Revisados en profundidad los endpoints de la ficha de actividad (`actividad_*`,
CRUD/fases/permisos). Correcciones de semantica respecto a lo autogenerado:

- **Operacion real**: `actividad_ver_datos`, `actividad_que_datos`,
  `actividad_que_filtros`, `actividad_tipo_get`, `actividad_status_labels_datos`,
  `actividad_nivel_stgr_default_datos`, `actividad_permiso_crear_datos`,
  `actividad_fase_completada_datos` y `actividad_fases_completadas_datos` son
  **consultas** (el generador las marcaba como mutacion).
- **Permisos en servidor**: solo `actividad_nuevo` (crear, via `getPermisoCrear`)
  y `actividad_eliminar` (`borrar` por actividad) validan con `procesos`
  instalada; `actividad_editar`, `actividad_cambiar_tipo`, `actividad_publicar`,
  `actividad_importar` y `actividad_duplicar` confian en el control de la UI.
- **Entrada muerta**: `post.tipo_horario` en `actividad_nuevo` se lee pero no se
  usa (el legacy original si lo guardaba).
- **Endpoint sin consumidor**: `actividad_fase_completada_datos` no tiene
  llamadas en `frontend/` ni `src/` (API de paridad; consulta unitaria de
  `actividad_fases_completadas_datos`).
- **Regresion latente UI**: `_actividad_form_body.html.twig` referencia la clase
  legacy `actividades\model\value_objects\StatusId` en la rama sin `procesos`;
  esa clase ya no existe (fallaria sin la app `procesos`).
- `actividad_eliminar` no siempre borra: PROYECTO propia ⇒ borrado fisico; resto
  propia/ex ⇒ status BORRABLE; importada de otra dl ⇒ solo quita la importacion.
- `actividad_duplicar` solo duplica la **primera** seleccion (copia `dup <nombre>`
  en PROYECTO, solo de la propia dl, o `mi_dele+'f'` con perm oficina `des`).
- `actividad_nuevo` con `dl_org` externa: alta en tabla `ex` (ACTUAL, publicada)
  + registro `Importada`; rechaza dl que ya usan Orbix.
- Detalle por endpoint: ver `docs/catalogo/actividades/api/*.md`
  (`estado_revision: revisado` en los 16 de la tanda 1).

## `/src/actividades/actividad_cambiar_tipo`

- Id: `actividades.actividad_cambiar_tipo`
- Semantica: mutacion; cambia tipo + datos de una actividad de la propia dl y regenera proceso (reset de fases). Sin permisos en servidor.
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_cambiar_tipo.php`
- Entrada: `post.desc_activ:string`, `post.dl_org:string`, `post.f_fin:string`, `post.f_ini:string`, `post.h_fin:string`, `post.h_ini:string`, `post.iactividad_val:integer`, `post.iasistentes_val:integer`, `post.id_activ:integer`, `post.id_repeticion:integer`, `post.id_tarifa:integer`, `post.id_tipo_activ:integer`, `post.id_ubi:integer`, `post.inom_tipo_val:string`, `post.isfsv_val:integer`, `post.lugar_esp:string`, `post.nivel_stgr:integer`, `post.nom_activ:string`, `post.num_asistentes:integer`, `post.observ:string`, `post.observ_material:string`, `post.plazas:integer`, `post.precio:mixed`, `post.status:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_duplicar`

- Id: `actividades.actividad_duplicar`
- Semantica: mutacion; duplica solo `sel[0]` como `dup <nombre>` en PROYECTO (solo propia dl; `des` amplia a `mi_dele+'f'`).
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_duplicar.php`
- Entrada: `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_editar`

- Id: `actividades.actividad_editar`
- Semantica: mutacion; guarda la ficha; regenera proceso si cambia dl_org desde/hacia la propia; propaga plazas. No valida `modificar` en servidor (control UI).
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_editar.php`
- Entrada: `post.desc_activ:string`, `post.dl_org:string`, `post.f_fin:string`, `post.f_ini:string`, `post.h_fin:string`, `post.h_ini:string`, `post.iactividad_val:integer`, `post.iasistentes_val:integer`, `post.id_activ:integer`, `post.id_repeticion:integer`, `post.id_tarifa:integer`, `post.id_tipo_activ:integer`, `post.id_ubi:integer`, `post.idioma:string`, `post.inom_tipo_val:string`, `post.isfsv_val:integer`, `post.lugar_esp:string`, `post.nivel_stgr:integer`, `post.nom_activ:string`, `post.num_asistentes:integer`, `post.observ:string`, `post.observ_material:string`, `post.plazas:integer`, `post.precio:mixed`, `post.publicado:string`, `post.status:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_eliminar`

- Id: `actividades.actividad_eliminar`
- Semantica: mutacion; PROYECTO propia ⇒ borra; resto ⇒ marca BORRABLE; importada de otra dl ⇒ quita Importada. Con `procesos` valida permiso `borrar` por actividad.
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_eliminar.php`
- Entrada: `post.id_activ:integer`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_fase_completada_datos`

- Id: `actividades.actividad_fase_completada_datos`
- Semantica: consulta; `{completada: bool}` para una fase. **Sin consumidor actual** (API de paridad). Acepta GET como fallback.
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_fase_completada_datos.php`
- Entrada: `post.id_activ:integer`, `post.id_fase:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_fases_completadas_datos`

- Id: `actividades.actividad_fases_completadas_datos`
- Semantica: consulta; `{fases_completadas: list<int>}`; prefill de `PermisosActividades::setFasesCompletadas` (helper `PrefillPermActividadesFases`).
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_fases_completadas_datos.php`
- Entrada: `post.id_activ:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_importar`

- Id: `actividades.actividad_importar`
- Semantica: mutacion; crea `Importada` por cada `sel[]` y regenera proceso; avisos en `data.avisos`. Sin permisos en servidor.
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_importar.php`
- Entrada: `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_nivel_stgr_default_datos`

- Id: `actividades.actividad_nivel_stgr_default_datos`
- Semantica: consulta estatica; `{nivel_stgr_default}`: est/semestre ⇒ 2, repaso ⇒ 4, resto ⇒ 9.
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_nivel_stgr_default_datos.php`
- Entrada: `post.id_tipo_activ:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_nuevo`

- Id: `actividades.actividad_nuevo`
- Semantica: mutacion; alta en tabla dl (propia) o ex+Importada (externa, forzada ACTUAL/publicada); con `procesos` valida `getPermisoCrear`. `tipo_horario` es entrada muerta.
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_nuevo.php`
- Entrada: `post.desc_activ:string`, `post.dl_org:string`, `post.f_fin:string`, `post.f_ini:string`, `post.h_fin:string`, `post.h_ini:string`, `post.id_repeticion:integer`, `post.id_tarifa:integer`, `post.id_tipo_activ:integer`, `post.id_ubi:integer`, `post.idioma:string`, `post.inom_tipo_val:string`, `post.lugar_esp:string`, `post.nivel_stgr:string`, `post.nom_activ:string`, `post.num_asistentes:integer`, `post.observ:string`, `post.observ_material:string`, `post.plazas:integer`, `post.precio:mixed`, `post.publicado:string`, `post.status:integer`, `post.tipo_horario:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_nuevo_curso_ejecutar`

- Id: `actividades.actividad_nuevo_curso_ejecutar`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_nuevo_curso_ejecutar.php`
- Entrada: `post.ver_lista:string`, `post.year:integer`, `post.year_ref:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_permiso_crear_datos`

- Id: `actividades.actividad_permiso_crear_datos`
- Semantica: consulta sobre sesion; `{permiso_crear: {of_responsable_txt, status}|false, aviso}` segun `dl_propia` (`'f'/'0'/'false'` ⇒ externa). Acepta GET como fallback.
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_permiso_crear_datos.php`
- Entrada: `post.dl_propia:string`, `post.id_tipo_activ:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_publicar`

- Id: `actividades.actividad_publicar`
- Semantica: mutacion; `publicado=true` por cada `sel[]` (idempotente; ids inexistentes ignorados). Sin permisos en servidor.
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_publicar.php`
- Entrada: `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_que_datos`

- Id: `actividades.actividad_que_datos`
- Semantica: consulta; `{actividad_tipo_html}` = bloque del selector de tipo (cascada) renderizado; permisos de sesion filtran opciones de asistentes.
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_que_datos.php`
- Entrada: `post.extendida:mixed`, `post.id_tipo_activ:mixed`, `post.para:string`, `post.perm_jefe:mixed`, `post.que:string`, `post.sactividad:string`, `post.sactividad2:string`, `post.sasistentes:string`, `post.sfsv:string`, `post.sfsv_all:mixed`, `post.snom_tipo:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_que_filtros`

- Id: `actividades.actividad_que_filtros`
- Semantica: consulta; `{html}` = filas de filtros lugar/organiza/publicada segun `modo` (buscar/importar/publicar); vacio para roles PAU `ctr`.
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_que_filtros.php`
- Entrada: `post.dl_org:string`, `post.filtro_lugar:string`, `post.id_ubi:integer`, `post.modo:string`, `post.publicado:integer`, `post.sfsv:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_select_datos`

- Id: `actividades.actividad_select_datos`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_select_datos.php`
- Entrada: `post.continuar:string`, `post.dl_org:string`, `post.empiezamax:string`, `post.empiezamin:string`, `post.fases_off:array`, `post.fases_on:array`, `post.filtro_lugar:string`, `post.id_tipo_activ:string`, `post.id_ubi:integer`, `post.modo:string`, `post.nom_activ:string`, `post.periodo:string`, `post.publicado:integer`, `post.sactividad:string`, `post.sactividad2:string`, `post.sasistentes:string`, `post.scroll_id:string`, `post.sel:array`, `post.ssfsv:string`, `post.stack_go:integer`, `post.status:integer`, `post.year:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_select_ubi_desplegable`

- Id: `actividades.actividad_select_ubi_desplegable`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_select_ubi_desplegable.php`
- Entrada: `post.dl_org:string`, `post.isfsv:integer`, `post.tipo:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_status_labels_datos`

- Id: `actividades.actividad_status_labels_datos`
- Semantica: consulta; `{id_to_label}`: 1 proyecto, 2 actual, 3 terminada, 4 borrable (+9 cualquiera con `with_all='t'`).
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_status_labels_datos.php`
- Entrada: `post.with_all:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_tipo_get`

- Id: `actividades.actividad_tipo_get`
- Semantica: consulta multiplexada por `salida`: desplegables (asistentes, actividad, nom_tipo, lugar, dl_org, filtro_lugar ⇒ payload `{id, opciones, selected, blanco, val_blanco, action}`) o contenido (nom_tipo_tabla, id_tarifa, nivel_stgr_defecto ⇒ `{content}`); `entrada` = prefijo del tipo con `.` como comodin.
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_tipo_get.php`
- Entrada: `post.entrada:string`, `post.extendida:string`, `post.isfsv:integer`, `post.modo:string`, `post.opcion_sel:string`, `post.salida:string`, `post.ssfsv:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_ver_datos`

- Id: `actividades.actividad_ver_datos`
- Semantica: consulta; `id_activ>0` ⇒ `{entidad, html_despl_*, nombre_ubi, ssfsv…}` con valores reales; si no, desplegables con los valores recibidos (+`tarifa_inicial` con `calc_tarifa_inicial=1`). Sin permisos (el corte esta en `actividad_ver.php`).
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_ver_datos.php`
- Entrada: `post.Bdl:string`, `post.calc_tarifa_inicial:integer`, `post.dl_org:string`, `post.id_activ:integer`, `post.id_repeticion:integer`, `post.id_tipo_activ:string`, `post.id_ubi:integer`, `post.idioma:string`, `post.isfsv:integer`, `post.lugar_esp:string`, `post.nivel_stgr:mixed`, `post.tarifa:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/calendario_listas_datos`

- Id: `actividades.calendario_listas_datos`
- Controller: `src/actividades/infrastructure/ui/http/controllers/calendario_listas_datos.php`
- Entrada: `post.empiezamax:string`, `post.empiezamin:string`, `post.id_cdc:array`, `post.periodo:string`, `post.que:string`, `post.ver_ctr:string`, `post.year:string`, `post.yeardefault:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/lista_activ_datos`

- Id: `actividades.lista_activ_datos`
- Controller: `src/actividades/infrastructure/ui/http/controllers/lista_activ_datos.php`
- Entrada: `post.asist:array`, `post.c_activ:array`, `post.dl_org:string`, `post.empiezamax:string`, `post.empiezamin:string`, `post.filtro_lugar:string`, `post.id_tipo_activ:string`, `post.id_ubi:integer`, `post.periodo:string`, `post.que:string`, `post.sactividad:string`, `post.sasistentes:string`, `post.seccion:array`, `post.snom_tipo:string`, `post.ssfsv:string`, `post.status:mixed`, `post.titulo:string`, `post.year:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/lista_actividades_sg_datos`

- Id: `actividades.lista_actividades_sg_datos`
- Controller: `src/actividades/infrastructure/ui/http/controllers/lista_actividades_sg_datos.php`
- Entrada: `post.continuar:string`, `post.dl_org:string`, `post.empiezamax:string`, `post.empiezamin:string`, `post.id_ubi:integer`, `post.periodo:string`, `post.scroll_id:string`, `post.sel:array`, `post.stack_go:integer`, `post.status:integer`, `post.tipo_activ_sg:string`, `post.year:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/lista_centros_activ_datos`

- Id: `actividades.lista_centros_activ_datos`
- Controller: `src/actividades/infrastructure/ui/http/controllers/lista_centros_activ_datos.php`
- Entrada: `post.empiezamax:string`, `post.empiezamin:string`, `post.id_ctr:array`, `post.id_ctr_num:integer`, `post.periodo:string`, `post.year:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/lista_sr_csv_datos`

- Id: `actividades.lista_sr_csv_datos`
- Controller: `src/actividades/infrastructure/ui/http/controllers/lista_sr_csv_datos.php`
- Entrada: `post.c_activ:array`, `post.dl_org:string`, `post.empiezamax:string`, `post.empiezamin:string`, `post.id_cdc:array`, `post.periodo:string`, `post.status:array`, `post.year:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/lista_sr_csv_que_datos`

- Id: `actividades.lista_sr_csv_que_datos`
- Controller: `src/actividades/infrastructure/ui/http/controllers/lista_sr_csv_que_datos.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/tipo_activ_eliminar`

- Id: `actividades.tipo_activ_eliminar`
- Controller: `src/actividades/infrastructure/ui/http/controllers/tipo_activ_eliminar.php`
- Entrada: `post.id_tipo_activ:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/tipo_activ_form_modificar`

- Id: `actividades.tipo_activ_form_modificar`
- Controller: `src/actividades/infrastructure/ui/http/controllers/tipo_activ_form_modificar.php`
- Entrada: `post.id_tipo_activ:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/tipo_activ_form_nuevo`

- Id: `actividades.tipo_activ_form_nuevo`
- Controller: `src/actividades/infrastructure/ui/http/controllers/tipo_activ_form_nuevo.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/tipo_activ_lista`

- Id: `actividades.tipo_activ_lista`
- Controller: `src/actividades/infrastructure/ui/http/controllers/tipo_activ_lista.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/tipo_activ_metadata`

- Id: `actividades.tipo_activ_metadata`
- Controller: `src/actividades/infrastructure/ui/http/controllers/tipo_activ_metadata.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/tipo_activ_nuevo`

- Id: `actividades.tipo_activ_nuevo`
- Controller: `src/actividades/infrastructure/ui/http/controllers/tipo_activ_nuevo.php`
- Entrada: `post.iactividad_val:string`, `post.iasistentes_val:string`, `post.id_nom_tipo_activ:string`, `post.isfsv_val:string`, `post.nom_tipo_activ:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/tipo_activ_update`

- Id: `actividades.tipo_activ_update`
- Controller: `src/actividades/infrastructure/ui/http/controllers/tipo_activ_update.php`
- Entrada: `post.id_tipo_activ:integer`, `post.nom_tipo_activ:string`
- Respuesta: `standard_envelope_string_data`
