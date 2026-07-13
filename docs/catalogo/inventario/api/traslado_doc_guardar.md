---
id: "inventario.traslado_doc_guardar"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/traslado_doc_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/inventario/infrastructure/ui/http/controllers/traslado_doc_guardar.php"
entrada: ["post.sel:array", "post.id_ubi_new:integer", "post.id_lugar_new:integer"]
entrada_obligatoria: ["sel"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["hay un error, no se ha guardado"]
frontend_referencias: ["frontend/inventario/controller/traslado_doc_lista.php"]
casos_uso: []
tags: ["inventario", "traslado", "doc", "guardar"]
estado_revision: "revisado"
---

# Trasladar documentos

Mueve documentos seleccionados (`sel` array id_doc) a otro centro/lugar (`id_ubi_new`, `id_lugar_new`). Si `id_ubi_new` vacío, desasigna ubi.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Mueve documentos seleccionados (`sel` array id_doc) a otro centro/lugar (`id_ubi_new`, `id_lugar_new`). Si `id_ubi_new` vacío, desasigna ubi.

## Endpoint

- URL: `/src/inventario/traslado_doc_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/inventario/infrastructure/ui/http/controllers/traslado_doc_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | POST | Si | |
| `id_ubi_new` | `integer` | POST | No | |
| `id_lugar_new` | `integer` | POST | No | |


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

- `frontend/inventario/controller/traslado_doc_lista.php`
