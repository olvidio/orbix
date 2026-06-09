---
id: "actividadessacd.lista_actividades_sacd_data"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/lista_actividades_sacd_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/lista_actividades_sacd_data.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.periodo:string", "post.tipo:string", "post.year:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadessacd_ListaActividadesSacdDataData"
respuesta_data: ["0:PermAccion, 1: PermAccion, 2: PermAccion"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadessacd/controller/activ_sacd.php"]
casos_uso: ["src\\actividadessacd\\application\\ListaActividadesSacdData"]
tags: ["actividadessacd", "lista", "actividades", "sacd", "data"]
estado_revision: "generado"
---

# Lista Actividades Sacd Data

Endpoint backend: devuelve el listado de actividades del tipo + periodo elegidos junto con los sacd encargados y los flags de permiso.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadessacd/lista_actividades_sacd_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/lista_actividades_sacd_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `empiezamax` | `string` | controller+application | No | controller+application |
| `empiezamin` | `string` | controller+application | No | controller+application |
| `periodo` | `string` | controller+application | No | controller+application |
| `tipo` | `string` | controller+application | No | controller+application |
| `year` | `string` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadessacd_ListaActividadesSacdDataData`):
  - `0` (`PermAccion, 1: PermAccion, 2: PermAccion`)

## Permisos

- Permiso oficina `des`

## Casos De Uso

- `src\actividadessacd\application\ListaActividadesSacdData`

## Frontend Relacionado

- `frontend/actividadessacd/controller/activ_sacd.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.