---
id: "actividadtarifas.tarifa_ubi_update_inc"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tarifa_ubi_update_inc"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_update_inc.php"
entrada: ["post.inc_cantidad:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/casas/controller/calendario_ubi_resumen.php"]
casos_uso: ["src\\actividadtarifas\\application\\TarifaUbiUpdateInc"]
tags: ["actividadtarifas", "tarifa", "ubi", "update", "inc"]
estado_revision: "generado"
---

# Tarifa Ubi Update Inc

Endpoint backend: actualiza en lote las cantidades de varias `TarifaUbi` desde el estudio economico de casa.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadtarifas/tarifa_ubi_update_inc`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_update_inc.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `inc_cantidad` | `array` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\actividadtarifas\application\TarifaUbiUpdateInc`

## Frontend Relacionado

- `frontend/casas/controller/calendario_ubi_resumen.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.