---
id: "inventario.lista_docs_no_asignados_por_tipo"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/lista_docs_no_asignados_por_tipo"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/inventario/infrastructure/ui/http/controllers/lista_docs_no_asignados_por_tipo.php"
entrada: ["post.id_tipo_doc:integer"]
entrada_obligatoria: ["id_tipo_doc"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/doc_no_asignado.php"]
casos_uso: []
tags: ["inventario", "lista", "docs", "no", "asignados", "por", "tipo"]
estado_revision: "revisado"
---

# Centros sin documento de un tipo

Centros que aún no tienen asignado el tipo de documento indicado.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Centros que aún no tienen asignado el tipo de documento indicado.

## Endpoint

- URL: `/src/inventario/lista_docs_no_asignados_por_tipo`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_docs_no_asignados_por_tipo.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_doc` | `integer` | POST | Si | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload `{a_cabeceras, a_botones, a_valores, nombreDoc}`.

## Errores conocidos

- Sin mensajes `_()` documentados en controller.

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/doc_no_asignado.php`
