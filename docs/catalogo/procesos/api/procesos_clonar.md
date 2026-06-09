---
id: "procesos.procesos_clonar"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/procesos_clonar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/procesos_clonar.php"
entrada: ["post.id_tipo_proceso:integer", "post.id_tipo_proceso_ref:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no se ha indicado el proceso a clonar"]
frontend_referencias: ["frontend/procesos/controller/procesos_select.php"]
casos_uso: ["src\\procesos\\application\\ProcesosClonar"]
tags: ["procesos", "clonar"]
estado_revision: "generado"
---

# Procesos Clonar

Caso de uso: clona las tareas de un proceso de referencia al proceso indicado.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/procesos_clonar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_clonar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_proceso` | `integer` | application | No | application |
| `id_tipo_proceso_ref` | `integer` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `no se ha indicado el proceso a clonar`

## Casos De Uso

- `src\procesos\application\ProcesosClonar`

## Frontend Relacionado

- `frontend/procesos/controller/procesos_select.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.