---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "ubis"
endpoints: 40
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - ubis

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/ubis/calendario_periodos_eliminar`

- Id: `ubis.calendario_periodos_eliminar`
- Controller: `src/ubis/infrastructure/ui/http/controllers/calendario_periodos_eliminar.php`
- Entrada: `post.id_item:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/calendario_periodos_form_periodo_data`

- Id: `ubis.calendario_periodos_form_periodo_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/calendario_periodos_form_periodo_data.php`
- Entrada: `post.id_item:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/calendario_periodos_get2_data`

- Id: `ubis.calendario_periodos_get2_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/calendario_periodos_get2_data.php`
- Entrada: `post.id_ubi:integer`, `post.year:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/calendario_periodos_get_data`

- Id: `ubis.calendario_periodos_get_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/calendario_periodos_get_data.php`
- Entrada: `post.id_ubi:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/calendario_periodos_guardar`

- Id: `ubis.calendario_periodos_guardar`
- Controller: `src/ubis/infrastructure/ui/http/controllers/calendario_periodos_guardar.php`
- Entrada: `post.f_fin:string`, `post.f_ini:string`, `post.id_item:integer`, `post.id_ubi:integer`, `post.sfsv:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/calendario_periodos_nuevo_data`

- Id: `ubis.calendario_periodos_nuevo_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/calendario_periodos_nuevo_data.php`
- Entrada: `post.id_ubi:integer`, `post.year:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/casas_opciones_data`

- Id: `ubis.casas_opciones_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/casas_opciones_data.php`
- Entrada: `post.active:mixed`, `post.id_ubi_in:mixed`, `post.sf:mixed`, `post.sv:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/centros_form_labor`

- Id: `ubis.centros_form_labor`
- Controller: `src/ubis/infrastructure/ui/http/controllers/centros_form_labor.php`
- Entrada: `post.id_ubi:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/centros_form_num`

- Id: `ubis.centros_form_num`
- Controller: `src/ubis/infrastructure/ui/http/controllers/centros_form_num.php`
- Entrada: `post.id_ubi:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/centros_form_plazas`

- Id: `ubis.centros_form_plazas`
- Controller: `src/ubis/infrastructure/ui/http/controllers/centros_form_plazas.php`
- Entrada: `post.id_ubi:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/centros_get_labor`

- Id: `ubis.centros_get_labor`
- Controller: `src/ubis/infrastructure/ui/http/controllers/centros_get_labor.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/centros_get_num`

- Id: `ubis.centros_get_num`
- Controller: `src/ubis/infrastructure/ui/http/controllers/centros_get_num.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/centros_get_plazas`

- Id: `ubis.centros_get_plazas`
- Controller: `src/ubis/infrastructure/ui/http/controllers/centros_get_plazas.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/centros_opciones_data`

- Id: `ubis.centros_opciones_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/centros_opciones_data.php`
- Entrada: `post.active:mixed`, `post.id_ubi_in:mixed`, `post.sf:mixed`, `post.sv:mixed`, `post.tipo_ctr:string`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/centros_update`

- Id: `ubis.centros_update`
- Controller: `src/ubis/infrastructure/ui/http/controllers/centros_update.php`
- Entrada: `post.id_ubi:integer`, `post.labor:string`, `post.n_buzon:integer`, `post.num_cartas:integer`, `post.num_habit_indiv:integer`, `post.num_pi:integer`, `post.plazas:integer`, `post.sede:string`, `post.tipo_ctr:string`, `post.tipo_labor:mixed`
- Respuesta: `raw_response`

## `/src/ubis/delegacion_que_data`

- Id: `ubis.delegacion_que_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/delegacion_que_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/delegaciones_region_stgr_data`

- Id: `ubis.delegaciones_region_stgr_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/delegaciones_region_stgr_data.php`
- Entrada: `post.region_stgr:string`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/direccion_update`

- Id: `ubis.direccion_update`
- Controller: `src/ubis/infrastructure/ui/http/controllers/direccion_update.php`
- Entrada: `post.a_p:string`, `post.c_p:string`, `post.cp_dcha:string`, `post.direccion:string`, `post.f_direccion:string`, `post.id_direccion:string`, `post.id_ubi:integer`, `post.idx:string`, `post.latitud:string`, `post.longitud:string`, `post.nom_sede:string`, `post.obj_dir:string`, `post.observ:string`, `post.pais:string`, `post.poblacion:string`, `post.principal:string`, `post.propietario:string`, `post.provincia:string`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/direcciones_asignar`

- Id: `ubis.direcciones_asignar`
- Controller: `src/ubis/infrastructure/ui/http/controllers/direcciones_asignar.php`
- Entrada: `post.id_direccion:integer`, `post.id_ubi:integer`, `post.obj_dir:string`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/direcciones_editar`

- Id: `ubis.direcciones_editar`
- Controller: `src/ubis/infrastructure/ui/http/controllers/direcciones_editar.php`
- Entrada: `post.id_direccion:string`, `post.id_ubi:integer`, `post.idx:integer`, `post.inc:string`, `post.mod:string`, `post.obj_dir:string`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/direcciones_que`

- Id: `ubis.direcciones_que`
- Controller: `src/ubis/infrastructure/ui/http/controllers/direcciones_que.php`
- Entrada: `post.id_ubi:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/direcciones_quitar`

