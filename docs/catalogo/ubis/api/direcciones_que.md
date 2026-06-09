---
id: "ubis.direcciones_que"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/direcciones_que"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/direcciones_que.php"
entrada: ["post.id_ubi:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_DireccionesQueDataData"
respuesta_data: ["tipo_ubi:string|null, titulo: string"]
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/direcciones_que.php"]
casos_uso: ["src\\ubis\\application\\DireccionesQueData"]
tags: ["ubis", "direcciones", "que"]
estado_revision: "generado"
---

# Direcciones Que

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/direcciones_que`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/direcciones_que.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `ubis_DireccionesQueDataData`):
  - `tipo_ubi` (`string|null, titulo: string`)

## Casos De Uso

- `src\ubis\application\DireccionesQueData`

## Frontend Relacionado

- `frontend/ubis/controller/direcciones_que.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.