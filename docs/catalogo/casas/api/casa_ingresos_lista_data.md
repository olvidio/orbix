---
id: "casas.casa_ingresos_lista_data"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/casa_ingresos_lista_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/casas/infrastructure/ui/http/controllers/casa_ingresos_lista_data.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.id_cdc:array", "post.periodo:string", "post.year:string"]
entrada_obligatoria: ["id_cdc"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Debe seleccionar una casa."]
frontend_referencias: ["frontend/casas/controller/casa_ingresos_lista.php"]
casos_uso: ["src\\casas\\application\\CasaIngresosListaData"]
tags: ["casas", "casa", "ingresos", "lista", "data"]
estado_revision: "revisado"
---

# Casa Ingresos Lista Data

Listado económico de actividades por casa y periodo (gestión económica).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Sucesor del listado económico de `apps/casas/controller/casa_que.php`. Por cada casa en `id_cdc`
calcula filas con ingresos previstos/reales, asistentes, precios y totales sv/sf. Filtra actividades
por permiso `economic.ver`. Si `id_cdc` está vacío devuelve `ok: false`.

## Endpoint

- URL: `/src/casas/casa_ingresos_lista_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/casas/infrastructure/ui/http/controllers/casa_ingresos_lista_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_cdc` | `array` | controller+application | Sí | IDs de casa (lista) |
| `periodo` | `string` | controller+application | No | P. ej. `desdeHoy` cambia filtro a `f_fin` |
| `year` | `string` | controller+application | No | Año del `Periodo` |
| `empiezamin` / `empiezamax` | `string` | controller+application | No | Rango personalizado |

## Salida

- Helper: `ContestarJson::enviar('', $payload)` (doble `JSON.parse`).
- Claves principales: `ok`, `error`, `a_cabeceras`, `a_valores` (agrupado por `id_ubi`), `a_grupos`,
  `nota` (ingresos proporcionales al periodo), `errores` (avisos HTML de datos incompletos).
- Filas con permiso `economic.modificar` incluyen celda `{script: fnjs_modificar(id_activ), valor}`.

## Errores conocidos

- `Debe seleccionar una casa.` (`ok: false`)
- Avisos acumulados en `errores`: asistentes previstos/reales o ingresos no definidos por actividad.

## Permisos

- Por actividad: `PermisosActividades` faceta `economic` (`ver` para incluir fila, `modificar` para
  enlace de edición). Requiere `$_SESSION['oPermActividades']`.

## Casos De Uso

- `src\casas\application\CasaIngresosListaData`

## Frontend Relacionado

- `frontend/casas/controller/casa_ingresos_lista.php`: fragmento AJAX de `casa.php` (modo por defecto).
