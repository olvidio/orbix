---
id: "procesos.usuario_perm_activ_ajax"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/usuario_perm_activ_ajax"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/usuario_perm_activ_ajax.php"
entrada: ["post.dl_propia:string", "post.id_tipo_activ:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "procesos_UsuarioPermActivFasesData"
respuesta_data: ["opciones:array"]
requiere_hashb: false
frontend_referencias: ["frontend/procesos/controller/usuario_perm_activ.php"]
casos_uso: ["src\\procesos\\application\\UsuarioPermActivFases"]
tags: ["procesos", "usuario", "perm", "activ", "ajax"]
estado_revision: "generado"
---

# Usuario Perm Activ Ajax

Caso de uso: opciones del desplegable fase_ref[] en usuario_perm_activ.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/usuario_perm_activ_ajax`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/usuario_perm_activ_ajax.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl_propia` | `string` | application | No | application |
| `id_tipo_activ` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `procesos_UsuarioPermActivFasesData`):
  - `opciones` (`array`)

## Casos De Uso

- `src\procesos\application\UsuarioPermActivFases`

## Frontend Relacionado

- `frontend/procesos/controller/usuario_perm_activ.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.