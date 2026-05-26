---
id: "usuarios.app_login"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/app_login"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/app_login.php"
entrada: ["post.esquema:string", "post.password:string", "post.username:string", "post.verification_code:string"]
entrada_obligatoria: ["username", "password"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "usuarios_AppMobileLoginData"
respuesta_data: ["id_usuario:integer", "username:string", "esquema:string", "require_password_change:bool"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\usuarios\\application\\AppMobileLogin"]
tags: ["usuarios", "app", "login", "cliente_movil"]
estado_revision: "revisado"
---

# App Login

Login JSON para clientes móviles (Camino B). Establece `$_SESSION['session_auth']` y cookies (`PHPSESSID`, `esquema`, `idioma`) igual que el login web.

Convenciones: [`_convenciones_api.md`](../_convenciones_api.md) · Clientes nativos: [`_clientes_nativos.md`](../_clientes_nativos.md)

## Endpoint

- URL: `/src/usuarios/app_login`
- Métodos: `POST` (recomendado), `GET` registrado
- Controller: `src/usuarios/infrastructure/ui/http/controllers/app_login.php`

## Entrada

Acepta **JSON en el cuerpo** (`Content-Type: application/json`) o campos POST clásicos. Si hay JSON, se fusiona con `$_POST` (prevalece el JSON).

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `username` | string | **Sí** | Usuario Orbix |
| `password` | string | **Sí** | Contraseña en claro (HTTPS en producción) |
| `esquema` | string | Condicional | Obligatorio si el servidor no define `ESQUEMA` en entorno |
| `verification_code` | string | Condicional | Obligatorio si el usuario tiene 2FA activo (`secret_2fa` en BD) |

Cabecera recomendada: `Accept: application/json`.

## Salida

- Helper: `ContestarJson::enviar`
- `data` es **string JSON escapado** con el payload (segundo parse en el cliente).

### Éxito (`success: true`)

Campos en `data`:

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_usuario` | int | ID en `aux_usuarios` |
| `username` | string | Usuario autenticado |
| `esquema` | string | Esquema de BD activo |
| `require_password_change` | bool | `true` si debe cambiar contraseña (`expire`) |

### Error (`success: false`)

Además de `mensaje`, `data` incluye `code`:

| `code` | Significado | Acción cliente |
|--------|-------------|----------------|
| `missing_credentials` | Usuario o contraseña vacíos | Validar formulario |
| `missing_schema` | Sin esquema en POST ni en env | Pedir esquema en ajustes |
| `invalid_schema` / `invalid_schema_env` | Esquema inexistente | Mostrar error de configuración |
| `invalid_credentials` | Usuario/contraseña incorrectos | Reintentar login |
| `need_2fa` | Falta `verification_code` | Mostrar campo TOTP y reenviar |
| `invalid_2fa` | Código TOTP incorrecto | Reintentar código |
| `need_2fa_setup` | 2FA pendiente de configurar | Abrir `ayuda_url` en `data` (web) |
| `dmz_denied` | Rol sin acceso DMZ | Bloquear acceso |
| `server_error` | Error interno | Reintentar más tarde |

## Ejemplo

**Request:**

```http
POST /orbix/src/usuarios/app_login HTTP/1.1
Accept: application/json
Content-Type: application/json

{"username":"pSacd","password":"***","esquema":"H-dlbv"}
```

**Response (éxito):**

```json
{
  "success": true,
  "data": "{\"id_usuario\":42,\"username\":\"pSacd\",\"esquema\":\"H-dlbv\",\"require_password_change\":false}"
}
```

**Response (2FA requerido):**

```json
{
  "success": false,
  "mensaje": "Código 2FA requerido",
  "data": "{\"code\":\"need_2fa\"}"
}
```

## Casos de uso

- `src\usuarios\application\AppMobileLogin`

## Cliente de referencia

- `orbix-android`: `MainActivity.runLogin()` — POST JSON + cookie jar.
