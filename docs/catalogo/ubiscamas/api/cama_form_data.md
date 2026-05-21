---
id: "ubiscamas.cama_form_data"
tipo: "endpoint"
modulo: "ubiscamas"
url: "/src/ubiscamas/cama_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/ubiscamas/infrastructure/ui/http/controllers/cama_form_data.php"
entrada: ["post.id_cama:string", "post.id_habitacion:mixed", "post.id_ubi:integer", "post.mod:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubiscamas/controller/cama_form.php"]
casos_uso: ["src\\ubiscamas\\application\\CamaFormData"]
tags: ["ubiscamas", "cama", "form", "data"]
estado_revision: "generado"
---

# Cama Form Data

Datos para `frontend/ubiscamas/controller/cama_form.php`. La composición de `HashFront` ocurre en {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubiscamas/cama_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/cama_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_cama` | `string` | application | No | application |
| `id_habitacion` | `mixed` | application | No | application |
| `id_ubi` | `integer` | application | No | application |
| `mod` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

Nota: el controller tambien lee `$_GET` directamente.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`

## Casos De Uso

- `src\ubiscamas\application\CamaFormData`

## Frontend Relacionado

- `frontend/ubiscamas/controller/cama_form.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.