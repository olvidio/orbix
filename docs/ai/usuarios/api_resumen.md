---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "usuarios"
endpoints: 44
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - usuarios

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/usuarios/app_login`

- Id: `usuarios.app_login`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/app_login.php`
- Entrada: `post.esquema:string`, `post.password:string`, `post.username:string`, `post.verification_code:string`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/app_session`

- Id: `usuarios.app_session`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/app_session.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/borrar_pwd`

- Id: `usuarios.borrar_pwd`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/borrar_pwd.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/check_first_login_2fa`

- Id: `usuarios.check_first_login_2fa`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/check_first_login_2fa.php`
- Entrada: ninguna detectada.
- Respuesta: `pendiente_revision`

## `/src/usuarios/grupo_eliminar`

- Id: `usuarios.grupo_eliminar`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/grupo_eliminar.php`
- Entrada: `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/grupo_guardar`

- Id: `usuarios.grupo_guardar`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/grupo_guardar.php`
- Entrada: `post.id_usuario:integer`, `post.usuario:string`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/grupo_info`

- Id: `usuarios.grupo_info`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/grupo_info.php`
- Entrada: `post.id_usuario:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/grupo_lista`

- Id: `usuarios.grupo_lista`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/grupo_lista.php`
- Entrada: `post.username:string`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/mails_contactos_region`

- Id: `usuarios.mails_contactos_region`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/mails_contactos_region.php`
- Entrada: `post.region:string`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/perm_activ_eliminar`

- Id: `usuarios.perm_activ_eliminar`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/perm_activ_eliminar.php`
- Entrada: `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/perm_activ_guardar`

- Id: `usuarios.perm_activ_guardar`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/perm_activ_guardar.php`
- Entrada: `post.afecta_a:array`, `post.dl_propia:string`, `post.fase_ref:array`, `post.iactividad_val:string`, `post.iasistentes_val:string`, `post.id_item:integer`, `post.id_tipo_activ:integer`, `post.id_usuario:integer`, `post.inom_tipo_val:string`, `post.isfsv_val:string`, `post.perm_off:array`, `post.perm_on:array`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/perm_activ_lista`

- Id: `usuarios.perm_activ_lista`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/perm_activ_lista.php`
- Entrada: `post.id_usuario:string`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/perm_menu_eliminar`

- Id: `usuarios.perm_menu_eliminar`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/perm_menu_eliminar.php`
- Entrada: `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/perm_menu_guardar`

- Id: `usuarios.perm_menu_guardar`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/perm_menu_guardar.php`
- Entrada: `post.id_item:integer`, `post.id_usuario:integer`, `post.menu_perm:array`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/perm_menu_info`

- Id: `usuarios.perm_menu_info`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/perm_menu_info.php`
- Entrada: `post.id_item:integer`, `post.id_usuario:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/perm_menu_lista`

- Id: `usuarios.perm_menu_lista`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/perm_menu_lista.php`
- Entrada: `post.id_usuario:string`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/preferencia_tabla_get`

- Id: `usuarios.preferencia_tabla_get`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/preferencia_tabla_get.php`
- Entrada: `post.id_tabla:string`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/preferencias_guardar`

- Id: `usuarios.preferencias_guardar`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/preferencias_guardar.php`
- Entrada: `post.estilo_color:string`, `post.idioma_nou:string`, `post.inicio:string`, `post.layout:string`, `post.oficina:string`, `post.ordenApellidos:string`, `post.que:string`, `post.sPrefs:string`, `post.tabla:string`, `post.tipo_menu:string`, `post.tipo_tabla:string`, `post.zona_horaria_nou:string`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/recuperar_2fa_mail`

- Id: `usuarios.recuperar_2fa_mail`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/recuperar_2fa_mail.php`
- Entrada: `post.esquema:string`, `post.esquema_web:string`, `post.ubicacion:string`, `post.url_base:string`, `post.username:string`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/recuperar_password_mail`

- Id: `usuarios.recuperar_password_mail`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/recuperar_password_mail.php`
- Entrada: `post.esquema:string`, `post.esquema_web:string`, `post.ubicacion:string`, `post.url_index:string`, `post.username:string`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/role_eliminar`

- Id: `usuarios.role_eliminar`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/role_eliminar.php`
- Entrada: `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/role_grupmenu_add`

