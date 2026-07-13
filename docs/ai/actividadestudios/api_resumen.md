---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "actividadestudios"
endpoints: 27
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - actividadestudios

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/actividadestudios/acta_notas_data`

- Id: `actividadestudios.acta_notas_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/acta_notas_data.php`
- Entrada: `post.id_activ:integer`, `post.id_asignatura:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadestudios/acta_notas_definitivas_grabar`

- Id: `actividadestudios.acta_notas_definitivas_grabar`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/acta_notas_definitivas_grabar.php`
- Entrada: `post.id_activ:integer`, `post.id_asignatura:integer`
- Respuesta: `raw_response`

## `/src/actividadestudios/acta_notas_matricula_guardar`

- Id: `actividadestudios.acta_notas_matricula_guardar`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/acta_notas_matricula_guardar.php`
- Entrada: `post.acta_nota:array`, `post.form_preceptor:array`, `post.id_activ:integer`, `post.id_asignatura:integer`, `post.id_nom:array`, `post.nota_max:array`, `post.nota_num:array`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadestudios/actividad_asignatura_editar`

- Id: `actividadestudios.actividad_asignatura_editar`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/actividad_asignatura_editar.php`
- Entrada: `post.avis_profesor:string`, `post.f_fin:string`, `post.f_ini:string`, `post.id_activ:integer`, `post.id_asignatura:integer`, `post.id_profesor:integer`, `post.tipo:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadestudios/actividad_asignatura_eliminar`

- Id: `actividadestudios.actividad_asignatura_eliminar`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/actividad_asignatura_eliminar.php`
- Entrada: `post.id_activ:integer`, `post.id_asignatura:integer`, `post.pau:string`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadestudios/actividad_asignatura_nueva`

- Id: `actividadestudios.actividad_asignatura_nueva`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/actividad_asignatura_nueva.php`
- Entrada: `post.avis_profesor:string`, `post.f_fin:string`, `post.f_ini:string`, `post.id_activ:integer`, `post.id_asignatura:integer`, `post.id_profesor:integer`, `post.tipo:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadestudios/asistente_observ`

- Id: `actividadestudios.asistente_observ`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/asistente_observ.php`
- Entrada: `post.id_activ:integer`, `post.id_nom:integer`, `post.id_pau:integer`, `post.observ:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadestudios/asistente_observ_est`

- Id: `actividadestudios.asistente_observ_est`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/asistente_observ_est.php`
- Entrada: `post.id_activ:integer`, `post.id_nom:integer`, `post.id_pau:integer`, `post.observ_est:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadestudios/asistente_plan_est_ok`

- Id: `actividadestudios.asistente_plan_est_ok`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/asistente_plan_est_ok.php`
- Entrada: `post.est_ok:string`, `post.id_activ:integer`, `post.id_nom:integer`, `post.id_pau:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadestudios/ca_posibles_data`

- Id: `actividadestudios.ca_posibles_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/ca_posibles_data.php`
- Entrada: `post.na:string`, `post.id_ctr_n:integer`, `post.id_ctr_agd:integer`, `post.ca_estudios:string`, `post.ca_repaso:string`, `post.ca_todos:string`, `post.grupo_estudios:string`, `post.periodo:string`, `post.year:integer`, `post.empiezamin:string`, `post.empiezamax:string`, `post.idca:string`, `post.texto:string`, `post.ref:string`, `post.obj_pau:string`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadestudios/ca_posibles_que_data`

- Id: `actividadestudios.ca_posibles_que_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/ca_posibles_que_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/actividadestudios/docencia_actualizar`

- Id: `actividadestudios.docencia_actualizar`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/docencia_actualizar.php`
- Entrada: `post.empiezamax:string`, `post.empiezamin:string`, `post.periodo:string`, `post.year:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadestudios/e43_data`

- Id: `actividadestudios.e43_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/e43_data.php`
- Entrada: `post.id_nom:integer`, `post.id_activ:integer`, `post.append_blank_footer:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadestudios/e43_imprimir_mpdf_data`

- Id: `actividadestudios.e43_imprimir_mpdf_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/e43_imprimir_mpdf_data.php`
- Entrada: `post.append_blank_footer:mixed`, `post.id_activ:integer`, `post.id_nom:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadestudios/form_asignaturas_de_una_actividad_data`

- Id: `actividadestudios.form_asignaturas_de_una_actividad_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/form_asignaturas_de_una_actividad_data.php`
- Entrada: `post.sel:array`, `post.pau:string`, `post.id_pau:integer`, `post.id_activ:integer`, `post.id_asignatura:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadestudios/form_matriculas_de_una_persona_data`

- Id: `actividadestudios.form_matriculas_de_una_persona_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/form_matriculas_de_una_persona_data.php`
- Entrada: `post.sel:array`, `post.id_nom:integer`, `post.id_pau:integer`, `post.id_activ:integer`, `post.id_asignatura:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadestudios/lista_clases_ca_data`

- Id: `actividadestudios.lista_clases_ca_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/lista_clases_ca_data.php`
- Entrada: `post.id_activ:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadestudios/matricula_automatica`

- Id: `actividadestudios.matricula_automatica`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/matricula_automatica.php`
- Entrada: `post.id_activ:integer`, `post.id_pau:integer`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadestudios/matricula_editar`

- Id: `actividadestudios.matricula_editar`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/matricula_editar.php`
- Entrada: `post.id_activ:integer`, `post.id_asignatura:integer`, `post.id_nivel:integer`, `post.id_nom:integer`, `post.id_pau:integer`, `post.id_preceptor:integer`, `post.id_situacion:integer`, `post.preceptor:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadestudios/matricula_eliminar`

- Id: `actividadestudios.matricula_eliminar`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/matricula_eliminar.php`
- Entrada: `post.id_activ:integer`, `post.id_asignatura:integer`, `post.id_nom:integer`, `post.id_pau:integer`, `post.pau:string`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadestudios/matricula_nueva`

- Id: `actividadestudios.matricula_nueva`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/matricula_nueva.php`
- Entrada: `post.id_activ:integer`, `post.id_asignatura:integer`, `post.id_nivel:integer`, `post.id_nom:integer`, `post.id_pau:integer`, `post.id_preceptor:integer`, `post.id_situacion:integer`, `post.preceptor:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadestudios/matriculas_lista_data`

- Id: `actividadestudios.matriculas_lista_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/matriculas_lista_data.php`
- Entrada: `post.inicioIso:string`, `post.finIso:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadestudios/matriculas_lista_otras_r_data`

- Id: `actividadestudios.matriculas_lista_otras_r_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/matriculas_lista_otras_r_data.php`
- Entrada: `post.apellido1:string`, `post.esquema_region_stgr:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadestudios/matriculas_pendientes_data`

- Id: `actividadestudios.matriculas_pendientes_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/matriculas_pendientes_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/actividadestudios/plan_estudios_ca_data`

- Id: `actividadestudios.plan_estudios_ca_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/plan_estudios_ca_data.php`
- Entrada: `post.id_activ:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadestudios/posibles_asignaturas_ca_data`

- Id: `actividadestudios.posibles_asignaturas_ca_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/posibles_asignaturas_ca_data.php`
- Entrada: `post.id_activ:integer`, `post.nom_activ:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadestudios/profesores_desplegable_data`

- Id: `actividadestudios.profesores_desplegable_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/profesores_desplegable_data.php`
- Entrada: `post.salida:string`, `post.id_asignatura:integer`, `post.id_activ:integer`, `post.id_profesor:integer`
- Respuesta: `standard_envelope_string_data`
