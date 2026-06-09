---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "notas"
endpoints: 33
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - notas

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/notas/acta_eliminar`

- Id: `notas.acta_eliminar`
- Controller: `src/notas/infrastructure/ui/http/controllers/acta_eliminar.php`
- Entrada: `post.acta:string`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/notas/acta_imprimir_presentacion_data`

- Id: `notas.acta_imprimir_presentacion_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/acta_imprimir_presentacion_data.php`
- Entrada: `post.acta:string`, `post.mode:string`
- Respuesta: `standard_envelope_string_data`

## `/src/notas/acta_listado_anual_data`

- Id: `notas.acta_listado_anual_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/acta_listado_anual_data.php`
- Entrada: `post.finIso:string`, `post.inicioIso:string`
- Respuesta: `standard_envelope_string_data`

## `/src/notas/acta_modificar`

- Id: `notas.acta_modificar`
- Controller: `src/notas/infrastructure/ui/http/controllers/acta_modificar.php`
- Entrada: `post.acta:string`, `post.examinadores:array`, `post.f_acta:string`, `post.id_activ:integer`, `post.id_asignatura:integer`, `post.libro:integer`, `post.linea:integer`, `post.lugar:string`, `post.observ:string`, `post.pagina:integer`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/notas/acta_nueva`

- Id: `notas.acta_nueva`
- Controller: `src/notas/infrastructure/ui/http/controllers/acta_nueva.php`
- Entrada: `post.acta:string`, `post.examinadores:array`, `post.f_acta:string`, `post.id_activ:integer`, `post.id_asignatura:integer`, `post.libro:integer`, `post.linea:integer`, `post.lugar:string`, `post.observ:string`, `post.pagina:integer`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/notas/acta_pdf_download`

- Id: `notas.acta_pdf_download`
- Controller: `src/notas/infrastructure/ui/http/controllers/acta_pdf_download.php`
- Entrada: `get.tk:mixed`
- Respuesta: `raw_response`

## `/src/notas/acta_pdf_eliminar`

- Id: `notas.acta_pdf_eliminar`
- Controller: `src/notas/infrastructure/ui/http/controllers/acta_pdf_eliminar.php`
- Entrada: `post.acta_num:string`
- Respuesta: `standard_envelope_string_data`

## `/src/notas/acta_pdf_subir`

- Id: `notas.acta_pdf_subir`
- Controller: `src/notas/infrastructure/ui/http/controllers/acta_pdf_subir.php`
- Entrada: `post.acta_num:string`
- Respuesta: `standard_envelope_string_data`

## `/src/notas/acta_select_data`

- Id: `notas.acta_select_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/acta_select_data.php`
- Entrada: `post.acta:string`, `post.mes_fin_stgr:integer`, `post.titulo:string`
- Respuesta: `standard_envelope_string_data`

## `/src/notas/acta_ver_form_data`

- Id: `notas.acta_ver_form_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/acta_ver_form_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/notas/actividades_buscar_data`

- Id: `notas.actividades_buscar_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/actividades_buscar_data.php`
- Entrada: `post.dl_org:string`, `post.f_acta_iso:string`, `post.id_activ:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/notas/asig_faltan_personas_select_data`

- Id: `notas.asig_faltan_personas_select_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/asig_faltan_personas_select_data.php`
- Entrada: `post.b_c:string`, `post.c1:string`, `post.c2:string`, `post.id_asignatura:integer`, `post.personas_agd:string`, `post.personas_n:string`
- Respuesta: `standard_envelope_string_data`

## `/src/notas/asig_faltan_select_data`

- Id: `notas.asig_faltan_select_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/asig_faltan_select_data.php`
- Entrada: `post.b_c:string`, `post.c1:string`, `post.c2:string`, `post.lista:string`, `post.numero:integer`, `post.personas_agd:string`, `post.personas_n:string`
- Respuesta: `standard_envelope_string_data`

## `/src/notas/asignaturas_pendientes_data`

