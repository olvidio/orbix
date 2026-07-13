---
id: "ubis.centros_opciones_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/centros_opciones_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/ubis/infrastructure/ui/http/controllers/centros_opciones_data.php"
entrada: ["post.active:string", "post.sv:string", "post.sf:string", "post.id_ubi_in:string", "post.tipo_ctr:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_CentrosOpcionesDataData"
respuesta_data: ["opciones:array"]
requiere_hashb: false
frontend_referencias: ["frontend/shared/web/CentrosQue.php"]
casos_uso: ["src\\ubis\\application\\CentrosOpcionesData"]
tags: ["ubis", "centros", "opciones", "data"]
estado_revision: "revisado"
errores: []
---

# Centros Opciones Data

Devuelve opciones de centros filtradas para desplegables compartidos del frontend.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve opciones de centros filtradas para desplegables compartidos del frontend.

## Endpoint

- URL: `/src/ubis/centros_opciones_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/centros_opciones_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `active` | `mixed` | application | No | |
| `sv` | `mixed` | application | No | |
| `sf` | `mixed` | application | No | |
| `id_ubi_in` | `mixed` | application | No | |
| `tipo_ctr` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `opciones`: map id_ubi=>nombre para desplegable CentrosQue

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Consumido por CentrosQue en otros módulos; sin permisos propios.

## Casos De Uso

- `src\ubis\application\CentrosOpcionesData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/shared/web/CentrosQue.php"]`).
