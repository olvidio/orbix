---
tipo: manual_usuario
modulo: usuarios
flujos: 33
estado_revision: revisado_parcial
---

# Manual De Usuario - usuarios

Autenticacion, **preferencias**, gestion de **usuarios**, **roles**, **grupos** y permisos de menu.

## Acceso Por Menu

| Texto | Controller | Rol |
|-------|------------|-----|
| **Preferencias** | `preferencias.php` | Todos (rol 1) |
| **Lista usuarios** | `usuario_lista.php` | Admin 13 |
| **Lista de roles** | `role_lista.php` | Admin 13 |
| **Grup menu** | `grupmenu_lista.php` | Admin 13 |

Login/sesion: endpoints `app_login`, `app_session`, 2FA (`usuario_2fa*`, `check_first_login_2fa`).

## Preferencias De Usuario

1. Menu **Preferencias** (cualquier rol).
2. Ajustar opciones personales (mail, pwd, tablas, ayuda).
3. Guardar — endpoints `usuario_guardar_mail`, `usuario_guardar_pwd`, `preferencia_tabla`.

## Administracion (Rol 13)

### Usuarios

1. **Lista usuarios** — alta/edition/baja, grupos (`usuario_grupo_*`).
2. Permisos menu: `perm_menu`, `perm_menu_info`.
3. Permisos actividad: `perm_activ` (vinculo **procesos**).

### Roles y grupos menu

1. **Lista roles** — asignar grupmenu (`role_grupmenu_*`).
2. **Grup menu** — mantenimiento agrupadores.

## Recuperacion Y 2FA

- Recuperar password/mail: `recuperar_password_mail`, `recuperar_2fa_mail`.
- Verificacion 2FA en login.

## Modulos Relacionados

menus (estructura menu), procesos (perm_activ), todos (permisos sesion).

Legacy: `documentacion/usuarios_login_migracion_baseline.md`
