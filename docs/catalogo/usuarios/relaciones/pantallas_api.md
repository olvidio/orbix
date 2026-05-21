---
tipo: "relacion_pantallas_api"
modulo: "usuarios"
pantallas: 24
endpoints_api: 44
capacidades: 33
estado_revision: "generado"
---

# Relacion Pantallas API - usuarios

Indice generado automaticamente para cruzar pantallas frontend, capacidades y endpoints API.

## Por Pantalla

### `usuarios.pantalla.ayuda_2fa_reset`

- Controller: `frontend/usuarios/controller/ayuda_2fa_reset.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- Ninguno detectado.

Capacidades:
- Ninguna detectada.

Endpoints aportados por capacidades:
- Ninguno adicional.

### `usuarios.pantalla.ayuda_acceso`

- Controller: `frontend/usuarios/controller/ayuda_acceso.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- Ninguno detectado.

Capacidades:
- Ninguna detectada.

Endpoints aportados por capacidades:
- Ninguno adicional.

### `usuarios.pantalla.borrar_todos_pwd`

- Controller: `frontend/usuarios/controller/borrar_todos_pwd.php`
- Subtipo: `pantalla`

Endpoints directos:
- `/src/usuarios/infrastructure/ui/http/controllers/borrar_pwd`

Capacidades:
- Ninguna detectada.

Endpoints aportados por capacidades:
- Ninguno adicional.

### `usuarios.pantalla.grupo_form`

- Controller: `frontend/usuarios/controller/grupo_form.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/usuarios/grupo_info`
- `/src/usuarios/perm_menu_eliminar`
- `/src/usuarios/perm_menu_lista`

Capacidades:
- `usuarios.grupo_info.gestionar`
- `usuarios.perm_menu.gestionar`

Endpoints aportados por capacidades:
- `/src/usuarios/perm_menu_guardar`

### `usuarios.pantalla.grupo_lista`

- Controller: `frontend/usuarios/controller/grupo_lista.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/usuarios/grupo_eliminar`
- `/src/usuarios/grupo_lista`

Capacidades:
- `usuarios.grupo.gestionar`

Endpoints aportados por capacidades:
- `/src/usuarios/grupo_guardar`

### `usuarios.pantalla.login`

- Controller: `frontend/usuarios/controller/login.php`
- Subtipo: `pantalla`

Endpoints directos:
- Ninguno detectado.

Capacidades:
- Ninguna detectada.

Endpoints aportados por capacidades:
- Ninguno adicional.

### `usuarios.pantalla.mails_contactos_region`

- Controller: `frontend/usuarios/controller/mails_contactos_region.php`
- Subtipo: `pantalla`

Endpoints directos:
- `/src/usuarios/mails_contactos_region`

Capacidades:
- `usuarios.mails_contactos_region.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `usuarios.pantalla.perm_activ_lista`

- Controller: `frontend/usuarios/controller/perm_activ_lista.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/usuarios/perm_activ_lista`

Capacidades:
- `usuarios.perm_activ.gestionar`

Endpoints aportados por capacidades:
- `/src/usuarios/perm_activ_eliminar`
- `/src/usuarios/perm_activ_guardar`

### `usuarios.pantalla.perm_menu_form`

- Controller: `frontend/usuarios/controller/perm_menu_form.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/usuarios/perm_menu_guardar`
- `/src/usuarios/perm_menu_info`

Capacidades:
- `usuarios.perm_menu.gestionar`
- `usuarios.perm_menu_info.gestionar`

Endpoints aportados por capacidades:
- `/src/usuarios/perm_menu_eliminar`
- `/src/usuarios/perm_menu_lista`

### `usuarios.pantalla.preferencias`

- Controller: `frontend/usuarios/controller/preferencias.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/shared/locales_posibles`
- `/src/usuarios/usuario_preferencias`

Capacidades:
- `usuarios.usuario_preferencias.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `usuarios.pantalla.recovery`

