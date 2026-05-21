---
id: "ubis.direcciones_quitar"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/direcciones_quitar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/direcciones_quitar.php"
entrada: ["post.id_direccion:string", "post.id_ubi:integer", "post.idx:integer", "post.obj_dir:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/direcciones_quitar.php"]
casos_uso: ["src\\ubis\\application\\DireccionesQuitar"]
tags: ["ubis", "direcciones", "quitar"]
estado_revision: "generado"
---

# Direcciones Quitar

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/direcciones_quitar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/direcciones_quitar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_direccion` | `string` | controller | No | controller |
| `id_ubi` | `integer` | controller | No | controller |
| `idx` | `integer` | controller | No | controller |
| `obj_dir` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\ubis\application\DireccionesQuitar`

## Frontend Relacionado

- `frontend/ubis/controller/direcciones_quitar.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.