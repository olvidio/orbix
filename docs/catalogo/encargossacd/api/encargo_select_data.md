---
id: "encargossacd.encargo_select_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/encargo_select_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/encargossacd/infrastructure/ui/http/controllers/encargo_select_data.php"
entrada: ["post.desc_enc:mixed", "post.id_tipo_enc:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_EncargoSelectDataData"
respuesta_data: ["filas:list<array{", "id_enc:integer", "sf_sv:integer", "idioma_enc:string", "id_ubi:integer", "desc_enc:string", "desc_lugar:string", "seccion:string", "nombre_ubi:string", "idioma:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/encargo_select.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoSelectData"]
tags: ["encargossacd", "encargo", "select", "data"]
estado_revision: "revisado"
---
# Encargo Select Data

Datos para la lista de encargos (`encargo_select`). El frontend construye la `frontend\shared\web\Lista` y los enlaces; aqui devolvemos unicamente los datos planos de cada fila.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Filas planas del listado de encargos (`desc_enc`, `id_tipo_enc`). El frontend construye la `Lista` y enlaces. Sucesor del listado en `encargo_select.php`.

## Endpoint

- URL: `/src/encargossacd/encargo_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/encargo_select_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `desc_enc` | `mixed` | controller | No | controller |
| `id_tipo_enc` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`.
- Claves: `filas[]` con `id_enc`, `sf_sv`, `desc_enc`, `desc_lugar`, `seccion`, `nombre_ubi`, `idioma` (doble `JSON.parse`).


## Permisos

Sin control propio; frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\encargossacd\application\EncargoSelectData`

## Frontend Relacionado

- `frontend/encargossacd/controller/encargo_select.php`

