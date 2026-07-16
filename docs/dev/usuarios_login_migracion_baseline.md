# Baseline migracion login `apps/permisos/controller/login_obj.php` -> `frontend/usuarios` + `src/usuarios`

## Rol actual del fichero

`apps/permisos/controller/login_obj.php` **no es un controlador HTTP con URL propia**: se incluye con `require_once(...)` en:

- `apps/core/global_object.inc:51` (bootstrap global que usan todas las pantallas legacy y la mayor parte del backend).
- `frontend/shared/FrontBootstrap.php` (bootstrap del frontend refactorizado).

Actua como **guardia de sesion**: si `$_SESSION['session_auth']` no existe, dibuja el form de login (y muere), y si llega POST de login valida y rellena `$_SESSION['session_auth']` y `$_SESSION['config']`.

## Parametros de entrada

### GET / entorno

- `getenv('ESQUEMA')`: si esta definido, fuerza ese esquema (el form oculta el desplegable de regiones).
- `getenv('UBICACION')`: `sv` / `sf` / vacio. Se pinta en la caja de login.
- `getenv('PRIVATE')`: se guarda en `$_SESSION['private']`.
- Cookies leidas al pintar el form (primera vez): `$_COOKIE['esquema']`, `$_COOKIE['idioma']`.

### POST (submit del form)

- `username` (obligatorio).
- `password` (obligatorio).
- `esquema` (si no hay `esquema_web` por env).
- `verification_code` (si el usuario tiene `has_2fa`).
- `idioma` (hidden, se arrastra).

## Flujo

1. `session_start()` (hecho en `global_object.inc` / `FrontBootstrap::boot()` antes del require de login).
2. Validar `$esquema_web` contra `DBPropiedades::array_posibles_esquemas(FALSE, TRUE)`. Si no existe -> `die("No existe este esquema: ...")`.
3. Rama sin `session_auth`:
   - Si **no** hay POST -> renderiza `login_form2.phtml` con `error=0`, cookies, `DesplRegiones`.
   - Si hay POST:
     - Calcula `$sfsv` (1 si esquema termina en `v`, 2 en `f`) y abre PDO `sv-e_select` o `sf-e`.
     - `SELECT * FROM aux_usuarios WHERE usuario = :usuario`.
     - Si no hay fila o `MyCrypt::encode(password, password_db) !== password_db` -> `error=1`, renderiza form y `die()`.
     - Si `cambio_password` o `password === '1ªVegada'` -> `expire = 1`.
     - Rama 2FA:
       - `has_2fa=true` y `secret_2fa` vacio -> `header('Location: frontend/usuarios/controller/ayuda_2fa_reset.php?...')` y `die()`.
       - `has_2fa=true` y `secret_2fa` lleno:
         - sin `verification_code` -> `error=3`, renderiza y `die()`.
         - `Verify2fa::verify_2fa_code(...)` false -> `error=4`, renderiza y `die()`.
     - Lee `aux_roles` con `id_role`, `pau`, `dmz`.
     - Si `ConfigGlobal::is_dmz()` y `role.dmz` vacio -> `error=2`, renderiza y `die()`.
     - Calcula `$app_installed`, `$a_mods_installed`, `$a_mods`, `$a_apps` (`m0_apps`, `m0_modulos`, `m0_mods_installed_dl`).
     - Lee `web_preferencias` para `idioma` y `ordenApellidos`.
     - `DBPropiedades::getIdSchema($esquema)`.
     - Rellena `$_SESSION['session_auth']` y `$_SESSION['config']`.
     - `cambiar_idioma()` (setlocale / gettext).
     - `setcookie('esquema', ..., 30 dias)` y `setcookie('idioma', ..., 30 dias)`.
4. Rama con `session_auth` existente -> `cambiar_idioma()` para asegurar gettext en el request actual.
5. Primera vez: fija `$_SESSION['session_go_to'] = 'a'`.

## Vista `login_form2.phtml`

