---
id: "inventario.lista_docs_de_ctr"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/lista_docs_de_ctr"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/inventario/infrastructure/ui/http/controllers/lista_docs_de_ctr.php"
entrada: ["post.id_ubi:integer", "post.id_lugar:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/traslado_doc_lista.php"]
casos_uso: []
tags: ["inventario", "lista", "docs", "de", "ctr"]
estado_revision: "revisado"
---

# Documentos de un centro/lugar

Documentos filtrados por `id_ubi` y/o `id_lugar` para traslado o consulta.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Documentos filtrados por `id_ubi` y/o `id_lugar` para traslado o consulta.

## Endpoint

- URL: `/src/inventario/lista_docs_de_ctr`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_docs_de_ctr.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | POST | No | |
| `id_lugar` | `integer` | POST | No | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload `{a_valores}` con `sel` = id_doc por fila.

## Errores conocidos

- Sin mensajes `_()` documentados en controller.

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/traslado_doc_lista.php`
