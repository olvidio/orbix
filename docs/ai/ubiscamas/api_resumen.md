---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "ubiscamas"
endpoints: 9
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - ubiscamas

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/ubiscamas/actividad_habitaciones_lista`

- Id: `ubiscamas.actividad_habitaciones_lista`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/actividad_habitaciones_lista.php`
- Entrada: `post.id_activ:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/ubiscamas/cama_delete`

- Id: `ubiscamas.cama_delete`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/cama_delete.php`
- Entrada: `post.id_cama:string`
- Respuesta: `standard_envelope_string_data`

## `/src/ubiscamas/cama_form_data`

- Id: `ubiscamas.cama_form_data`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/cama_form_data.php`
- Entrada: `post.id_cama:string`, `post.id_habitacion:string`, `post.id_ubi:integer`, `post.mod:string`
- Respuesta: `standard_envelope_string_data`

## `/src/ubiscamas/cama_update`

- Id: `ubiscamas.cama_update`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/cama_update.php`
- Entrada: `post.sel:array`, `post.id_cama:string`, `post.id_habitacion:string`, `post.descripcion:string`, `post.larga:string`, `post.vip:string`
- Respuesta: `standard_envelope_string_data`

## `/src/ubiscamas/habitacion_delete`

- Id: `ubiscamas.habitacion_delete`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/habitacion_delete.php`
- Entrada: `post.sel:array`, `post.id_habitacion:string`
- Respuesta: `standard_envelope_string_data`

## `/src/ubiscamas/habitacion_form_data`

- Id: `ubiscamas.habitacion_form_data`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/habitacion_form_data.php`
- Entrada: `post.id_habitacion:string`, `post.id_ubi:integer`, `post.nuevo:string`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/ubiscamas/habitacion_update`

- Id: `ubiscamas.habitacion_update`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/habitacion_update.php`
- Entrada: `post.sel:array`, `post.id_habitacion:string`, `post.id_ubi:integer`, `post.orden:integer`, `post.nombre:string`, `post.numero_camas:integer`, `post.numero_camas_vip:integer`, `post.planta:string`, `post.tipoLavabo:integer`, `post.sillon:string`, `post.adaptada:string`, `post.observaciones:string`, `post.despacho:string`, `post.new_camas_desc:array`, `post.new_camas_larga:array`, `post.new_camas_vip:array`
- Respuesta: `standard_envelope_string_data`

## `/src/ubiscamas/update_cama_asistente`

- Id: `ubiscamas.update_cama_asistente`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/update_cama_asistente.php`
- Entrada: `post.ctx:string`, `post.id_nom:integer`, `post.id_cama:string`
- Respuesta: `raw_response`

## `/src/ubiscamas/update_solo_vip`

- Id: `ubiscamas.update_solo_vip`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/update_solo_vip.php`
- Entrada: `post.ctx:string`, `post.solo_vip:string`
- Respuesta: `raw_response`
