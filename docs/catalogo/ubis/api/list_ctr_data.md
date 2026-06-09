---
id: "ubis.list_ctr_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/list_ctr_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/list_ctr_data.php"
entrada: ["post.id_sel:string", "post.loc:string", "post.que_lista:string", "post.scroll_id:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_ListCtrDataData"
respuesta_data: ["opciones_loc:array", "opciones_que_lista:array", "a_cabeceras:list<mixed>", "a_valores:array", "a_botones:list<array{txt: string, click: string}>"]
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/list_ctr.php"]
casos_uso: ["src\\ubis\\application\\ListCtrData"]
tags: ["ubis", "list", "ctr", "data"]
estado_revision: "generado"
---

# List Ctr Data

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/list_ctr_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/list_ctr_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_sel` | `string` | controller | No | controller |
| `loc` | `string` | controller | No | controller |
| `que_lista` | `string` | controller | No | controller |
| `scroll_id` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `ubis_ListCtrDataData`):
  - `opciones_loc` (`array`)
  - `opciones_que_lista` (`array`)
  - `a_cabeceras` (`list<mixed>`)
  - `a_valores` (`array`)
  - `a_botones` (`list<array{txt: string, click: string}>`)

## Permisos

- Permiso oficina `vcsd`
- Permiso oficina `des`
- Permiso oficina `admin_sv`

## Casos De Uso

- `src\ubis\application\ListCtrData`

## Frontend Relacionado

- `frontend/ubis/controller/list_ctr.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.