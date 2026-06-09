---
id: "procesos.fases_activ_cambio_update"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/fases_activ_cambio_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/fases_activ_cambio_update.php"
entrada: ["post.accion:string", "post.id_fase_nueva:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/procesos/controller/fases_activ_cambio.php"]
casos_uso: ["src\\procesos\\application\\FasesActivCambioUpdate"]
tags: ["procesos", "fases", "activ", "cambio", "update"]
estado_revision: "generado"
---

# Fases Activ Cambio Update

Caso de uso: aplica setCompletado a la fase nueva para actividades seleccionadas.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/fases_activ_cambio_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/fases_activ_cambio_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `accion` | `string` | application | No | application |
| `id_fase_nueva` | `string` | application | No | application |
| `sel` | `array` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\procesos\application\FasesActivCambioUpdate`

## Frontend Relacionado

- `frontend/procesos/controller/fases_activ_cambio.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.