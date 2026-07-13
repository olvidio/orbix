---
id: "encargossacd.encargo_ver_editar"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/encargo_ver_editar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/encargo_ver_editar.php"
entrada: ["post.desc_enc:string", "post.desc_lugar:string", "post.filtro_ctr:integer", "post.id_enc:integer", "post.id_tipo_enc:string", "post.id_zona:integer", "post.idioma_enc:string", "post.lst_ctrs:integer", "post.observ:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_EncargoVerEditarData"
respuesta_data: ["error:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/encargo_ver.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoVerEditar"]
errores: ["Debe llenar el campo descripción", "No se encuentra el encargo %d", "hay un error, no se ha guardado"]
tags: ["encargossacd", "encargo", "ver", "editar"]
estado_revision: "revisado"
---
# Encargo Ver Editar

Actualización de encargo desde `encargo_ver` (antes `encargo_ajax.php` que=editar).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Actualiza un encargo existente desde `encargo_ver`. Sucesor de `encargo_ajax.php` con `que=editar`.

## Endpoint

- URL: `/src/encargossacd/encargo_ver_editar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/encargo_ver_editar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `desc_enc` | `string` | application | No | application |
| `desc_lugar` | `string` | application | No | application |
| `filtro_ctr` | `integer` | application | No | application |
| `id_enc` | `integer` | application | No | application |
| `id_tipo_enc` | `string` | application | No | application |
| `id_zona` | `integer` | application | No | application |
| `idioma_enc` | `string` | application | No | application |
| `lst_ctrs` | `integer` | application | No | application |
| `observ` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`.
- Éxito: `data: ""` (`error` vacío).
- Error: mensaje en `data`.


## Errores conocidos

- `Debe llenar el campo descripción`
- `No se encuentra el encargo %d`
- `hay un error, no se ha guardado`

## Permisos

Sin control propio; frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\encargossacd\application\EncargoVerEditar`

## Frontend Relacionado

- `frontend/encargossacd/controller/encargo_ver.php`

