---
id: "casas.casa_ec_gastos_form_data"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/casa_ec_gastos_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/casas/infrastructure/ui/http/controllers/casa_ec_gastos_form_data.php"
entrada: ["post.id_cdc:array", "post.year:integer"]
entrada_obligatoria: ["id_cdc"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Debe seleccionar una casa."]
frontend_referencias: ["frontend/casas/controller/casa_ec_gastos_lista.php"]
casos_uso: ["src\\casas\\application\\CasaEcGastosFormData"]
tags: ["casas", "casa", "ec", "gastos", "form", "data"]
estado_revision: "revisado"
---

# Casa Ec Gastos Form Data

Formulario anual de gastos y aportaciones (sv/sf) por mes de una o varias casas.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Sucesor de la rama `que=getGastos` de `apps/casas/controller/casa_ec_ajax.php`. Carga los
`UbiGasto` del año (`year`, por defecto año actual) y devuelve, por casa, los 12 meses con gasto (`g`),
aportación sv (`ap_sv`) y sf (`ap_sf`), más sumas anuales.

## Endpoint

- URL: `/src/casas/casa_ec_gastos_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/casas/infrastructure/ui/http/controllers/casa_ec_gastos_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_cdc` | `array` | controller+application | Sí | IDs de casa |
| `year` | `integer` | controller+application | No | Por defecto año actual |

## Salida

- Helper: `ContestarJson::enviar('', $payload)` (doble `JSON.parse`).
- Éxito: `ok: true`, `casas[]` con `id_ubi`, `titulo`, `meses[]` (`mes`, `g`, `ap_sv`, `ap_sf`),
  `suma_g`, `suma_sv`, `suma_sf`, `year`.
- Error: `ok: false`, `error`, `casas: []`.

## Errores conocidos

- `Debe seleccionar una casa.`

## Permisos

- Sin control propio; autorización en frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\casas\application\CasaEcGastosFormData`

## Frontend Relacionado

- `frontend/casas/controller/casa_ec_gastos_lista.php`: `casa.php` con `tipo_lista=datosEcGastos`.
