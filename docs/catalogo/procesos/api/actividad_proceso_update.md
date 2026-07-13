---
id: "procesos.actividad_proceso_update"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/actividad_proceso_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/actividad_proceso_update.php"
entrada: ["post.completado:string", "post.id_item:integer", "post.observ:string"]
entrada_obligatoria: ["id_item"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no se encuentra la tarea del proceso", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/procesos/controller/actividad_proceso.php"]
casos_uso: ["src\\procesos\\application\\ActividadProcesoUpdate"]
tags: ["procesos", "actividad", "proceso", "update"]
estado_revision: "revisado"
---

# Actividad Proceso Update

Guarda el estado (completado/observaciones) de una tarea del proceso de actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Actualiza `completado` y `observ` de un `ActividadProcesoTarea` identificado por `id_item`.
Usa `ProcesoActividadService::guardar` para persistir y propagar efectos colaterales del proceso.

## Endpoint

- URL: `/src/procesos/actividad_proceso_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/actividad_proceso_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `integer` | application | Si | PK de `actividad_proceso_tarea` |
| `completado` | `string` | application | No | Flag booleano (`t`/`f`, `1`/`0`, etc.) |
| `observ` | `string` | application | No | Observaciones de la tarea |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `no se encuentra la tarea del proceso`
- `hay un error, no se ha guardado` (puede concatenar texto del repositorio)
- Errores adicionales de `ProcesoActividadService::getErrorTxt()` si los hay

## Permisos

- Sin control de permisos propio; el frontend solo habilita edición en filas con `puede_editar`.

## Casos De Uso

- `src\procesos\application\ActividadProcesoUpdate`

## Frontend Relacionado

- `frontend/procesos/controller/actividad_proceso.php` (URL emitida como `url_update`)
