---
id: "encargossacd.sacd_ficha_update"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/sacd_ficha_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/sacd_ficha_update.php"
entrada: ["post.dedic_m:mixed", "post.dedic_t:mixed", "post.dedic_v:mixed", "post.enc_num:integer", "post.id_enc:mixed", "post.id_nom:integer", "post.id_tipo_enc:mixed", "post.observ:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_SacdFichaUpdateData"
respuesta_data: ["error:string, mensajes: string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/sacd_ficha_ajax.php"]
casos_uso: ["src\\encargossacd\\application\\SacdFichaUpdate"]
errores: ["id_nom no valido", "Error con las tareas", "hay un error, no se ha eliminado", "hay un error, no se ha guardado"]
tags: ["encargossacd", "sacd", "ficha", "update"]
estado_revision: "revisado"
---
# Sacd Ficha Update

Mutacion de la ficha de encargos de un SACD (`sacd_ficha_ajax?que=update`). Porta la logica del antiguo controlador frontend, haciendo la misma actualizacion de dedicaciones por modulo y de observaciones.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Guarda observaciones y tareas/horarios de la ficha SACD. Sucesor de ramas update de `sacd_ficha_ajax.php`.

## Endpoint

- URL: `/src/encargossacd/sacd_ficha_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/sacd_ficha_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dedic_m` | `mixed` | application | No | application |
| `dedic_t` | `mixed` | application | No | application |
| `dedic_v` | `mixed` | application | No | application |
| `enc_num` | `integer` | application | No | application |
| `id_enc` | `mixed` | application | No | application |
| `id_nom` | `integer` | application | No | application |
| `id_tipo_enc` | `mixed` | application | No | application |
| `observ` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Éxito: `error` vacío.
- Error: `error` + `mensajes`.


## Errores conocidos

- `id_nom no valido`
- `Error con las tareas`
- `hay un error, no se ha eliminado`
- `hay un error, no se ha guardado`

## Permisos

Edición condicionada por `permiso` del payload de ficha.

## Casos De Uso

- `src\encargossacd\application\SacdFichaUpdate`

## Frontend Relacionado

- `frontend/encargossacd/controller/sacd_ficha_ajax.php`

