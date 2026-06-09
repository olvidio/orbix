---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "dbextern"
endpoints: 16
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - dbextern

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/dbextern/refrescar_bdu`

- Id: `dbextern.refrescar_bdu`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/refrescar_bdu.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/dbextern/sincro_baja`

- Id: `dbextern.sincro_baja`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_baja.php`
- Entrada: `post.dl:string`, `post.id_nom_orbix:integer`, `post.tipo_persona:string`
- Respuesta: `standard_envelope_string_data`

## `/src/dbextern/sincro_crear`

- Id: `dbextern.sincro_crear`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_crear.php`
- Entrada: `post.id:integer`, `post.id_nom_listas:integer`, `post.tipo_persona:string`
- Respuesta: `standard_envelope_string_data`

## `/src/dbextern/sincro_crear_todos`

- Id: `dbextern.sincro_crear_todos`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_crear_todos.php`
- Entrada: `post.dl:string`, `post.region:string`, `post.tipo_persona:string`
- Respuesta: `standard_envelope_string_data`

## `/src/dbextern/sincro_desunir`

- Id: `dbextern.sincro_desunir`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_desunir.php`
- Entrada: `post.id_nom_listas:integer`, `post.tipo_persona:string`
- Respuesta: `standard_envelope_string_data`

## `/src/dbextern/sincro_index_datos`

- Id: `dbextern.sincro_index_datos`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_index_datos.php`
- Entrada: `post.tipo:string`
- Respuesta: `standard_envelope_string_data`

## `/src/dbextern/sincro_syncro`

- Id: `dbextern.sincro_syncro`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_syncro.php`
- Entrada: `post.dl_listas:string`, `post.region:string`, `post.tipo_persona:string`
- Respuesta: `standard_envelope_string_data`

## `/src/dbextern/sincro_trasladar`

- Id: `dbextern.sincro_trasladar`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_trasladar.php`
- Entrada: `post.dl:string`, `post.id_nom_orbix:integer`, `post.tipo_persona:string`
- Respuesta: `standard_envelope_string_data`

## `/src/dbextern/sincro_trasladar_a`

- Id: `dbextern.sincro_trasladar_a`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_trasladar_a.php`
- Entrada: `post.dl:string`, `post.id_nom_orbix:integer`, `post.tipo_persona:string`
- Respuesta: `standard_envelope_string_data`

## `/src/dbextern/sincro_unir`

- Id: `dbextern.sincro_unir`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_unir.php`
- Entrada: `post.id:integer`, `post.id_nom_listas:integer`, `post.id_orbix:integer`, `post.tipo_persona:string`
- Respuesta: `standard_envelope_string_data`

## `/src/dbextern/ver_desaparecidos_de_listas_datos`

- Id: `dbextern.ver_desaparecidos_de_listas_datos`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/ver_desaparecidos_de_listas_datos.php`
- Entrada: `post.ids_desaparecidos_de_listas:string`, `post.tipo_persona:string`
- Respuesta: `standard_envelope_string_data`

## `/src/dbextern/ver_desaparecidos_de_orbix_datos`

- Id: `dbextern.ver_desaparecidos_de_orbix_datos`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/ver_desaparecidos_de_orbix_datos.php`
- Entrada: `post.ids_desaparecidos_de_orbix:string`, `post.tipo_persona:string`
- Respuesta: `standard_envelope_string_data`

## `/src/dbextern/ver_listas_datos`

- Id: `dbextern.ver_listas_datos`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/ver_listas_datos.php`
- Entrada: `post.dl:string`, `post.first_load:boolean`, `post.id_nom_bdu:integer`, `post.region:string`, `post.tipo_persona:string`
- Respuesta: `standard_envelope_string_data`

## `/src/dbextern/ver_orbix_datos`

- Id: `dbextern.ver_orbix_datos`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/ver_orbix_datos.php`
- Entrada: `post.dl:string`, `post.id_nom_orbix:integer`, `post.region:string`, `post.tipo_persona:string`
- Respuesta: `standard_envelope_string_data`

## `/src/dbextern/ver_orbix_otradl_datos`

- Id: `dbextern.ver_orbix_otradl_datos`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/ver_orbix_otradl_datos.php`
- Entrada: `post.ids_traslados_A:string`, `post.tipo_persona:string`
- Respuesta: `standard_envelope_string_data`

## `/src/dbextern/ver_traslados_datos`

- Id: `dbextern.ver_traslados_datos`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/ver_traslados_datos.php`
- Entrada: `post.ids_traslados:string`, `post.tipo_persona:string`
- Respuesta: `standard_envelope_string_data`
