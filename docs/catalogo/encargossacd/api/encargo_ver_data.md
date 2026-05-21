---
id: "encargossacd.encargo_ver_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/encargo_ver_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/encargo_ver_data.php"
entrada: ["post.desc_enc:mixed", "post.desc_lugar:mixed", "post.filtro_ctr:mixed", "post.grupo:mixed", "post.id_enc:mixed", "post.id_tipo_enc:mixed", "post.id_zona:mixed", "post.que:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/encargo_ver.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoVerData"]
tags: ["encargossacd", "encargo", "ver", "data"]
estado_revision: "generado"
---

# Encargo Ver Data

Datos para la pantalla `encargo_ver` (nuevo / editar encargo). El frontend arma los `frontend\shared\web\Desplegable` a partir de los arrays devueltos.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/encargossacd/encargo_ver_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/encargo_ver_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `desc_enc` | `mixed` | controller | No | controller |
| `desc_lugar` | `mixed` | controller | No | controller |
| `filtro_ctr` | `mixed` | controller | No | controller |
| `grupo` | `mixed` | controller | No | controller |
| `id_enc` | `mixed` | controller | No | controller |
| `id_tipo_enc` | `mixed` | controller | No | controller |
| `id_zona` | `mixed` | controller | No | controller |
| `que` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\encargossacd\application\EncargoVerData`

## Frontend Relacionado

- `frontend/encargossacd/controller/encargo_ver.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.