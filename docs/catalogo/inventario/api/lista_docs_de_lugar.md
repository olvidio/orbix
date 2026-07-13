---
id: "inventario.lista_docs_de_lugar"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/lista_docs_de_lugar"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/inventario/infrastructure/ui/http/controllers/lista_docs_de_lugar.php"
entrada: ["post.id_lugar:integer"]
entrada_obligatoria: ["id_lugar"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/equipajes_lista_docs.php", "frontend/inventario/controller/equipajes_ver_docs.php"]
casos_uso: []
tags: ["inventario", "lista", "docs", "de", "lugar"]
estado_revision: "revisado"
---

# Documentos por lugar

Documentos en un lugar concreto (`id_lugar`) para equipajes.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Documentos en un lugar concreto (`id_lugar`) para equipajes.

## Endpoint

- URL: `/src/inventario/lista_docs_de_lugar`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_docs_de_lugar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_lugar` | `integer` | POST | Si | |


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
- `frontend/inventario/controller/equipajes_ver_docs.php`
