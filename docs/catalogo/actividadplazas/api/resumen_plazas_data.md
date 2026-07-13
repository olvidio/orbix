---
id: "actividadplazas.resumen_plazas_data"
tipo: "endpoint"
modulo: "actividadplazas"
url: "/src/actividadplazas/resumen_plazas_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/actividadplazas/infrastructure/ui/http/controllers/resumen_plazas_data.php"
entrada: ["post.id_activ:integer", "post.nom_activ:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadplazas_ResumenPlazasDataData"
respuesta_data: ["id_activ:integer", "nom_activ:string", "publicado:boolean", "otra_dl:boolean", "a_plazas:array", "plazas_totales:integer", "tot_calendario:integer", "tot_cedidas:integer", "tot_conseguidas:integer", "tot_disponibles:integer", "tot_ocupadas:integer", "dl_opciones:array"]
requiere_hashb: false
errores: ["falta parametro id_activ"]
frontend_referencias: ["frontend/actividadplazas/controller/resumen_plazas.php"]
casos_uso: ["src\\actividadplazas\\application\\ResumenPlazasData"]
tags: ["actividadplazas", "resumen", "plazas", "data"]
estado_revision: "revisado"
---

# Resumen Plazas Data

Data builder del resumen de plazas por actividad: plazas por dl (calendario, cedidas, conseguidas,
disponibles, ocupadas), totales agregados, opciones del desplegable de dl para "ceder" y flags
`publicado` / `otra_dl`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Reúne el detalle de plazas de una actividad para pintar la tabla resumen y el formulario de cesión:

- Carga la actividad para calcular `publicado` y `otra_dl` (organiza otra dl distinta de la mía).
- Delega en `ResumenPlazasService::getResumen()` el desglose `a_plazas` por dl y sus totales.
- Extrae los totales (`tot_calendario`, `tot_cedidas`, `tot_conseguidas`, `tot_disponibles`,
  `tot_ocupadas`, `plazas_totales`) de la fila `total`.
- Obtiene `dl_opciones` (esquemas/delegaciones posibles) para el desplegable de "ceder plazas".
- Si `id_activ` no es positivo, devuelve la estructura vacía con `error = "falta parametro id_activ"`
  (el controller lo traslada al campo `mensaje` del envelope).

## Endpoint

- URL: `/src/actividadplazas/resumen_plazas_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/resumen_plazas_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller | No* | Actividad a resumir; si `<= 0` responde error de negocio |
| `nom_activ` | `string` | controller | No | Nombre de la actividad; se devuelve tal cual para el título |

\* No es obligatorio a nivel de framework, pero sin un `id_activ` positivo el caso de uso devuelve error.

## Salida

- Helper: `ContestarJson::enviar` (`data` serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- El controller extrae la clave `error` del array y la envía como `mensaje`; el resto es `data`.
- Payload en `data` (schema `actividadplazas_ResumenPlazasDataData`):
  - `id_activ` (`integer`), `nom_activ` (`string`)
  - `publicado` (`boolean`), `otra_dl` (`boolean`)
  - `a_plazas` (`array`): desglose por dl (`calendario`, `total_cedidas`, `total_conseguidas`,
    `total_disponibles`, `total_ocupadas`, `cedidas`, `conseguidas`…) más la fila `total`.
  - `plazas_totales` (`integer`), `tot_calendario` (`integer`), `tot_cedidas` (`integer`),
    `tot_conseguidas` (`integer`), `tot_disponibles` (`integer`), `tot_ocupadas` (`integer`)
  - `dl_opciones` (`array`): opciones de dl destino para ceder.

## Errores conocidos

- `falta parametro id_activ`

## Permisos

- Sin control de permisos propio; la autorización de oficina se resuelve en frontend
  (`resumen_plazas.php`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadplazas\application\ResumenPlazasData`

## Frontend Relacionado

- `frontend/actividadplazas/controller/resumen_plazas.php`