- Id: `usuarios.role_grupmenu_add`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/role_grupmenu_add.php`
- Entrada: `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/role_grupmenu_del`

- Id: `usuarios.role_grupmenu_del`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/role_grupmenu_del.php`
- Entrada: `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/role_grupmenu_info`

- Id: `usuarios.role_grupmenu_info`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/role_grupmenu_info.php`
- Entrada: `post.id_role:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/role_guardar`

- Id: `usuarios.role_guardar`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/role_guardar.php`
- Entrada: `post.dmz:integer`, `post.id_role:integer`, `post.pau:string`, `post.role:string`, `post.sf:integer`, `post.sv:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/role_info`

- Id: `usuarios.role_info`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/role_info.php`
- Entrada: `post.id_role:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/role_lista`

- Id: `usuarios.role_lista`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/role_lista.php`
- Entrada: ninguna detectada.
- Respuesta: `custom_json`

## `/src/usuarios/usuario_2fa_info`

- Id: `usuarios.usuario_2fa_info`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_2fa_info.php`
- Entrada: `post.id_usuario:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/usuario_2fa_update`

- Id: `usuarios.usuario_2fa_update`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_2fa_update.php`
- Entrada: `post.enable_2fa:boolean`, `post.id_usuario:integer`, `post.secret_2fa:string`, `post.verification_code:string`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/usuario_2fa_verify`

- Id: `usuarios.usuario_2fa_verify`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_2fa_verify.php`
- Entrada: `post.secret_2fa:mixed`, `post.verification_code:mixed`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/usuario_ayuda_info`

- Id: `usuarios.usuario_ayuda_info`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_ayuda_info.php`
- Entrada: `post.esquema:string`, `post.esquema_web:string`, `post.ubicacion:string`, `post.username:string`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/usuario_check_pwd`

- Id: `usuarios.usuario_check_pwd`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_check_pwd.php`
- Entrada: `post.id_usuario:integer`, `post.password:string`, `post.usuario:string`
- Respuesta: `pendiente_revision`

## `/src/usuarios/usuario_eliminar`

- Id: `usuarios.usuario_eliminar`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_eliminar.php`
- Entrada: `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/usuario_form`

- Id: `usuarios.usuario_form`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_form.php`
- Entrada: `post.id_usuario:integer`, `post.quien:string`
- Respuesta: `pendiente_revision`

## `/src/usuarios/usuario_grupo_add`

- Id: `usuarios.usuario_grupo_add`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_grupo_add.php`
- Entrada: `post.ctx:string`, `post.id_grupo:integer`, `post.id_usuario:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/usuario_grupo_del`

- Id: `usuarios.usuario_grupo_del`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_grupo_del.php`
- Entrada: `post.ctx:string`, `post.id_grupo:integer`, `post.id_usuario:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/usuario_grupo_del_lst`

- Id: `usuarios.usuario_grupo_del_lst`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_grupo_del_lst.php`
- Entrada: `post.id_usuario:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/usuario_grupo_lst`

- Id: `usuarios.usuario_grupo_lst`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_grupo_lst.php`
- Entrada: `post.id_usuario:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/usuario_guardar`

- Id: `usuarios.usuario_guardar`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_guardar.php`
- Entrada: `post.cambio_password:boolean`, `post.casas:array`, `post.ctx:string`, `post.email:string`, `post.has_2fa:boolean`, `post.id_ctr:integer`, `post.id_nom:integer`, `post.id_role:integer`, `post.id_usuario:integer`, `post.nom_usuario:string`, `post.pass:string`, `post.password:string`, `post.perm_activ:array`, `post.que_user:string`, `post.usuario:string`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/usuario_guardar_mail`

- Id: `usuarios.usuario_guardar_mail`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_guardar_mail.php`
- Entrada: `post.email:string`, `post.id_usuario:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/usuario_guardar_pwd`

- Id: `usuarios.usuario_guardar_pwd`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_guardar_pwd.php`
- Entrada: `post.id_usuario:integer`, `post.password:string`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/usuario_info`

- Id: `usuarios.usuario_info`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_info.php`
- Entrada: `post.id_usuario:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/usuario_lista`

- Id: `usuarios.usuario_lista`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_lista.php`
- Entrada: `post.username:string`
- Respuesta: `custom_json`

## `/src/usuarios/usuario_preferencias`

- Id: `usuarios.usuario_preferencias`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_preferencias.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`
