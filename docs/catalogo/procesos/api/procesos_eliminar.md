---
id: "procesos.procesos_eliminar"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/procesos_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/procesos_eliminar.php"
entrada: ["post.id_item:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no sé cuál he de borar", "no se encuentra la tarea a borrar", "hay un error, no se ha eliminado"]
frontend_referencias: ["frontend/procesos/controller/procesos_select.php"]
casos_uso: ["src\\procesos\\application\\ProcesosEliminar"]
tags: ["procesos", "eliminar"]
estado_revision: "generado"
---

# Procesos Eliminar

Caso de uso: elimina una tarea_proceso por su id_item.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/procesos_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `integer` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Caso de uso: elimina una tarea_proceso por su id_item.

## Errores conocidos

- `no sé cuál he de borar`
- `no se encuentra la tarea a borrar`
- `hay un error, no se ha eliminado`

## Casos De Uso

- `src\procesos\application\ProcesosEliminar`

## Frontend Relacionado

- `frontend/procesos/controller/procesos_select.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.