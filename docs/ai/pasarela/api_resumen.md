---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "pasarela"
endpoints: 21
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - pasarela

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/pasarela/activacion_default_data`

- Id: `pasarela.activacion_default_data`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/activacion_default_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/pasarela/activacion_default_guardar`

- Id: `pasarela.activacion_default_guardar`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/activacion_default_guardar.php`
- Entrada: `post.default:string`
- Respuesta: `standard_envelope_string_data`

## `/src/pasarela/activacion_excepcion_eliminar`

- Id: `pasarela.activacion_excepcion_eliminar`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/activacion_excepcion_eliminar.php`
- Entrada: `post.id_tipo_activ:string`
- Respuesta: `standard_envelope_string_data`

## `/src/pasarela/activacion_excepcion_guardar`

- Id: `pasarela.activacion_excepcion_guardar`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/activacion_excepcion_guardar.php`
- Entrada: `post.id_tipo_activ:string`, `post.valor:string`
- Respuesta: `standard_envelope_string_data`

## `/src/pasarela/activacion_lista`

- Id: `pasarela.activacion_lista`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/activacion_lista.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/pasarela/contribucion_no_duerme_default_data`

- Id: `pasarela.contribucion_no_duerme_default_data`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/contribucion_no_duerme_default_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/pasarela/contribucion_no_duerme_default_guardar`

- Id: `pasarela.contribucion_no_duerme_default_guardar`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/contribucion_no_duerme_default_guardar.php`
- Entrada: `post.default:string`
- Respuesta: `standard_envelope_string_data`

## `/src/pasarela/contribucion_no_duerme_excepcion_eliminar`

- Id: `pasarela.contribucion_no_duerme_excepcion_eliminar`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/contribucion_no_duerme_excepcion_eliminar.php`
- Entrada: `post.id_tipo_activ:string`
- Respuesta: `standard_envelope_string_data`

## `/src/pasarela/contribucion_no_duerme_excepcion_guardar`

- Id: `pasarela.contribucion_no_duerme_excepcion_guardar`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/contribucion_no_duerme_excepcion_guardar.php`
- Entrada: `post.id_tipo_activ:string`, `post.valor:string`
- Respuesta: `standard_envelope_string_data`

## `/src/pasarela/contribucion_no_duerme_lista`

- Id: `pasarela.contribucion_no_duerme_lista`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/contribucion_no_duerme_lista.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/pasarela/contribucion_reserva_default_data`

- Id: `pasarela.contribucion_reserva_default_data`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/contribucion_reserva_default_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/pasarela/contribucion_reserva_default_guardar`

- Id: `pasarela.contribucion_reserva_default_guardar`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/contribucion_reserva_default_guardar.php`
- Entrada: `post.default:string`
- Respuesta: `standard_envelope_string_data`

## `/src/pasarela/contribucion_reserva_excepcion_eliminar`

- Id: `pasarela.contribucion_reserva_excepcion_eliminar`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/contribucion_reserva_excepcion_eliminar.php`
- Entrada: `post.id_tipo_activ:string`
- Respuesta: `standard_envelope_string_data`

## `/src/pasarela/contribucion_reserva_excepcion_guardar`

- Id: `pasarela.contribucion_reserva_excepcion_guardar`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/contribucion_reserva_excepcion_guardar.php`
- Entrada: `post.id_tipo_activ:string`, `post.valor:string`
- Respuesta: `standard_envelope_string_data`

## `/src/pasarela/contribucion_reserva_lista`

- Id: `pasarela.contribucion_reserva_lista`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/contribucion_reserva_lista.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/pasarela/exportar_actividades_data`

- Id: `pasarela.exportar_actividades_data`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/exportar_actividades_data.php`
- Entrada: `post.fin_iso:string`, `post.iactividad_val:string`, `post.iasistentes_val:string`, `post.id_cdc:array`, `post.id_tipo_activ:string`, `post.inicio_iso:string`, `post.isfsv_val:string`
- Respuesta: `standard_envelope_string_data`

## `/src/pasarela/exportar_que_actividad_tipo_html`

- Id: `pasarela.exportar_que_actividad_tipo_html`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/exportar_que_actividad_tipo_html.php`
- Entrada: `post.id_tipo_activ:string`, `post.sactividad:string`, `post.sasistentes:string`, `post.snom_tipo:string`
- Respuesta: `standard_envelope_string_data`

## `/src/pasarela/nombre_excepcion_eliminar`

- Id: `pasarela.nombre_excepcion_eliminar`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/nombre_excepcion_eliminar.php`
- Entrada: `post.id_tipo_activ:string`
- Respuesta: `standard_envelope_string_data`

## `/src/pasarela/nombre_excepcion_guardar`

- Id: `pasarela.nombre_excepcion_guardar`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/nombre_excepcion_guardar.php`
- Entrada: `post.id_tipo_activ:string`, `post.valor:string`
- Respuesta: `standard_envelope_string_data`

## `/src/pasarela/nombre_lista`

- Id: `pasarela.nombre_lista`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/nombre_lista.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/pasarela/tipo_activ_txt_data`

- Id: `pasarela.tipo_activ_txt_data`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/tipo_activ_txt_data.php`
- Entrada: `post.id_tipo_activ:string`
- Respuesta: `standard_envelope_string_data`
