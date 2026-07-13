---
id: "inventario.equipajes_doc_casa"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/equipajes_doc_casa"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/inventario/infrastructure/ui/http/controllers/equipajes_doc_casa.php"
entrada: ["post.id_equipaje:integer"]
entrada_obligatoria: ["id_equipaje"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/equipajes_doc_casa.php", "frontend/inventario/controller/equipajes_imprimir.php"]
casos_uso: []
tags: ["inventario", "equipajes", "doc", "casa"]
estado_revision: "revisado"
---

# Documentos por casa en equipaje

Lista documentos agrupados por casa/centro para un equipaje (`id_equipaje`), usado en impresión.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lista documentos agrupados por casa/centro para un equipaje (`id_equipaje`), usado en impresión.

## Endpoint

- URL: `/src/inventario/equipajes_doc_casa`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_doc_casa.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_equipaje` | `integer` | POST | Si | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload `{a_valores, nombre_ubi, id_ubi}` o `[]` si no hay datos. Tabla con nombre compuesto por tipo doc.

## Errores conocidos

- Sin mensajes `_()` documentados en controller.

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/equipajes_doc_casa.php`
- `frontend/inventario/controller/equipajes_imprimir.php`
