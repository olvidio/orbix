---
id: "actividadessacd.comunicacion_activ_sacd_data"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/comunicacion_activ_sacd_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/comunicacion_activ_sacd_data.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.id_nom:integer", "post.periodo:string", "post.propuesta:string", "post.que:string", "post.sel:mixed", "post.year:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadessacd_ComunicacionActividadesSacdDataData"
respuesta_data: ["que:string", "propuesta:string", "mi_dele:string", "lugar_fecha:string", "periodo_txt:string", "sacds:array", "sacds_paso:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadessacd/controller/com_sacd_activ_periodo.php", "frontend/actividadessacd/view/com_sacd_activ_periodo.phtml"]
casos_uso: ["src\\actividadessacd\\application\\ComunicacionActividadesSacdData"]
tags: ["actividadessacd", "comunicacion", "activ", "sacd", "data"]
estado_revision: "generado"
---

# Comunicacion Activ Sacd Data

Endpoint backend: construye el listado de atencion de actividades a comunicar a los sacd (incluidas las de los "sacd de paso" cuando procede).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadessacd/comunicacion_activ_sacd_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/comunicacion_activ_sacd_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `empiezamax` | `string` | application | No | application |
| `empiezamin` | `string` | application | No | application |
| `id_nom` | `integer` | application | No | application |
| `periodo` | `string` | application | No | application |
| `propuesta` | `string` | application | No | application |
| `que` | `string` | application | No | application |
| `sel` | `mixed` | application | No | application |
| `year` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadessacd_ComunicacionActividadesSacdDataData`):
  - `que` (`string`)
  - `propuesta` (`string`)
  - `mi_dele` (`string`)
  - `lugar_fecha` (`string`)
  - `periodo_txt` (`string`)
  - `sacds` (`array`)
  - `sacds_paso` (`array`)

## Casos De Uso

- `src\actividadessacd\application\ComunicacionActividadesSacdData`

## Frontend Relacionado

- `frontend/actividadessacd/controller/com_sacd_activ_periodo.php`
- `frontend/actividadessacd/view/com_sacd_activ_periodo.phtml`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.