- Controller: `frontend/usuarios/controller/recovery.php`
- Subtipo: `pantalla`

Endpoints directos:
- Ninguno detectado.

Capacidades:
- Ninguna detectada.

Endpoints aportados por capacidades:
- Ninguno adicional.

### `usuarios.pantalla.recuperar_2fa`

- Controller: `frontend/usuarios/controller/recuperar_2fa.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- Ninguno detectado.

Capacidades:
- Ninguna detectada.

Endpoints aportados por capacidades:
- Ninguno adicional.

### `usuarios.pantalla.recuperar_password`

- Controller: `frontend/usuarios/controller/recuperar_password.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- Ninguno detectado.

Capacidades:
- Ninguna detectada.

Endpoints aportados por capacidades:
- Ninguno adicional.

### `usuarios.pantalla.role_form`

- Controller: `frontend/usuarios/controller/role_form.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/usuarios/role_grupmenu_del`
- `/src/usuarios/role_guardar`
- `/src/usuarios/role_info`

Capacidades:
- `usuarios.role.gestionar`
- `usuarios.role_grupmenu_del.gestionar`
- `usuarios.role_info.gestionar`

Endpoints aportados por capacidades:
- `/src/usuarios/role_eliminar`
- `/src/usuarios/role_lista`

### `usuarios.pantalla.role_grupmenu`

- Controller: `frontend/usuarios/controller/role_grupmenu.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/usuarios/role_grupmenu_add`
- `/src/usuarios/role_grupmenu_info`

Capacidades:
- `usuarios.role_grupmenu_add.gestionar`
- `usuarios.role_grupmenu_info.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `usuarios.pantalla.role_lista`

- Controller: `frontend/usuarios/controller/role_lista.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/usuarios/role_eliminar`
- `/src/usuarios/role_lista`

Capacidades:
- `usuarios.role.gestionar`

Endpoints aportados por capacidades:
- `/src/usuarios/role_guardar`

### `usuarios.pantalla.usuario_form`

- Controller: `frontend/usuarios/controller/usuario_form.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/usuarios/usuario_ajax`
- `/src/usuarios/usuario_check_pwd`
- `/src/usuarios/usuario_form`
- `/src/usuarios/usuario_grupo_add`
- `/src/usuarios/usuario_grupo_del`
- `/src/usuarios/usuario_guardar`
- `/src/usuarios/usuario_info`

Capacidades:
- `usuarios.usuario.gestionar`
- `usuarios.usuario_check_pwd.gestionar`
- `usuarios.usuario_grupo_add.gestionar`
- `usuarios.usuario_grupo_del.gestionar`
- `usuarios.usuario_info.gestionar`

Endpoints aportados por capacidades:
- `/src/usuarios/usuario_eliminar`
- `/src/usuarios/usuario_lista`

### `usuarios.pantalla.usuario_form_2fa`

- Controller: `frontend/usuarios/controller/usuario_form_2fa.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/usuarios/usuario_2fa_info`
- `/src/usuarios/usuario_2fa_update`
- `/src/usuarios/usuario_2fa_verify`
- `/src/usuarios/usuario_info`

Capacidades:
- `usuarios.usuario_2fa.gestionar`
- `usuarios.usuario_2fa_info.gestionar`
- `usuarios.usuario_2fa_verify.gestionar`
- `usuarios.usuario_info.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `usuarios.pantalla.usuario_form_mail`

- Controller: `frontend/usuarios/controller/usuario_form_mail.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/usuarios/usuario_info`

Capacidades:
- `usuarios.usuario_info.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `usuarios.pantalla.usuario_form_pwd`

- Controller: `frontend/usuarios/controller/usuario_form_pwd.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/usuarios/usuario_check_pwd`
- `/src/usuarios/usuario_guardar_pwd`
- `/src/usuarios/usuario_info`

Capacidades:
- `usuarios.usuario_check_pwd.gestionar`
- `usuarios.usuario_guardar_pwd.gestionar`
- `usuarios.usuario_info.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `usuarios.pantalla.usuario_grupo_del_lst`

