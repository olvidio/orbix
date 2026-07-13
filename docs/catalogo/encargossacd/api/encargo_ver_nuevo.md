---
id: "encargossacd.encargo_ver_nuevo"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/encargo_ver_nuevo"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/encargo_ver_nuevo.php"
entrada: ["post.desc_enc:string", "post.desc_lugar:string", "post.filtro_ctr:integer", "post.grupo:string", "post.id_tipo_enc:string", "post.id_zona:integer", "post.idioma_enc:string", "post.lst_ctrs:integer", "post.nom_tipo:string", "post.observ:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_EncargoVerNuevoData"
respuesta_data: ["error:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/encargo_ver.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoVerNuevo"]
errores: ["Debe seleccionar un tipo de encargo", "Debe llenar el campo descripción", "grupo de encargo no valido", "hay un error, no se ha guardado"]
tags: ["encargossacd", "encargo", "ver", "nuevo"]
estado_revision: "revisado"
---
# Encargo Ver Nuevo

Alta de encargo desde el formulario de `encargo_ver` (antes `encargo_ajax.php` que=nuevo).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Alta de encargo desde `encargo_ver`. Sucesor de `encargo_ajax.php` con `que=nuevo`.

## Endpoint

- URL: `/src/encargossacd/encargo_ver_nuevo`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/encargo_ver_nuevo.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `desc_enc` | `string` | application | No | application |
| `desc_lugar` | `string` | application | No | application |
| `filtro_ctr` | `integer` | application | No | application |
| `grupo` | `string` | application | No | application |
| `id_tipo_enc` | `string` | application | No | application |
| `id_zona` | `integer` | application | No | application |
| `idioma_enc` | `string` | application | No | application |
| `lst_ctrs` | `integer` | application | No | application |
| `nom_tipo` | `string` | application | No | application |
| `observ` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`.
- Éxito: `data: ""`.
- Error: mensaje en `data`.


## Errores conocidos

- `Debe seleccionar un tipo de encargo`
- `Debe llenar el campo descripción`
- `grupo de encargo no valido`
- `hay un error, no se ha guardado`

## Permisos

Sin control propio; frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\encargossacd\application\EncargoVerNuevo`

## Frontend Relacionado

- `frontend/encargossacd/controller/encargo_ver.php`

