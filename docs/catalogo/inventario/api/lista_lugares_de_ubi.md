---
id: "inventario.lista_lugares_de_ubi"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/lista_lugares_de_ubi"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/inventario/infrastructure/ui/http/controllers/lista_lugares_de_ubi.php"
entrada: ["post.id_ubi:integer"]
entrada_obligatoria: ["id_ubi"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/traslado_doc_que.php"]
casos_uso: []
tags: ["inventario", "lista", "lugares", "de", "ubi"]
estado_revision: "revisado"
---

# Lugares de un centro

Desplegable AJAX de lugares (`id_lugar`, nombre) filtrados por `id_ubi`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Desplegable AJAX de lugares (`id_lugar`, nombre) filtrados por `id_ubi`.

## Endpoint

- URL: `/src/inventario/lista_lugares_de_ubi`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_lugares_de_ubi.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | POST | Si | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Array directo `[{value, text}, …]` en `data` (no objeto wrapper). Doble `JSON.parse`.

## Errores conocidos

- Sin mensajes `_()` documentados en controller.

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/traslado_doc_que.php`
