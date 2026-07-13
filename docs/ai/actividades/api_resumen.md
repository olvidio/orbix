---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "actividades"
endpoints: 32
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - actividades

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/actividades/actividad_cambiar_tipo`

- Id: `actividades.actividad_cambiar_tipo`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_cambiar_tipo.php`
- Entrada: `post.desc_activ:string`, `post.dl_org:string`, `post.f_fin:string`, `post.f_ini:string`, `post.h_fin:string`, `post.h_ini:string`, `post.iactividad_val:integer`, `post.iasistentes_val:integer`, `post.id_activ:integer`, `post.id_repeticion:integer`, `post.id_tarifa:integer`, `post.id_tipo_activ:integer`, `post.id_ubi:integer`, `post.inom_tipo_val:string`, `post.isfsv_val:integer`, `post.lugar_esp:string`, `post.nivel_stgr:integer`, `post.nom_activ:string`, `post.num_asistentes:integer`, `post.observ:string`, `post.observ_material:string`, `post.plazas:integer`, `post.precio:mixed`, `post.status:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_duplicar`

- Id: `actividades.actividad_duplicar`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_duplicar.php`
- Entrada: `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_editar`

- Id: `actividades.actividad_editar`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_editar.php`
- Entrada: `post.desc_activ:string`, `post.dl_org:string`, `post.f_fin:string`, `post.f_ini:string`, `post.h_fin:string`, `post.h_ini:string`, `post.iactividad_val:integer`, `post.iasistentes_val:integer`, `post.id_activ:integer`, `post.id_repeticion:integer`, `post.id_tarifa:integer`, `post.id_tipo_activ:integer`, `post.id_ubi:integer`, `post.idioma:string`, `post.inom_tipo_val:string`, `post.isfsv_val:integer`, `post.lugar_esp:string`, `post.nivel_stgr:integer`, `post.nom_activ:string`, `post.num_asistentes:integer`, `post.observ:string`, `post.observ_material:string`, `post.plazas:integer`, `post.precio:mixed`, `post.publicado:string`, `post.status:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_eliminar`

- Id: `actividades.actividad_eliminar`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_eliminar.php`
- Entrada: `post.id_activ:integer`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_fase_completada_datos`

- Id: `actividades.actividad_fase_completada_datos`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_fase_completada_datos.php`
- Entrada: `post.id_activ:integer`, `post.id_fase:integer`, `get.id_activ:integer`, `get.id_fase:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_fases_completadas_datos`

- Id: `actividades.actividad_fases_completadas_datos`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_fases_completadas_datos.php`
- Entrada: `post.id_activ:integer`, `get.id_activ:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_importar`

- Id: `actividades.actividad_importar`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_importar.php`
- Entrada: `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_nivel_stgr_default_datos`

- Id: `actividades.actividad_nivel_stgr_default_datos`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_nivel_stgr_default_datos.php`
- Entrada: `post.id_tipo_activ:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_nuevo`

- Id: `actividades.actividad_nuevo`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_nuevo.php`
- Entrada: `post.desc_activ:string`, `post.dl_org:string`, `post.f_fin:string`, `post.f_ini:string`, `post.h_fin:string`, `post.h_ini:string`, `post.id_repeticion:integer`, `post.id_tarifa:integer`, `post.id_tipo_activ:integer`, `post.id_ubi:integer`, `post.idioma:string`, `post.inom_tipo_val:string`, `post.lugar_esp:string`, `post.nivel_stgr:string`, `post.nom_activ:string`, `post.num_asistentes:integer`, `post.observ:string`, `post.observ_material:string`, `post.plazas:integer`, `post.precio:mixed`, `post.publicado:string`, `post.status:integer`, `post.tipo_horario:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_nuevo_curso_ejecutar`

- Id: `actividades.actividad_nuevo_curso_ejecutar`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_nuevo_curso_ejecutar.php`
- Entrada: `post.year_ref:integer`, `post.year:integer`, `post.ver_lista:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_permiso_crear_datos`

- Id: `actividades.actividad_permiso_crear_datos`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_permiso_crear_datos.php`
- Entrada: `post.id_tipo_activ:string`, `post.dl_propia:string`, `get.id_tipo_activ:string`, `get.dl_propia:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_publicar`

- Id: `actividades.actividad_publicar`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_publicar.php`
- Entrada: `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_que_datos`

- Id: `actividades.actividad_que_datos`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_que_datos.php`
- Entrada: `post.extendida:mixed`, `post.id_tipo_activ:mixed`, `post.para:string`, `post.perm_jefe:mixed`, `post.que:string`, `post.sactividad:string`, `post.sactividad2:string`, `post.sasistentes:string`, `post.sfsv:string`, `post.sfsv_all:mixed`, `post.snom_tipo:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_que_filtros`

- Id: `actividades.actividad_que_filtros`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_que_filtros.php`
- Entrada: `post.dl_org:string`, `post.filtro_lugar:string`, `post.id_ubi:integer`, `post.modo:string`, `post.publicado:integer`, `post.sfsv:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_select_datos`

