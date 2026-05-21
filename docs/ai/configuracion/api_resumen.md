---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "configuracion"
endpoints: 6
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - configuracion

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/configuracion/modulos_form_data`

- Id: `configuracion.modulos_form_data`
- Controller: `src/configuracion/infrastructure/ui/http/controllers/modulos_form_data.php`
- Entrada: `post.id_mod:integer`, `post.mod:string`, `post.sel:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/configuracion/modulos_select_data`

- Id: `configuracion.modulos_select_data`
- Controller: `src/configuracion/infrastructure/ui/http/controllers/modulos_select_data.php`
- Entrada: `post.id_sel:string`, `post.restored_id_sel:string`, `post.restored_scroll_id:string`, `post.scroll_id:string`, `post.stack:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/configuracion/modulos_update`

- Id: `configuracion.modulos_update`
- Controller: `src/configuracion/infrastructure/ui/http/controllers/modulos_update.php`
- Entrada: ninguna detectada.
- Respuesta: `raw_response`

## `/src/configuracion/parametros_lista`

- Id: `configuracion.parametros_lista`
- Controller: `src/configuracion/infrastructure/ui/http/controllers/parametros_lista.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/configuracion/parametros_update`

- Id: `configuracion.parametros_update`
- Controller: `src/configuracion/infrastructure/ui/http/controllers/parametros_update.php`
- Entrada: `post.fin_dia:integer`, `post.fin_mes:integer`, `post.ini_dia:integer`, `post.ini_mes:integer`, `post.parametro:string`, `post.valor:string`
- Respuesta: `pendiente_revision`

## `/src/configuracion/periodo_calendario_escolar_data`

- Id: `configuracion.periodo_calendario_escolar_data`
- Controller: `src/configuracion/infrastructure/ui/http/controllers/periodo_calendario_escolar_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`