- Variables pasadas: `error`, `ubicacion`, `esquema_web`, `DesplRegiones`, `idioma`, `username`, `url` (= `ConfigGlobal::getWeb()`).
- Form POST a `$_SERVER['PHP_SELF']` (asi el submit vuelve al mismo URL que ya viene protegido y el guardia intercepta el POST).
- Campos: `idioma` (hidden), `esquema` (hidden o desplegable segun haya `esquema_web`), `username`, `password`, `verification_code`, submit.
- Mensajes de error (por codigo):
  - `1` -> credenciales invalidas.
  - `2` -> usuario no puede entrar desde esta instalacion (DMZ).
  - `3` -> falta codigo 2FA.
  - `4` -> codigo 2FA invalido.
- `fnjs_goHelp()` -> fetch POST a `frontend/usuarios/controller/ayuda_acceso.php` con el form, reemplaza el body con la respuesta.
- `fnjs_goTop()` -> si detecta `menu` en el DOM, redirige a `$url` (caso de sesion ya viva).

## Traducciones

Las cadenas traducibles viven en los `.po` bajo `permisos/controller/login_obj.php` y `permisos/view/login_form2.phtml`. Tras mover:

- Cadenas van a `frontend/usuarios/controller/login.php` y `frontend/usuarios/view/login_form.phtml`.
- Los `.po` se regeneran con `xgettext` habitual; las entradas antiguas quedan obsoletas (no tocamos los `.po` en este slice, se actualizaran en el ciclo normal de traducciones).

## Impacto de la migracion

- **Mover logica pura a `src/usuarios/application/LoginProcesar.php`**: validacion de credenciales, 2FA, lectura de role, construccion de `session_auth` y `config`. Devuelve array `{ok, error, session_auth?, session_config?, redirect?, idioma?}`; no hace `setcookie` ni `header()`, para poder testarlo.
- **Side-effects de presentacion (cookies, `cambiar_idioma`, renderizar form) viven en `frontend/usuarios/controller/login.php`**: es el guardia incluido por los bootstraps.
- **Renombrar `permisos\model\MyCrypt` -> `src\usuarios\domain\PasswordHasher`** y actualizar los 6 consumidores:
  - `apps/permisos/controller/login_obj.php` (se borra entero en este slice).
  - `src/usuarios/application/AppMobileLogin.php`.
  - `src/usuarios/infrastructure/ui/http/controllers/borrar_pwd.php`.
  - `src/usuarios/infrastructure/ui/http/controllers/recuperar_password_mail.php`.
  - `src/usuarios/infrastructure/ui/http/controllers/usuario_check_pwd.php`.
  - `src/usuarios/infrastructure/ui/http/controllers/usuario_guardar.php`.
  - `src/usuarios/infrastructure/ui/http/controllers/usuario_guardar_pwd.php`.
- `src/shared/global_object.inc` y `FrontBootstrap` pasan a requerir `frontend/usuarios/controller/login.php`.

## Fuera del alcance de este slice

- La parte **permisos** de `apps/permisos/model/*` (PermDl, XPermisos, XResto, PermisosActividades, PermisosActividadesTrue, perm_invalid.phtml) se aborda en un slice posterior movida a `src/permisos/domain/`. Mientras tanto sigue viviendo en `apps/permisos/model/*` y sus `use permisos\model\...` no se tocan.
- Unificacion de `LoginProcesar` con `AppMobileLogin::attempt` (hoy tienen flujos paralelos): se deja como fase posterior.

## Verificacion

- `php -l` en todos los ficheros tocados.
- Login real: usuario valido, usuario invalido, password invalido, 2FA necesario sin configurar, 2FA necesario con codigo erroneo, 2FA correcto, usuario sin DMZ. Comparar con baseline anterior.
- `rg "permisos\\\\model\\\\MyCrypt"` debe dar cero.
- `rg "apps/permisos/controller/login_obj"` debe dar cero fuera de `.po`/traducciones.
- `rg "login_form2.phtml"` debe dar cero.
