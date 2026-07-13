---
id: "inventario.lista_equipajes_posibles_maletas"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/lista_equipajes_posibles_maletas"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/inventario/infrastructure/ui/http/controllers/lista_equipajes_posibles_maletas.php"
entrada: ["post.id_equipaje:integer"]
entrada_obligatoria: ["id_equipaje"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/equipajes_posibles_maletas.php"]
casos_uso: []
tags: ["inventario", "lista", "equipajes", "posibles", "maletas"]
estado_revision: "revisado"
---

# Maletas/grupos posibles

Grupos EGM existentes y opción «nuevo» para añadir documentos a equipaje.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Grupos EGM existentes y opción «nuevo» para añadir documentos a equipaje.

## Endpoint

- URL: `/src/inventario/lista_equipajes_posibles_maletas`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_equipajes_posibles_maletas.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_equipaje` | `integer` | POST | Si | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload `{a_opciones, new_id_grupo}`.

## Errores conocidos

- Sin mensajes `_()` documentados en controller.

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/equipajes_posibles_maletas.php`
