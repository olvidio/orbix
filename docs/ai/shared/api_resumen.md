---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "shared"
endpoints: 6
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - shared

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/shared/locales_posibles`

- Id: `shared.locales_posibles`
- Controller: `src/shared/infrastructure/ui/http/controllers/locales_posibles.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/shared/tablaDB_buscar_datos`

- Id: `shared.tablaDB_buscar_datos`
- Controller: `src/shared/infrastructure/ui/http/controllers/tablaDB_buscar_datos.php`
- Entrada: `post.aSerieBuscar:string`, `post.clase_info:string`, `post.id_pau:integer`, `post.k_buscar:string`, `post.obj_pau:string`, `post.pau:string`
- Respuesta: `standard_envelope_string_data`

## `/src/shared/tablaDB_depende_datos`

- Id: `shared.tablaDB_depende_datos`
- Controller: `src/shared/infrastructure/ui/http/controllers/tablaDB_depende_datos.php`
- Entrada: `post.clase_info:string`, `post.opcion_sel:string`, `post.pKeyRepository:string`, `post.valor_depende:string`
- Respuesta: `standard_envelope_string_data`

## `/src/shared/tablaDB_formulario_datos`

- Id: `shared.tablaDB_formulario_datos`
- Controller: `src/shared/infrastructure/ui/http/controllers/tablaDB_formulario_datos.php`
- Entrada: `post.a_pkey:mixed`, `post.clase_info:mixed`, `post.mod:mixed`, `post.obj_pau:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/shared/tablaDB_lista_datos`

- Id: `shared.tablaDB_lista_datos`
- Controller: `src/shared/infrastructure/ui/http/controllers/tablaDB_lista_datos.php`
- Entrada: `post.clase_info:string`, `post.id_pau:integer`, `post.k_buscar:string`, `post.obj_pau:integer`, `post.pau:string`
- Respuesta: `standard_envelope_string_data`

## `/src/shared/tablaDB_update`

- Id: `shared.tablaDB_update`
- Controller: `src/shared/infrastructure/ui/http/controllers/tablaDB_update.php`
- Entrada: `post.clase_info:string`, `post.go_to:string`, `post.id_pau:string`, `post.mod:string`, `post.obj_pau:string`, `post.s_pkey:string`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`
