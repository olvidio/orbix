---
id: "ubis.direcciones_tabla"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/direcciones_tabla"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/direcciones_tabla.php"
entrada: ["post.c_p:string", "post.ciudad:string", "post.id_ubi:integer", "post.obj_dir:string", "post.pais:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/direcciones_tabla.php"]
casos_uso: ["src\\ubis\\application\\DireccionesTablaData"]
tags: ["ubis", "direcciones", "tabla"]
estado_revision: "generado"
---

# Direcciones Tabla

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/direcciones_tabla`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/direcciones_tabla.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `c_p` | `string` | controller | No | controller |
| `ciudad` | `string` | controller | No | controller |
| `id_ubi` | `integer` | controller | No | controller |
| `obj_dir` | `string` | controller | No | controller |
| `pais` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\ubis\application\DireccionesTablaData`

## Frontend Relacionado

- `frontend/ubis/controller/direcciones_tabla.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.