- Controller: `frontend/usuarios/controller/usuario_grupo_del_lst.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/usuarios/usuario_grupo_del_lst`

Capacidades:
- `usuarios.usuario_grupo_del_lst.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `usuarios.pantalla.usuario_grupo_lst`

- Controller: `frontend/usuarios/controller/usuario_grupo_lst.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/usuarios/usuario_grupo_lst`

Capacidades:
- `usuarios.usuario_grupo_lst.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `usuarios.pantalla.usuario_lista`

- Controller: `frontend/usuarios/controller/usuario_lista.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/usuarios/usuario_eliminar`
- `/src/usuarios/usuario_lista`

Capacidades:
- `usuarios.usuario.gestionar`

Endpoints aportados por capacidades:
- `/src/usuarios/usuario_form`
- `/src/usuarios/usuario_guardar`

### `usuarios.pantalla.usuario_reset_2fa`

- Controller: `frontend/usuarios/controller/usuario_reset_2fa.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/usuarios/usuario_2fa_update`

Capacidades:
- `usuarios.usuario_2fa.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

## Por Endpoint API

### `/src/usuarios/app_login`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/app_session`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/borrar_pwd`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/check_first_login_2fa`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/grupo_eliminar`

Pantallas directas:
- `usuarios.pantalla.grupo_lista`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/grupo_guardar`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- `usuarios.pantalla.grupo_lista`

### `/src/usuarios/grupo_info`

Pantallas directas:
- `usuarios.pantalla.grupo_form`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/grupo_lista`

Pantallas directas:
- `usuarios.pantalla.grupo_lista`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/mails_contactos_region`

Pantallas directas:
- `usuarios.pantalla.mails_contactos_region`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/perm_activ_eliminar`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- `usuarios.pantalla.perm_activ_lista`

### `/src/usuarios/perm_activ_guardar`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- `usuarios.pantalla.perm_activ_lista`

### `/src/usuarios/perm_activ_lista`

Pantallas directas:
- `usuarios.pantalla.perm_activ_lista`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/perm_menu_eliminar`

Pantallas directas:
- `usuarios.pantalla.grupo_form`

Pantallas via capacidad:
- `usuarios.pantalla.perm_menu_form`

### `/src/usuarios/perm_menu_guardar`

Pantallas directas:
- `usuarios.pantalla.perm_menu_form`

Pantallas via capacidad:
- `usuarios.pantalla.grupo_form`

### `/src/usuarios/perm_menu_info`

Pantallas directas:
- `usuarios.pantalla.perm_menu_form`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/perm_menu_lista`

Pantallas directas:
- `usuarios.pantalla.grupo_form`

Pantallas via capacidad:
- `usuarios.pantalla.perm_menu_form`

### `/src/usuarios/preferencia_tabla_get`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/preferencias_guardar`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/recuperar_2fa_mail`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/recuperar_password_mail`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/role_eliminar`

Pantallas directas:
- `usuarios.pantalla.role_lista`

Pantallas via capacidad:
- `usuarios.pantalla.role_form`

### `/src/usuarios/role_grupmenu_add`

Pantallas directas:
- `usuarios.pantalla.role_grupmenu`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/role_grupmenu_del`

Pantallas directas:
- `usuarios.pantalla.role_form`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/role_grupmenu_info`

Pantallas directas:
- `usuarios.pantalla.role_grupmenu`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/role_guardar`

Pantallas directas:
- `usuarios.pantalla.role_form`

Pantallas via capacidad:
- `usuarios.pantalla.role_lista`

### `/src/usuarios/role_info`

