---
id: "procesos.procesos_eliminar"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/procesos_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/procesos_eliminar.php"
entrada: ["post.id_item:integer"]
entrada_obligatoria: ["id_item"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no sé cuál he de borar", "no se encuentra la tarea a borrar", "hay un error, no se ha eliminado"]
frontend_referencias: ["frontend/procesos/controller/procesos_select.php"]
casos_uso: ["src\\procesos\\application\\ProcesosEliminar"]
tags: ["procesos", "eliminar"]
estado_revision: "revisado"
---

# Procesos Eliminar

Elimina una `tarea_proceso` por su `id_item`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Borra la definición de tarea del proceso tipo indicado. Requiere `id_item` válido (> 0).

## Endpoint

- URL: `/src/procesos/procesos_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `integer` | application | Si | PK de `tareas_proceso` |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `no sé cuál he de borar`
- `no se encuentra la tarea a borrar`
- `hay un error, no se ha eliminado` (puede concatenar error de repositorio)

## Permisos

- Sin control de permisos propio; autorización en `procesos_select.php` y `$_SESSION['oPerm']`.

## Casos De Uso

- `src\procesos\application\ProcesosEliminar`

## Frontend Relacionado

- `frontend/procesos/controller/procesos_select.php` (URL emitida como `url_eliminar`)
