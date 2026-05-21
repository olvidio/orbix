---
id: "ubis.direccion_update"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/direccion_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/direccion_update.php"
entrada: ["post.a_p:string", "post.c_p:string", "post.cp_dcha:string", "post.direccion:string", "post.f_direccion:string", "post.id_direccion:string", "post.id_ubi:integer", "post.idx:string", "post.latitud:string", "post.longitud:string", "post.nom_sede:string", "post.obj_dir:string", "post.observ:string", "post.pais:string", "post.poblacion:string", "post.principal:string", "post.propietario:string", "post.provincia:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no se encuentra el ubi", "no se encuentra la dirección"]
frontend_referencias: ["frontend/ubis/controller/direccion_update.php"]
casos_uso: ["src\\ubis\\application\\DireccionUpdate"]
tags: ["ubis", "direccion", "update"]
estado_revision: "generado"
---

# Direccion Update

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/direccion_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/direccion_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `a_p` | `string` | application | No | application |
| `c_p` | `string` | application | No | application |
| `cp_dcha` | `string` | application | No | application |
| `direccion` | `string` | application | No | application |
| `f_direccion` | `string` | application | No | application |
| `id_direccion` | `string` | application | No | application |
| `id_ubi` | `integer` | application | No | application |
| `idx` | `string` | application | No | application |
| `latitud` | `string` | application | No | application |
| `longitud` | `string` | application | No | application |
| `nom_sede` | `string` | application | No | application |
| `obj_dir` | `string` | application | No | application |
| `observ` | `string` | application | No | application |
| `pais` | `string` | application | No | application |
| `poblacion` | `string` | application | No | application |
| `principal` | `string` | application | No | application |
| `propietario` | `string` | application | No | application |
| `provincia` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `no se encuentra el ubi`
- `no se encuentra la dirección`

## Casos De Uso

- `src\ubis\application\DireccionUpdate`

## Frontend Relacionado

- `frontend/ubis/controller/direccion_update.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.