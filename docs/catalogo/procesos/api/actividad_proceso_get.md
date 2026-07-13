---
id: "procesos.actividad_proceso_get"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/actividad_proceso_get"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/procesos/infrastructure/ui/http/controllers/actividad_proceso_get.php"
entrada: ["post.id_activ:integer"]
entrada_obligatoria: ["id_activ"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["error: La fase del proceso tipo: %s, fase: %s, tarea: %s"]
frontend_referencias: ["frontend/procesos/controller/actividad_proceso.php", "frontend/procesos/controller/actividad_proceso_get.php"]
casos_uso: ["src\\procesos\\application\\ActividadProcesoGet"]
tags: ["procesos", "actividad", "proceso", "get"]
estado_revision: "revisado"
---

# Actividad Proceso Get

Listado de tareas del proceso de una actividad con permiso de edición por fila.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Construye las filas de la tabla de fases/tareas del proceso para un `id_activ`. Cada fila incluye
si el usuario puede editarla según la oficina responsable (`$_SESSION['oPerm']`). Si falta la
definición de una tarea en `tareas_proceso`, devuelve `error` en el payload (no en el sobre).

## Endpoint

- URL: `/src/procesos/actividad_proceso_get`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/procesos/infrastructure/ui/http/controllers/actividad_proceso_get.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | Si | Actividad cuyo proceso se lista |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Claves en `data` (doble `JSON.parse`):
  - `error` (`string`; vacío si todo correcto)
  - `a_rows` (`list`): cada fila con `id_item`, `fase`, `tarea`, `of_responsable_txt`,
    `completado` (`bool`), `observ`, `puede_editar` (`bool`)

## Errores conocidos

- `error: La fase del proceso tipo: %s, fase: %s, tarea: %s` (en `data.error`, no en `mensaje`)

## Permisos

- No hay `perm_*` en el caso de uso; `puede_editar` por fila según oficina responsable y
  `$_SESSION['oPerm']->have_perm_oficina()`.

## Casos De Uso

- `src\procesos\application\ActividadProcesoGet`

## Frontend Relacionado

- `frontend/procesos/controller/actividad_proceso_get.php` (renderer HTML de la tabla)
- `frontend/procesos/controller/actividad_proceso.php` (URL en `url_get` / `param_actualizar`)
