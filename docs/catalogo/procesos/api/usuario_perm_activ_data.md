---
id: "procesos.usuario_perm_activ_data"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/usuario_perm_activ_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/usuario_perm_activ_data.php"
entrada: ["post.dl_propia:mixed", "post.id_tipo_activ_txt:string", "post.id_usuario:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/procesos/controller/usuario_perm_activ.php"]
casos_uso: ["src\\procesos\\application\\UsuarioPermActivData"]
tags: ["procesos", "usuario", "perm", "activ", "data"]
estado_revision: "generado"
---

# Usuario Perm Activ Data

Caso de uso: datos para la pantalla usuario_perm_activ.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/usuario_perm_activ_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/usuario_perm_activ_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl_propia` | `mixed` | application | No | application |
| `id_tipo_activ_txt` | `string` | application | No | application |
| `id_usuario` | `integer` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Permisos

- Permiso oficina `des`
- Permiso oficina `vcsd`
- Permiso oficina `calendario`

## Casos De Uso

- `src\procesos\application\UsuarioPermActivData`

## Frontend Relacionado

- `frontend/procesos/controller/usuario_perm_activ.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.