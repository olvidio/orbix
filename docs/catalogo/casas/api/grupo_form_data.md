---
id: "casas.grupo_form_data"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/grupo_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/casas/infrastructure/ui/http/controllers/grupo_form_data.php"
entrada: ["post.id_item:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "casas_GrupoCasaFormDataData"
respuesta_data: ["es_nuevo:boolean", "id_item:string", "id_ubi_padre:integer", "id_ubi_hijo:integer", "opciones_casas:array"]
requiere_hashb: false
frontend_referencias: ["frontend/casas/controller/grupo_form.php"]
casos_uso: ["src\\casas\\application\\GrupoCasaFormData"]
tags: ["casas", "grupo", "form", "data"]
estado_revision: "generado"
---

# Grupo Form Data

Endpoint backend: datos del formulario `GrupoCasa` (nuevo/editar).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/casas/grupo_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/casas/infrastructure/ui/http/controllers/grupo_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `string` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload en `data` (schema `casas_GrupoCasaFormDataData`):
  - `es_nuevo` (`boolean`)
  - `id_item` (`string`)
  - `id_ubi_padre` (`integer`)
  - `id_ubi_hijo` (`integer`)
  - `opciones_casas` (`array`)

## Casos De Uso

- `src\casas\application\GrupoCasaFormData`

## Frontend Relacionado

- `frontend/casas/controller/grupo_form.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.