---
id: "inventario.lista_casas_posibles_periodo"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/lista_casas_posibles_periodo"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/inventario/infrastructure/ui/http/controllers/lista_casas_posibles_periodo.php"
entrada: ["post.periodo:string", "post.year:integer", "post.empiezamin:string", "post.empiezamax:string", "post.inicio:string", "post.fin:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/equipajes_casas_posibles.php"]
casos_uso: []
tags: ["inventario", "lista", "casas", "posibles", "periodo"]
estado_revision: "revisado"
---

# Casas posibles en periodo

Opciones de casas/centros con actividades en el periodo indicado (para equipajes).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Opciones de casas/centros con actividades en el periodo indicado (para equipajes).

## Endpoint

- URL: `/src/inventario/lista_casas_posibles_periodo`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_casas_posibles_periodo.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `periodo` | `string` | POST | No | |
| `year` | `integer` | POST | No | |
| `empiezamin` | `string` | POST | No | |
| `empiezamax` | `string` | POST | No | |
| `inicio` | `string` | POST | No | |
| `fin` | `string` | POST | No | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload `{a_opciones}`.

## Errores conocidos

- Sin mensajes `_()` documentados en controller.

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/equipajes_casas_posibles.php`
