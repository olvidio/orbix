---
id: "actividadessacd.solapes_sacd_data"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/solapes_sacd_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/solapes_sacd_data.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.periodo:string", "post.year:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/actividadessacd/controller/activ_sacd.php"]
casos_uso: ["src\\actividadessacd\\application\\SolapesSacdData"]
tags: ["actividadessacd", "solapes", "sacd", "data"]
estado_revision: "generado"
---

# Solapes Sacd Data

Endpoint backend: devuelve sacd con actividades incompatibles (solapes).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadessacd/solapes_sacd_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/solapes_sacd_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `empiezamax` | `string` | controller+application | No | controller+application |
| `empiezamin` | `string` | controller+application | No | controller+application |
| `periodo` | `string` | controller+application | No | controller+application |
| `year` | `string` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\actividadessacd\application\SolapesSacdData`

## Frontend Relacionado

- `frontend/actividadessacd/controller/activ_sacd.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.