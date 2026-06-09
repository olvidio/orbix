---
id: "ubis.direcciones_asignar"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/direcciones_asignar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/direcciones_asignar.php"
entrada: ["post.id_direccion:integer", "post.id_ubi:integer", "post.obj_dir:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_DireccionesAsignarData"
respuesta_data: ["ok:boolean"]
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/direcciones_asignar.php"]
casos_uso: ["src\\ubis\\application\\DireccionesAsignar"]
tags: ["ubis", "direcciones", "asignar"]
estado_revision: "generado"
---

# Direcciones Asignar

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/direcciones_asignar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/direcciones_asignar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_direccion` | `integer` | controller | No | controller |
| `id_ubi` | `integer` | controller | No | controller |
| `obj_dir` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `ubis_DireccionesAsignarData`):
  - `ok` (`boolean`)

## Casos De Uso

- `src\ubis\application\DireccionesAsignar`

## Frontend Relacionado

- `frontend/ubis/controller/direcciones_asignar.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.