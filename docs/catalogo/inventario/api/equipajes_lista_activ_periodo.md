---
id: "inventario.equipajes_lista_activ_periodo"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/equipajes_lista_activ_periodo"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/inventario/infrastructure/ui/http/controllers/equipajes_lista_activ_periodo.php"
entrada: ["post.id_cdc:integer", "post.periodo:string", "post.year:integer", "post.empiezamin:string", "post.empiezamax:string", "post.inicio:string", "post.fin:string"]
entrada_obligatoria: ["id_cdc"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["debe seleccionar un lugar"]
frontend_referencias: ["frontend/inventario/controller/equipajes_lista_activ_periodo.php"]
casos_uso: []
tags: ["inventario", "equipajes", "lista", "activ", "periodo"]
estado_revision: "revisado"
---

# Actividades por periodo y lugar

Filtra actividades de un CDC/lugar en un periodo para seleccionar al crear equipaje.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Filtra actividades de un CDC/lugar en un periodo para seleccionar al crear equipaje.

## Endpoint

- URL: `/src/inventario/equipajes_lista_activ_periodo`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_lista_activ_periodo.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_cdc` | `integer` | POST | Si | |
| `periodo` | `string` | POST | No | |
| `year` | `integer` | POST | No | |
| `empiezamin` | `string` | POST | No | |
| `empiezamax` | `string` | POST | No | |
| `inicio` | `string` | POST | No | |
| `fin` | `string` | POST | No | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload `{a_valores, nombre_ubi}`. Error en mensaje si falta lugar.

## Errores conocidos

  - `debe seleccionar un lugar`

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/equipajes_lista_activ_periodo.php`
