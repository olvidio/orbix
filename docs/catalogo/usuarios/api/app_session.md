---
id: "usuarios.app_session"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/app_session"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/usuarios/infrastructure/ui/http/controllers/app_session.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data: ["authenticated:bool", "id_usuario?:integer", "username?:string", "esquema?:string"]
requiere_hashb: false
frontend_referencias: []
casos_uso: []
tags: ["usuarios", "app", "session", "cliente_movil"]
estado_revision: "revisado"
---

# App Session

Comprueba si la cookie de sesión actual identifica un usuario autenticado. Útil al arrancar la app sin volver a pedir credenciales.

Convenciones: [`_convenciones_api.md`](../_convenciones_api.md) · Clientes nativos: [`_clientes_nativos.md`](../_clientes_nativos.md)

## Endpoint

- URL: `/src/usuarios/app_session`
- Métodos: `GET` (recomendado para comprobar cookie), `POST` registrado
- Controller: `src/usuarios/infrastructure/ui/http/controllers/app_session.php`
- Sin parámetros de entrada

## Salida

- Helper: `ContestarJson::enviar`
- `data` es string JSON escapado (segundo parse).

### Sesión válida

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `authenticated` | bool | `true` |
| `id_usuario` | int | ID de sesión |
| `username` | string | Usuario |
| `esquema` | string | Esquema activo |

### Sin sesión

| Campo | Tipo |
|-------|------|
| `authenticated` | bool (`false`) |

No incluye `mensaje` en el caso normal; HTTP 200 y `success: true`.

## Ejemplo

**Request:**

```http
GET /orbix/src/usuarios/app_session HTTP/1.1
Accept: application/json
Cookie: PHPSESSID=abc123...
```

**Response (autenticado):**

```json
{
  "success": true,
  "data": "{\"authenticated\":true,\"id_usuario\":42,\"username\":\"pSacd\",\"esquema\":\"H-dlbv\"}"
}
```

**Response (sin sesión):**

```json
{
  "success": true,
  "data": "{\"authenticated\":false}"
}
```

## Cliente de referencia

- `orbix-android`: `fetchSessionAuthenticated()` — GET con cookie jar OkHttp.
