---
id: "inventario.lista_docs_libres"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/lista_docs_libres"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/inventario/infrastructure/ui/http/controllers/lista_docs_libres.php"
entrada: ["post.id_equipaje:integer", "post.id_tipo_doc:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/equipajes_docs_libres.php"]
casos_uso: []
tags: ["inventario", "lista", "docs", "libres"]
estado_revision: "revisado"
---

# Documentos libres para equipaje

Documentos no asignados a maletas, filtrables por equipaje y tipo doc.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Documentos no asignados a maletas, filtrables por equipaje y tipo doc.

## Endpoint

- URL: `/src/inventario/lista_docs_libres`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_docs_libres.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_equipaje` | `integer` | POST | No | |
| `id_tipo_doc` | `integer` | POST | No | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload `{a_valores}`.

## Errores conocidos

- Sin mensajes `_()` documentados en controller.

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/equipajes_docs_libres.php`
