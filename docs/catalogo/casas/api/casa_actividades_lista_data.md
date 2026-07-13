---
id: "casas.casa_actividades_lista_data"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/casa_actividades_lista_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/casas/infrastructure/ui/http/controllers/casa_actividades_lista_data.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.id_cdc:array", "post.periodo:string", "post.year:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/casas/controller/casa_actividades_lista.php"]
casos_uso: ["src\\casas\\application\\CasaActividadesListaData"]
tags: ["casas", "casa", "actividades", "lista", "data"]
estado_revision: "revisado"
---

# Casa Actividades Lista Data

Listado de actividades por casa y periodo (modo `tipo_lista=lista_activ`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Sucesor del listado de actividades de `apps/casas/controller/casa_que.php` con `que=lista_activ`.
Devuelve cabeceras y filas agrupadas por casa con fechas, tipo, centros encargados, SACDs y tarifa.
Oculta datos si el permiso `datos.ver` no lo permite (muestra «ocupado»).

## Endpoint

- URL: `/src/casas/casa_actividades_lista_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/casas/infrastructure/ui/http/controllers/casa_actividades_lista_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_cdc` | `array` | controller+application | No | IDs de casa |
| `periodo` | `string` | controller+application | No | |
| `year` | `string` | controller+application | No | |
| `empiezamin` / `empiezamax` | `string` | controller+application | No | |

## Salida

- Helper: `ContestarJson::enviar('', $payload)` (doble `JSON.parse`).
- `ok: true`, `a_cabeceras`, `a_valores` (por `id_ubi`), `a_grupos` (títulos de casa).

## Permisos

- Por actividad: `PermisosActividades` facetas `datos` (`ocupado`/`ver`), `ctr` (`ver` centros),
  `sacd` (`ver` + aprobación SACD si `procesos`+sf). Sin sesión de permisos usa `PermisosActividadesTrue`.

## Casos De Uso

- `src\casas\application\CasaActividadesListaData`

## Frontend Relacionado

- `frontend/casas/controller/casa_actividades_lista.php`: fragmento de `casa.php` con
  `tipo_lista=lista_activ`.
