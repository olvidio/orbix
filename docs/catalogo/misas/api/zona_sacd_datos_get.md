---
id: "misas.zona_sacd_datos_get"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/zona_sacd_datos_get"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/zona_sacd_datos_get.php"
entrada: ["post.id_sacd:integer", "post.id_zona:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_ZonaSacdDatosGetData"
respuesta_data: ["error:string, payload: array<string, mixed>"]
requiere_hashb: false
frontend_referencias: ["frontend/zonassacd/controller/zona_sacd.php"]
casos_uso: ["src\\misas\\application\\ZonaSacdDatosGet"]
tags: ["misas", "zona", "sacd", "datos", "get"]
estado_revision: "generado"
---

# Zona Sacd Datos Get

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/zona_sacd_datos_get`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/zona_sacd_datos_get.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_sacd` | `integer` | controller | No | controller |
| `id_zona` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `misas_ZonaSacdDatosGetData`):
  - `error` (`string, payload: array<string, mixed>`)

## Casos De Uso

- `src\misas\application\ZonaSacdDatosGet`

## Frontend Relacionado

- `frontend/zonassacd/controller/zona_sacd.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.