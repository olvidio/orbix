---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "devel_db_admin"
endpoints: 16
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - devel_db_admin

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/devel_db_admin/absorber_esquema`

- Id: `devel_db_admin.absorber_esquema`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/absorber_esquema.php`
- Entrada: `post.esquema_del:string`, `post.esquema_matriz:string`
- Respuesta: `standard_envelope_string_data`

## `/src/devel_db_admin/apptables_apps_data`

- Id: `devel_db_admin.apptables_apps_data`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/apptables_apps_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/devel_db_admin/apptables_update`

- Id: `devel_db_admin.apptables_update`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/apptables_update.php`
- Entrada: `post.accion:string`, `post.esquema:string`, `post.id_app:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/devel_db_admin/copiar_esquema`

- Id: `devel_db_admin.copiar_esquema`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/copiar_esquema.php`
- Entrada: `post.comun:integer`, `post.dl:string`, `post.esquema:string`, `post.region:string`, `post.sf:integer`, `post.sv:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/devel_db_admin/corregir_renombrar_esquema`

- Id: `devel_db_admin.corregir_renombrar_esquema`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/corregir_renombrar_esquema.php`
- Entrada: `post.comun:integer`, `post.dl:string`, `post.esquema:string`, `post.esquema_origen:string`, `post.region:string`, `post.sf:integer`, `post.sv:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/devel_db_admin/crear_esquema`

- Id: `devel_db_admin.crear_esquema`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/crear_esquema.php`
- Entrada: `post.comun:integer`, `post.dl:string`, `post.esquema:string`, `post.region:string`, `post.sf:integer`, `post.sv:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/devel_db_admin/crear_usuarios`

- Id: `devel_db_admin.crear_usuarios`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/crear_usuarios.php`
- Entrada: `post.dl:string`, `post.region:string`
- Respuesta: `standard_envelope_string_data`

## `/src/devel_db_admin/db_lugar`

- Id: `devel_db_admin.db_lugar`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/db_lugar.php`
- Entrada: `post.region:string`
- Respuesta: `standard_envelope_string_data`

## `/src/devel_db_admin/db_propiedades_data`

- Id: `devel_db_admin.db_propiedades_data`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/db_propiedades_data.php`
- Entrada: `post.default_esquema:string`, `post.op:string`, `post.tabla:string`
- Respuesta: `standard_envelope_string_data`

## `/src/devel_db_admin/eliminar_esquema`

- Id: `devel_db_admin.eliminar_esquema`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/eliminar_esquema.php`
- Entrada: `post.comun:integer`, `post.dl:string`, `post.region:string`, `post.sf:integer`, `post.sv:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/devel_db_admin/migraciones_ejecutar`

- Id: `devel_db_admin.migraciones_ejecutar`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/migraciones_ejecutar.php`
- Entrada: `post.modo:string`, `post.prefijo_hasta:string`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/devel_db_admin/migraciones_lista_data`

- Id: `devel_db_admin.migraciones_lista_data`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/migraciones_lista_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/devel_db_admin/migraciones_quitar_registro`

- Id: `devel_db_admin.migraciones_quitar_registro`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/migraciones_quitar_registro.php`
- Entrada: `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/devel_db_admin/mover_tabla`

- Id: `devel_db_admin.mover_tabla`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/mover_tabla.php`
- Entrada: `post.tabla:string`
- Respuesta: `standard_envelope_string_data`

## `/src/devel_db_admin/renombrar_esquema`

- Id: `devel_db_admin.renombrar_esquema`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/renombrar_esquema.php`
- Entrada: `post.comun:integer`, `post.dl:string`, `post.esquema:string`, `post.esquema_origen:string`, `post.region:string`, `post.sf:integer`, `post.sv:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/devel_db_admin/verificar_renombrar_esquema`

- Id: `devel_db_admin.verificar_renombrar_esquema`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/verificar_renombrar_esquema.php`
- Entrada: `post.comun:integer`, `post.dl:string`, `post.esquema:string`, `post.esquema_origen:string`, `post.region:string`, `post.sf:integer`, `post.sv:integer`
- Respuesta: `standard_envelope_string_data`
