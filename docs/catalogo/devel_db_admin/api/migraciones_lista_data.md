---
id: "devel_db_admin.migraciones_lista_data"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/migraciones_lista_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/migraciones_lista_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/devel_db_admin/controller/migraciones_lista.php"]
casos_uso: ["src\\devel_db_admin\\application\\MigracionesListaData"]
tags: ["devel_db_admin", "migraciones", "lista", "data"]
estado_revision: "revisado"
---

# Migraciones Lista Data

Listado de migraciones SQL escaneadas con estado de aplicación.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Escanea `db/migrations`, cruza con `migracion_aplicada` y devuelve filas para SlickGrid con
cabeceras traducidas. Cada fila incluye `sel` = id de migración (sin `#`; el grid lo añade).

## Endpoint

- URL: `/src/devel_db_admin/migraciones_lista_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/migraciones_lista_data.php`

## Entrada

Sin parámetros.

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload:
  - `a_cabeceras` (`array`): columnas SlickGrid
  - `a_valores` (`array`): filas (`sel`, `fichero`, `prefijo`, `descripcion`, `bds`, `tipo`, `estado`, `fecha`)
  - `warnings` (`list<string>`): avisos del escaneo

## Permisos

- Sin control propio.

## Casos De Uso

- `src\devel_db_admin\application\MigracionesListaData`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/migraciones_lista.php`
