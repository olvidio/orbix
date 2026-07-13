---
id: "actividadessacd.solapes_sacd_data"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/solapes_sacd_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/solapes_sacd_data.php"
entrada: ["post.year:string", "post.periodo:string", "post.empiezamin:string", "post.empiezamax:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadessacd_SolapesSacdDataData"
respuesta_data: ["titulo:string", "inicio_iso:string", "fin_iso:string", "texto_fase_ok_sacd:string", "filas:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadessacd/controller/activ_sacd.php", "frontend/actividadessacd/view/activ_sacd.phtml"]
casos_uso: ["src\\actividadessacd\\application\\SolapesSacdData"]
tags: ["actividadessacd", "solapes", "sacd", "data"]
estado_revision: "revisado"
---

# Solapes Sacd Data

Lista los sacd que tienen actividades incompatibles (solapes) en el periodo: para cada sacd de la
delegación, las actividades en las que participa que se pisan en fechas.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- Resuelve el periodo con `Periodo` y selecciona las actividades del rango (`status < TERMINADA`).
- Carga los sacd de la delegación (`id_tabla in ('n','a')`, `sacd = t`, `dl = mi_delef()`).
- Calcula los solapes (`CargoOAsistente::getSolapes(sacds, actividades)`): mapa `id_nom => [id_activ,…]`.
- Por cada sacd con solapes construye una fila con su nombre y la lista de actividades solapadas,
  marcando la clase visual (`plaza4` si aprobada, `wrong-soft` si proyecto, `tachado` si repite lugar).

## Endpoint

- URL: `/src/actividadessacd/solapes_sacd_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/solapes_sacd_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `year` | `string` | controller (`inputString`) | No | Año del periodo (por defecto `next`) |
| `periodo` | `string` | controller (`inputString`) | No | Selector de periodo (`Periodo`) |
| `empiezamin` | `string` | controller (`inputString`) | No | Límite inferior de `f_ini` |
| `empiezamax` | `string` | controller (`inputString`) | No | Límite superior de `f_ini` |

El controller construye `$input` con estos cuatro campos.

## Salida

- Helper: `ContestarJson::enviar('', $useCase->execute($input))` — `data` es el payload serializado
  como string JSON; el front hace un segundo `JSON.parse`.
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadessacd_SolapesSacdDataData`):
  - `titulo` (`string`)
  - `inicio_iso` / `fin_iso` (`string`): rango del periodo.
  - `texto_fase_ok_sacd` (`string`): descripción de la fase `FASE_OK_SACD`.
  - `filas` (`array`): por sacd, `{id_nom (int), nom_sacd (string), actividades: [{clase, nom_activ}]}`.

## Permisos

- El caso de uso no aplica control de permisos propio (acota por la delegación del usuario,
  `ConfigGlobal::mi_delef()`). La autorización se resuelve en el frontend (`activ_sacd.php`) y en
  `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadessacd\application\SolapesSacdData`

## Frontend Relacionado

- `frontend/actividadessacd/controller/activ_sacd.php` (emite `url_solapes`; se usa cuando `tipo = solape`).
- `frontend/actividadessacd/view/activ_sacd.phtml` (`fnjs_ver` → `fnjs_construir_tabla_solapes`).
