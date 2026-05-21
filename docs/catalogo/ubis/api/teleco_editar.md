---
id: "ubis.teleco_editar"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/teleco_editar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/teleco_editar.php"
entrada: ["post.id_ubi:integer", "post.mod:string", "post.obj_pau:string", "post.s_pkey:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/teleco_editar.php"]
casos_uso: ["src\\ubis\\application\\TelecoEditarData"]
tags: ["ubis", "teleco", "editar"]
estado_revision: "generado"
---

# Teleco Editar

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/teleco_editar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/teleco_editar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | controller | No | controller |
| `mod` | `string` | controller | No | controller |
| `obj_pau` | `string` | controller | No | controller |
| `s_pkey` | `string` | controller | No | controller |
| `sel` | `array` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\ubis\application\TelecoEditarData`

## Frontend Relacionado

- `frontend/ubis/controller/teleco_editar.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.