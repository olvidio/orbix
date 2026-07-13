---
id: "ubis.centros_get_plazas"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/centros_get_plazas"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/ubis/infrastructure/ui/http/controllers/centros_get_plazas.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/centros_get_plazas.php"]
casos_uso: ["src\\ubis\\application\\CentrosGetPlazasData"]
tags: ["ubis", "centros", "get", "plazas"]
estado_revision: "revisado"
errores: []
---

# Centros Get Plazas

Lista centros DL activos con plazas, habitaciones individuales y flag sede.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lista centros DL activos con plazas, habitaciones individuales y flag sede.

## Endpoint

- URL: `/src/ubis/centros_get_plazas`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/centros_get_plazas.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| _(ninguno)_ | | | | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `a_cabeceras`: cabeceras tabla
  - `a_valores`: filas con habitaciones, plazas y sede

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `UbiPermisos` (`puedeModificarPorObjeto`, `dlPerteneceAMiDelegacion`), `have_perm_oficina(scdl|scl|vcsd|des|admin_sv)` y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\ubis\application\CentrosGetPlazasData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/centros_get_plazas.php"]`).
