---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "cambios"
endpoints: 12
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - cambios

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/cambios/avisos_generar_lista_data`

- Id: `cambios.avisos_generar_lista_data`
- Controller: `src/cambios/infrastructure/ui/http/controllers/avisos_generar_lista_data.php`
- Entrada: `post.aviso_tipo:integer`, `post.id_usuario:integer`, `post.is_admin:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/cambios/cambio_usuario_eliminar`

- Id: `cambios.cambio_usuario_eliminar`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_eliminar.php`
- Entrada: `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/cambios/cambio_usuario_eliminar_hasta_fecha`

- Id: `cambios.cambio_usuario_eliminar_hasta_fecha`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_eliminar_hasta_fecha.php`
- Entrada: `post.f_fin:string`
- Respuesta: `standard_envelope_string_data`

## `/src/cambios/cambio_usuario_objeto_pref_eliminar`

- Id: `cambios.cambio_usuario_objeto_pref_eliminar`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_objeto_pref_eliminar.php`
- Entrada: `post.id_item_usuario_objeto:integer`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/cambios/cambio_usuario_objeto_pref_fases_data`

- Id: `cambios.cambio_usuario_objeto_pref_fases_data`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_objeto_pref_fases_data.php`
- Entrada: `post.dl_propia:string`, `post.id_tipo_activ:string`, `post.objeto:string`
- Respuesta: `standard_envelope_string_data`

## `/src/cambios/cambio_usuario_objeto_pref_guardar`

- Id: `cambios.cambio_usuario_objeto_pref_guardar`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_objeto_pref_guardar.php`
- Entrada: `post.aviso_off:string`, `post.aviso_on:string`, `post.aviso_outdate:string`, `post.aviso_tipo:integer`, `post.casas:array`, `post.dl_propia:string`, `post.id_fase_ref:integer`, `post.id_item_usuario_objeto:integer`, `post.id_tipo_activ:string`, `post.id_usuario:integer`, `post.objeto:string`
- Respuesta: `standard_envelope_string_data`

## `/src/cambios/cambio_usuario_objeto_pref_propiedades_data`

- Id: `cambios.cambio_usuario_objeto_pref_propiedades_data`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_objeto_pref_propiedades_data.php`
- Entrada: `post.id_item_usuario_objeto:integer`, `post.objeto:string`
- Respuesta: `standard_envelope_string_data`

## `/src/cambios/cambio_usuario_propiedad_pref_guardar_todas`

- Id: `cambios.cambio_usuario_propiedad_pref_guardar_todas`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_propiedad_pref_guardar_todas.php`
- Entrada: `post.id_item_usuario_objeto_prop:integer`, `post.objeto_prop:string`
- Respuesta: `standard_envelope_string_data`

## `/src/cambios/cambio_usuario_propiedad_pref_item_data`

- Id: `cambios.cambio_usuario_propiedad_pref_item_data`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_propiedad_pref_item_data.php`
- Entrada: `post.id_item:integer`, `post.objeto:string`, `post.propiedad:string`
- Respuesta: `standard_envelope_string_data`

## `/src/cambios/cambio_usuario_propiedad_pref_preview`

- Id: `cambios.cambio_usuario_propiedad_pref_preview`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_propiedad_pref_preview.php`
- Entrada: `post.id_item:integer`, `post.id_ubi:array`, `post.objeto:string`, `post.operador:string`, `post.propiedad:string`, `post.valor:string`, `post.valor_new:string`, `post.valor_old:string`
- Respuesta: `standard_envelope_string_data`

## `/src/cambios/usuario_avisos_pref_form_data`

- Id: `cambios.usuario_avisos_pref_form_data`
- Controller: `src/cambios/infrastructure/ui/http/controllers/usuario_avisos_pref_form_data.php`
- Entrada: `post.id_item_usuario_objeto:integer`, `post.id_usuario:integer`, `post.quien:string`, `post.salida:string`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/cambios/usuario_form_avisos_data`

- Id: `cambios.usuario_form_avisos_data`
- Controller: `src/cambios/infrastructure/ui/http/controllers/usuario_form_avisos_data.php`
- Entrada: `post.id_usuario:integer`, `post.quien:string`
- Respuesta: `standard_envelope_string_data`
