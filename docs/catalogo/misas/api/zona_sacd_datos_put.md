---
id: "misas.zona_sacd_datos_put"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/zona_sacd_datos_put"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/zona_sacd_datos_put.php"
entrada: ["post.dw1:string", "post.dw2:string", "post.dw3:string", "post.dw4:string", "post.dw5:string", "post.dw6:string", "post.dw7:string", "post.id_sacd:integer", "post.id_zona:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_ZonaSacdDatosPutData"
respuesta_data: ["error:string"]
requiere_hashb: false
frontend_referencias: ["frontend/zonassacd/controller/zona_sacd.php"]
casos_uso: ["src\\misas\\application\\ZonaSacdDatosPut"]
tags: ["misas", "zona", "sacd", "datos", "put"]
estado_revision: "generado"
---

# Zona Sacd Datos Put

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/zona_sacd_datos_put`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/zona_sacd_datos_put.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dw1` | `string` | controller | No | controller |
| `dw2` | `string` | controller | No | controller |
| `dw3` | `string` | controller | No | controller |
| `dw4` | `string` | controller | No | controller |
| `dw5` | `string` | controller | No | controller |
| `dw6` | `string` | controller | No | controller |
| `dw7` | `string` | controller | No | controller |
| `id_sacd` | `integer` | controller | No | controller |
| `id_zona` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `misas_ZonaSacdDatosPutData`):
  - `error` (`string`)

## Casos De Uso

- `src\misas\application\ZonaSacdDatosPut`

## Frontend Relacionado

- `frontend/zonassacd/controller/zona_sacd.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.