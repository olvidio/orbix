---
id: "encargossacd.horario_ver_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/horario_ver_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/encargossacd/infrastructure/ui/http/controllers/horario_ver_data.php"
entrada: ["post.id_enc:mixed", "post.id_item_h:mixed", "post.mod:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/horario_ver.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoHorarioVerData"]
tags: ["encargossacd", "horario", "ver", "data"]
estado_revision: "revisado"
---
# Horario Ver Data

Datos del formulario de horario de encargo (no sacd).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Datos de horarios de encargo para `horario_ver` (listado/edición popup).

## Endpoint

- URL: `/src/encargossacd/horario_ver_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/horario_ver_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_enc` | `mixed` | controller | No | controller |
| `id_item_h` | `mixed` | controller | No | controller |
| `mod` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`.
- Filas de horario (doble `JSON.parse`).


## Permisos

Sin control propio; frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\encargossacd\application\EncargoHorarioVerData`

## Frontend Relacionado

- `frontend/encargossacd/controller/horario_ver.php`

