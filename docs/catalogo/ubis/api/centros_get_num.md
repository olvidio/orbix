---
id: "ubis.centros_get_num"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/centros_get_num"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/ubis/infrastructure/ui/http/controllers/centros_get_num.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_CentrosGetNumDataData"
respuesta_data: ["a_cabeceras:list<mixed>, a_valores: array<int, array<int, mixed>>"]
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/centros_get_num.php"]
casos_uso: ["src\\ubis\\application\\CentrosGetNumData"]
tags: ["ubis", "centros", "get", "num"]
estado_revision: "revisado"
errores: []
---

# Centros Get Num

Lista centros DL activos con sus datos numéricos de buzón, pi y cartas.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lista centros DL activos con sus datos numéricos de buzón, pi y cartas.

## Endpoint

- URL: `/src/ubis/centros_get_num`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/centros_get_num.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| _(ninguno)_ | | | | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `a_cabeceras`: cabeceras tabla
  - `a_valores`: filas con n_buzon, num_pi, num_cartas

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `UbiPermisos` (`puedeModificarPorObjeto`, `dlPerteneceAMiDelegacion`), `have_perm_oficina(scdl|scl|vcsd|des|admin_sv)` y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\ubis\application\CentrosGetNumData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/centros_get_num.php"]`).
