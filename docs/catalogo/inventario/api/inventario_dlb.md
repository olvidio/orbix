---
id: "inventario.inventario_dlb"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/inventario_dlb"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/inventario/infrastructure/ui/http/controllers/inventario_dlb.php"
entrada: ["post.sel:string"]
entrada_obligatoria: ["sel"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/doc_imprimir_dlb.php"]
casos_uso: []
tags: ["inventario", "dlb"]
estado_revision: "revisado"
---

# Inventario imprimible DLB/casa

Igual que `inventario_ctr` pero para documentos DLB/casa (sin ubi de centro).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Igual que `inventario_ctr` pero para documentos DLB/casa (sin ubi de centro).

## Endpoint

- URL: `/src/inventario/inventario_dlb`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/inventario_dlb.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `string` | POST | Si | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload `{a_valores, a_llave, a_tipo, a_lugar, a_nom_coleccion}`.

## Errores conocidos

- Sin mensajes `_()` documentados en controller.

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/doc_imprimir_dlb.php`