- Id: `ubis.direcciones_quitar`
- Controller: `src/ubis/infrastructure/ui/http/controllers/direcciones_quitar.php`
- Entrada: `post.id_direccion:string`, `post.id_ubi:integer`, `post.idx:integer`, `post.obj_dir:string`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/direcciones_tabla`

- Id: `ubis.direcciones_tabla`
- Controller: `src/ubis/infrastructure/ui/http/controllers/direcciones_tabla.php`
- Entrada: `post.c_p:string`, `post.ciudad:string`, `post.id_ubi:integer`, `post.obj_dir:string`, `post.pais:string`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/home_ubis_data`

- Id: `ubis.home_ubis_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/home_ubis_data.php`
- Entrada: `post.id_ubi:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/list_ctr_data`

- Id: `ubis.list_ctr_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/list_ctr_data.php`
- Entrada: `post.id_sel:string`, `post.loc:string`, `post.que_lista:string`, `post.scroll_id:string`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/lista_ctrs_data`

- Id: `ubis.lista_ctrs_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/lista_ctrs_data.php`
- Entrada: `post.error:string`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/teleco_desc_lista`

- Id: `ubis.teleco_desc_lista`
- Controller: `src/ubis/infrastructure/ui/http/controllers/teleco_desc_lista.php`
- Entrada: `post.id_tipo_teleco:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/teleco_editar`

- Id: `ubis.teleco_editar`
- Controller: `src/ubis/infrastructure/ui/http/controllers/teleco_editar.php`
- Entrada: `post.id_ubi:integer`, `post.mod:string`, `post.obj_pau:string`, `post.s_pkey:string`, `post.sel:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/teleco_eliminar`

- Id: `ubis.teleco_eliminar`
- Controller: `src/ubis/infrastructure/ui/http/controllers/teleco_eliminar.php`
- Entrada: `post.obj_pau:string`, `post.sel:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/teleco_guardar`

- Id: `ubis.teleco_guardar`
- Controller: `src/ubis/infrastructure/ui/http/controllers/teleco_guardar.php`
- Entrada: `post.id_desc_teleco:integer`, `post.id_tipo_teleco:integer`, `post.id_ubi:integer`, `post.num_teleco:string`, `post.obj_pau:string`, `post.observ:string`, `post.s_pkey:string`, `post.sel:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/teleco_tabla`

- Id: `ubis.teleco_tabla`
- Controller: `src/ubis/infrastructure/ui/http/controllers/teleco_tabla.php`
- Entrada: `post.id_ubi:integer`, `post.obj_pau:string`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/trasladar_ubis`

- Id: `ubis.trasladar_ubis`
- Controller: `src/ubis/infrastructure/ui/http/controllers/trasladar_ubis.php`
- Entrada: `post.dl_dst:string`, `post.sel:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/ubis_buscar_data`

- Id: `ubis.ubis_buscar_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/ubis_buscar_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/ubis_editar_data`

- Id: `ubis.ubis_editar_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/ubis_editar_data.php`
- Entrada: `post.dl:string`, `post.obj_pau:string`, `post.region:string`, `post.tipo_ubi:string`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/ubis_editar_load_data`

- Id: `ubis.ubis_editar_load_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/ubis_editar_load_data.php`
- Entrada: `post.dl:string`, `post.id_ubi:integer`, `post.nombre_ubi:string`, `post.nuevo:string`, `post.obj_pau:string`, `post.region:string`, `post.tipo_ubi:string`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/ubis_editar_normalize_dl_data`

- Id: `ubis.ubis_editar_normalize_dl_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/ubis_editar_normalize_dl_data.php`
- Entrada: `post.id_ubi:integer`, `post.nombre_ubi:string`, `post.obj_pau:string`, `post.tipo_ubi:string`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/ubis_eliminar`

- Id: `ubis.ubis_eliminar`
- Controller: `src/ubis/infrastructure/ui/http/controllers/ubis_eliminar.php`
- Entrada: `post.id_ubi:integer`, `post.obj_pau:string`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/ubis_guardar`

- Id: `ubis.ubis_guardar`
- Controller: `src/ubis/infrastructure/ui/http/controllers/ubis_guardar.php`
- Entrada: `post.active:string`, `post.cdc:string`, `post.dl:string`, `post.id_ctr_padre:integer`, `post.id_ubi:integer`, `post.n_buzon:integer`, `post.nombre_ubi:string`, `post.num_cartas:integer`, `post.num_cartas_mensuales:integer`, `post.num_habit_indiv:integer`, `post.num_pi:integer`, `post.num_sacd:integer`, `post.obj_pau:string`, `post.observ:string`, `post.plazas:integer`, `post.plazas_min:integer`, `post.region:string`, `post.sf:string`, `post.sv:string`, `post.tipo_casa:string`, `post.tipo_ctr:string`, `post.tipo_labor:mixed`, `post.tipo_ubi:string`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/ubis_lista_data`

- Id: `ubis.ubis_lista_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/ubis_lista_data.php`
- Entrada: `post.error:string`, `post.nombre_ubi:string`
- Respuesta: `standard_envelope_string_data`

## `/src/ubis/ubis_tabla_data`

- Id: `ubis.ubis_tabla_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/ubis_tabla_data.php`
- Entrada: `post.error:string`
- Respuesta: `standard_envelope_string_data`
