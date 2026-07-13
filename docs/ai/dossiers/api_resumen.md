---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "dossiers"
endpoints: 6
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - dossiers

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/dossiers/dossiers_lista_fichas_data`

- Id: `dossiers.dossiers_lista_fichas_data`
- Controller: `src/dossiers/infrastructure/ui/http/controllers/dossiers_lista_fichas_data.php`
- Entrada: `post.id_pau:integer`, `post.obj_pau:string`, `post.pau:string`
- Respuesta: `standard_envelope_string_data`

## `/src/dossiers/dossiers_ver_pantalla_data`

- Id: `dossiers.dossiers_ver_pantalla_data`
- Controller: `src/dossiers/infrastructure/ui/http/controllers/dossiers_ver_pantalla_data.php`
- Entrada: `post.clase_info:string`, `post.id_activ:integer`, `post.id_dossier:string`, `post.id_pau:integer`, `post.mod:string`, `post.modo_curso:integer`, `post.obj_pau:string`, `post.pau:string`, `post.permiso:string`, `post.que:string`, `post.queSel:string`, `post.refresh:integer`, `post.restored_id_sel:mixed`, `post.restored_scroll_id:integer`, `post.scroll_id:integer`, `post.sel:mixed`, `post.stack:string`
- Respuesta: `standard_envelope_string_data`

## `/src/dossiers/perm_dossier_ver_data`

- Id: `dossiers.perm_dossier_ver_data`
- Controller: `src/dossiers/infrastructure/ui/http/controllers/perm_dossier_ver_data.php`
- Entrada: `post.id_tipo_dossier:integer`, `post.tipo:string`
- Respuesta: `standard_envelope_string_data`

## `/src/dossiers/perm_dossiers_data`

- Id: `dossiers.perm_dossiers_data`
- Controller: `src/dossiers/infrastructure/ui/http/controllers/perm_dossiers_data.php`
- Entrada: `post.tipo:string`
- Respuesta: `standard_envelope_string_data`

## `/src/dossiers/tipo_dossier_eliminar`

- Id: `dossiers.tipo_dossier_eliminar`
- Controller: `src/dossiers/infrastructure/ui/http/controllers/tipo_dossier_eliminar.php`
- Entrada: `post.id_tipo_dossier:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/dossiers/tipo_dossier_guardar`

- Id: `dossiers.tipo_dossier_guardar`
- Controller: `src/dossiers/infrastructure/ui/http/controllers/tipo_dossier_guardar.php`
- Entrada: `post.Permiso_escritura:array`, `post.Permiso_lectura:array`, `post.app:string`, `post.campo_to:string`, `post.class:string`, `post.codigo:string`, `post.depende_modificar:string`, `post.descripcion:string`, `post.id_tipo_dossier:integer`, `post.id_tipo_dossier_rel:integer`, `post.tabla_from:string`, `post.tabla_to:string`
- Respuesta: `standard_envelope_string_data`
