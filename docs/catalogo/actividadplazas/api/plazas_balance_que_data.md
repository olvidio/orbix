---
id: "actividadplazas.plazas_balance_que_data"
tipo: "endpoint"
modulo: "actividadplazas"
url: "/src/actividadplazas/plazas_balance_que_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/actividadplazas/infrastructure/ui/http/controllers/plazas_balance_que_data.php"
entrada: ["post.id_tipo_activ:string", "post.sactividad:string", "post.sasistentes:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadplazas_PlazasBalanceQueDataData"
respuesta_data: ["id_tipo_activ:string", "delegaciones_opciones:array<string, string>"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadplazas/controller/plazas_balance_que.php"]
casos_uso: ["src\\actividadplazas\\application\\PlazasBalanceQueData"]
tags: ["actividadplazas", "plazas", "balance", "que", "data"]
estado_revision: "revisado"
---

# Plazas Balance Que Data

Data builder de la pantalla de filtro `plazas_balance_que`: resuelve el `id_tipo_activ` y devuelve el
desplegable de delegaciones activas para elegir con qué dl comparar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- Si no llega `id_tipo_activ`, lo calcula a partir de `sasistentes` + `sactividad` (y el sf/sv de mi
  configuración) construyendo un `TiposActividades`.
- Devuelve `delegaciones_opciones`: mapa `dl => nombre_dl` de todas las delegaciones activas ordenado
  por nombre, para el `<select>` de la pantalla de balance.

## Endpoint

- URL: `/src/actividadplazas/plazas_balance_que_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/plazas_balance_que_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `string` | controller | No | Si llega, se usa directamente; si no, se deriva |
| `sasistentes` | `string` | controller | No | Colectivo para derivar `id_tipo_activ` |
| `sactividad` | `string` | controller | No | Tipo de actividad para derivar `id_tipo_activ` |

## Salida

- Helper: `ContestarJson::enviar` (`data` serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadplazas_PlazasBalanceQueDataData`):
  - `id_tipo_activ` (`string`): tipo resuelto.
  - `delegaciones_opciones` (`array<string, string>`): `dl => nombre_dl` de las delegaciones activas.

## Permisos

- Sin control de permisos propio; la autorización de oficina se resuelve en frontend
  (`plazas_balance_que.php`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadplazas\application\PlazasBalanceQueData`

## Frontend Relacionado

- `frontend/actividadplazas/controller/plazas_balance_que.php`
