---
id: "procesos.procesos_depende"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/procesos_depende"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/procesos/infrastructure/ui/http/controllers/procesos_depende.php"
entrada: ["post.acc:string", "post.valor_depende:string"]
entrada_obligatoria: ["acc"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/procesos/controller/procesos_ver.php"]
casos_uso: ["src\\procesos\\application\\ProcesosDepende"]
tags: ["procesos", "depende"]
estado_revision: "revisado"
---

# Procesos Depende

Opciones del desplegable de tareas dependientes de una fase en `procesos_ver`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Según el selector que disparó la petición (`acc` = `#id_tarea` o `#id_tarea_previa`), devuelve las
tareas posibles para la fase indicada en `valor_depende`. Otros valores de `acc` devuelven opciones
vacías.

## Endpoint

- URL: `/src/procesos/procesos_depende`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_depende.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `acc` | `string` | application | Si | `#id_tarea` o `#id_tarea_previa` |
| `valor_depende` | `string` | application | No | `id_fase` para listar tareas (se castea a int) |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Claves en `data` (doble `JSON.parse`):
  - `opciones` (`list<array{0: string, 1: string}>`)
  - `blanco` (`bool`): `true`

## Errores conocidos

- _(ninguno documentado en el caso de uso)_

## Permisos

- Sin control de permisos propio; autorización en `procesos_ver.php` y `$_SESSION['oPerm']`.

## Casos De Uso

- `src\procesos\application\ProcesosDepende`

## Frontend Relacionado

- `frontend/procesos/controller/procesos_ver.php` (acción `fnjs_get_depende` en desplegables de fase)
