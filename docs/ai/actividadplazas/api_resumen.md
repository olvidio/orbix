---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "actividadplazas"
endpoints: 11
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - actividadplazas

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/actividadplazas/gestion_plazas_data`

- Id: `actividadplazas.gestion_plazas_data`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/gestion_plazas_data.php`
- Entrada: `post.empiezamax:string`, `post.empiezamin:string`, `post.id_tipo_activ:string`, `post.periodo:string`, `post.sactividad:string`, `post.sactividad2:string`, `post.sasistentes:string`, `post.year:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadplazas/gestion_plazas_update`

- Id: `actividadplazas.gestion_plazas_update`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/gestion_plazas_update.php`
- Entrada: `post.colName:string`, `post.data:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadplazas/peticiones_activ_data`

- Id: `actividadplazas.peticiones_activ_data`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/peticiones_activ_data.php`
- Entrada: `post.id_ctr_agd:integer`, `post.id_ctr_n:integer`, `post.id_nom:integer`, `post.na:string`, `post.que:string`, `post.sactividad:string`, `post.todos:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadplazas/peticiones_eliminar`

- Id: `actividadplazas.peticiones_eliminar`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/peticiones_eliminar.php`
- Entrada: `post.id_nom:integer`, `post.sactividad:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadplazas/peticiones_guardar`

- Id: `actividadplazas.peticiones_guardar`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/peticiones_guardar.php`
- Entrada: `post.actividades:array`, `post.id_nom:integer`, `post.sactividad:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadplazas/peticiones_incorporar`

- Id: `actividadplazas.peticiones_incorporar`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/peticiones_incorporar.php`
- Entrada: `post.sactividad:string`, `post.sasistentes:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadplazas/plazas_balance_data`

- Id: `actividadplazas.plazas_balance_data`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/plazas_balance_data.php`
- Entrada: `post.dl:string`, `post.id_tipo_activ:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadplazas/plazas_balance_que_data`

- Id: `actividadplazas.plazas_balance_que_data`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/plazas_balance_que_data.php`
- Entrada: `post.id_tipo_activ:string`, `post.sactividad:string`, `post.sasistentes:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadplazas/plazas_ceder`

- Id: `actividadplazas.plazas_ceder`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/plazas_ceder.php`
- Entrada: `post.id_activ:integer`, `post.num_plazas:integer`, `post.region_dl:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadplazas/posibles_propietarios_data`

- Id: `actividadplazas.posibles_propietarios_data`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/posibles_propietarios_data.php`
- Entrada: `post.id_activ:integer`, `post.id_nom:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadplazas/resumen_plazas_data`

- Id: `actividadplazas.resumen_plazas_data`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/resumen_plazas_data.php`
- Entrada: `post.id_activ:integer`, `post.nom_activ:string`
- Respuesta: `standard_envelope_string_data`
