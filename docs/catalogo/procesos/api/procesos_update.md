---
id: "procesos.procesos_update"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/procesos_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/procesos_update.php"
entrada: ["post.id_fase:integer", "post.id_fase_previa:array", "post.id_item:integer", "post.id_of_responsable:integer", "post.id_tarea:integer", "post.id_tarea_previa:array", "post.id_tipo_proceso:integer", "post.mensaje_requisito:array", "post.status:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no se encuentra la tarea del proceso", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/procesos/controller/procesos_select.php", "frontend/procesos/controller/procesos_ver.php"]
casos_uso: ["src\\procesos\\application\\ProcesosUpdate"]
tags: ["procesos", "update"]
estado_revision: "generado"
---

# Procesos Update

Caso de uso: guarda una tarea_proceso del proceso.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/procesos_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_fase` | `integer` | application | No | application |
| `id_fase_previa` | `array` | application | No | application |
| `id_item` | `integer` | application | No | application |
| `id_of_responsable` | `integer` | application | No | application |
| `id_tarea` | `integer` | application | No | application |
| `id_tarea_previa` | `array` | application | No | application |
| `id_tipo_proceso` | `integer` | application | No | application |
| `mensaje_requisito` | `array` | application | No | application |
| `status` | `integer` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `no se encuentra la tarea del proceso`
- `hay un error, no se ha guardado`

## Casos De Uso

- `src\procesos\application\ProcesosUpdate`

## Frontend Relacionado

- `frontend/procesos/controller/procesos_select.php`
- `frontend/procesos/controller/procesos_ver.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.