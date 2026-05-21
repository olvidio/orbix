---
id: "encargossacd.sacd_ficha_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/sacd_ficha_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/sacd_ficha_data.php"
entrada: ["post.id_nom:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/sacd_ficha_ajax.php"]
casos_uso: ["src\\encargossacd\\application\\SacdFichaData"]
tags: ["encargossacd", "sacd", "ficha", "data"]
estado_revision: "generado"
---

# Sacd Ficha Data

Datos para la ficha de encargos de un SACD (`sacd_ficha_ajax?que=ficha`). Porta la lectura del antiguo controlador frontend y devuelve un payload estructurado con los encargos y sus dedicaciones (horario del centro y del SACD ya calculadas como texto cuando `mod_horario=3`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/encargossacd/sacd_ficha_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/sacd_ficha_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Permisos

- Permiso oficina `des`
- Permiso oficina `vcsd`

## Casos De Uso

- `src\encargossacd\application\SacdFichaData`

## Frontend Relacionado

- `frontend/encargossacd/controller/sacd_ficha_ajax.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.