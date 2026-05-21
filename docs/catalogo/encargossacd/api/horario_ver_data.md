---
id: "encargossacd.horario_ver_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/horario_ver_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/horario_ver_data.php"
entrada: ["post.id_enc:mixed", "post.id_item_h:mixed", "post.mod:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/horario_ver.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoHorarioVerData"]
tags: ["encargossacd", "horario", "ver", "data"]
estado_revision: "generado"
---

# Horario Ver Data

Datos del formulario de horario de encargo (no sacd).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/encargossacd/horario_ver_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/horario_ver_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_enc` | `mixed` | controller | No | controller |
| `id_item_h` | `mixed` | controller | No | controller |
| `mod` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\encargossacd\application\EncargoHorarioVerData`

## Frontend Relacionado

- `frontend/encargossacd/controller/horario_ver.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.