---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "encargossacd"
endpoints: 34
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - encargossacd

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/encargossacd/comprobaciones_ctr`

- Id: `encargossacd.comprobaciones_ctr`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/comprobaciones_ctr.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/ctr_ficha_data`

- Id: `encargossacd.ctr_ficha_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/ctr_ficha_data.php`
- Entrada: `post.filtro_ctr:mixed`, `post.id_ubi:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/ctr_ficha_update`

- Id: `encargossacd.ctr_ficha_update`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/ctr_ficha_update.php`
- Entrada: `post.cl:mixed`, `post.dedic_ctr_m:string`, `post.dedic_ctr_t:string`, `post.dedic_ctr_v:string`, `post.dedic_m:mixed`, `post.dedic_t:mixed`, `post.dedic_v:mixed`, `post.e:integer`, `post.id_enc_:integer`, `post.id_sacd:mixed`, `post.id_sacd_suplente:integer`, `post.id_sacd_titular:integer`, `post.id_ubi_:integer`, `post.mod_:string`, `post.n_sacd:integer`, `post.num_alum:integer`, `post.observ:string`, `post.sacd_num:integer`, `post.tipo_centro_:string`
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/ctr_get_ficha_data`

- Id: `encargossacd.ctr_get_ficha_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/ctr_get_ficha_data.php`
- Entrada: `post.id_ubi:mixed`, `post.seleccion_sacd:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/ctr_get_select_data`

- Id: `encargossacd.ctr_get_select_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/ctr_get_select_data.php`
- Entrada: `post.filtro_ctr:mixed`, `post.id_ubi:mixed`, `post.id_zona:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/encargo_horario_select_data`

- Id: `encargossacd.encargo_horario_select_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/encargo_horario_select_data.php`
- Entrada: `post.id_enc:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/encargo_lst_tipo_enc_data`

- Id: `encargossacd.encargo_lst_tipo_enc_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/encargo_lst_tipo_enc_data.php`
- Entrada: `post.grupo:mixed`, `post.id_tipo_enc:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/encargo_select_data`

- Id: `encargossacd.encargo_select_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/encargo_select_data.php`
- Entrada: `post.desc_enc:mixed`, `post.id_tipo_enc:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/encargo_ver_data`

- Id: `encargossacd.encargo_ver_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/encargo_ver_data.php`
- Entrada: `post.desc_enc:mixed`, `post.desc_lugar:mixed`, `post.filtro_ctr:mixed`, `post.grupo:mixed`, `post.id_enc:mixed`, `post.id_tipo_enc:mixed`, `post.id_zona:mixed`, `post.que:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/encargo_ver_editar`

- Id: `encargossacd.encargo_ver_editar`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/encargo_ver_editar.php`
- Entrada: `post.desc_enc:string`, `post.desc_lugar:string`, `post.filtro_ctr:integer`, `post.id_enc:integer`, `post.id_tipo_enc:string`, `post.id_zona:integer`, `post.idioma_enc:string`, `post.lst_ctrs:integer`, `post.observ:string`
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/encargo_ver_eliminar`

- Id: `encargossacd.encargo_ver_eliminar`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/encargo_ver_eliminar.php`
- Entrada: `post.sel:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/encargo_ver_nuevo`

- Id: `encargossacd.encargo_ver_nuevo`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/encargo_ver_nuevo.php`
- Entrada: `post.desc_enc:string`, `post.desc_lugar:string`, `post.filtro_ctr:integer`, `post.grupo:string`, `post.id_tipo_enc:string`, `post.id_zona:integer`, `post.idioma_enc:string`, `post.lst_ctrs:integer`, `post.nom_tipo:string`, `post.observ:string`
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/horario_sacd_update_data`

- Id: `encargossacd.horario_sacd_update_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/horario_sacd_update_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/horario_sacd_ver_data`

- Id: `encargossacd.horario_sacd_ver_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/horario_sacd_ver_data.php`
- Entrada: `post.desc_enc:mixed`, `post.id_enc:mixed`, `post.id_item:mixed`, `post.id_nom:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/horario_update_data`

- Id: `encargossacd.horario_update_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/horario_update_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/horario_ver_data`

- Id: `encargossacd.horario_ver_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/horario_ver_data.php`
- Entrada: `post.id_enc:mixed`, `post.id_item_h:mixed`, `post.mod:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/listas_a_data`

- Id: `encargossacd.listas_a_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/listas_a_data.php`
- Entrada: `post.sf:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/listas_b_data`

- Id: `encargossacd.listas_b_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/listas_b_data.php`
- Entrada: `post.sf:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/listas_c_data`

- Id: `encargossacd.listas_c_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/listas_c_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/listas_cl_data`

- Id: `encargossacd.listas_cl_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/listas_cl_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/listas_com_ctr_data`

- Id: `encargossacd.listas_com_ctr_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/listas_com_ctr_data.php`
- Entrada: `post.sfsv:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/listas_com_sacd_data`

- Id: `encargossacd.listas_com_sacd_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/listas_com_sacd_data.php`
- Entrada: `post.sel:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/listas_com_txt_data`

- Id: `encargossacd.listas_com_txt_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/listas_com_txt_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/listas_com_txt_get`

- Id: `encargossacd.listas_com_txt_get`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/listas_com_txt_get.php`
- Entrada: `post.clave:mixed`, `post.idioma:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/listas_com_txt_update`

- Id: `encargossacd.listas_com_txt_update`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/listas_com_txt_update.php`
- Entrada: `post.clave:mixed`, `post.comunicacion:mixed`, `post.idioma:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/listas_d_data`

- Id: `encargossacd.listas_d_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/listas_d_data.php`
- Entrada: `post.sf:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/listas_exigencia_ctr_data`

- Id: `encargossacd.listas_exigencia_ctr_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/listas_exigencia_ctr_data.php`
- Entrada: `post.ctr_igl:mixed`, `post.sf:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/sacd_ausencias_get_data`

- Id: `encargossacd.sacd_ausencias_get_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/sacd_ausencias_get_data.php`
- Entrada: `post.historial:mixed`, `post.id_nom:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/sacd_ausencias_jefe_zona_data`

- Id: `encargossacd.sacd_ausencias_jefe_zona_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/sacd_ausencias_jefe_zona_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/sacd_ausencias_update`

- Id: `encargossacd.sacd_ausencias_update`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/sacd_ausencias_update.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/sacd_ficha_data`

- Id: `encargossacd.sacd_ficha_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/sacd_ficha_data.php`
- Entrada: `post.id_nom:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/sacd_ficha_update`

- Id: `encargossacd.sacd_ficha_update`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/sacd_ficha_update.php`
- Entrada: `post.dedic_m:mixed`, `post.dedic_t:mixed`, `post.dedic_v:mixed`, `post.enc_num:integer`, `post.id_enc:mixed`, `post.id_nom:integer`, `post.id_tipo_enc:mixed`, `post.observ:string`
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/sacd_select_data`

- Id: `encargossacd.sacd_select_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/sacd_select_data.php`
- Entrada: `post.filtro_sacd:mixed`, `post.id_nom:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/encargossacd/zonas_get_select_data`

- Id: `encargossacd.zonas_get_select_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/zonas_get_select_data.php`
- Entrada: `post.id_zona:mixed`
- Respuesta: `standard_envelope_string_data`
