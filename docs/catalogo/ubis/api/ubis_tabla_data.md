---
id: "ubis.ubis_tabla_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/ubis_tabla_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/ubis/infrastructure/ui/http/controllers/ubis_tabla_data.php"
entrada: ["post.loc:string", "post.tipo:string", "post.simple:integer", "post.sWhere:string", "post.sOperador:string", "post.sWhereD:string", "post.sOperadorD:string", "post.metodo:string", "post.titulo:string", "post.cmb:string", "post.obj_pau:string", "post.id_sel:string", "post.scroll_id:string", "post.nombre_ubi:string", "post.region:string", "post.dl:string", "post.tipo_ctr:string", "post.tipo_casa:string", "post.ciudad:string", "post.pais:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_UbisTablaDataData"
respuesta_data: ["0:string|list<string>, 1: string, 2: array<string, mixed>"]
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/ubis_tabla.php"]
casos_uso: ["src\\ubis\\application\\UbisTablaData"]
tags: ["ubis", "tabla", "data"]
estado_revision: "revisado"
errores: ["debe poner algún criterio de búsqueda"]
---

# Ubis Tabla Data

Busca ubis por nombre y/o dirección con filtros tipo/loc y construye tabla navegable.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Busca ubis por nombre y/o dirección con filtros tipo/loc y construye tabla navegable.

## Endpoint

- URL: `/src/ubis/ubis_tabla_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/ubis_tabla_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `loc` | `string` | application | No | |
| `tipo` | `string` | application | No | |
| `simple` | `integer` | application | No | |
| `sWhere` | `string` | application | No | |
| `sOperador` | `string` | application | No | |
| `sWhereD` | `string` | application | No | |
| `sOperadorD` | `string` | application | No | |
| `metodo` | `string` | application | No | |
| `titulo` | `string` | application | No | |
| `cmb` | `string` | application | No | |
| `obj_pau` | `mixed` | application | No | |
| `id_sel` | `string` | application | No | |
| `scroll_id` | `string` | application | No | |
| `nombre_ubi` | `string` | application | No | |
| `region` | `string` | application | No | |
| `dl` | `string` | application | No | |
| `tipo_ctr` | `string` | application | No | |
| `tipo_casa` | `string` | application | No | |
| `ciudad` | `string` | application | No | |
| `pais` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `titulo`: título tabla
  - `nueva_ficha`: nueva|aviso|especificar
  - `pagina_link_spec`: enlace alta
  - `a_cabeceras`: cabeceras
  - `a_valores`: filas con links
  - `a_botones`: modificar/eliminar
  - `go_back`: estado navegación
  - `hash_hidden`: filtros serializados

## Errores conocidos
- `debe poner algún criterio de búsqueda`

## Permisos

have_perm_oficina(scl): botón eliminar. have_perm_oficina(vcsd|des): tablas sf.

## Casos De Uso

- `src\ubis\application\UbisTablaData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/ubis_tabla.php"]`).
