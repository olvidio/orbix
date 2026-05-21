---
id: "asistentes.asistente_mover_data"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/asistente_mover_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/asistentes/infrastructure/ui/http/controllers/asistente_mover_data.php"
entrada: ["post.id_activ:integer", "post.id_nom:integer", "post.id_pau:integer", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/asistentes/controller/asistente_mover.php"]
casos_uso: ["src\\asistentes\\application\\AsistenteMoverData"]
tags: ["asistentes", "asistente", "mover", "data"]
estado_revision: "generado"
---

# Asistente Mover Data

JSON para {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/asistentes/asistente_mover_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/asistente_mover_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | No | application |
| `id_nom` | `integer` | application | No | application |
| `id_pau` | `integer` | application | No | application |
| `sel` | `array` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Modal mover asistente (`asistente_mover.php`).
- URL de guardado, hash y desplegable HTML: {@see \frontend\asistentes\helpers\AsistenteMoverRender}.

## Casos De Uso

- `src\asistentes\application\AsistenteMoverData`

## Frontend Relacionado

- `frontend/asistentes/controller/asistente_mover.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.