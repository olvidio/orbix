---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "actividadessacd"
endpoints: 15
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
- Respuesta: `standard_envelope_string_data` (incluye `ctx_enviar`, cápsula `HashB` acción `comunicacion_activ_sacd_enviar`, contexto `{que, id_nom, propuesta}`)

## `/src/actividadessacd/comunicacion_activ_sacd_enviar`

- Id: `actividadessacd.comunicacion_activ_sacd_enviar`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/comunicacion_activ_sacd_enviar.php`
- Entrada: `post.ctx_enviar:string` (cápsula `HashB` acción `comunicacion_activ_sacd_enviar`, contexto `{que, id_nom, propuesta}`; emitida por `comunicacion_activ_sacd_data`), más filtros de periodo del formulario (`post.periodo`, `post.year`, …)
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
- Entrada: `post.ctx_asignar:string` (cápsula `HashB` acción `sacd_asignar`, contexto `{id_activ, id_nom}`; emitida por `sacds_disponibles_data`)
- Respuesta: `standard_envelope_string_data`

## `/src/actividadessacd/sacd_asignar_auto`

- Id: `actividadessacd.sacd_asignar_auto`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/sacd_asignar_auto.php`
- Entrada: `post.ctx_asignar_auto:string` (cápsula `HashB` acción `sacd_asignar_auto`, contexto `{f_ini_iso}`; emitida por `sacd_asignar_auto_form_data`)
- Respuesta: `standard_envelope_string_data`

## `/src/actividadessacd/sacd_asignar_auto_form_data`

- Id: `actividadessacd.sacd_asignar_auto_form_data`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/sacd_asignar_auto_form_data.php`
- Entrada: ninguna
- Respuesta: `standard_envelope_string_data` (`f_ini_iso`, `ctx_asignar_auto`)

## `/src/actividadessacd/sacd_eliminar`

- Id: `actividadessacd.sacd_eliminar`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/sacd_eliminar.php`
- Entrada: `post.ctx_eliminar:string` (cápsula `HashB` acción `sacd_eliminar`, contexto `{id_activ, id_nom, id_cargo}`; emitida por `lista_actividades_sacd_data` y `sacds_encargados_data`)
- Respuesta: `standard_envelope_string_data`

## `/src/actividadessacd/sacd_reordenar`

- Id: `actividadessacd.sacd_reordenar`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/sacd_reordenar.php`
- Entrada: `post.ctx_reordenar:string` (cápsula `HashB` acción `sacd_reordenar`, contexto `{id_activ, id_nom}`; emitida por `lista_actividades_sacd_data` y `sacds_encargados_data`), `post.num_orden:string`
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
- Respuesta: `standard_envelope_string_data` (incluye `ctx_guardar` cuando clave e idioma no están vacíos)

## `/src/actividadessacd/texto_comunicacion_guardar`

- Id: `actividadessacd.texto_comunicacion_guardar`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/texto_comunicacion_guardar.php`
- Entrada: `post.ctx_guardar:string` (cápsula `HashB` acción `texto_comunicacion_guardar`, contexto `{clave, idioma}`; emitida por `texto_comunicacion_data`), `post.texto:string`
- Respuesta: `standard_envelope_string_data`