Pantallas directas:
- `usuarios.pantalla.role_form`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/role_lista`

Pantallas directas:
- `usuarios.pantalla.role_lista`

Pantallas via capacidad:
- `usuarios.pantalla.role_form`

### `/src/usuarios/usuario_2fa_info`

Pantallas directas:
- `usuarios.pantalla.usuario_form_2fa`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/usuario_2fa_update`

Pantallas directas:
- `usuarios.pantalla.usuario_form_2fa`
- `usuarios.pantalla.usuario_reset_2fa`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/usuario_2fa_verify`

Pantallas directas:
- `usuarios.pantalla.usuario_form_2fa`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/usuario_ayuda_info`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/usuario_check_pwd`

Pantallas directas:
- `usuarios.pantalla.usuario_form`
- `usuarios.pantalla.usuario_form_pwd`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/usuario_eliminar`

Pantallas directas:
- `usuarios.pantalla.usuario_lista`

Pantallas via capacidad:
- `usuarios.pantalla.usuario_form`

### `/src/usuarios/usuario_form`

Pantallas directas:
- `usuarios.pantalla.usuario_form`

Pantallas via capacidad:
- `usuarios.pantalla.usuario_lista`

### `/src/usuarios/usuario_grupo_add`

Pantallas directas:
- `usuarios.pantalla.usuario_form`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/usuario_grupo_del`

Pantallas directas:
- `usuarios.pantalla.usuario_form`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/usuario_grupo_del_lst`

Pantallas directas:
- `usuarios.pantalla.usuario_grupo_del_lst`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/usuario_grupo_lst`

Pantallas directas:
- `usuarios.pantalla.usuario_grupo_lst`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/usuario_guardar`

Pantallas directas:
- `usuarios.pantalla.usuario_form`

Pantallas via capacidad:
- `usuarios.pantalla.usuario_lista`

### `/src/usuarios/usuario_guardar_mail`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/usuario_guardar_pwd`

Pantallas directas:
- `usuarios.pantalla.usuario_form_pwd`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/usuario_info`

Pantallas directas:
- `usuarios.pantalla.usuario_form`
- `usuarios.pantalla.usuario_form_2fa`
- `usuarios.pantalla.usuario_form_mail`
- `usuarios.pantalla.usuario_form_pwd`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/usuarios/usuario_lista`

Pantallas directas:
- `usuarios.pantalla.usuario_lista`

Pantallas via capacidad:
- `usuarios.pantalla.usuario_form`

### `/src/usuarios/usuario_preferencias`

Pantallas directas:
- `usuarios.pantalla.preferencias`

Pantallas via capacidad:
- Ninguna adicional.

## Alertas De Revision

Endpoints sin pantalla directa detectada:
- `/src/usuarios/app_login`
- `/src/usuarios/app_session`
- `/src/usuarios/borrar_pwd`
- `/src/usuarios/check_first_login_2fa`
- `/src/usuarios/grupo_guardar`
- `/src/usuarios/perm_activ_eliminar`
- `/src/usuarios/perm_activ_guardar`
- `/src/usuarios/preferencia_tabla_get`
- `/src/usuarios/preferencias_guardar`
- `/src/usuarios/recuperar_2fa_mail`
- `/src/usuarios/recuperar_password_mail`
- `/src/usuarios/usuario_ayuda_info`
- `/src/usuarios/usuario_guardar_mail`

Endpoints sin pantalla directa ni capacidad relacionada:
- Ninguno — los endpoints listados arriba se consumen via AJAX, forms `.phtml`/`.twig` o login; ver `docs/REPASSO_FINAL.md` § B.

## Revision Manual

- Repaso 2026-05-21: huerfanos aceptados como patron normal Orbix (PostRequest / fetch desde vista).

## Revision Manual

- Confirmar si los endpoints sin pantalla directa se usan desde fragmentos AJAX no enlazados.
- Revisar pantallas que dependen de varias capacidades.
- Completar relaciones si hay navegacion generada dinamicamente o desde menus.
