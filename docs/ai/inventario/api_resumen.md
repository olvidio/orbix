---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "inventario"
endpoints: 43
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - inventario

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/inventario/cabecera_pie_txt`

- Id: `inventario.cabecera_pie_txt`
- Controller: `src/inventario/infrastructure/ui/http/controllers/cabecera_pie_txt.php`
- Entrada: `post.id_equipaje:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/cabecera_pie_txt_guardar`

- Id: `inventario.cabecera_pie_txt_guardar`
- Controller: `src/inventario/infrastructure/ui/http/controllers/cabecera_pie_txt_guardar.php`
- Entrada: `post.cabecera:string`, `post.cabeceraB:string`, `post.firma:string`, `post.pie:string`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/doc_asignar_ctr_guardar`

- Id: `inventario.doc_asignar_ctr_guardar`
- Controller: `src/inventario/infrastructure/ui/http/controllers/doc_asignar_ctr_guardar.php`
- Entrada: `post.f_asignado:string`, `post.f_recibido:string`, `post.id_tipo_doc:string`, `post.numerado:string`, `post.str_selected_id:string`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/doc_asignar_dlb_guardar`

- Id: `inventario.doc_asignar_dlb_guardar`
- Controller: `src/inventario/infrastructure/ui/http/controllers/doc_asignar_dlb_guardar.php`
- Entrada: `post.f_asignado:string`, `post.f_recibido:string`, `post.id_tipo_doc:string`, `post.numerado:string`, `post.str_selected_id:string`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/documentos_guardar`

- Id: `inventario.documentos_guardar`
- Controller: `src/inventario/infrastructure/ui/http/controllers/documentos_guardar.php`
- Entrada: `post.chk_eliminado:string`, `post.chk_f_asignado:string`, `post.chk_f_eliminado:string`, `post.chk_f_recibido:string`, `post.chk_num_fin:string`, `post.chk_num_ini:string`, `post.documentos:string`, `post.eliminado:integer`, `post.f_asignado:string`, `post.f_eliminado:string`, `post.f_recibido:string`, `post.num_fin:string`, `post.num_ini:string`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/equipajes_add_doc`

- Id: `inventario.equipajes_add_doc`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_add_doc.php`
- Entrada: `post.id_item_egm:integer`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/equipajes_del_doc`

- Id: `inventario.equipajes_del_doc`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_del_doc.php`
- Entrada: `post.id_item_egm:integer`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/equipajes_doc_casa`

- Id: `inventario.equipajes_doc_casa`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_doc_casa.php`
- Entrada: `post.id_equipaje:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/equipajes_egm`

- Id: `inventario.equipajes_egm`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_egm.php`
- Entrada: `post.id_equipaje:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/equipajes_eliminar`

- Id: `inventario.equipajes_eliminar`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_eliminar.php`
- Entrada: `post.id_equipaje:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/equipajes_eliminar_grupo`

- Id: `inventario.equipajes_eliminar_grupo`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_eliminar_grupo.php`
- Entrada: `post.id_equipaje:integer`, `post.id_grupo:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/equipajes_lista_activ_equipaje`

- Id: `inventario.equipajes_lista_activ_equipaje`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_lista_activ_equipaje.php`
- Entrada: `post.id_equipaje:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/equipajes_lista_activ_periodo`

- Id: `inventario.equipajes_lista_activ_periodo`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_lista_activ_periodo.php`
- Entrada: `post.empiezamax:string`, `post.empiezamin:string`, `post.fin:string`, `post.id_cdc:integer`, `post.inicio:string`, `post.periodo:string`, `post.year:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/equipajes_lista_activ_sel`

- Id: `inventario.equipajes_lista_activ_sel`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_lista_activ_sel.php`
- Entrada: `post.id_cdc:integer`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/equipajes_movimientos`

- Id: `inventario.equipajes_movimientos`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_movimientos.php`
- Entrada: `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/equipajes_nuevo_guardar`

- Id: `inventario.equipajes_nuevo_guardar`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_nuevo_guardar.php`
- Entrada: `post.f_fin:string`, `post.f_ini:string`, `post.id_ubi_activ:integer`, `post.ids_activ:string`, `post.lugar:string`, `post.nom_equipaje:string`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/equipajes_texto_listado_guardar`

- Id: `inventario.equipajes_texto_listado_guardar`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_texto_listado_guardar.php`
- Entrada: `post.id_equipaje:integer`, `post.loc:string`, `post.texto:string`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/equipajes_update_grupo`

