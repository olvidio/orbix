---
id: "inventario.lista_docs_de_dlb"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/lista_docs_de_dlb"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/inventario/infrastructure/ui/http/controllers/lista_docs_de_dlb.php"
entrada: ["post.id_tipo_doc:integer", "post.inventario:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/doc_de_dlb.php"]
casos_uso: []
tags: ["inventario", "lista", "docs", "de", "dlb"]
estado_revision: "revisado"
---

# Documentos DLB de un tipo

Lista documentos DLB/casa de un tipo para inventario o impresión.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lista documentos DLB/casa de un tipo para inventario o impresión.

## Endpoint

- URL: `/src/inventario/lista_docs_de_dlb`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_docs_de_dlb.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_doc` | `integer` | POST | No | |
| `inventario` | `integer` | POST | No | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload `{a_valores, aGrupos, nombreDoc}`.

## Errores conocidos

- Sin mensajes `_()` documentados en controller.

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/doc_de_dlb.php`
