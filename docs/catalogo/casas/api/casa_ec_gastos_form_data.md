---
id: "casas.casa_ec_gastos_form_data"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/casa_ec_gastos_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/casas/infrastructure/ui/http/controllers/casa_ec_gastos_form_data.php"
entrada: ["post.id_cdc:array", "post.year:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/casas/controller/casa_ec_gastos_lista.php"]
casos_uso: ["src\\casas\\application\\CasaEcGastosFormData"]
tags: ["casas", "casa", "ec", "gastos", "form", "data"]
estado_revision: "generado"
---

# Casa Ec Gastos Form Data

Data builder: formulario anual con gastos y aportaciones (sv/sf) por mes de una casa. Sucesor de la rama `que=getGastos` de `apps/casas/controller/casa_ec_ajax.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/casas/casa_ec_gastos_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/casas/infrastructure/ui/http/controllers/casa_ec_gastos_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_cdc` | `array` | controller+application | No | controller+application |
| `year` | `integer` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`

## Casos De Uso

- `src\casas\application\CasaEcGastosFormData`

## Frontend Relacionado

- `frontend/casas/controller/casa_ec_gastos_lista.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.