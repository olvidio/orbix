---
id: "encargossacd.encargo_ver_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/encargo_ver_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/encargossacd/infrastructure/ui/http/controllers/encargo_ver_data.php"
entrada: ["post.desc_enc:mixed", "post.desc_lugar:mixed", "post.filtro_ctr:mixed", "post.grupo:mixed", "post.id_enc:mixed", "post.id_tipo_enc:mixed", "post.id_zona:mixed", "post.que:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/encargo_ver.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoVerData"]
tags: ["encargossacd", "encargo", "ver", "data"]
estado_revision: "revisado"
---
# Encargo Ver Data

Datos para la pantalla `encargo_ver` (nuevo / editar encargo). El frontend arma los `frontend\shared\web\Desplegable` a partir de los arrays devueltos.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Payload del formulario nuevo/editar encargo: desplegables de centros, zonas, idiomas, tipos y valores actuales. Sucesor de ramas de `apps/encargossacd/controller/encargo_ajax.php`.

## Endpoint

- URL: `/src/encargossacd/encargo_ver_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
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

- Helper: `ContestarJson::enviar`.
- Claves: `que`, `opciones_*`, valores del encargo (doble `JSON.parse`).


## Permisos

Sin control propio; frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\encargossacd\application\EncargoVerData`

## Frontend Relacionado

- `frontend/encargossacd/controller/encargo_ver.php`

