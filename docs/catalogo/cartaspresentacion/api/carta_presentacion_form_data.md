---
id: "cartaspresentacion.carta_presentacion_form_data"
tipo: "endpoint"
modulo: "cartaspresentacion"
url: "/src/cartaspresentacion/carta_presentacion_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/cartaspresentacion/infrastructure/ui/http/controllers/carta_presentacion_form_data.php"
entrada: ["post.id_direccion:integer", "post.id_ubi:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "cartaspresentacion_CartaPresentacionFormDataData"
respuesta_data: ["ok:boolean", "mensaje:string", "id_ubi:integer", "id_direccion:integer", "nombre_ubi:string", "pres_nom:string", "pres_telf:string", "pres_mail:string", "zona:string", "observ:string"]
requiere_hashb: false
frontend_referencias: ["frontend/cartaspresentacion/controller/cartas_presentacion_form.php"]
casos_uso: ["src\\cartaspresentacion\\application\\CartaPresentacionFormData"]
tags: ["cartaspresentacion", "carta", "presentacion", "form", "data"]
estado_revision: "generado"
---

# Carta Presentacion Form Data

Endpoint backend: datos del formulario de modificacion de una `CartaPresentacion` (valida permisos: solo dl propia o `cr`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/cartaspresentacion/carta_presentacion_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/cartaspresentacion/infrastructure/ui/http/controllers/carta_presentacion_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_direccion` | `integer` | controller+application | No | controller+application |
| `id_ubi` | `integer` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload en `data` (schema `cartaspresentacion_CartaPresentacionFormDataData`):
  - `ok` (`boolean`)
  - `mensaje` (`string`)
  - `id_ubi` (`integer`)
  - `id_direccion` (`integer`)
  - `nombre_ubi` (`string`)
  - `pres_nom` (`string`)
  - `pres_telf` (`string`)
  - `pres_mail` (`string`)
  - `zona` (`string`)
  - `observ` (`string`)

## Casos De Uso

- `src\cartaspresentacion\application\CartaPresentacionFormData`

## Frontend Relacionado

- `frontend/cartaspresentacion/controller/cartas_presentacion_form.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.