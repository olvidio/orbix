---
id: "ubis.teleco_tabla"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/teleco_tabla"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/teleco_tabla.php"
entrada: ["post.id_ubi:integer", "post.obj_pau:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/teleco_tabla.php"]
casos_uso: ["src\\ubis\\application\\TelecoTablaData"]
tags: ["ubis", "teleco", "tabla"]
estado_revision: "generado"
---

# Teleco Tabla

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/teleco_tabla`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/teleco_tabla.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | controller | No | controller |
| `obj_pau` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\ubis\application\TelecoTablaData`

## Frontend Relacionado

- `frontend/ubis/controller/teleco_tabla.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.