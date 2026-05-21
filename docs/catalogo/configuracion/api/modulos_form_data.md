---
id: "configuracion.modulos_form_data"
tipo: "endpoint"
modulo: "configuracion"
url: "/src/configuracion/modulos_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/configuracion/infrastructure/ui/http/controllers/modulos_form_data.php"
entrada: ["post.id_mod:integer", "post.mod:string", "post.sel:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/configuracion/controller/modulos_form.php"]
casos_uso: ["src\\configuracion\\application\\ModulosFormData"]
tags: ["configuracion", "modulos", "form", "data"]
estado_revision: "generado"
---

# Modulos Form Data

JSON para {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/configuracion/modulos_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/configuracion/infrastructure/ui/http/controllers/modulos_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_mod` | `integer` | application | No | application |
| `mod` | `string` | application | No | application |
| `sel` | `mixed` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`

## Casos De Uso

- `src\configuracion\application\ModulosFormData`

## Frontend Relacionado

- `frontend/configuracion/controller/modulos_form.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.