---
id: "procesos.procesos_regenerar"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/procesos_regenerar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/procesos_regenerar.php"
entrada: ["post.id_tipo_proceso:integer"]
entrada_obligatoria: ["id_tipo_proceso"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/procesos/controller/procesos_select.php"]
casos_uso: ["src\\procesos\\application\\ProcesosRegenerar"]
tags: ["procesos", "regenerar"]
estado_revision: "revisado"
---

# Procesos Regenerar

Regenera las instancias de tareas de actividad a partir de `tareas_proceso`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Para cada `tarea_proceso` del proceso tipo, llama a `addFaseTarea` en el repositorio de tareas de
actividad y al final elimina fases/tareas huérfanas con `borrarFaseTareaInexistente`.

## Endpoint

- URL: `/src/procesos/procesos_regenerar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_regenerar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_proceso` | `integer` | application | Si | Proceso tipo a regenerar en actividades |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- _(ninguno documentado en el caso de uso)_

## Permisos

- Sin control de permisos propio; autorización en `procesos_select.php` y `$_SESSION['oPerm']`.

## Casos De Uso

- `src\procesos\application\ProcesosRegenerar`

## Frontend Relacionado

- `frontend/procesos/controller/procesos_select.php` (URL emitida como `url_regenerar`)
