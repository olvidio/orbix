---
id: "casas.casa_ingresos_lista_data"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/casa_ingresos_lista_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/casas/infrastructure/ui/http/controllers/casa_ingresos_lista_data.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.id_cdc:array", "post.periodo:string", "post.year:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/casas/controller/casa_ingresos_lista.php"]
casos_uso: ["src\\casas\\application\\CasaIngresosListaData"]
tags: ["casas", "casa", "ingresos", "lista", "data"]
estado_revision: "generado"
---

# Casa Ingresos Lista Data

Endpoint backend: listado económico de actividades por casa (`casa_ingresos_lista`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/casas/casa_ingresos_lista_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/casas/infrastructure/ui/http/controllers/casa_ingresos_lista_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `empiezamax` | `string` | controller+application | No | controller+application |
| `empiezamin` | `string` | controller+application | No | controller+application |
| `id_cdc` | `array` | controller+application | No | controller+application |
| `periodo` | `string` | controller+application | No | controller+application |
| `year` | `string` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`

## Casos De Uso

- `src\casas\application\CasaIngresosListaData`

## Frontend Relacionado

- `frontend/casas/controller/casa_ingresos_lista.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.