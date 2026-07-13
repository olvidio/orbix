---
id: "ubis.lista_ctrs_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/lista_ctrs_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/ubis/infrastructure/ui/http/controllers/lista_ctrs_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_CentrosSListaDataData"
respuesta_data: ["a_cabeceras:list<string>, a_valores: array<int, array<int, int|string>>, num_total_s: int"]
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/lista_ctrs.php"]
casos_uso: ["src\\ubis\\application\\CentrosSListaData"]
tags: ["ubis", "lista", "ctrs", "data"]
estado_revision: "revisado"
errores: []
---

# Lista Ctrs Data

Lista centros tipo s de la delegación con el número de sacerdotes asignados en cada uno.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lista centros tipo s de la delegación con el número de sacerdotes asignados en cada uno.

## Endpoint

- URL: `/src/ubis/lista_ctrs_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/lista_ctrs_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| _(ninguno)_ | | | | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `a_cabeceras`: centro, num s
  - `a_valores`: filas nombre y cuenta sacerdotes
  - `num_total_s`: total sacerdotes

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `UbiPermisos` (`puedeModificarPorObjeto`, `dlPerteneceAMiDelegacion`), `have_perm_oficina(scdl|scl|vcsd|des|admin_sv)` y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\ubis\application\CentrosSListaData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/lista_ctrs.php"]`).
