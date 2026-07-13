---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "actividadessacd"
endpoints: 14
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - actividadessacd

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/actividadessacd/com_sacd_activ_periodo_page_data`

- Id: `actividadessacd.com_sacd_activ_periodo_page_data`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/com_sacd_activ_periodo_page_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/actividadessacd/comunicacion_activ_sacd_data`

- Id: `actividadessacd.comunicacion_activ_sacd_data`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/comunicacion_activ_sacd_data.php`
- Entrada: `post.que:string`, `post.id_nom:integer`, `post.propuesta:string`, `post.periodo:string`, `post.year:string`, `post.empiezamin:string`, `post.empiezamax:string`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadessacd/comunicacion_activ_sacd_enviar`

- Id: `actividadessacd.comunicacion_activ_sacd_enviar`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/comunicacion_activ_sacd_enviar.php`
- Entrada: `post.que:string`, `post.id_nom:integer`, `post.propuesta:string`, `post.periodo:string`, `post.year:string`, `post.empiezamin:string`, `post.empiezamax:string`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadessacd/lista_actividades_sacd_data`

- Id: `actividadessacd.lista_actividades_sacd_data`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/lista_actividades_sacd_data.php`
- Entrada: `post.tipo:string`, `post.year:string`, `post.periodo:string`, `post.empiezamin:string`, `post.empiezamax:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadessacd/locales_desplegable_data`

- Id: `actividadessacd.locales_desplegable_data`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/locales_desplegable_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/actividadessacd/sacd_asignar`

- Id: `actividadessacd.sacd_asignar`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/sacd_asignar.php`
- Entrada: `post.id_activ:integer`, `post.id_nom:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadessacd/sacd_asignar_auto`

- Id: `actividadessacd.sacd_asignar_auto`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/sacd_asignar_auto.php`
- Entrada: `post.f_ini_iso:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadessacd/sacd_eliminar`

- Id: `actividadessacd.sacd_eliminar`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/sacd_eliminar.php`
- Entrada: `post.id_activ:integer`, `post.id_cargo:integer`, `post.id_nom:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadessacd/sacd_reordenar`

- Id: `actividadessacd.sacd_reordenar`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/sacd_reordenar.php`
- Entrada: `post.id_activ:integer`, `post.id_nom:integer`, `post.num_orden:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadessacd/sacds_disponibles_data`

- Id: `actividadessacd.sacds_disponibles_data`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/sacds_disponibles_data.php`
- Entrada: `post.id_activ:integer`, `post.seleccion:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadessacd/sacds_encargados_data`

- Id: `actividadessacd.sacds_encargados_data`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/sacds_encargados_data.php`
- Entrada: `post.id_activ:integer`, `post.id_tipo_activ:string`, `post.dl_org:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadessacd/solapes_sacd_data`

- Id: `actividadessacd.solapes_sacd_data`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/solapes_sacd_data.php`
- Entrada: `post.year:string`, `post.periodo:string`, `post.empiezamin:string`, `post.empiezamax:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadessacd/texto_comunicacion_data`

- Id: `actividadessacd.texto_comunicacion_data`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/texto_comunicacion_data.php`
- Entrada: `post.clave:string`, `post.idioma:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadessacd/texto_comunicacion_guardar`

- Id: `actividadessacd.texto_comunicacion_guardar`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/texto_comunicacion_guardar.php`
- Entrada: `post.clave:string`, `post.idioma:string`, `post.texto:string`
- Respuesta: `standard_envelope_string_data`
