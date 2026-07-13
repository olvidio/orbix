---
id: "dossiers.perm_dossiers_data"
tipo: "endpoint"
modulo: "dossiers"
url: "/src/dossiers/perm_dossiers_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/dossiers/infrastructure/ui/http/controllers/perm_dossiers_data.php"
entrada: ["post.tipo:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "dossiers_PermDossiersListaDataData"
respuesta_data: ["a_filas:array"]
requiere_hashb: false
frontend_referencias: ["frontend/dossiers/controller/perm_dossiers.php"]
casos_uso: ["src\\dossiers\\application\\PermDossiersListaData"]
tags: ["dossiers", "perm", "data"]
estado_revision: "revisado"
---

# Perm Dossiers Data

Listado de tipos de dossier para la pantalla de administración de permisos.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve las filas de los `TipoDossier` de un ámbito (`tabla_from = tipo`), ordenadas por
`id_tipo_dossier`, para pintar el listado desde el que se accede a la edición de permisos de cada
tipo. Cada fila lleva su `pagina_link_spec` hacia `perm_dossier_ver.php` (que el frontend firma).

## Endpoint

- URL: `/src/dossiers/perm_dossiers_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/dossiers/infrastructure/ui/http/controllers/perm_dossiers_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `tipo` | `string` | controller | No | Ámbito (`tabla_from`) a listar. El controller aplica `'p'` por defecto si llega vacío |

El controller lee `tipo` de `$_POST`, aplica el valor por defecto `'p'` y llama a `PermDossiersListaData::build()`.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace un segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `dossiers_PermDossiersListaDataData`):
  - `a_filas`: lista de filas, cada una con:
    - `descripcion` (`string`)
    - `pagina_link_spec` (link spec hacia `frontend/dossiers/controller/perm_dossier_ver.php` con
      `query`: `id_tipo_dossier`, `depende_modificar`, `tipo`); se firma en el frontend
      (`DossiersListaSupport::signFilas`), no en el borde HTTP.

## Permisos

- El caso de uso no aplica un control de permisos propio: la autorización se resuelve en el frontend
  (`perm_dossiers.php`) y en `$_SESSION['oPerm']`. No inferir permisos concretos aquí.

## Casos De Uso

- `src\dossiers\application\PermDossiersListaData`

## Frontend Relacionado

- `frontend/dossiers/controller/perm_dossiers.php` (firma las filas con
  `DossiersListaSupport::signFilas(..., ['pagina'])` y pinta `perm_dossiers.phtml`).
