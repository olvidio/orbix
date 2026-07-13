---
id: "inventario.lista_de_ctr_con_docs"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/lista_de_ctr_con_docs"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/inventario/infrastructure/ui/http/controllers/lista_de_ctr_con_docs.php"
entrada: ["post.id_tipo_doc:integer", "post.inventario:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/doc_de_ctr.php"]
casos_uso: []
tags: ["inventario", "lista", "de", "ctr", "con", "docs"]
estado_revision: "revisado"
---

# Centros con documentos de un tipo

Lista centros que tienen documentos del tipo (`id_tipo_doc`) para inventario o impresión CTR.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lista centros que tienen documentos del tipo (`id_tipo_doc`) para inventario o impresión CTR.

## Endpoint

- URL: `/src/inventario/lista_de_ctr_con_docs`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_de_ctr_con_docs.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_doc` | `integer` | POST | No | |
| `inventario` | `integer` | POST | No | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload `{a_valores, nombreDoc}` o `[]`.

## Errores conocidos

- Sin mensajes `_()` documentados en controller.

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/doc_de_ctr.php`
