---
id: "cambios.cambio_usuario_objeto_pref_guardar"
tipo: "endpoint"
modulo: "cambios"
url: "/src/cambios/cambio_usuario_objeto_pref_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/cambios/infrastructure/ui/http/controllers/cambio_usuario_objeto_pref_guardar.php"
entrada: ["post.aviso_off:string", "post.aviso_on:string", "post.aviso_outdate:string", "post.aviso_tipo:integer", "post.casas:array", "post.dl_propia:string", "post.id_fase_ref:integer", "post.id_item_usuario_objeto:integer", "post.id_tipo_activ:string", "post.id_usuario:integer", "post.objeto:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "cambios_CambioUsuarioObjetoPrefGuardarData"
respuesta_data: ["error:string, id_item_usuario_objeto: int"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\cambios\\application\\CambioUsuarioObjetoPrefGuardar"]
tags: ["cambios", "cambio", "usuario", "objeto", "pref", "guardar"]
estado_revision: "generado"
---

# Cambio Usuario Objeto Pref Guardar

Endpoint JSON: crea o actualiza un `CambioUsuarioObjetoPref`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/cambios/cambio_usuario_objeto_pref_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_objeto_pref_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `aviso_off` | `string` | controller+application | No | controller+application |
| `aviso_on` | `string` | controller+application | No | controller+application |
| `aviso_outdate` | `string` | controller+application | No | controller+application |
| `aviso_tipo` | `integer` | controller+application | No | controller+application |
| `casas` | `array` | controller+application | No | controller+application |
| `dl_propia` | `string` | controller+application | No | controller+application |
| `id_fase_ref` | `integer` | controller+application | No | controller+application |
| `id_item_usuario_objeto` | `integer` | controller+application | No | controller+application |
| `id_tipo_activ` | `string` | controller+application | No | controller+application |
| `id_usuario` | `integer` | controller+application | No | controller+application |
| `objeto` | `string` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `cambios_CambioUsuarioObjetoPrefGuardarData`):
  - `error` (`string, id_item_usuario_objeto: int`)

## Casos De Uso

- `src\cambios\application\CambioUsuarioObjetoPrefGuardar`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.