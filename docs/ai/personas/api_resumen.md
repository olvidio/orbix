---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "personas"
endpoints: 9
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - personas

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/personas/home_persona_data`

- Id: `personas.home_persona_data`
- Controller: `src/personas/infrastructure/ui/http/controllers/home_persona_data.php`
- Entrada: `post.id_nom:integer`, `post.id_tabla:string`, `post.obj_pau:string`, `post.sel:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/personas/persona_eliminar`

- Id: `personas.persona_eliminar`
- Controller: `src/personas/infrastructure/ui/http/controllers/persona_eliminar.php`
- Entrada: `post.id_nom:integer`, `post.obj_pau:string`
- Respuesta: `standard_envelope_string_data`

## `/src/personas/persona_update`

- Id: `personas.persona_update`
- Controller: `src/personas/infrastructure/ui/http/controllers/persona_update.php`
- Entrada: `post.apel_fam:string`, `post.apellido1:string`, `post.apellido2:string`, `post.ce:integer`, `post.ce_fin:integer`, `post.ce_ini:integer`, `post.ce_lugar:string`, `post.dl:string`, `post.eap:string`, `post.edad:string`, `post.f_inc:string`, `post.f_nacimiento:string`, `post.f_situacion:string`, `post.id_ctr:integer`, `post.id_nom:integer`, `post.idioma_preferido:string`, `post.inc:string`, `post.lugar_nacimiento:string`, `post.nivel_stgr:integer`, `post.nom:string`, `post.nx1:string`, `post.nx2:string`, `post.obj_pau:string`, `post.observ:string`, `post.profesion:string`, `post.profesor_stgr:string`, `post.sacd:string`, `post.situacion:string`, `post.trato:string`
- Respuesta: `standard_envelope_string_data`

## `/src/personas/personas_editar_data`

- Id: `personas.personas_editar_data`
- Controller: `src/personas/infrastructure/ui/http/controllers/personas_editar_data.php`
- Entrada: `post.apellido1:string`, `post.id_nom:integer`, `post.nuevo:integer`, `post.obj_pau:string`, `post.sel:mixed`, `post.tabla:string`
- Respuesta: `standard_envelope_string_data`

## `/src/personas/personas_select_data`

- Id: `personas.personas_select_data`
- Controller: `src/personas/infrastructure/ui/http/controllers/personas_select_data.php`
- Entrada: `post.apellido1:string`, `post.apellido2:string`, `post.centro:string`, `post.cmb:string`, `post.es_sacd:integer`, `post.exacto:string`, `post.na:string`, `post.nombre:string`, `post.tabla:string`, `post.tipo:string`
- Respuesta: `standard_envelope_string_data`

## `/src/personas/stgr_cambio_data`

- Id: `personas.stgr_cambio_data`
- Controller: `src/personas/infrastructure/ui/http/controllers/stgr_cambio_data.php`
- Entrada: `post.id_nom:integer`, `post.id_tabla:string`, `post.sel:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/personas/stgr_update`

- Id: `personas.stgr_update`
- Controller: `src/personas/infrastructure/ui/http/controllers/stgr_update.php`
- Entrada: `post.id_nom:integer`, `post.id_tabla:string`, `post.nivel_stgr:string`
- Respuesta: `standard_envelope_string_data`

## `/src/personas/traslado_form_data`

- Id: `personas.traslado_form_data`
- Controller: `src/personas/infrastructure/ui/http/controllers/traslado_form_data.php`
- Entrada: `post.id_pau:integer`, `post.sel:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/personas/traslado_update`

- Id: `personas.traslado_update`
- Controller: `src/personas/infrastructure/ui/http/controllers/traslado_update.php`
- Entrada: `post.ctr_o:string`, `post.dl:string`, `post.f_ctr:string`, `post.f_dl:string`, `post.id_ctr_o:string`, `post.id_pau:integer`, `post.new_ctr:string`, `post.new_dl:string`, `post.obj_pau:string`, `post.situacion:string`
- Respuesta: `standard_envelope_string_data`
