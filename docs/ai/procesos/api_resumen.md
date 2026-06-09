---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "procesos"
endpoints: 23
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - procesos

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/procesos/actividad_proceso_data`

- Id: `procesos.actividad_proceso_data`
- Controller: `src/procesos/infrastructure/ui/http/controllers/actividad_proceso_data.php`
- Entrada: `post.id_activ:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/procesos/actividad_proceso_generar`

- Id: `procesos.actividad_proceso_generar`
- Controller: `src/procesos/infrastructure/ui/http/controllers/actividad_proceso_generar.php`
- Entrada: `post.id_activ:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/procesos/actividad_proceso_get`

- Id: `procesos.actividad_proceso_get`
- Controller: `src/procesos/infrastructure/ui/http/controllers/actividad_proceso_get.php`
- Entrada: `post.id_activ:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/procesos/actividad_proceso_update`

- Id: `procesos.actividad_proceso_update`
- Controller: `src/procesos/infrastructure/ui/http/controllers/actividad_proceso_update.php`
- Entrada: `post.completado:string`, `post.id_item:integer`, `post.observ:string`
- Respuesta: `standard_envelope_string_data`

## `/src/procesos/actividad_que_fases_ajax`

- Id: `procesos.actividad_que_fases_ajax`
- Controller: `src/procesos/infrastructure/ui/http/controllers/actividad_que_fases_ajax.php`
- Entrada: `post.dl_propia:string`, `post.id_tipo_activ:string`, `post.selected:string`
- Respuesta: `standard_envelope_string_data`

## `/src/procesos/fases_activ_cambio_get`

- Id: `procesos.fases_activ_cambio_get`
- Controller: `src/procesos/infrastructure/ui/http/controllers/fases_activ_cambio_get.php`
- Entrada: `post.dl_propia:string`, `post.id_fase_sel:string`, `post.id_tipo_activ:string`
- Respuesta: `standard_envelope_string_data`

## `/src/procesos/fases_activ_cambio_lista`

- Id: `procesos.fases_activ_cambio_lista`
- Controller: `src/procesos/infrastructure/ui/http/controllers/fases_activ_cambio_lista.php`
- Entrada: `post.accion:string`, `post.dl_propia:string`, `post.empiezamax:string`, `post.empiezamin:string`, `post.id_fase_nueva:string`, `post.id_tipo_activ:string`, `post.periodo:string`, `post.year:string`
- Respuesta: `standard_envelope_string_data`

## `/src/procesos/fases_activ_cambio_tipo_html`

- Id: `procesos.fases_activ_cambio_tipo_html`
- Controller: `src/procesos/infrastructure/ui/http/controllers/fases_activ_cambio_tipo_html.php`
- Entrada: `post.id_tipo_activ:string`, `post.sactividad:string`, `post.sactividad2:string`, `post.sasistentes:string`
- Respuesta: `standard_envelope_string_data`

## `/src/procesos/fases_activ_cambio_update`

- Id: `procesos.fases_activ_cambio_update`
- Controller: `src/procesos/infrastructure/ui/http/controllers/fases_activ_cambio_update.php`
- Entrada: `post.accion:string`, `post.id_fase_nueva:string`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/procesos/procesos_clonar`

- Id: `procesos.procesos_clonar`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_clonar.php`
- Entrada: `post.id_tipo_proceso:integer`, `post.id_tipo_proceso_ref:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/procesos/procesos_depende`

- Id: `procesos.procesos_depende`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_depende.php`
- Entrada: `post.acc:string`, `post.valor_depende:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/procesos/procesos_eliminar`

- Id: `procesos.procesos_eliminar`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_eliminar.php`
- Entrada: `post.id_item:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/procesos/procesos_get`

- Id: `procesos.procesos_get`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_get.php`
- Entrada: `post.id_tipo_proceso:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/procesos/procesos_get_listado`

- Id: `procesos.procesos_get_listado`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_get_listado.php`
- Entrada: `post.id_tipo_proceso:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/procesos/procesos_regenerar`

- Id: `procesos.procesos_regenerar`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_regenerar.php`
- Entrada: `post.id_tipo_proceso:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/procesos/procesos_select_data`

- Id: `procesos.procesos_select_data`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_select_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/procesos/procesos_update`

- Id: `procesos.procesos_update`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_update.php`
- Entrada: `post.id_fase:integer`, `post.id_fase_previa:array`, `post.id_item:integer`, `post.id_of_responsable:integer`, `post.id_tarea:integer`, `post.id_tarea_previa:array`, `post.id_tipo_proceso:integer`, `post.mensaje_requisito:array`, `post.status:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/procesos/procesos_ver_data`

- Id: `procesos.procesos_ver_data`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_ver_data.php`
- Entrada: `post.id_item:integer`, `post.mod:string`
- Respuesta: `standard_envelope_string_data`

## `/src/procesos/tipo_activ_proceso_asignar`

- Id: `procesos.tipo_activ_proceso_asignar`
- Controller: `src/procesos/infrastructure/ui/http/controllers/tipo_activ_proceso_asignar.php`
- Entrada: `post.id_tipo_activ:integer`, `post.id_tipo_proceso:integer`, `post.propio:string`
- Respuesta: `standard_envelope_string_data`

## `/src/procesos/tipo_activ_proceso_lista`

- Id: `procesos.tipo_activ_proceso_lista`
- Controller: `src/procesos/infrastructure/ui/http/controllers/tipo_activ_proceso_lista.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/procesos/tipo_activ_proceso_lst_posibles`

- Id: `procesos.tipo_activ_proceso_lst_posibles`
- Controller: `src/procesos/infrastructure/ui/http/controllers/tipo_activ_proceso_lst_posibles.php`
- Entrada: `post.id_tipo_activ:integer`, `post.propio:string`
- Respuesta: `standard_envelope_string_data`

## `/src/procesos/usuario_perm_activ_ajax`

- Id: `procesos.usuario_perm_activ_ajax`
- Controller: `src/procesos/infrastructure/ui/http/controllers/usuario_perm_activ_ajax.php`
- Entrada: `post.dl_propia:string`, `post.id_tipo_activ:string`
- Respuesta: `standard_envelope_string_data`

## `/src/procesos/usuario_perm_activ_data`

- Id: `procesos.usuario_perm_activ_data`
- Controller: `src/procesos/infrastructure/ui/http/controllers/usuario_perm_activ_data.php`
- Entrada: `post.dl_propia:mixed`, `post.id_tipo_activ_txt:string`, `post.id_usuario:integer`
- Respuesta: `standard_envelope_string_data`
