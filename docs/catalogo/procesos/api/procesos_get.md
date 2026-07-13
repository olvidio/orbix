---
id: "procesos.procesos_get"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/procesos_get"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/procesos/infrastructure/ui/http/controllers/procesos_get.php"
entrada: ["post.id_tipo_proceso:integer"]
entrada_obligatoria: ["id_tipo_proceso"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/procesos/controller/procesos_get.php", "frontend/procesos/controller/procesos_get_listado.php", "frontend/procesos/controller/procesos_select.php"]
casos_uso: ["src\\procesos\\application\\ProcesosGet"]
tags: ["procesos", "get"]
estado_revision: "revisado"
---

# Procesos Get

Estructura padres/hijos del árbol de fases del proceso.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve el árbol de fases del proceso tipo (`id_tipo_proceso`) agrupado por fase previa.
Filtra fases según el SFSV del usuario (o SuperAdmin ve todas). Cada nodo incluye `id` y `nom`
de la fase.

## Endpoint

- URL: `/src/procesos/procesos_get`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_get.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_proceso` | `integer` | application | Si | Proceso tipo a visualizar |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Claves en `data` (doble `JSON.parse`):
  - `aPadres` (`array<int, array<int, array{id: int, nom: string}>>`): clave externa =
    `id_fase_previa` (0 si ninguna), índice interno secuencial, valor con `id` y `nom` de fase

## Errores conocidos

- _(ninguno documentado en el caso de uso)_

## Permisos

- Filtrado implícito por SFSV/rol (SuperAdmin ve SF+SV); no usa `perm_*` de oficina.

## Casos De Uso

- `src\procesos\application\ProcesosGet`

## Frontend Relacionado

- `frontend/procesos/controller/procesos_get.php` (renderer HTML del árbol)
- `frontend/procesos/controller/procesos_select.php` (URL en `url_get`)
