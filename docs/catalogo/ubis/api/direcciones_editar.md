---
id: "ubis.direcciones_editar"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/direcciones_editar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/direcciones_editar.php"
entrada: ["post.id_direccion:string", "post.id_ubi:integer", "post.idx:integer", "post.inc:string", "post.mod:string", "post.obj_dir:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/direcciones_editar.php"]
casos_uso: ["src\\ubis\\application\\DireccionesEditarData"]
tags: ["ubis", "direcciones", "editar"]
estado_revision: "generado"
---

# Direcciones Editar

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/direcciones_editar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/direcciones_editar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_direccion` | `string` | controller | No | controller |
| `id_ubi` | `integer` | controller | No | controller |
| `idx` | `integer` | controller | No | controller |
| `inc` | `string` | controller | No | controller |
| `mod` | `string` | controller | No | controller |
| `obj_dir` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\ubis\application\DireccionesEditarData`

## Frontend Relacionado

- `frontend/ubis/controller/direcciones_editar.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.