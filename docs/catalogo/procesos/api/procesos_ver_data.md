---
id: "procesos.procesos_ver_data"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/procesos_ver_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/procesos/infrastructure/ui/http/controllers/procesos_ver_data.php"
entrada: ["post.id_item:integer", "post.mod:string"]
entrada_obligatoria: ["mod"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/procesos/controller/procesos_ver.php"]
casos_uso: ["src\\procesos\\application\\ProcesosVerData"]
tags: ["procesos", "ver", "data"]
estado_revision: "revisado"
---

# Procesos Ver Data

Datos para el formulario de alta/edición de tarea de proceso (`procesos_ver`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Prepara desplegables y valores del formulario de una tarea de proceso. En modo `editar` carga la
ficha por `id_item` (fase, tarea, status, oficina y requisitos previos con sus tareas). En alta
devuelve estructura vacía con una fila de requisitos en blanco.

## Endpoint

- URL: `/src/procesos/procesos_ver_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_ver_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `mod` | `string` | controller | Si | `editar` carga ficha; otro valor → alta |
| `id_item` | `integer` | controller | No | Obligatorio en práctica si `mod=editar` |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Claves en `data` (doble `JSON.parse`):
  - `mod` (`string`)
  - `a_oficinas`, `a_status`, `a_fases` (`array`): opciones de desplegables
  - `a_tareas` (`array`): tareas de la fase seleccionada (vacío en alta)
  - `status`, `id_of_responsable`, `id_fase`, `id_tarea` (`string|int`): valores en edición
  - `a_fases_previas` (`list`): filas con `id_fase_previa`, `id_tarea_previa`, `mensaje_requisito`,
    `a_tareas_previa`

## Errores conocidos

- _(ninguno; si `id_item` no existe en edición devuelve payload con valores vacíos)_

## Permisos

- Sin control de permisos propio; autorización en `procesos_ver.php` y `$_SESSION['oPerm']`.

## Casos De Uso

- `src\procesos\application\ProcesosVerData`

## Frontend Relacionado

- `frontend/procesos/controller/procesos_ver.php` (carga inicial vía `PostRequest::getDataFromUrl`)