- Id: `inventario.equipajes_update_grupo`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_update_grupo.php`
- Entrada: `post.id_equipaje:integer`, `post.id_grupo:integer`, `post.id_lugar:integer`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/inventario_css_inline_data`

- Id: `inventario.inventario_css_inline_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/inventario_css_inline_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/inventario_ctr`

- Id: `inventario.inventario_ctr`
- Controller: `src/inventario/infrastructure/ui/http/controllers/inventario_ctr.php`
- Entrada: `post.sel:string`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/inventario_dlb`

- Id: `inventario.inventario_dlb`
- Controller: `src/inventario/infrastructure/ui/http/controllers/inventario_dlb.php`
- Entrada: `post.sel:string`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/lista_casas_posibles_periodo`

- Id: `inventario.lista_casas_posibles_periodo`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_casas_posibles_periodo.php`
- Entrada: `post.empiezamax:string`, `post.empiezamin:string`, `post.fin:string`, `post.inicio:string`, `post.periodo:string`, `post.year:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/lista_colecciones`

- Id: `inventario.lista_colecciones`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_colecciones.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/lista_de_ctr`

- Id: `inventario.lista_de_ctr`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_de_ctr.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/lista_de_ctr_con_docs`

- Id: `inventario.lista_de_ctr_con_docs`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_de_ctr_con_docs.php`
- Entrada: `post.id_tipo_doc:integer`, `post.inventario:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/lista_docs_asignados_por_tipo`

- Id: `inventario.lista_docs_asignados_por_tipo`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_docs_asignados_por_tipo.php`
- Entrada: `post.id_tipo_doc:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/lista_docs_asignar_ctr`

- Id: `inventario.lista_docs_asignar_ctr`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_docs_asignar_ctr.php`
- Entrada: `post.id_tipo_doc:integer`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/lista_docs_asignar_dlb`

- Id: `inventario.lista_docs_asignar_dlb`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_docs_asignar_dlb.php`
- Entrada: `post.id_tipo_doc:integer`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/lista_docs_con_observaciones`

- Id: `inventario.lista_docs_con_observaciones`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_docs_con_observaciones.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/lista_docs_de_ctr`

- Id: `inventario.lista_docs_de_ctr`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_docs_de_ctr.php`
- Entrada: `post.id_lugar:integer`, `post.id_ubi:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/lista_docs_de_dlb`

- Id: `inventario.lista_docs_de_dlb`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_docs_de_dlb.php`
- Entrada: `post.id_tipo_doc:integer`, `post.inventario:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/lista_docs_de_egm`

- Id: `inventario.lista_docs_de_egm`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_docs_de_egm.php`
- Entrada: `post.id_item_egm:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/lista_docs_de_lugar`

- Id: `inventario.lista_docs_de_lugar`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_docs_de_lugar.php`
- Entrada: `post.id_lugar:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/lista_docs_en_busqueda`

- Id: `inventario.lista_docs_en_busqueda`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_docs_en_busqueda.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/lista_docs_libres`

- Id: `inventario.lista_docs_libres`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_docs_libres.php`
- Entrada: `post.id_equipaje:integer`, `post.id_tipo_doc:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/lista_docs_no_asignados_por_tipo`

- Id: `inventario.lista_docs_no_asignados_por_tipo`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_docs_no_asignados_por_tipo.php`
- Entrada: `post.id_tipo_doc:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/lista_docs_perdidos`

- Id: `inventario.lista_docs_perdidos`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_docs_perdidos.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/lista_equipajes_desde_fecha`

- Id: `inventario.lista_equipajes_desde_fecha`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_equipajes_desde_fecha.php`
- Entrada: `post.f_ini_iso:string`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/lista_equipajes_posibles_maletas`

- Id: `inventario.lista_equipajes_posibles_maletas`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_equipajes_posibles_maletas.php`
- Entrada: `post.id_equipaje:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/lista_lugares_de_ubi`

- Id: `inventario.lista_lugares_de_ubi`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_lugares_de_ubi.php`
- Entrada: `post.id_ubi:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/lista_tipo_doc`

- Id: `inventario.lista_tipo_doc`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_tipo_doc.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/texto_de_egm`

- Id: `inventario.texto_de_egm`
- Controller: `src/inventario/infrastructure/ui/http/controllers/texto_de_egm.php`
- Entrada: `post.id_equipaje:integer`, `post.id_grupo:integer`, `post.id_item_egm:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/inventario/traslado_doc_guardar`

- Id: `inventario.traslado_doc_guardar`
- Controller: `src/inventario/infrastructure/ui/http/controllers/traslado_doc_guardar.php`
- Entrada: `post.id_lugar_new:integer`, `post.id_ubi_new:integer`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`
