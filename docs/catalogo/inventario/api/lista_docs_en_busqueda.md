---
id: "inventario.lista_docs_en_busqueda"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/lista_docs_en_busqueda"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/inventario/infrastructure/ui/http/controllers/lista_docs_en_busqueda.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/docs_en_busqueda.php"]
casos_uso: []
tags: ["inventario", "lista", "docs", "en", "busqueda"]
estado_revision: "revisado"
---

# Documentos pendientes de búsqueda

Listado de documentos marcados en estado de búsqueda/pendientes.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Listado de documentos marcados en estado de búsqueda/pendientes.

## Endpoint

- URL: `/src/inventario/lista_docs_en_busqueda`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_docs_en_busqueda.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| *(ninguno)* | — | — | — | Sin parámetros en controller |


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

- `frontend/inventario/controller/docs_en_busqueda.php`
