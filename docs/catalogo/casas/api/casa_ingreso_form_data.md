---
id: "casas.casa_ingreso_form_data"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/casa_ingreso_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/casas/infrastructure/ui/http/controllers/casa_ingreso_form_data.php"
entrada: ["post.id_activ:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/casas/controller/casa_ingreso_form.php"]
casos_uso: ["src\\casas\\application\\CasaIngresoFormData"]
tags: ["casas", "casa", "ingreso", "form", "data"]
estado_revision: "generado"
---

# Casa Ingreso Form Data

Endpoint backend: datos para el formulario de ingreso de una actividad (`casa_ingreso_form`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/casas/casa_ingreso_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/casas/infrastructure/ui/http/controllers/casa_ingreso_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`

## Casos De Uso

- `src\casas\application\CasaIngresoFormData`

## Frontend Relacionado

- `frontend/casas/controller/casa_ingreso_form.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.