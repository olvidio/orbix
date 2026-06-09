---
id: "casas.casas_resumen_data"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/casas_resumen_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/casas/infrastructure/ui/http/controllers/casas_resumen_data.php"
entrada: ["post.cdc_sel:integer", "post.empiezamax:string", "post.empiezamin:string", "post.id_cdc:array", "post.periodo:string", "post.que:string", "post.year:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "casas_CasasResumenDataData"
respuesta_data: ["row:array", "0:array", "1:array", "2:array", "_id_ubi_padre:integer", "_id_ubi_hijo:integer", "avisos:list<string>"]
requiere_hashb: false
frontend_referencias: ["frontend/casas/controller/casas_resumen_lista.php"]
casos_uso: ["src\\casas\\application\\CasasResumenData"]
tags: ["casas", "resumen", "data"]
estado_revision: "generado"
---

# Casas Resumen Data

Endpoint backend: resumen económico de casas (`casas_resumen_data`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/casas/casas_resumen_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/casas/infrastructure/ui/http/controllers/casas_resumen_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `cdc_sel` | `integer` | controller+application | No | controller+application |
| `empiezamax` | `string` | controller+application | No | controller+application |
| `empiezamin` | `string` | controller+application | No | controller+application |
| `id_cdc` | `array` | controller+application | No | controller+application |
| `periodo` | `string` | controller+application | No | controller+application |
| `que` | `string` | controller+application | No | controller+application |
| `year` | `string` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `casas_CasasResumenDataData`):
  - `row` (`array`)
  - `0` (`array`)
  - `1` (`array`)
  - `2` (`array`)
  - `_id_ubi_padre` (`integer`)
  - `_id_ubi_hijo` (`integer`)
  - `avisos` (`list<string>`)

## Efectos colaterales

- Use case: resumen económico de casas (dias ocupados, asistentes previstos/reales, ingresos, gastos, aportaciones, superávit).

## Casos De Uso

- `src\casas\application\CasasResumenData`

## Frontend Relacionado

- `frontend/casas/controller/casas_resumen_lista.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.