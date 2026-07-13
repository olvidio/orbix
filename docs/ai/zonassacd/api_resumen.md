---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "zonassacd"
endpoints: 9
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - zonassacd

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/zonassacd/zona_ctr`

- Id: `zonassacd.zona_ctr`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_ctr.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/zonassacd/zona_ctr_ajax`

- Id: `zonassacd.zona_ctr_ajax`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_ctr_ajax.php`
- Entrada: ninguna detectada.
- Respuesta: `pendiente_revision`

## `/src/zonassacd/zona_ctr_lista`

- Id: `zonassacd.zona_ctr_lista`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_ctr_lista.php`
- Entrada: `post.id_zona:string`
- Respuesta: `standard_envelope_string_data`

## `/src/zonassacd/zona_ctr_update`

- Id: `zonassacd.zona_ctr_update`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_ctr_update.php`
- Entrada: `post.id_zona_new:string`, `post.sel:string`
- Respuesta: `standard_envelope_string_data`

## `/src/zonassacd/zona_sacd`

- Id: `zonassacd.zona_sacd`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_sacd.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/zonassacd/zona_sacd_ajax`

- Id: `zonassacd.zona_sacd_ajax`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_sacd_ajax.php`
- Entrada: ninguna detectada.
- Respuesta: `pendiente_revision`

## `/src/zonassacd/zona_sacd_lista`

- Id: `zonassacd.zona_sacd_lista`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_sacd_lista.php`
- Entrada: `post.id_zona:string`
- Respuesta: `standard_envelope_string_data`

## `/src/zonassacd/zona_sacd_lista_tot`

- Id: `zonassacd.zona_sacd_lista_tot`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_sacd_lista_tot.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/zonassacd/zona_sacd_update`

- Id: `zonassacd.zona_sacd_update`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_sacd_update.php`
- Entrada: `post.acumular:integer`, `post.id_zona:string`, `post.id_zona_new:string`, `post.sel:string`
- Respuesta: `standard_envelope_string_data`
