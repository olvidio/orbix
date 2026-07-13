---
id: "ubis.centros_get_labor"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/centros_get_labor"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/ubis/infrastructure/ui/http/controllers/centros_get_labor.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/centros_get_labor.php"]
casos_uso: ["src\\ubis\\application\\CentrosGetLaborData"]
tags: ["ubis", "centros", "get", "labor"]
estado_revision: "revisado"
errores: []
---

# Centros Get Labor

Lista todos los centros DL activos con su tipo de centro y tipo de labor.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lista todos los centros DL activos con su tipo de centro y tipo de labor.

## Endpoint

- URL: `/src/ubis/centros_get_labor`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/centros_get_labor.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| _(ninguno)_ | | | | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `a_cabeceras`: cabeceras tabla
  - `a_valores`: filas centros con tipo_ctr y tipo_labor
  - `tipo_labor_bit_map`: mapa bits etiquetados

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `UbiPermisos` (`puedeModificarPorObjeto`, `dlPerteneceAMiDelegacion`), `have_perm_oficina(scdl|scl|vcsd|des|admin_sv)` y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\ubis\application\CentrosGetLaborData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/centros_get_labor.php"]`).
