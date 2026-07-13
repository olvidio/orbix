---
id: "procesos.procesos_get_listado"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/procesos_get_listado"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/procesos/infrastructure/ui/http/controllers/procesos_get_listado.php"
entrada: ["post.id_tipo_proceso:integer"]
entrada_obligatoria: ["id_tipo_proceso"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/procesos/controller/procesos_get_listado.php"]
casos_uso: ["src\\procesos\\application\\ProcesosGetListado"]
tags: ["procesos", "get", "listado"]
estado_revision: "revisado"
---

# Procesos Get Listado

Listado estructurado de fases/tareas del proceso tipo.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve filas tabulares de `tareas_proceso` para un `id_tipo_proceso`, con texto de status,
oficina responsable, fase, tarea y fases previas concatenadas. Filtra fases por SFSV del usuario.

## Endpoint

- URL: `/src/procesos/procesos_get_listado`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_get_listado.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_proceso` | `integer` | application | Si | Proceso tipo a listar |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Claves en `data` (doble `JSON.parse`):
  - `a_rows` (`list`): cada fila con `id_item`, `status_txt`, `responsable`, `fase`, `tarea`,
    `fase_previa`

## Errores conocidos

- _(ninguno documentado en el caso de uso)_

## Permisos

- Filtrado implícito por SFSV/rol (SuperAdmin ve SF+SV); no usa `perm_*` de oficina.

## Casos De Uso

- `src\procesos\application\ProcesosGetListado`

## Frontend Relacionado

- `frontend/procesos/controller/procesos_get_listado.php` (renderer HTML de la tabla)
