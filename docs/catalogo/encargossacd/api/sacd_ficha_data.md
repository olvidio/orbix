---
id: "encargossacd.sacd_ficha_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/sacd_ficha_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/encargossacd/infrastructure/ui/http/controllers/sacd_ficha_data.php"
entrada: ["post.id_nom:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/sacd_ficha_ajax.php"]
casos_uso: ["src\\encargossacd\\application\\SacdFichaData"]
tags: ["encargossacd", "sacd", "ficha", "data"]
estado_revision: "revisado"
---
# Sacd Ficha Data

Datos para la ficha de encargos de un SACD (`sacd_ficha_ajax?que=ficha`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Ficha de encargos de un SACD: tareas, horarios, observaciones, avisos. Sucesor de `apps/encargossacd/controller/sacd_ficha_ajax.php?que=ficha`.

## Endpoint

- URL: `/src/encargossacd/sacd_ficha_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/sacd_ficha_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `mixed` | controller | No | controller |

## Salida

- Payload: `encargos[]`, `permiso`, `observ_sacd`, `avisos[]` (doble `JSON.parse`).


## Permisos

- Permiso oficina `des`
- Permiso oficina `vcsd`

## Casos De Uso

- `src\encargossacd\application\SacdFichaData`

## Frontend Relacionado

- `frontend/encargossacd/controller/sacd_ficha_ajax.php`

