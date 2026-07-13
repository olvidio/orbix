---
id: "inventario.lista_docs_asignar_dlb"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/lista_docs_asignar_dlb"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/inventario/infrastructure/ui/http/controllers/lista_docs_asignar_dlb.php"
entrada: ["post.id_tipo_doc:integer", "post.sel:array"]
entrada_obligatoria: ["id_tipo_doc"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/doc_asignar_dlb.php"]
casos_uso: []
tags: ["inventario", "lista", "docs", "asignar", "dlb"]
estado_revision: "revisado"
---

# Formulario asignación DLB

Igual que `lista_docs_asignar_ctr` para inventario DLB/casa.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Igual que `lista_docs_asignar_ctr` para inventario DLB/casa.

## Endpoint

- URL: `/src/inventario/lista_docs_asignar_dlb`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_docs_asignar_dlb.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_doc` | `integer` | POST | Si | |
| `sel` | `array` | POST | No | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload `{a_valores, nombreDoc, isNumerado, sCamposForm}`.

## Errores conocidos

- Sin mensajes `_()` documentados en controller.

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/doc_asignar_dlb.php`
