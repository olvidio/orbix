---
id: "ubis.direccion_update"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/direccion_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/direccion_update.php"
entrada: ["post.obj_dir:string", "post.id_ubi:integer", "post.idx:string", "post.id_direccion:string", "post.nom_sede:string", "post.direccion:string", "post.a_p:string", "post.c_p:string", "post.poblacion:string", "post.provincia:string", "post.pais:string", "post.observ:string", "post.f_direccion:string", "post.latitud:string", "post.longitud:string", "post.cp_dcha:string", "post.propietario:string", "post.principal:string"]
entrada_obligatoria: ["obj_dir", "id_ubi"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no se encuentra el ubi", "operación no soportada para este tipo de dirección", "no se encuentra la dirección"]
frontend_referencias: ["frontend/ubis/controller/direccion_update.php"]
casos_uso: ["src\\ubis\\application\\DireccionUpdate"]
tags: ["ubis", "direccion", "update"]
estado_revision: "revisado"
---

# Direccion Update

Crea o modifica una dirección y su relación con el ubi (principal, propietario).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Crea o modifica una dirección y su relación con el ubi (principal, propietario).

## Endpoint

- URL: `/src/ubis/direccion_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/direccion_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `obj_dir` | `string` | application | Si | |
| `id_ubi` | `integer` | application | Si | |
| `idx` | `string` | application | No | |
| `id_direccion` | `string` | application | No | |
| `nom_sede` | `string` | application | No | |
| `direccion` | `string` | application | No | |
| `a_p` | `string` | application | No | |
| `c_p` | `string` | application | No | |
| `poblacion` | `string` | application | No | |
| `provincia` | `string` | application | No | |
| `pais` | `string` | application | No | |
| `observ` | `string` | application | No | |
| `f_direccion` | `string` | application | No | |
| `latitud` | `string` | application | No | |
| `longitud` | `string` | application | No | |
| `cp_dcha` | `string` | application | No | |
| `propietario` | `string` | application | No | |
| `principal` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: payload en `data`:
  - `ok`: 1

## Errores conocidos
- `no se encuentra el ubi`
- `operación no soportada para este tipo de dirección`
- `no se encuentra la dirección`

## Permisos

UbiPermisos: frontend oculta botones si no puedeModificarPorObjeto.

## Casos De Uso

- `src\ubis\application\DireccionUpdate`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/direccion_update.php"]`).
