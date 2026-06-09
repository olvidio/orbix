---
id: "actividadtarifas.tarifa_ubi_lista_data"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tarifa_ubi_lista_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_lista_data.php"
entrada: ["post.id_ubi:integer", "post.year:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadtarifas_TarifaUbiListaDataData"
respuesta_data: ["a_cabeceras:array", "a_valores:array", "any_anterior:integer", "any_actual:integer", "puede_anadir:boolean", "id_ubi:integer", "year:integer", "token_copiar:string"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_ubi_lista.php"]
casos_uso: ["src\\actividadtarifas\\application\\TarifaUbiListaData"]
tags: ["actividadtarifas", "tarifa", "ubi", "lista", "data"]
estado_revision: "generado"
---

# Tarifa Ubi Lista Data

Endpoint backend: listado de `TarifaUbi` por `id_ubi` + `year`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadtarifas/tarifa_ubi_lista_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_lista_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | controller+application | No | controller+application |
| `year` | `integer` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload en `data` (schema `actividadtarifas_TarifaUbiListaDataData`):
  - `a_cabeceras` (`array`)
  - `a_valores` (`array`)
  - `any_anterior` (`integer`)
  - `any_actual` (`integer`)
  - `puede_anadir` (`boolean`)
  - `id_ubi` (`integer`)
  - `year` (`integer`)
  - `token_copiar` (`string`)

## Casos De Uso

- `src\actividadtarifas\application\TarifaUbiListaData`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_ubi_lista.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.