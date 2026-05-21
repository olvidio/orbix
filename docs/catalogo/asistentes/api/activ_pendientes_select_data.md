---
id: "asistentes.activ_pendientes_select_data"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/activ_pendientes_select_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/asistentes/infrastructure/ui/http/controllers/activ_pendientes_select_data.php"
entrada: ["post.any:integer", "post.sactividad:string", "post.tipo_personas:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/asistentes/controller/activ_pendientes_select.php"]
casos_uso: ["src\\asistentes\\application\\ActivPendientesSelectData"]
tags: ["asistentes", "activ", "pendientes", "select", "data"]
estado_revision: "generado"
---

# Activ Pendientes Select Data

Actividades pendientes por curso (`activ_pendientes_select.php`). Datos y `link_spec` sin firmar; hash, firmas y tablas en {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/asistentes/activ_pendientes_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/activ_pendientes_select_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `any` | `integer` | application | No | application |
| `sactividad` | `string` | application | No | application |
| `tipo_personas` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Datos y `link_spec` sin firmar; hash, firmas y tablas en {@see \frontend\asistentes\helpers\ActivPendientesSelectRender}.

## Casos De Uso

- `src\asistentes\application\ActivPendientesSelectData`

## Frontend Relacionado

- `frontend/asistentes/controller/activ_pendientes_select.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.