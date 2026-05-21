---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "asistentes"
endpoints: 15
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - asistentes

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/asistentes/activ_pendientes_select_data`

- Id: `asistentes.activ_pendientes_select_data`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/activ_pendientes_select_data.php`
- Entrada: `post.any:integer`, `post.sactividad:string`, `post.tipo_personas:string`
- Respuesta: `standard_envelope_string_data`

## `/src/asistentes/asistente_eliminar`

- Id: `asistentes.asistente_eliminar`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/asistente_eliminar.php`
- Entrada: `post.id_activ:integer`, `post.id_nom:integer`, `post.id_pau:integer`, `post.pau:string`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/asistentes/asistente_guardar`

- Id: `asistentes.asistente_guardar`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/asistente_guardar.php`
- Entrada: `post.cfi:string`, `post.cfi_con:integer`, `post.encargo:string`, `post.est_ok:string`, `post.falta:string`, `post.id_activ:integer`, `post.id_activ_old:integer`, `post.id_nom:integer`, `post.id_pau:integer`, `post.mod:string`, `post.observ:string`, `post.observ_est:string`, `post.pau:string`, `post.plaza:integer`, `post.propietario:string`, `post.propio:string`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/asistentes/asistente_mover_data`

- Id: `asistentes.asistente_mover_data`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/asistente_mover_data.php`
- Entrada: `post.id_activ:integer`, `post.id_nom:integer`, `post.id_pau:integer`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/asistentes/asistente_plaza_asignar`

- Id: `asistentes.asistente_plaza_asignar`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/asistente_plaza_asignar.php`
- Entrada: `post.id_activ:integer`, `post.lista_json:string`, `post.plaza:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/asistentes/form_actividades_de_una_persona_data`

- Id: `asistentes.form_actividades_de_una_persona_data`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/form_actividades_de_una_persona_data.php`
- Entrada: `post.id_pau:integer`, `post.id_tipo:string`, `post.obj_pau:string`, `post.que_dl:string`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/asistentes/form_asistentes_a_una_actividad_data`

- Id: `asistentes.form_asistentes_a_una_actividad_data`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/form_asistentes_a_una_actividad_data.php`
- Entrada: `post.id_activ:integer`, `post.id_nom:integer`, `post.id_pau:integer`, `post.na:string`, `post.obj_pau:string`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/asistentes/lista_activ_ctr_data`

- Id: `asistentes.lista_activ_ctr_data`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/lista_activ_ctr_data.php`
- Entrada: `post.empiezamax:string`, `post.empiezamin:string`, `post.id_ubi:integer`, `post.n_agd:string`, `post.periodo:string`, `post.sactividad:string`, `post.sasistentes:string`, `post.ssfsv:string`, `post.year:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/asistentes/lista_asis_conjunto_activ_data`

- Id: `asistentes.lista_asis_conjunto_activ_data`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/lista_asis_conjunto_activ_data.php`
- Entrada: `post.dl_org:string`, `post.empiezamax:string`, `post.empiezamin:string`, `post.id_tipo_activ:string`, `post.id_ubi:integer`, `post.modo:string`, `post.nom_activ:string`, `post.periodo:string`, `post.que:string`, `post.sactividad:string`, `post.sasistentes:string`, `post.sfsv:string`, `post.snom_tipo:string`, `post.status:integer`, `post.year:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/asistentes/lista_asistentes_data`

- Id: `asistentes.lista_asistentes_data`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/lista_asistentes_data.php`
- Entrada: `post.id_pau:integer`, `post.queSel:string`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/asistentes/lista_est_ctr_data`

- Id: `asistentes.lista_est_ctr_data`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/lista_est_ctr_data.php`
- Entrada: `post.empiezamax:string`, `post.empiezamin:string`, `post.id_ubi:integer`, `post.n_agd:string`, `post.periodo:string`, `post.year:string`
- Respuesta: `standard_envelope_string_data`

## `/src/asistentes/lista_ultim_que_ctr_data`

- Id: `asistentes.lista_ultim_que_ctr_data`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/lista_ultim_que_ctr_data.php`
- Entrada: `post.curso:string`, `post.que:string`
- Respuesta: `standard_envelope_string_data`

## `/src/asistentes/lista_ultima_activ_data`

- Id: `asistentes.lista_ultima_activ_data`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/lista_ultima_activ_data.php`
- Entrada: `post.curso:string`, `post.id_ubi:string`, `post.que:string`
- Respuesta: `standard_envelope_string_data`

## `/src/asistentes/que_ctr_lista_data`

- Id: `asistentes.que_ctr_lista_data`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/que_ctr_lista_data.php`
- Entrada: `post.id_ubi:integer`, `post.lista:string`, `post.n_agd:string`, `post.periodo:string`, `post.sactividad:string`, `post.sasistentes:string`, `post.ssfsv:string`, `post.tipo:string`, `post.year:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/asistentes/tabla_peticiones_data`

- Id: `asistentes.tabla_peticiones_data`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/tabla_peticiones_data.php`
- Entrada: `post.id_activ_old:integer`, `post.restored_id_sel:mixed`, `post.restored_scroll_id:mixed`, `post.sel:array`, `post.stack:mixed`
- Respuesta: `standard_envelope_string_data`
