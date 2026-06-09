---
id: "ubis.centros_opciones_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/centros_opciones_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/centros_opciones_data.php"
entrada: ["post.active:mixed", "post.id_ubi_in:mixed", "post.sf:mixed", "post.sv:mixed", "post.tipo_ctr:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_CentrosOpcionesDataData"
respuesta_data: ["opciones:array"]
requiere_hashb: false
frontend_referencias: ["frontend/shared/web/CentrosQue.php"]
casos_uso: ["src\\ubis\\application\\CentrosOpcionesData"]
tags: ["ubis", "centros", "opciones", "data"]
estado_revision: "generado"
---

# Centros Opciones Data

Devuelve el payload (solo datos) para poblar el <select> de centros en `frontend\shared\web\CentrosQue`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/centros_opciones_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/centros_opciones_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `active` | `mixed` | controller | No | controller |
| `id_ubi_in` | `mixed` | controller | No | controller |
| `sf` | `mixed` | controller | No | controller |
| `sv` | `mixed` | controller | No | controller |
| `tipo_ctr` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `ubis_CentrosOpcionesDataData`):
  - `opciones` (`array`)

## Casos De Uso

- `src\ubis\application\CentrosOpcionesData`

## Frontend Relacionado

- `frontend/shared/web/CentrosQue.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.