---
id: "ubis.ubis_editar_load_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/ubis_editar_load_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/ubis_editar_load_data.php"
entrada: ["post.dl:string", "post.id_ubi:integer", "post.nombre_ubi:string", "post.nuevo:string", "post.obj_pau:string", "post.region:string", "post.tipo_ubi:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/ubis_editar.php"]
casos_uso: ["src\\ubis\\application\\UbisEditarLoadData"]
tags: ["ubis", "editar", "load", "data"]
estado_revision: "generado"
---

# Ubis Editar Load Data

Carga ficha ubis (centro/casa) para `frontend/ubis/controller/ubis_editar.php` sin repositorios en el frontend.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/ubis_editar_load_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/ubis_editar_load_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl` | `string` | application | No | application |
| `id_ubi` | `integer` | application | No | application |
| `nombre_ubi` | `string` | application | No | application |
| `nuevo` | `string` | application | No | application |
| `obj_pau` | `string` | application | No | application |
| `region` | `string` | application | No | application |
| `tipo_ubi` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Permisos

- Permiso oficina `scdl`

## Casos De Uso

- `src\ubis\application\UbisEditarLoadData`

## Frontend Relacionado

- `frontend/ubis/controller/ubis_editar.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.