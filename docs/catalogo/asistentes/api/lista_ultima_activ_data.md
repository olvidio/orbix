---
id: "asistentes.lista_ultima_activ_data"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/lista_ultima_activ_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/asistentes/infrastructure/ui/http/controllers/lista_ultima_activ_data.php"
entrada: ["post.curso:string", "post.id_ubi:string", "post.que:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "asistentes_ListaUltimaActivDataData"
respuesta_data: ["alert_html:string, titulo: string, stats_html: string, tabla_html: string"]
requiere_hashb: false
frontend_referencias: ["frontend/asistentes/controller/lista_ultima_activ.php"]
casos_uso: ["src\\asistentes\\application\\ListaUltimaActivData"]
tags: ["asistentes", "lista", "ultima", "activ", "data"]
estado_revision: "generado"
---

# Lista Ultima Activ Data

Listado última actividad / seguimiento (`lista_ultima_activ.php`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/asistentes/lista_ultima_activ_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/lista_ultima_activ_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `curso` | `string` | application | No | application |
| `id_ubi` | `string` | application | No | application |
| `que` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `asistentes_ListaUltimaActivDataData`):
  - `alert_html` (`string, titulo: string, stats_html: string, tabla_html: string`)

## Casos De Uso

- `src\asistentes\application\ListaUltimaActivData`

## Frontend Relacionado

- `frontend/asistentes/controller/lista_ultima_activ.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.