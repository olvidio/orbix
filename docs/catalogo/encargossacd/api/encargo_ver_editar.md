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
tags: ["encargossacd", "encargo", "ver", "editar"]
estado_revision: "generado"
---

# Encargo Ver Editar

Actualización de encargo desde `encargo_ver` (antes `encargo_ajax.php` que=editar).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

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

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `encargossacd_EncargoVerEditarData`):
  - `error` (`string`)

## Casos De Uso

- `src\encargossacd\application\EncargoVerEditar`

## Frontend Relacionado

- `frontend/encargossacd/controller/encargo_ver.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.