- Id: `actividades.actividad_select_datos`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_select_datos.php`
- Entrada: `post.continuar:string`, `post.modo:string`, `post.status:integer`, `post.id_tipo_activ:string`, `post.filtro_lugar:string`, `post.id_ubi:integer`, `post.nom_activ:string`, `post.periodo:string`, `post.year:string`, `post.dl_org:string`, `post.empiezamin:string`, `post.empiezamax:string`, `post.fases_on:array`, `post.fases_off:array`, `post.publicado:integer`, `post.ssfsv:string`, `post.sasistentes:string`, `post.sactividad:string`, `post.sactividad2:string`, `post.sel:array`, `post.scroll_id:string`, `post.stack_go:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_select_ubi_desplegable`

- Id: `actividades.actividad_select_ubi_desplegable`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_select_ubi_desplegable.php`
- Entrada: `post.tipo:string`, `post.dl_org:string`, `post.isfsv:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_status_labels_datos`

- Id: `actividades.actividad_status_labels_datos`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_status_labels_datos.php`
- Entrada: `post.with_all:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_tipo_get`

- Id: `actividades.actividad_tipo_get`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_tipo_get.php`
- Entrada: `post.salida:string`, `post.entrada:string`, `post.extendida:string`, `post.modo:string`, `post.opcion_sel:string`, `post.isfsv:integer`, `post.ssfsv:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/actividad_ver_datos`

- Id: `actividades.actividad_ver_datos`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_ver_datos.php`
- Entrada: `post.Bdl:string`, `post.calc_tarifa_inicial:integer`, `post.dl_org:string`, `post.id_activ:integer`, `post.id_repeticion:integer`, `post.id_tipo_activ:string`, `post.id_ubi:integer`, `post.idioma:string`, `post.isfsv:integer`, `post.lugar_esp:string`, `post.nivel_stgr:mixed`, `post.tarifa:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/calendario_listas_datos`

- Id: `actividades.calendario_listas_datos`
- Controller: `src/actividades/infrastructure/ui/http/controllers/calendario_listas_datos.php`
- Entrada: `post.que:string`, `post.ver_ctr:string`, `post.periodo:string`, `post.year:string`, `post.yeardefault:string`, `post.empiezamin:string`, `post.empiezamax:string`, `post.id_cdc:array`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/lista_activ_datos`

- Id: `actividades.lista_activ_datos`
- Controller: `src/actividades/infrastructure/ui/http/controllers/lista_activ_datos.php`
- Entrada: `post.que:string`, `post.status:mixed`, `post.id_tipo_activ:string`, `post.filtro_lugar:string`, `post.id_ubi:integer`, `post.periodo:string`, `post.year:string`, `post.dl_org:string`, `post.empiezamin:string`, `post.empiezamax:string`, `post.c_activ:array`, `post.asist:array`, `post.seccion:array`, `post.ssfsv:string`, `post.sasistentes:string`, `post.sactividad:string`, `post.snom_tipo:string`, `post.titulo:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/lista_actividades_sg_datos`

- Id: `actividades.lista_actividades_sg_datos`
- Controller: `src/actividades/infrastructure/ui/http/controllers/lista_actividades_sg_datos.php`
- Entrada: `post.continuar:string`, `post.status:integer`, `post.tipo_activ_sg:string`, `post.id_ubi:integer`, `post.periodo:string`, `post.year:string`, `post.dl_org:string`, `post.empiezamin:string`, `post.empiezamax:string`, `post.sel:array`, `post.scroll_id:string`, `post.stack_go:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/lista_centros_activ_datos`

- Id: `actividades.lista_centros_activ_datos`
- Controller: `src/actividades/infrastructure/ui/http/controllers/lista_centros_activ_datos.php`
- Entrada: `post.id_ctr_num:integer`, `post.id_ctr:array`, `post.periodo:string`, `post.year:string`, `post.empiezamin:string`, `post.empiezamax:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/lista_sr_csv_datos`

- Id: `actividades.lista_sr_csv_datos`
- Controller: `src/actividades/infrastructure/ui/http/controllers/lista_sr_csv_datos.php`
- Entrada: `post.periodo:string`, `post.year:string`, `post.dl_org:string`, `post.empiezamin:string`, `post.empiezamax:string`, `post.c_activ:array`, `post.status:array`, `post.id_cdc:array`
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
- Entrada: `post.isfsv_val:string`, `post.iasistentes_val:string`, `post.iactividad_val:string`, `post.id_nom_tipo_activ:string`, `post.nom_tipo_activ:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividades/tipo_activ_update`

- Id: `actividades.tipo_activ_update`
- Controller: `src/actividades/infrastructure/ui/http/controllers/tipo_activ_update.php`
- Entrada: `post.id_tipo_activ:integer`, `post.nom_tipo_activ:string`
- Respuesta: `standard_envelope_string_data`
