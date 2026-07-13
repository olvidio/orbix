---
id: "inventario.lista_docs_con_observaciones"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/lista_docs_con_observaciones"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/inventario/infrastructure/ui/http/controllers/lista_docs_con_observaciones.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/docs_con_observaciones.php"]
casos_uso: []
tags: ["inventario", "lista", "docs", "con", "observaciones"]
estado_revision: "revisado"
---

# Documentos con observaciones

Listado de documentos que tienen texto en observaciones (centro o general).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Listado de documentos que tienen texto en observaciones (centro o general).

## Endpoint

- URL: `/src/inventario/lista_docs_con_observaciones`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_docs_con_observaciones.php`

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

- `frontend/inventario/controller/docs_con_observaciones.php`
