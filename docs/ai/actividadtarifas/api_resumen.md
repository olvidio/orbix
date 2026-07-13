---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "actividadtarifas"
endpoints: 14
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - actividadtarifas

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/actividadtarifas/relacion_tarifa_eliminar`

- Id: `actividadtarifas.relacion_tarifa_eliminar`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/relacion_tarifa_eliminar.php`
- Entrada: `post.id_item:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadtarifas/relacion_tarifa_form_data`

- Id: `actividadtarifas.relacion_tarifa_form_data`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/relacion_tarifa_form_data.php`
- Entrada: `post.id_item:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadtarifas/relacion_tarifa_lista_data`

- Id: `actividadtarifas.relacion_tarifa_lista_data`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/relacion_tarifa_lista_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/actividadtarifas/relacion_tarifa_update`

- Id: `actividadtarifas.relacion_tarifa_update`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/relacion_tarifa_update.php`
- Entrada: `post.id_item:string`, `post.id_tarifa:integer`, `post.id_tipo_activ:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadtarifas/tarifa_ubi_copiar`

- Id: `actividadtarifas.tarifa_ubi_copiar`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_copiar.php`
- Entrada: `post.ctx_copiar:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadtarifas/tarifa_ubi_eliminar`

- Id: `actividadtarifas.tarifa_ubi_eliminar`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_eliminar.php`
- Entrada: `post.ctx_eliminar:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadtarifas/tarifa_ubi_form_data`

- Id: `actividadtarifas.tarifa_ubi_form_data`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_form_data.php`
- Entrada: `post.id_item:string`, `post.id_ubi:integer`, `post.letra:string`, `post.year:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadtarifas/tarifa_ubi_lista_data`

- Id: `actividadtarifas.tarifa_ubi_lista_data`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_lista_data.php`
- Entrada: `post.id_ubi:integer`, `post.year:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadtarifas/tarifa_ubi_update`

- Id: `actividadtarifas.tarifa_ubi_update`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_update.php`
- Entrada: `post.cantidad:string`, `post.ctx_update:string`, `post.id_serie:integer`, `post.id_tarifa:integer`, `post.observ:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadtarifas/tarifa_ubi_update_inc`

- Id: `actividadtarifas.tarifa_ubi_update_inc`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_update_inc.php`
- Entrada: `post.inc_cantidad:array`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadtarifas/tipo_tarifa_eliminar`

- Id: `actividadtarifas.tipo_tarifa_eliminar`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tipo_tarifa_eliminar.php`
- Entrada: `post.id_tarifa:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadtarifas/tipo_tarifa_form_data`

- Id: `actividadtarifas.tipo_tarifa_form_data`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tipo_tarifa_form_data.php`
- Entrada: `post.id_tarifa:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadtarifas/tipo_tarifa_lista_data`

- Id: `actividadtarifas.tipo_tarifa_lista_data`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tipo_tarifa_lista_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/actividadtarifas/tipo_tarifa_update`

- Id: `actividadtarifas.tipo_tarifa_update`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tipo_tarifa_update.php`
- Entrada: `post.id_tarifa:string`, `post.letra:string`, `post.modo:string`, `post.observ:string`
- Respuesta: `standard_envelope_string_data`
