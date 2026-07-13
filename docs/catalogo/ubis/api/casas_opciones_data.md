---
id: "ubis.casas_opciones_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/casas_opciones_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/ubis/infrastructure/ui/http/controllers/casas_opciones_data.php"
entrada: ["post.active:string", "post.sv:string", "post.sf:string", "post.id_ubi_in:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_CasasOpcionesDataData"
respuesta_data: ["opciones:array"]
requiere_hashb: false
frontend_referencias: ["frontend/casas/controller/calendario_ubi_resumen.php", "frontend/shared/web/CasasQue.php"]
casos_uso: ["src\\ubis\\application\\CasasOpcionesData"]
tags: ["ubis", "casas", "opciones", "data"]
estado_revision: "revisado"
errores: []
---

# Casas Opciones Data

Devuelve opciones de casas filtradas para desplegables compartidos del frontend.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve opciones de casas filtradas para desplegables compartidos del frontend.

## Endpoint

- URL: `/src/ubis/casas_opciones_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/casas_opciones_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `active` | `mixed` | application | No | |
| `sv` | `mixed` | application | No | |
| `sf` | `mixed` | application | No | |
| `id_ubi_in` | `mixed` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `opciones`: map id_ubi=>nombre para desplegable CasasQue

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Consumido por CasasQue en otros módulos; sin permisos propios.

## Casos De Uso

- `src\ubis\application\CasasOpcionesData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/casas/controller/calendario_ubi_resumen.php", "frontend/shared/web/CasasQue.php"]`).
