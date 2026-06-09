---
id: "actividadestudios.matriculas_lista_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/matriculas_lista_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/matriculas_lista_data.php"
entrada: ["post.finIso:string", "post.fin_iso:string", "post.inicioIso:string", "post.inicio_iso:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadestudios_MatriculasListaDataData"
respuesta_data: ["titulo:string", "msg_err:string", "a_valores:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadestudios/controller/matriculas_lista.php"]
casos_uso: ["src\\actividadestudios\\application\\MatriculasListaData"]
tags: ["actividadestudios", "matriculas", "lista", "data"]
estado_revision: "generado"
---

# Matriculas Lista Data

Listado de matrículas en un intervalo de fechas (actividades cuyo `f_ini` cae en el periodo). Usado por `matriculas_lista` vía PostRequest.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadestudios/matriculas_lista_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/matriculas_lista_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `finIso` | `string` | controller | No | controller |
| `fin_iso` | `string` | application | No | application |
| `inicioIso` | `string` | controller | No | controller |
| `inicio_iso` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload en `data` (schema `actividadestudios_MatriculasListaDataData`):
  - `titulo` (`string`)
  - `msg_err` (`string`)
  - `a_valores` (`array`)

## Casos De Uso

- `src\actividadestudios\application\MatriculasListaData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/matriculas_lista.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.