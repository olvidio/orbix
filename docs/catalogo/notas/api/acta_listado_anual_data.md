---
id: "notas.acta_listado_anual_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/acta_listado_anual_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/notas/infrastructure/ui/http/controllers/acta_listado_anual_data.php"
entrada: ["post.finIso:string", "post.inicioIso:string"]
entrada_obligatoria: ["inicio", "fin"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/notas/controller/acta_listado_anual.php"]
casos_uso: ["src\\notas\\application\\ListadoAnualActasData"]
tags: ["notas", "acta", "listado", "anual", "data"]
estado_revision: "revisado"
---

# Acta Listado Anual Data

Lista actas en un rango de fechas para el listado anual.

Lista las actas en un rango de fechas (ISO) ordenadas por nivel y fecha. En ambito `rstgr` considera todas las delegaciones de la region de stgr; en los demas ambitos, solo la delegacion actual. Cada item es un array asociativo `{id_nivel, acta, f_acta, nombre_corto}`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/acta_listado_anual_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/acta_listado_anual_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `finIso` | `string` | controller | No | controller |
| `inicioIso` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Helper: `ContestarJson::enviar` (doble `JSON.parse` salvo excepciones).
- Array en `data`: items `{id_nivel, acta, f_acta, nombre_corto}` (doble `JSON.parse`).

## Objetivo funcional

Devuelve actas entre `inicio` y `fin` (ISO), ordenadas por nivel y fecha. En `rstgr` filtra delegaciones de la región.

## Permisos

- Menú listado actas; `$_SESSION['oPerm']`.

## Casos De Uso

- `src\notas\application\ListadoAnualActasData`

## Frontend Relacionado

- `frontend/notas/controller/acta_listado_anual.php`.