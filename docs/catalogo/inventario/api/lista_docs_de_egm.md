---
id: "inventario.lista_docs_de_egm"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/lista_docs_de_egm"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/inventario/infrastructure/ui/http/controllers/lista_docs_de_egm.php"
entrada: ["post.id_item_egm:integer"]
entrada_obligatoria: ["id_item_egm"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/equipajes_lista_docs.php", "frontend/inventario/controller/equipajes_form_del.php"]
casos_uso: []
tags: ["inventario", "lista", "docs", "de", "egm"]
estado_revision: "revisado"
---

# Documentos de una maleta

Documentos asociados a un item EGM (`id_item_egm`) dentro de un equipaje.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Documentos asociados a un item EGM (`id_item_egm`) dentro de un equipaje.

## Endpoint

- URL: `/src/inventario/lista_docs_de_egm`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_docs_de_egm.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item_egm` | `integer` | POST | Si | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload `{a_valores, nombre_valija}`.

## Errores conocidos

- Sin mensajes `_()` documentados en controller.

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/equipajes_lista_docs.php`
- `frontend/inventario/controller/equipajes_form_del.php`