- Id: `notas.asignaturas_pendientes_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/asignaturas_pendientes_data.php`
- Entrada: `post.dl:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/notas/asignaturas_pendientes_resumen_data`

- Id: `notas.asignaturas_pendientes_resumen_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/asignaturas_pendientes_resumen_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/notas/asignaturas_search`

- Id: `notas.asignaturas_search`
- Controller: `src/notas/infrastructure/ui/http/controllers/asignaturas_search.php`
- Entrada: `post.search:string`
- Respuesta: `raw_response`

## `/src/notas/buscar_acta`

- Id: `notas.buscar_acta`
- Controller: `src/notas/infrastructure/ui/http/controllers/buscar_acta.php`
- Entrada: `post.acta:string`
- Respuesta: `standard_envelope_string_data`

## `/src/notas/comprobar_notas_constants_data`

- Id: `notas.comprobar_notas_constants_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/comprobar_notas_constants_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/notas/comprobar_notas_page_data`

- Id: `notas.comprobar_notas_page_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/comprobar_notas_page_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/notas/examinadores_search`

- Id: `notas.examinadores_search`
- Controller: `src/notas/infrastructure/ui/http/controllers/examinadores_search.php`
- Entrada: `post.search:string`
- Respuesta: `raw_response`

## `/src/notas/informe_stgr_agd_data`

- Id: `notas.informe_stgr_agd_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/informe_stgr_agd_data.php`
- Entrada: `post.dl:array`, `post.lista:string`
- Respuesta: `standard_envelope_string_data`

## `/src/notas/informe_stgr_n_data`

- Id: `notas.informe_stgr_n_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/informe_stgr_n_data.php`
- Entrada: `post.dl:array`, `post.lista:string`
- Respuesta: `standard_envelope_string_data`

## `/src/notas/informe_stgr_profesores_data`

- Id: `notas.informe_stgr_profesores_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/informe_stgr_profesores_data.php`
- Entrada: `post.lista:string`
- Respuesta: `standard_envelope_string_data`

## `/src/notas/nota_persona_form_data`

- Id: `notas.nota_persona_form_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/nota_persona_form_data.php`
- Entrada: `post.id_asignatura_real:string`, `post.id_pau:integer`, `post.mod:string`, `post.pau:string`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/notas/persona_nota_editar`

- Id: `notas.persona_nota_editar`
- Controller: `src/notas/infrastructure/ui/http/controllers/persona_nota_editar.php`
- Entrada: `post.id_asignatura_real:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/notas/persona_nota_eliminar`

- Id: `notas.persona_nota_eliminar`
- Controller: `src/notas/infrastructure/ui/http/controllers/persona_nota_eliminar.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/notas/persona_nota_nueva`

- Id: `notas.persona_nota_nueva`
- Controller: `src/notas/infrastructure/ui/http/controllers/persona_nota_nueva.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/notas/posibles_opcionales_data`

- Id: `notas.posibles_opcionales_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/posibles_opcionales_data.php`
- Entrada: `post.id_nom:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/notas/posibles_preceptores_data`

- Id: `notas.posibles_preceptores_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/posibles_preceptores_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/notas/tessera_copiar`

- Id: `notas.tessera_copiar`
- Controller: `src/notas/infrastructure/ui/http/controllers/tessera_copiar.php`
- Entrada: `post.id_nom_dst:integer`, `post.id_nom_org:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/notas/tessera_copiar_select_data`

- Id: `notas.tessera_copiar_select_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/tessera_copiar_select_data.php`
- Entrada: `post.id_nom:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/notas/tessera_imprimir_data`

- Id: `notas.tessera_imprimir_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/tessera_imprimir_data.php`
- Entrada: `post.id_nom:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/notas/tessera_ver_data`

- Id: `notas.tessera_ver_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/tessera_ver_data.php`
- Entrada: `post.id_nom:integer`
- Respuesta: `standard_envelope_string_data`
