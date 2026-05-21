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
respuesta_data_schema: "procesos_UsuarioPermActivDataData"
respuesta_data: ["nombre:string", "dl_propia:string", "perm_jefe:boolean", "tipo_actividad_html:string", "a_fases:array", "a_acciones:array", "a_afecta_a:array", "aPerm:array"]
requiere_hashb: false
frontend_referencias: ["frontend/procesos/controller/usuario_perm_activ.php"]
casos_uso: ["src\\procesos\\application\\UsuarioPermActivData"]
tags: ["procesos", "usuario", "perm", "activ", "data"]
estado_revision: "generado"
---

# Usuario Perm Activ Data

Caso de uso: datos para la pantalla usuario_perm_activ (alta/edicion de permisos de actividad para un usuario). Agrupa la resolucion de repositorios para que el controlador frontend no acceda directamente al contenedor ni a `use src\...`. El frontend recibe arrays serializables y construye los `frontend\shared\web\Desplegable`.

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
- Payload en `data` (schema `procesos_UsuarioPermActivDataData`):
  - `nombre` (`string`)
  - `dl_propia` (`string`)
  - `perm_jefe` (`boolean`)
  - `tipo_actividad_html` (`string`)
  - `a_fases` (`array`)
  - `a_acciones` (`array`)
  - `a_afecta_a` (`array`)
  - `aPerm` (`array`)

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