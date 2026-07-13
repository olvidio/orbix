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
- Entrada: `post.id_ubi:integer`, `post.year:integer`, `post.g1:string`, `post.g2:string`, `post.g3:string`, `post.g4:string`, `post.g5:string`, `post.g6:string`, `post.g7:string`, `post.g8:string`, `post.g9:string`, `post.g10:string`, `post.g11:string`, `post.g12:string`, `post.ap_sv1:string`, `post.ap_sv2:string`, `post.ap_sv3:string`, `post.ap_sv4:string`, `post.ap_sv5:string`, `post.ap_sv6:string`, `post.ap_sv7:string`, `post.ap_sv8:string`, `post.ap_sv9:string`, `post.ap_sv10:string`, `post.ap_sv11:string`, `post.ap_sv12:string`, `post.ap_sf1:string`, `post.ap_sf2:string`, `post.ap_sf3:string`, `post.ap_sf4:string`, `post.ap_sf5:string`, `post.ap_sf6:string`, `post.ap_sf7:string`, `post.ap_sf8:string`, `post.ap_sf9:string`, `post.ap_sf10:string`, `post.ap_sf11:string`, `post.ap_sf12:string`
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
- Entrada: `post.id_activ:integer`, `post.id_tarifa:string`, `post.ingresos:string`, `post.num_asistentes:integer`, `post.observ:string`, `post.precio:string`
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
