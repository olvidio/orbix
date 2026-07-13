---
id: "inventario.doc_asignar_ctr_guardar"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/doc_asignar_ctr_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/inventario/infrastructure/ui/http/controllers/doc_asignar_ctr_guardar.php"
entrada: ["post.id_tipo_doc:string", "post.numerado:string", "post.str_selected_id:string", "post.f_recibido:string", "post.f_asignado:string", "post.num_{id_ubi}:integer"]
entrada_obligatoria: ["id_tipo_doc"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["hay un error, no se ha guardado"]
frontend_referencias: ["frontend/inventario/controller/doc_asignar_ctr.php"]
casos_uso: []
tags: ["inventario", "doc", "asignar", "ctr", "guardar"]
estado_revision: "revisado"
---

# Asignar documento a centros

Crea o actualiza documentos de un tipo en los centros seleccionados (`str_selected_id` JSON). Si el tipo es numerado guarda `num_reg`; si no, `num_ejemplares`. Opcionalmente fija fechas de recepción/asignación.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Crea o actualiza documentos de un tipo en los centros seleccionados (`str_selected_id` JSON). Si el tipo es numerado guarda `num_reg`; si no, `num_ejemplares`. Opcionalmente fija fechas de recepción/asignación.

## Endpoint

- URL: `/src/inventario/doc_asignar_ctr_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/inventario/infrastructure/ui/http/controllers/doc_asignar_ctr_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_doc` | `string` | POST | Si | |
| `numerado` | `string` | POST | No | |
| `str_selected_id` | `string` | POST | No | |
| `f_recibido` | `string` | POST | No | |
| `f_asignado` | `string` | POST | No | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Éxito: `data: "ok"`.

## Errores conocidos

  - `hay un error, no se ha guardado`

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/doc_asignar_ctr.php`
