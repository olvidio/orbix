---
id: "procesos.procesos_depende"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/procesos_depende"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/procesos_depende.php"
entrada: ["post.acc:string", "post.valor_depende:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "procesos_ProcesosDependeData"
respuesta_data: ["opciones:array"]
requiere_hashb: false
frontend_referencias: ["frontend/procesos/controller/procesos_ver.php"]
casos_uso: ["src\\procesos\\application\\ProcesosDepende"]
tags: ["procesos", "depende"]
estado_revision: "generado"
---

# Procesos Depende

Caso de uso: opciones del desplegable de tareas dependientes de una fase.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/procesos_depende`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_depende.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `acc` | `string` | application | No | application |
| `valor_depende` | `integer` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `procesos_ProcesosDependeData`):
  - `opciones` (`array`)

## Casos De Uso

- `src\procesos\application\ProcesosDepende`

## Frontend Relacionado

- `frontend/procesos/controller/procesos_ver.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.