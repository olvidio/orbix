---
id: "casas.calendario_ubi_resumen_data"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/calendario_ubi_resumen_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/casas/infrastructure/ui/http/controllers/calendario_ubi_resumen_data.php"
entrada: ["post.G:integer", "post.id_ubi:integer", "post.inc_t:integer", "post.seccion:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/casas/controller/calendario_ubi_resumen.php", "frontend/casas/controller/calendario_ubi_resumen_body.php"]
casos_uso: ["src\\casas\\application\\CalendarioUbiResumenData"]
tags: ["casas", "calendario", "ubi", "resumen", "data"]
estado_revision: "generado"
---

# Calendario Ubi Resumen Data

Endpoint backend: estudio económico de una casa (`calendario_ubi_resumen_data`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/casas/calendario_ubi_resumen_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/casas/infrastructure/ui/http/controllers/calendario_ubi_resumen_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `G` | `integer` | controller+application | No | controller+application |
| `id_ubi` | `integer` | controller+application | No | controller+application |
| `inc_t` | `integer` | controller+application | No | controller+application |
| `seccion` | `string` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\casas\application\CalendarioUbiResumenData`

## Frontend Relacionado

- `frontend/casas/controller/calendario_ubi_resumen.php`
- `frontend/casas/controller/calendario_ubi_resumen_body.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.