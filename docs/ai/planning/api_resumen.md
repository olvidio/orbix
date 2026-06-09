---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "planning"
endpoints: 7
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - planning

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/planning/planning_casa_que_data`

- Id: `planning.planning_casa_que_data`
- Controller: `src/planning/infrastructure/ui/http/controllers/planning_casa_que_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/planning/planning_casa_ver_data`

- Id: `planning.planning_casa_ver_data`
- Controller: `src/planning/infrastructure/ui/http/controllers/planning_casa_ver_data.php`
- Entrada: `post.cdc_sel:integer`, `post.f_fin_iso:string`, `post.f_ini_iso:string`, `post.sSeleccionados:string`, `post.sin_activ:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/planning/planning_ctr_select_data`

- Id: `planning.planning_ctr_select_data`
- Controller: `src/planning/infrastructure/ui/http/controllers/planning_ctr_select_data.php`
- Entrada: `post.ctr:string`, `post.empiezamax:string`, `post.empiezamin:string`, `post.periodo:string`, `post.sacd:string`, `post.todos_agd:string`, `post.todos_n:string`, `post.todos_s:string`, `post.year:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/planning/planning_persona_select_data`

- Id: `planning.planning_persona_select_data`
- Controller: `src/planning/infrastructure/ui/http/controllers/planning_persona_select_data.php`
- Entrada: `post.apellido1:string`, `post.apellido2:string`, `post.centro:string`, `post.na:string`, `post.nombre:string`, `post.obj_pau:string`
- Respuesta: `standard_envelope_string_data`

## `/src/planning/planning_persona_ver_data`

- Id: `planning.planning_persona_ver_data`
- Controller: `src/planning/infrastructure/ui/http/controllers/planning_persona_ver_data.php`
- Entrada: `post.empiezamax:string`, `post.empiezamin:string`, `post.obj_pau:string`, `post.periodo:string`, `post.sel:array`, `post.year:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/planning/planning_zones_que_data`

- Id: `planning.planning_zones_que_data`
- Controller: `src/planning/infrastructure/ui/http/controllers/planning_zones_que_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/planning/planning_zones_select_data`

- Id: `planning.planning_zones_select_data`
- Controller: `src/planning/infrastructure/ui/http/controllers/planning_zones_select_data.php`
- Entrada: `post.actividad:string`, `post.id_zona:string`, `post.propuesta:string`, `post.trimestre:integer`, `post.year:integer`
- Respuesta: `standard_envelope_string_data`
