---
id: "ubis.ubis_editar_load_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/ubis_editar_load_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/ubis/infrastructure/ui/http/controllers/ubis_editar_load_data.php"
entrada: ["post.id_ubi:integer", "post.obj_pau:string", "post.nuevo:string", "post.tipo_ubi:string", "post.dl:string", "post.region:string", "post.nombre_ubi:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/ubis_editar.php"]
casos_uso: ["src\\ubis\\application\\UbisEditarLoadData"]
tags: ["ubis", "editar", "load", "data"]
estado_revision: "revisado"
errores: ["falta definir obj_pau", "No se encuentra ubi id %s", "tipo de entidad inesperado para centro dl", "tipo de entidad inesperado para centro ex", "tipo de entidad inesperado para casa"]
---

# Ubis Editar Load Data

Carga la ficha completa de un ubi para edición o alta, normalizando obj_pau de delegación.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Carga la ficha completa de un ubi para edición o alta, normalizando obj_pau de delegación.

## Endpoint

- URL: `/src/ubis/ubis_editar_load_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/ubis_editar_load_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | application | No | |
| `obj_pau` | `string` | application | No | |
| `nuevo` | `string` | application | No | |
| `tipo_ubi` | `string` | application | No | |
| `dl` | `string` | application | No | |
| `region` | `string` | application | No | |
| `nombre_ubi` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `tipo_ubi`: tipo
  - `obj_pau`: objeto pau normalizado
  - `id_ubi`: id
  - `es_de_dl`: boolean delegación propia
  - `botones`: 1,2 o 0
  - `campos ficha`: según tipo ctrdl/ctrex/cdcdl/cdcex

## Errores conocidos
- `falta definir obj_pau`
- `No se encuentra ubi id %s`
- `tipo de entidad inesperado para centro dl`
- `tipo de entidad inesperado para centro ex`
- `tipo de entidad inesperado para casa`

## Permisos

UbiPermisos.puedeModificarPorObjeto: botones. UbiPermisos.dlPerteneceAMiDelegacion: reclasifica Ex vs Dl.

## Casos De Uso

- `src\ubis\application\UbisEditarLoadData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/ubis_editar.php"]`).
