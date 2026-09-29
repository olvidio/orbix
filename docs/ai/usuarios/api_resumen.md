---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "usuarios"
endpoints: 47
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - usuarios

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/usuarios/app_login`

- Id: `usuarios.app_login`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/app_login.php`
- Entrada: `post.username:string`, `post.password:string`, `post.esquema:string`, `post.verification_code:string`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/app_session`

- Id: `usuarios.app_session`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/app_session.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/borrar_pwd`

- Id: `usuarios.borrar_pwd`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/borrar_pwd.php`
- Entrada: `post.ctx_guardar:string` (cápsula `HashB` acción `borrar_pwd`, sin contexto — utilidad de solo-pruebas sin identidad de fila; emitida por `borrar_pwd_form_data`)

## `/src/usuarios/borrar_pwd_form_data`

- Id: `usuarios.borrar_pwd_form_data`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/borrar_pwd_form_data.php`
- Entrada: ninguna
- Respuesta: `ctx_guardar`, cápsula `HashB` para `borrar_pwd`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/check_first_login_2fa`

- Id: `usuarios.check_first_login_2fa`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/check_first_login_2fa.php`
- Entrada: ninguna detectada.
- Respuesta: `pendiente_revision`

## `/src/usuarios/grupo_eliminar`

- Id: `usuarios.grupo_eliminar`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/grupo_eliminar.php`
- Entrada: `post.sel:array` (elemento 0 = cápsula `HashB` acción `grupo_eliminar`, contexto `{id_usuario}`; `sel` deja de llevar el id en claro, lo emite `grupo_lista` por fila)
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/grupo_guardar`

- Id: `usuarios.grupo_guardar`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/grupo_guardar.php`
- Entrada: `post.ctx_guardar:string` (cápsula `HashB` acción `grupo_guardar`, contexto `{que_user, id_usuario}`; emitida por `grupo_info`), `post.usuario:string`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/grupo_info`

- Id: `usuarios.grupo_info`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/grupo_info.php`
- Entrada: `post.id_usuario:integer` (`0` o vacío → alta nueva)
- Respuesta: `standard_envelope_string_data` (incluye `nombre` y `ctx_guardar`, cápsula `HashB` para `grupo_guardar`)

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
- Entrada: `post.ctx_eliminar:string` (cápsula `HashB` acción `perm_activ_eliminar`, contexto `{id_item}`; emitida por fila en `perm_activ_lista`, transportada aparte de `sel` porque `sel` sigue en claro para el flujo de modificar en `frontend/procesos/controller/usuario_perm_activ.php`)
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/perm_activ_guardar`

- Id: `usuarios.perm_activ_guardar`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/perm_activ_guardar.php`
- Entrada: `post.id_usuario:integer`, `post.id_tipo_activ:integer`, `post.id_item:integer`, `post.dl_propia:string`, `post.fase_ref:array`, `post.perm_on:array`, `post.perm_off:array`, `post.afecta_a:array`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/perm_activ_lista`

- Id: `usuarios.perm_activ_lista`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/perm_activ_lista.php`
- Entrada: `post.id_usuario:string`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/perm_menu_eliminar`

- Id: `usuarios.perm_menu_eliminar`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/perm_menu_eliminar.php`
- Entrada: `post.sel:array` (elemento 0 = cápsula `HashB` acción `perm_menu_eliminar`, contexto `{id_item}`; emitida por fila en `perm_menu_lista`)
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/perm_menu_guardar`

- Id: `usuarios.perm_menu_guardar`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/perm_menu_guardar.php`
- Entrada: `post.id_item:integer`, `post.id_usuario:integer`, `post.menu_perm:array`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/perm_menu_info`

- Id: `usuarios.perm_menu_info`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/perm_menu_info.php`
- Entrada: `post.id_usuario:integer`, `post.id_item:integer`
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
- Entrada: `post.que:string` (`'slickGrid'` o vacío/otro). Rama `que=''` (preferencias personales, `preferencias.phtml`): `post.ctx_guardar:string` (cápsula `HashB` acción `preferencias_guardar`, contexto `{id_usuario}` — siempre `ConfigGlobal::mi_id_usuario()`, nunca del POST — emitida por `usuario_preferencias`), `post.layout:string`, `post.oficina:string`, `post.inicio:string`, `post.tipo_tabla:string`, `post.ordenApellidos:string`, `post.idioma_nou:string`, `post.zona_horaria_nou:string`, `post.estilo_color:string`, `post.tipo_menu:string`. Rama `que='slickGrid'` (`post.tabla:string`, `post.sPrefs:string`): **pendiente de `HashB`** — la dispara `scripts/index.js.php` desde cualquier página con tabla SlickGrid, firmada con un `HashF` calculado una vez en `index.php`; no hay paso de lectura por página al que atar una cápsula sin añadir una llamada a `src/` en cada carga de página. `id_usuario` ya viene de la sesión en ambas ramas, no hay IDOR.
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/recuperar_2fa_mail`

- Id: `usuarios.recuperar_2fa_mail`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/recuperar_2fa_mail.php`
- Entrada: `post.username:string`, `post.ubicacion:string`, `post.esquema:string`, `post.esquema_web:string`, `post.url_base:string`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/recuperar_password_mail`

