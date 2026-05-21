---
id: "asistentes.tabla_peticiones_data"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/tabla_peticiones_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/asistentes/infrastructure/ui/http/controllers/tabla_peticiones_data.php"
entrada: ["post.id_activ_old:integer", "post.restored_id_sel:mixed", "post.restored_scroll_id:mixed", "post.sel:array", "post.stack:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/asistentes/controller/tabla_peticiones.php"]
casos_uso: ["src\\asistentes\\application\\TablaPeticionesData"]
tags: ["asistentes", "tabla", "peticiones", "data"]
estado_revision: "generado"
---

# Tabla Peticiones Data

JSON para {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/asistentes/tabla_peticiones_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/tabla_peticiones_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ_old` | `integer` | application | No | application |
| `restored_id_sel` | `mixed` | application | No | application |
| `restored_scroll_id` | `mixed` | application | No | application |
| `sel` | `array` | application | No | application |
| `stack` | `mixed` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- HTML de tabla, enlaces firmados y metadatos AJAX: {@see \frontend\asistentes\helpers\TablaPeticionesRender}.

## Casos De Uso

- `src\asistentes\application\TablaPeticionesData`

## Frontend Relacionado

- `frontend/asistentes/controller/tabla_peticiones.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.