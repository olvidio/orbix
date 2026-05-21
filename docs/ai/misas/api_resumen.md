---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "misas"
endpoints: 32
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - misas

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/misas/anadir_ctr_tarea`

- Id: `misas.anadir_ctr_tarea`
- Controller: `src/misas/infrastructure/ui/http/controllers/anadir_ctr_tarea.php`
- Entrada: `post.id_item:integer`, `post.id_tarea:integer`, `post.id_ubi:integer`, `post.que:string`
- Respuesta: `standard_envelope_string_data`

## `/src/misas/buscar_plan_ctr_data`

- Id: `misas.buscar_plan_ctr_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/buscar_plan_ctr_data.php`
- Entrada: `post.id_zona:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/misas/buscar_plan_sacd_data`

- Id: `misas.buscar_plan_sacd_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/buscar_plan_sacd_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/misas/cambiar_status_data`

- Id: `misas.cambiar_status_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/cambiar_status_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/misas/crear_nuevo_periodo_data`

- Id: `misas.crear_nuevo_periodo_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/crear_nuevo_periodo_data.php`
- Entrada: `post.empiezamax:string`, `post.empiezamin:string`, `post.id_zona:integer`, `post.orden:string`, `post.periodo:string`, `post.seleccion:integer`, `post.tipo_plantilla:string`
- Respuesta: `standard_envelope_string_data`

## `/src/misas/cuadricula_update`

- Id: `misas.cuadricula_update`
- Controller: `src/misas/infrastructure/ui/http/controllers/cuadricula_update.php`
- Entrada: `post.dia:string`, `post.id_enc:integer`, `post.id_zona:integer`, `post.key:string`, `post.observ:string`, `post.tend:string`, `post.tipo_plantilla:string`, `post.tstart:string`, `post.uuid_item:string`
- Respuesta: `standard_envelope_string_data`

## `/src/misas/desplegable_encargos`

- Id: `misas.desplegable_encargos`
- Controller: `src/misas/infrastructure/ui/http/controllers/desplegable_encargos.php`
- Entrada: `post.id_enc:mixed`, `post.id_zona:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/misas/desplegable_sacd`

- Id: `misas.desplegable_sacd`
- Controller: `src/misas/infrastructure/ui/http/controllers/desplegable_sacd.php`
- Entrada: `post.dia:string`, `post.id_sacd:integer`, `post.id_zona:integer`, `post.seleccion:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/misas/eliminar_encargo_centro`

- Id: `misas.eliminar_encargo_centro`
- Controller: `src/misas/infrastructure/ui/http/controllers/eliminar_encargo_centro.php`
- Entrada: `post.id_item:string`
- Respuesta: `standard_envelope_string_data`

## `/src/misas/eliminar_encargo_zona`

- Id: `misas.eliminar_encargo_zona`
- Controller: `src/misas/infrastructure/ui/http/controllers/eliminar_encargo_zona.php`
- Entrada: `post.id_enc:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/misas/guardar_encargo_centro`

- Id: `misas.guardar_encargo_centro`
- Controller: `src/misas/infrastructure/ui/http/controllers/guardar_encargo_centro.php`
- Entrada: `post.id_ctr:integer`, `post.id_enc:integer`, `post.id_item:string`
- Respuesta: `standard_envelope_string_data`

## `/src/misas/guardar_encargo_zona`

- Id: `misas.guardar_encargo_zona`
- Controller: `src/misas/infrastructure/ui/http/controllers/guardar_encargo_zona.php`
- Entrada: `post.descripcion_lugar:string`, `post.encargo:string`, `post.id_enc:integer`, `post.id_tipo_enc:integer`, `post.id_ubi:integer`, `post.id_zona:integer`, `post.idioma_enc:string`, `post.observ:string`, `post.orden:integer`, `post.prioridad:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/misas/guardar_horario`

- Id: `misas.guardar_horario`
- Controller: `src/misas/infrastructure/ui/http/controllers/guardar_horario.php`
- Entrada: `post.id_item_h:integer`, `post.t_end:string`, `post.t_start:string`
- Respuesta: `standard_envelope_string_data`

## `/src/misas/horario_tarea_data`

- Id: `misas.horario_tarea_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/horario_tarea_data.php`
- Entrada: `post.id_item_h:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/misas/importar_plantilla_data`

- Id: `misas.importar_plantilla_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/importar_plantilla_data.php`
- Entrada: `post.id_zona:integer`, `post.tipo_plantilla_destino:string`, `post.tipo_plantilla_origen:string`
- Respuesta: `standard_envelope_string_data`

## `/src/misas/modificar_encargos_centros_data`

- Id: `misas.modificar_encargos_centros_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/modificar_encargos_centros_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/misas/modificar_encargos_data`

- Id: `misas.modificar_encargos_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/modificar_encargos_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/misas/modificar_iniciales_sacd_zona_data`

- Id: `misas.modificar_iniciales_sacd_zona_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/modificar_iniciales_sacd_zona_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/misas/modificar_plantilla_data`

- Id: `misas.modificar_plantilla_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/modificar_plantilla_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/misas/nuevo_status`

- Id: `misas.nuevo_status`
- Controller: `src/misas/infrastructure/ui/http/controllers/nuevo_status.php`
- Entrada: `post.empiezamax:string`, `post.empiezamin:string`, `post.estado:integer`, `post.id_zona:integer`, `post.periodo:string`
- Respuesta: `standard_envelope_string_data`

## `/src/misas/plan_de_misas_pantalla_data`

- Id: `misas.plan_de_misas_pantalla_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/plan_de_misas_pantalla_data.php`
- Entrada: `post.pantalla:string`
- Respuesta: `standard_envelope_string_data`

## `/src/misas/quitar_horario`

- Id: `misas.quitar_horario`
- Controller: `src/misas/infrastructure/ui/http/controllers/quitar_horario.php`
- Entrada: `post.id_item:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/misas/update_iniciales`

- Id: `misas.update_iniciales`
- Controller: `src/misas/infrastructure/ui/http/controllers/update_iniciales.php`
- Entrada: `post.color:string`, `post.id_sacd:integer`, `post.iniciales:string`
- Respuesta: `standard_envelope_string_data`

## `/src/misas/ver_cuadricula_zona_data`

- Id: `misas.ver_cuadricula_zona_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/ver_cuadricula_zona_data.php`
- Entrada: `post.columna:integer`, `post.empiezamax:string`, `post.empiezamin:string`, `post.fila:integer`, `post.id_zona:integer`, `post.orden:string`, `post.periodo:string`, `post.seleccion:integer`, `post.tipo_plantilla:string`
- Respuesta: `standard_envelope_string_data`

## `/src/misas/ver_encargos_centros_data`

- Id: `misas.ver_encargos_centros_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/ver_encargos_centros_data.php`
- Entrada: `post.id_zona:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/misas/ver_encargos_zona_data`

- Id: `misas.ver_encargos_zona_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/ver_encargos_zona_data.php`
- Entrada: `post.id_zona:integer`, `post.orden:string`
- Respuesta: `standard_envelope_string_data`

## `/src/misas/ver_iniciales_zona_data`

- Id: `misas.ver_iniciales_zona_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/ver_iniciales_zona_data.php`
- Entrada: `post.id_zona:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/misas/ver_misas_zona_data`

- Id: `misas.ver_misas_zona_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/ver_misas_zona_data.php`
- Entrada: `post.empiezamax:string`, `post.empiezamin:string`, `post.id_zona:integer`, `post.seleccion:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/misas/ver_plan_ctr_data`

- Id: `misas.ver_plan_ctr_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/ver_plan_ctr_data.php`
- Entrada: `post.empiezamax:string`, `post.empiezamin:string`, `post.id_ubi:integer`, `post.periodo:string`
- Respuesta: `standard_envelope_string_data`

## `/src/misas/ver_plan_sacd_data`

- Id: `misas.ver_plan_sacd_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/ver_plan_sacd_data.php`
- Entrada: `post.empiezamax:string`, `post.empiezamin:string`, `post.id_sacd:string`, `post.periodo:string`
- Respuesta: `standard_envelope_string_data`

## `/src/misas/zona_sacd_datos_get`

- Id: `misas.zona_sacd_datos_get`
- Controller: `src/misas/infrastructure/ui/http/controllers/zona_sacd_datos_get.php`
- Entrada: `post.id_sacd:integer`, `post.id_zona:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/misas/zona_sacd_datos_put`

- Id: `misas.zona_sacd_datos_put`
- Controller: `src/misas/infrastructure/ui/http/controllers/zona_sacd_datos_put.php`
- Entrada: `post.dw1:string`, `post.dw2:string`, `post.dw3:string`, `post.dw4:string`, `post.dw5:string`, `post.dw6:string`, `post.dw7:string`, `post.id_sacd:integer`, `post.id_zona:integer`
- Respuesta: `standard_envelope_string_data`