- Id: `usuarios.recuperar_password_mail`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/recuperar_password_mail.php`
- Entrada: `post.username:string`, `post.ubicacion:string`, `post.esquema:string`, `post.esquema_web:string`, `post.url_index:string`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/role_eliminar`

- Id: `usuarios.role_eliminar`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/role_eliminar.php`
- Entrada: `post.ctx_eliminar:string` (cápsula `HashB` acción `role_eliminar`, contexto `{id_role}`; emitida por fila en `role_lista`, transportada aparte de `sel` porque `sel` sigue en claro para el flujo de modificar en `frontend/usuarios/controller/role_form.php`)
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
- Entrada: `post.role:string`, `post.id_role:integer`, `post.sf:integer`, `post.sv:integer`, `post.pau:string`, `post.dmz:integer`
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
- Entrada: ninguna (el `id_usuario` viene siempre de `ConfigGlobal::mi_id_usuario()`, nunca del POST: 2FA es siempre autoservicio del propio usuario)
- Respuesta: incluye `ctx_2fa_update`, cápsula `HashB` para `usuario_2fa_update` atada a `{id_usuario}`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/usuario_2fa_update`

- Id: `usuarios.usuario_2fa_update`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_2fa_update.php`
- Entrada: `post.ctx_2fa_update:string` (cápsula `HashB` acción `usuario_2fa_update`, contexto `{id_usuario}`; emitida por `usuario_2fa_info`), `post.secret_2fa:string`, `post.enable_2fa:boolean`, `post.verification_code:string`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/usuario_2fa_verify`

- Id: `usuarios.usuario_2fa_verify`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_2fa_verify.php`
- Entrada: `post.verification_code:string`, `post.secret_2fa:string`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/usuario_ayuda_info`

- Id: `usuarios.usuario_ayuda_info`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_ayuda_info.php`
- Entrada: `post.username:string`, `post.ubicacion:string`, `post.esquema:string`, `post.esquema_web:string`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/usuario_check_pwd`

- Id: `usuarios.usuario_check_pwd`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_check_pwd.php`
- Entrada: `post.id_usuario:integer`, `post.usuario:string`, `post.password:string`
- Respuesta: `pendiente_revision`

## `/src/usuarios/usuario_eliminar`

- Id: `usuarios.usuario_eliminar`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_eliminar.php`
- Entrada: `post.sel:array` (elemento 0 = cápsula `HashB` acción `usuario_eliminar`, contexto `{id_usuario}`; emitida por fila en `usuario_lista`)
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/usuario_form`

- Id: `usuarios.usuario_form`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_form.php`
- Entrada: `post.id_usuario:integer`, `post.quien:string`
- Respuesta: `pendiente_revision`

## `/src/usuarios/usuario_grupo_add`

- Id: `usuarios.usuario_grupo_add`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_grupo_add.php`
- Entrada: `post.ctx:string`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/usuario_grupo_del`

- Id: `usuarios.usuario_grupo_del`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_grupo_del.php`
- Entrada: `post.ctx:string`
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
- Entrada: `post.ctx:string`, `post.usuario:string`, `post.id_role:integer`, `post.email:string`, `post.nom_usuario:string`, `post.password:string`, `post.id_nom:integer`, `post.id_ctr:integer`, `post.casas:array`, `post.cambio_password:boolean`, `post.has_2fa:boolean`, `post.perm_activ:array`
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/usuario_guardar_mail`

- Id: `usuarios.usuario_guardar_mail`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_guardar_mail.php`
- Entrada: `post.ctx_guardar:string` (cápsula `HashB` acción `usuario_guardar_mail`, contexto `{id_usuario}` — siempre el propio usuario; emitida por `usuario_guardar_mail_form_data`), `post.email:string`

## `/src/usuarios/usuario_guardar_mail_form_data`

- Id: `usuarios.usuario_guardar_mail_form_data`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_guardar_mail_form_data.php`
- Entrada: ninguna (`id_usuario` siempre de `ConfigGlobal::mi_id_usuario()`)
- Respuesta: `usuario`, `email`, `ctx_guardar` (cápsula `HashB` para `usuario_guardar_mail`)
- Respuesta: `standard_envelope_string_data`

## `/src/usuarios/usuario_guardar_pwd`

- Id: `usuarios.usuario_guardar_pwd`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_guardar_pwd.php`
- Entrada: `post.ctx_guardar:string` (cápsula `HashB` acción `usuario_guardar_pwd`, contexto `{id_usuario}` — siempre el propio usuario; emitida por `usuario_guardar_pwd_form_data`), `post.password:string`

## `/src/usuarios/usuario_guardar_pwd_form_data`

- Id: `usuarios.usuario_guardar_pwd_form_data`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_guardar_pwd_form_data.php`
- Entrada: ninguna (`id_usuario` siempre de `ConfigGlobal::mi_id_usuario()`)
- Respuesta: `ctx_guardar`, cápsula `HashB` para `usuario_guardar_pwd`
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
- Respuesta: incluye `ctx_guardar`, cápsula `HashB` para `preferencias_guardar` (rama de preferencias personales) atada a `{id_usuario}`
- Respuesta: `standard_envelope_string_data`
