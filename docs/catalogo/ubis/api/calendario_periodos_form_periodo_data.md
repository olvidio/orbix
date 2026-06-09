---
id: "ubis.calendario_periodos_form_periodo_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/calendario_periodos_form_periodo_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/calendario_periodos_form_periodo_data.php"
entrada: ["post.id_item:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/calendario_periodos_form_periodo.php"]
casos_uso: ["src\\ubis\\application\\CalendarioPeriodosFormPeriodoData"]
tags: ["ubis", "calendario", "periodos", "form", "periodo", "data"]
estado_revision: "generado"
---

# Calendario Periodos Form Periodo Data

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/calendario_periodos_form_periodo_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/calendario_periodos_form_periodo_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `integer` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\ubis\application\CalendarioPeriodosFormPeriodoData`

## Frontend Relacionado

- `frontend/ubis/controller/calendario_periodos_form_periodo.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.