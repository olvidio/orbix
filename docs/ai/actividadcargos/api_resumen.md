---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "actividadcargos"
endpoints: 5
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - actividadcargos

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/actividadcargos/cargo_editar`

- Id: `actividadcargos.cargo_editar`
- Controller: `src/actividadcargos/infrastructure/ui/http/controllers/cargo_editar.php`
- Entrada: `post.asis:string`, `post.asis_presente:string`, `post.id_activ:integer`, `post.id_cargo:integer`, `post.id_item:integer`, `post.id_nom:integer`, `post.observ:string`, `post.puede_agd:string`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadcargos/cargo_eliminar`

- Id: `actividadcargos.cargo_eliminar`
- Controller: `src/actividadcargos/infrastructure/ui/http/controllers/cargo_eliminar.php`
- Entrada: `post.elim_asis:integer`, `post.id_item:integer`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/actividadcargos/cargo_nuevo`

- Id: `actividadcargos.cargo_nuevo`
- Controller: `src/actividadcargos/infrastructure/ui/http/controllers/cargo_nuevo.php`
- Entrada: `post.asis:string`, `post.id_activ:integer`, `post.id_cargo:integer`, `post.id_nom:integer`, `post.observ:string`, `post.puede_agd:string`
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
