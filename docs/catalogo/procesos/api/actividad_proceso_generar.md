---
id: "procesos.actividad_proceso_generar"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/actividad_proceso_generar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/actividad_proceso_generar.php"
entrada: ["post.id_activ:integer"]
entrada_obligatoria: ["id_activ"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/procesos/controller/actividad_proceso.php"]
casos_uso: ["src\\procesos\\application\\ActividadProcesoGenerar"]
tags: ["procesos", "actividad", "proceso", "generar"]
estado_revision: "revisado"
---

# Actividad Proceso Generar

(Re)genera las tareas del proceso asociadas a una actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Invoca `generarProceso` en el repositorio de tareas de actividad para el `id_activ` indicado,
usando el SFSV de la sesión y forzando la regeneración (`true`). Equivale al botón «crear proceso
de nuevo» de `actividad_proceso`.

## Endpoint

- URL: `/src/procesos/actividad_proceso_generar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/actividad_proceso_generar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | Si | Actividad cuyo proceso se regenera |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- _(ninguno documentado en el caso de uso; errores de repositorio no se propagan como `_()`)_

## Permisos

- Sin control de permisos propio; el frontend solo muestra la acción si `permiso_calendario`.

## Casos De Uso

- `src\procesos\application\ActividadProcesoGenerar`

## Frontend Relacionado

- `frontend/procesos/controller/actividad_proceso.php` (URL emitida como `url_generar`)
