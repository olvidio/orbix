---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "cartaspresentacion"
endpoints: 8
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - cartaspresentacion

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/cartaspresentacion/carta_presentacion_eliminar`

- Id: `cartaspresentacion.carta_presentacion_eliminar`
- Controller: `src/cartaspresentacion/infrastructure/ui/http/controllers/carta_presentacion_eliminar.php`
- Entrada: `post.id_direccion:integer`, `post.id_ubi:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/cartaspresentacion/carta_presentacion_form_data`

- Id: `cartaspresentacion.carta_presentacion_form_data`
- Controller: `src/cartaspresentacion/infrastructure/ui/http/controllers/carta_presentacion_form_data.php`
- Entrada: `post.id_direccion:integer`, `post.id_ubi:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/cartaspresentacion/carta_presentacion_update`

- Id: `cartaspresentacion.carta_presentacion_update`
- Controller: `src/cartaspresentacion/infrastructure/ui/http/controllers/carta_presentacion_update.php`
- Entrada: `post.id_direccion:integer`, `post.id_ubi:integer`, `post.observ:string`, `post.pres_mail:string`, `post.pres_nom:string`, `post.pres_telf:string`, `post.zona:string`
- Respuesta: `standard_envelope_string_data`

## `/src/cartaspresentacion/cartas_presentacion_buscar_data`

- Id: `cartaspresentacion.cartas_presentacion_buscar_data`
- Controller: `src/cartaspresentacion/infrastructure/ui/http/controllers/cartas_presentacion_buscar_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/cartaspresentacion/cartas_presentacion_lista_data`

- Id: `cartaspresentacion.cartas_presentacion_lista_data`
- Controller: `src/cartaspresentacion/infrastructure/ui/http/controllers/cartas_presentacion_lista_data.php`
- Entrada: `post.dl:string`, `post.pais:string`, `post.poblacion:string`, `post.que:string`, `post.region:string`
- Respuesta: `standard_envelope_string_data`

## `/src/cartaspresentacion/cartas_presentacion_shell_data`

- Id: `cartaspresentacion.cartas_presentacion_shell_data`
- Controller: `src/cartaspresentacion/infrastructure/ui/http/controllers/cartas_presentacion_shell_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/cartaspresentacion/poblaciones_data`

- Id: `cartaspresentacion.poblaciones_data`
- Controller: `src/cartaspresentacion/infrastructure/ui/http/controllers/poblaciones_data.php`
- Entrada: `post.filtro:string`
- Respuesta: `standard_envelope_string_data`

## `/src/cartaspresentacion/ubis_lista_data`

- Id: `cartaspresentacion.ubis_lista_data`
- Controller: `src/cartaspresentacion/infrastructure/ui/http/controllers/ubis_lista_data.php`
- Entrada: `post.poblacion_sel:string`, `post.tipo_lista:string`
- Respuesta: `standard_envelope_string_data`
