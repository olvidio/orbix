---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "casas"
endpoints: 15
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - casas

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/casas/calendario_ubi_resumen_data`

- Id: `casas.calendario_ubi_resumen_data`
- Controller: `src/casas/infrastructure/ui/http/controllers/calendario_ubi_resumen_data.php`
- Entrada: `post.G:integer`, `post.id_ubi:integer`, `post.inc_t:integer`, `post.seccion:string`
- Respuesta: `standard_envelope_string_data`

## `/src/casas/casa_actividades_lista_data`

- Id: `casas.casa_actividades_lista_data`
- Controller: `src/casas/infrastructure/ui/http/controllers/casa_actividades_lista_data.php`
- Entrada: `post.empiezamax:string`, `post.empiezamin:string`, `post.id_cdc:array`, `post.periodo:string`, `post.year:string`
- Respuesta: `standard_envelope_string_data`

## `/src/casas/casa_ec_gastos_form_data`

- Id: `casas.casa_ec_gastos_form_data`
- Controller: `src/casas/infrastructure/ui/http/controllers/casa_ec_gastos_form_data.php`
- Entrada: `post.id_cdc:array`, `post.year:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/casas/casa_ec_gastos_guardar`

- Id: `casas.casa_ec_gastos_guardar`
- Controller: `src/casas/infrastructure/ui/http/controllers/casa_ec_gastos_guardar.php`
- Entrada: `post.ap_sf$m:string`, `post.ap_sv$m:string`, `post.g$m:string`, `post.id_ubi:integer`, `post.year:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/casas/casa_ingreso_eliminar`

- Id: `casas.casa_ingreso_eliminar`
- Controller: `src/casas/infrastructure/ui/http/controllers/casa_ingreso_eliminar.php`
- Entrada: `post.id_activ:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/casas/casa_ingreso_form_data`

- Id: `casas.casa_ingreso_form_data`
- Controller: `src/casas/infrastructure/ui/http/controllers/casa_ingreso_form_data.php`
- Entrada: `post.id_activ:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/casas/casa_ingreso_update`

- Id: `casas.casa_ingreso_update`
- Controller: `src/casas/infrastructure/ui/http/controllers/casa_ingreso_update.php`
- Entrada: `post.id_activ:integer`, `post.id_tarifa:mixed`, `post.ingresos:string`, `post.num_asistentes:integer`, `post.observ:string`, `post.precio:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/casas/casa_ingresos_lista_data`

- Id: `casas.casa_ingresos_lista_data`
- Controller: `src/casas/infrastructure/ui/http/controllers/casa_ingresos_lista_data.php`
- Entrada: `post.empiezamax:string`, `post.empiezamin:string`, `post.id_cdc:array`, `post.periodo:string`, `post.year:string`
- Respuesta: `standard_envelope_string_data`

## `/src/casas/casas_resumen_data`

- Id: `casas.casas_resumen_data`
- Controller: `src/casas/infrastructure/ui/http/controllers/casas_resumen_data.php`
- Entrada: `post.cdc_sel:integer`, `post.empiezamax:string`, `post.empiezamin:string`, `post.id_cdc:array`, `post.periodo:string`, `post.que:string`, `post.year:string`
- Respuesta: `standard_envelope_string_data`

## `/src/casas/grupo_eliminar`

- Id: `casas.grupo_eliminar`
- Controller: `src/casas/infrastructure/ui/http/controllers/grupo_eliminar.php`
- Entrada: `post.id_item:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/casas/grupo_form_data`

- Id: `casas.grupo_form_data`
- Controller: `src/casas/infrastructure/ui/http/controllers/grupo_form_data.php`
- Entrada: `post.id_item:string`
- Respuesta: `standard_envelope_string_data`

## `/src/casas/grupo_lista_data`

- Id: `casas.grupo_lista_data`
- Controller: `src/casas/infrastructure/ui/http/controllers/grupo_lista_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/casas/grupo_update`

- Id: `casas.grupo_update`
- Controller: `src/casas/infrastructure/ui/http/controllers/grupo_update.php`
- Entrada: `post.id_item:string`, `post.id_ubi_hijo:integer`, `post.id_ubi_padre:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/casas/ingreso_plazas_previstas_update`

- Id: `casas.ingreso_plazas_previstas_update`
- Controller: `src/casas/infrastructure/ui/http/controllers/ingreso_plazas_previstas_update.php`
- Entrada: `post.colName:string`, `post.data:string`
- Respuesta: `standard_envelope_string_data`

## `/src/casas/prevision_asistentes_data`

- Id: `casas.prevision_asistentes_data`
- Controller: `src/casas/infrastructure/ui/http/controllers/prevision_asistentes_data.php`
- Entrada: `post.fin_iso:string`, `post.inicio_iso:string`, `post.mi_of:string`, `post.periodo:string`
- Respuesta: `standard_envelope_string_data`
