---
id: "procesos.procesos_update"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/procesos_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/procesos_update.php"
entrada: ["post.id_fase:integer", "post.id_fase_previa:array", "post.id_item:integer", "post.id_of_responsable:integer", "post.id_tarea:integer", "post.id_tarea_previa:array", "post.id_tipo_proceso:integer", "post.mensaje_requisito:array", "post.status:integer"]
entrada_obligatoria: ["id_item"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no se encuentra la tarea del proceso", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/procesos/controller/procesos_select.php", "frontend/procesos/controller/procesos_ver.php"]
casos_uso: ["src\\procesos\\application\\ProcesosUpdate"]
tags: ["procesos", "update"]
estado_revision: "revisado"
---

# Procesos Update

Guarda una `tarea_proceso` del proceso tipo.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Actualiza status, oficina responsable, fase, tarea y el JSON de fases previas (arrays paralelos
`id_fase_previa[]`, `id_tarea_previa[]`, `mensaje_requisito[]`) de una tarea de proceso existente.

## Endpoint

- URL: `/src/procesos/procesos_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `integer` | application | Si | PK de `tareas_proceso` |
| `id_tipo_proceso` | `integer` | application | No | Proceso tipo |
| `status` | `integer` | application | No | Estado de la tarea |
| `id_of_responsable` | `integer` | application | No | Oficina responsable (bits menú) |
| `id_fase` | `integer` | application | No | Fase de la tarea |
| `id_tarea` | `integer` | application | No | Tarea (0 permitido) |
| `id_fase_previa` | `array` | application | No | Índices paralelos de requisitos |
| `id_tarea_previa` | `array` | application | No | Tarea previa por requisito |
| `mensaje_requisito` | `array` | application | No | Mensaje personalizado por requisito |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `no se encuentra la tarea del proceso`
- `hay un error, no se ha guardado` (puede concatenar error de repositorio)

## Permisos

- Sin control de permisos propio; autorización en frontend y `$_SESSION['oPerm']`.

## Casos De Uso

- `src\procesos\application\ProcesosUpdate`

## Frontend Relacionado

- `frontend/procesos/controller/procesos_ver.php` (submit del formulario de edición)
- `frontend/procesos/controller/procesos_select.php` (URL emitida como `url_update`)
