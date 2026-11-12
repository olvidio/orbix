---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "actividadcargos"
endpoints: 6
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - actividadcargos

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/actividadcargos/cargo_editar`

- Id: `actividadcargos.cargo_editar`
- Controller: `src/actividadcargos/infrastructure/ui/http/controllers/cargo_editar.php`
- Entrada: `post.ctx_guardar:string` (HashB `cargo_editar`: `id_item`, `id_activ`, `id_nom`; mandan sobre el POST), `post.asis:string`, `post.asis_presente:string`, `post.id_cargo:integer`, `post.observ:string`, `post.puede_agd:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadcargos/cargo_eliminar`

- Id: `actividadcargos.cargo_eliminar`
- Controller: `src/actividadcargos/infrastructure/ui/http/controllers/cargo_eliminar.php`
- Entrada: `post.ctx_eliminar_cargo:string` (HashB `cargo_eliminar`: `id_item`, `elim_asis`; la firma cada fila de las listas 3102, 1302 y 3101)
- Respuesta: `standard_envelope_string_data`

## `/src/actividadcargos/cargo_nuevo`

- Id: `actividadcargos.cargo_nuevo`
- Controller: `src/actividadcargos/infrastructure/ui/http/controllers/cargo_nuevo.php`
- Entrada: `post.ctx_guardar:string` (HashB `cargo_nuevo`: `id_activ` y/o `id_nom` que fija el formulario; 0 = los elige el usuario), `post.asis:string`, `post.id_activ:integer`, `post.id_cargo:integer`, `post.id_nom:integer`, `post.observ:string`, `post.puede_agd:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadcargos/form_cargos_de_actividad_data`

- Id: `actividadcargos.form_cargos_de_actividad_data`
- Controller: `src/actividadcargos/infrastructure/ui/http/controllers/form_cargos_de_actividad_data.php`
- Entrada: `post.id_dossier:integer`, `post.id_nom:integer`, `post.id_pau:integer`, `post.mod:string`, `post.obj_pau:string`, `post.permiso:string`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadcargos/form_cargos_personas_en_actividad_data`

- Id: `actividadcargos.form_cargos_personas_en_actividad_data`
- Controller: `src/actividadcargos/infrastructure/ui/http/controllers/form_cargos_personas_en_actividad_data.php`
- Entrada: `post.id_dossier:integer`, `post.id_pau:integer`, `post.id_tipo:integer`, `post.mod:string`, `post.permiso:integer`, `post.que_dl:string`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadcargos/cargos_activ_resincronizar`

- Id: `actividadcargos.cargos_activ_resincronizar`
- Controller: `src/actividadcargos/infrastructure/ui/http/controllers/cargos_activ_resincronizar.php`
- Entrada: `post.aplicar:string`, `post.esquema:string`
- Respuesta: `standard_envelope_string_data`
