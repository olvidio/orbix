---
id: "inventario.inventario_ctr"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/inventario_ctr"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/inventario/infrastructure/ui/http/controllers/inventario_ctr.php"
entrada: ["post.sel:string"]
entrada_obligatoria: ["sel"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/doc_imprimir_ctr.php"]
casos_uso: []
tags: ["inventario", "ctr"]
estado_revision: "revisado"
---

# Inventario imprimible de centros

Genera tablas de documentos por centro (`sel` JSON array de `id_ubi`) para impresión de inventario de centros.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Genera tablas de documentos por centro (`sel` JSON array de `id_ubi`) para impresión de inventario de centros.

## Endpoint

- URL: `/src/inventario/inventario_ctr`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/inventario_ctr.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `string` | POST | Si | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload `{a_valores, a_llave, a_tipo, a_lugar, a_nom_coleccion}` indexado por nombre de ubi.

## Errores conocidos

- Sin mensajes `_()` documentados en controller.

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/doc_imprimir_ctr.php`
