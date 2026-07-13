---
id: "ubis.list_ctr_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/list_ctr_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/ubis/infrastructure/ui/http/controllers/list_ctr_data.php"
entrada: ["post.loc:string", "post.que_lista:string", "post.id_sel:string", "post.scroll_id:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_ListCtrDataData"
respuesta_data: ["opciones_loc:array", "opciones_que_lista:array", "a_cabeceras:list<mixed>", "a_valores:array", "a_botones:list<array{txt: string, click: string}>"]
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/list_ctr.php"]
casos_uso: ["src\\ubis\\application\\ListCtrData"]
tags: ["ubis", "list", "ctr", "data"]
estado_revision: "revisado"
errores: []
---

# List Ctr Data

Lista centros y casas filtrados por delegación/exterior y tipo, con teléfonos y enlaces a ficha.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lista centros y casas filtrados por delegación/exterior y tipo, con teléfonos y enlaces a ficha.

## Endpoint

- URL: `/src/ubis/list_ctr_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/list_ctr_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `loc` | `string` | application | No | |
| `que_lista` | `string` | application | No | |
| `id_sel` | `string` | application | No | |
| `scroll_id` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `opciones_loc`: desplegable ubicación
  - `opciones_que_lista`: filtros tipo lista
  - `a_cabeceras`: cabeceras tabla
  - `a_valores`: filas centros/casas
  - `a_botones`: modificar y opcional trasladar

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

have_perm_oficina(vcsd|des): filtro sv en casas. have_perm_oficina(admin_sv): botón trasladar.

## Casos De Uso

- `src\ubis\application\ListCtrData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/list_ctr.php"]`).
