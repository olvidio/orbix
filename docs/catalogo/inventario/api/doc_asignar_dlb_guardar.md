---
id: "inventario.doc_asignar_dlb_guardar"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/doc_asignar_dlb_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/inventario/infrastructure/ui/http/controllers/doc_asignar_dlb_guardar.php"
entrada: ["post.id_tipo_doc:string", "post.numerado:string", "post.str_selected_id:string", "post.f_recibido:string", "post.f_asignado:string", "post.num_{id_lugar}:integer"]
entrada_obligatoria: ["id_tipo_doc"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se encuentra el documento", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/inventario/controller/doc_asignar_dlb.php"]
casos_uso: []
tags: ["inventario", "doc", "asignar", "dlb", "guardar"]
estado_revision: "revisado"
---

# Asignar documento a DLB/casa

Actualiza documentos existentes por lugar (`str_selected_id` JSON de `id_lugar`). Campos dinámicos `num_{id_lugar}`. Requiere documento único por ubi+lugar+tipo.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Actualiza documentos existentes por lugar (`str_selected_id` JSON de `id_lugar`). Campos dinámicos `num_{id_lugar}`. Requiere documento único por ubi+lugar+tipo.

## Endpoint

- URL: `/src/inventario/doc_asignar_dlb_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/inventario/infrastructure/ui/http/controllers/doc_asignar_dlb_guardar.php`

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

  - `No se encuentra el documento`
  - `hay un error, no se ha guardado`

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/doc_asignar_dlb.php`
