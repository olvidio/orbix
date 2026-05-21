---
id: "encargossacd.horario_sacd_ver_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/horario_sacd_ver_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/horario_sacd_ver_data.php"
entrada: ["post.desc_enc:mixed", "post.id_enc:mixed", "post.id_item:mixed", "post.id_nom:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/horario_sacd_ver.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoSacdHorarioVerData"]
tags: ["encargossacd", "horario", "sacd", "ver", "data"]
estado_revision: "generado"
---

# Horario Sacd Ver Data

Datos del formulario horario sacd (ficha tareas).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/encargossacd/horario_sacd_ver_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/horario_sacd_ver_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `desc_enc` | `mixed` | controller | No | controller |
| `id_enc` | `mixed` | controller | No | controller |
| `id_item` | `mixed` | controller | No | controller |
| `id_nom` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\encargossacd\application\EncargoSacdHorarioVerData`

## Frontend Relacionado

- `frontend/encargossacd/controller/horario_sacd_ver.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.