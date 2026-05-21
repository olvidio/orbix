---
id: "ubis.teleco_guardar"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/teleco_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/teleco_guardar.php"
entrada: ["post.id_desc_teleco:integer", "post.id_tipo_teleco:integer", "post.id_ubi:integer", "post.num_teleco:string", "post.obj_pau:string", "post.observ:string", "post.s_pkey:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\ubis\\application\\TelecoGuardar"]
tags: ["ubis", "teleco", "guardar"]
estado_revision: "generado"
---

# Teleco Guardar

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/teleco_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/teleco_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_desc_teleco` | `integer` | controller | No | controller |
| `id_tipo_teleco` | `integer` | controller | No | controller |
| `id_ubi` | `integer` | controller | No | controller |
| `num_teleco` | `string` | controller | No | controller |
| `obj_pau` | `string` | controller | No | controller |
| `observ` | `string` | controller | No | controller |
| `s_pkey` | `string` | controller | No | controller |
| `sel` | `array` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\ubis\application\TelecoGuardar`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.