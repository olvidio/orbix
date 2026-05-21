---
id: "ubis.ubis_eliminar"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/ubis_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/ubis_eliminar.php"
entrada: ["post.id_ubi:integer", "post.obj_pau:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no se encuentra el ubi a borrar", "hay un error, no se ha eliminado"]
frontend_referencias: ["frontend/ubis/controller/ubis_eliminar.php"]
casos_uso: ["src\\ubis\\application\\UbisEliminar"]
tags: ["ubis", "eliminar"]
estado_revision: "generado"
---

# Ubis Eliminar

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/ubis_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/ubis_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | controller | No | controller |
| `obj_pau` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `no se encuentra el ubi a borrar`
- `hay un error, no se ha eliminado`

## Casos De Uso

- `src\ubis\application\UbisEliminar`

## Frontend Relacionado

- `frontend/ubis/controller/ubis_eliminar.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.