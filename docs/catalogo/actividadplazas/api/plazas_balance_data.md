---
id: "actividadplazas.plazas_balance_data"
tipo: "endpoint"
modulo: "actividadplazas"
url: "/src/actividadplazas/plazas_balance_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividadplazas/infrastructure/ui/http/controllers/plazas_balance_data.php"
entrada: ["post.dl:string", "post.id_tipo_activ:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadplazas_PlazasBalanceDataData"
respuesta_data: ["dlA:string", "dlB:string", "concedidasA2B:integer", "concedidasB2A:integer", "a_cabeceras:list<array<string, mixed>>", "a_valores:array"]
requiere_hashb: false
errores: ["falta parametro dl", "no se puede comparar una dl consigo misma"]
frontend_referencias: ["frontend/actividadplazas/controller/plazas_balance_dl.php"]
casos_uso: ["src\\actividadplazas\\application\\PlazasBalanceData"]
tags: ["actividadplazas", "plazas", "balance", "data"]
estado_revision: "revisado"
---

# Plazas Balance Data

Data builder del grid comparativo A vs B: plazas concedidas y libres entre mi dl (A) y otra dl (B)
para un tipo de actividad en el curso vigente.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Compara plazas entre dos delegaciones:

- `dlA` es siempre mi dl (`ConfigGlobal::mi_delef()`); `dlB` es el `dl` recibido.
- Resuelve el rango de curso (`ca`/`cv` → curso `est`, `crt` → curso `crt`) a partir de
  `id_tipo_activ` y del `ConfigSnapshot` de sesión.
- Por cada actividad del tipo en estado actual dentro del curso, calcula por dl las concedidas
  (`<dl>-c`) y las libres (`<dl>-l = concedidas - ocupadas`), marcando `editable` según qué dl es
  la mía.
- Devuelve además los totales cruzados `concedidasA2B` y `concedidasB2A`.
- Errores de negocio: si falta `dl`, o si `dlA === dlB`, devuelve `error` (el controller lo traslada
  a `mensaje`).

## Endpoint

- URL: `/src/actividadplazas/plazas_balance_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/plazas_balance_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl` | `string` | controller | No* | dl con la que comparar (dlB); si vacío o igual a mi dl, error de negocio |
| `id_tipo_activ` | `string` | controller | No | Tipo de actividad a comparar; determina el rango de curso |

\* No obligatorio a nivel de framework, pero sin `dl` válido el caso de uso devuelve error.

## Salida

- Helper: `ContestarJson::enviar` (`data` serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- El controller extrae la clave `error` del array y la envía como `mensaje`; el resto es `data`.
- Payload en `data` (schema `actividadplazas_PlazasBalanceDataData`):
  - `dlA` (`string`): mi dl. `dlB` (`string`): dl comparada.
  - `concedidasA2B` (`integer`), `concedidasB2A` (`integer`): totales concedidos en cada sentido.
  - `a_cabeceras` (`list<array<string, mixed>>`): id (oculta), actividad, dl org y `<dlA>-c`/`<dlA>-l`/`<dlB>-c`/`<dlB>-l`.
  - `a_valores` (`array`): filas por actividad con las celdas `{editable, valor}`.

## Errores conocidos

- `falta parametro dl`
- `no se puede comparar una dl consigo misma`

## Permisos

- Sin control de permisos propio; la editabilidad de celdas depende de que la dl sea la mía y la
  autorización de oficina se resuelve en frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadplazas\application\PlazasBalanceData`

## Frontend Relacionado

- `frontend/actividadplazas/controller/plazas_balance_dl.php` (inserta el grid en `#comparativa`).
