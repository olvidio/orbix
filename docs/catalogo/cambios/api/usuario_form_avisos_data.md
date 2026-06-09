---
id: "cambios.usuario_form_avisos_data"
tipo: "endpoint"
modulo: "cambios"
url: "/src/cambios/usuario_form_avisos_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/cambios/infrastructure/ui/http/controllers/usuario_form_avisos_data.php"
entrada: ["post.id_usuario:integer", "post.quien:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "cambios_UsuarioFormAvisosDataData"
respuesta_data: ["error:string", "a_valores:array", "nombre_usuario:string"]
requiere_hashb: false
frontend_referencias: ["frontend/cambios/controller/usuario_form_avisos.php"]
casos_uso: ["src\\cambios\\application\\UsuarioFormAvisosData"]
tags: ["cambios", "usuario", "form", "avisos", "data"]
estado_revision: "generado"
---

# Usuario Form Avisos Data

Endpoint backend: datos para el listado de avisos de un usuario.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/cambios/usuario_form_avisos_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/cambios/infrastructure/ui/http/controllers/usuario_form_avisos_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_usuario` | `integer` | controller+application | No | controller+application |
| `quien` | `string` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `cambios_UsuarioFormAvisosDataData`):
  - `error` (`string`)
  - `a_valores` (`array`)
  - `nombre_usuario` (`string`)

## Casos De Uso

- `src\cambios\application\UsuarioFormAvisosData`

## Frontend Relacionado

- `frontend/cambios/controller/usuario_form_avisos.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.