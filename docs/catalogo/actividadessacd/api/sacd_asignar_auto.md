---
id: "actividadessacd.sacd_asignar_auto"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/sacd_asignar_auto"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/sacd_asignar_auto.php"
entrada: ["post.f_ini_iso:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadessacd_SacdAsignarAutoData"
respuesta_data: ["asignadas:int, sin_asignar: int"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadessacd/controller/asignar_sacd_auto.php", "frontend/actividadessacd/view/asignar_sacd_auto.phtml"]
casos_uso: ["src\\actividadessacd\\application\\SacdAsignarAuto"]
tags: ["actividadessacd", "sacd", "asignar", "auto"]
estado_revision: "generado"
---

# Sacd Asignar Auto

Endpoint backend: auto-asignacion masiva del sacd titular del centro encargado a actividades sr/sg sin sacd.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadessacd/sacd_asignar_auto`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/sacd_asignar_auto.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `f_ini_iso` | `string` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadessacd_SacdAsignarAutoData`):
  - `asignadas` (`int, sin_asignar: int`)

## Casos De Uso

- `src\actividadessacd\application\SacdAsignarAuto`

## Frontend Relacionado

- `frontend/actividadessacd/controller/asignar_sacd_auto.php`
- `frontend/actividadessacd/view/asignar_sacd_auto.phtml`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.