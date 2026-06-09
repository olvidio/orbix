---
id: "ubis.calendario_periodos_get2_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/calendario_periodos_get2_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/calendario_periodos_get2_data.php"
entrada: ["post.id_ubi:integer", "post.year:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/calendario_periodos_get2.php"]
casos_uso: ["src\\ubis\\application\\CalendarioPeriodosGet2Data"]
tags: ["ubis", "calendario", "periodos", "get2", "data"]
estado_revision: "generado"
---

# Calendario Periodos Get2 Data

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/calendario_periodos_get2_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/calendario_periodos_get2_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | controller | No | controller |
| `year` | `integer` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\ubis\application\CalendarioPeriodosGet2Data`

## Frontend Relacionado

- `frontend/ubis/controller/calendario_periodos_get2.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.