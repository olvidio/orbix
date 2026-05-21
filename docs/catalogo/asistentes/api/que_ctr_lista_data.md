---
id: "asistentes.que_ctr_lista_data"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/que_ctr_lista_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/asistentes/infrastructure/ui/http/controllers/que_ctr_lista_data.php"
entrada: ["post.id_ubi:integer", "post.lista:string", "post.n_agd:string", "post.periodo:string", "post.sactividad:string", "post.sasistentes:string", "post.ssfsv:string", "post.tipo:string", "post.year:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/asistentes/controller/que_ctr_lista.php"]
casos_uso: ["src\\asistentes\\application\\QueCtrListaData"]
tags: ["asistentes", "que", "ctr", "lista", "data"]
estado_revision: "generado"
---

# Que Ctr Lista Data

JSON para {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/asistentes/que_ctr_lista_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/que_ctr_lista_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | application | No | application |
| `lista` | `string` | application | No | application |
| `n_agd` | `string` | application | No | application |
| `periodo` | `string` | application | No | application |
| `sactividad` | `string` | application | No | application |
| `sasistentes` | `string` | application | No | application |
| `ssfsv` | `string` | application | No | application |
| `tipo` | `string` | application | No | application |
| `year` | `integer` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`

## Efectos colaterales

- Hash, bloque PeriodoQue y URL absoluta del action en {@see \frontend\asistentes\helpers\QueCtrListaRender}.

## Casos De Uso

- `src\asistentes\application\QueCtrListaData`

## Frontend Relacionado

- `frontend/asistentes/controller/que_ctr_lista.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.