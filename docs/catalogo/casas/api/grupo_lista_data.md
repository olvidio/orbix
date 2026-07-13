---
id: "casas.grupo_lista_data"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/grupo_lista_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/casas/infrastructure/ui/http/controllers/grupo_lista_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "casas_GrupoCasaListaDataData"
respuesta_data: ["a_cabeceras:array", "a_valores:array", "puede_anadir:boolean"]
requiere_hashb: false
frontend_referencias: ["frontend/casas/controller/grupo_lista.php"]
casos_uso: ["src\\casas\\application\\GrupoCasaListaData"]
tags: ["casas", "grupo", "lista", "data"]
estado_revision: "revisado"
---

# Grupo Lista Data

Listado de relaciones `GrupoCasa` (casa padre ↔ casa hijo) para la tabla del flujo de grupos.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Sucesor de `apps/casas/controller/grupo_lista.php`. Carga todos los grupos, resuelve los nombres de
casa padre/hijo y devuelve cabeceras + filas con botones `fnjs_modificar` / `fnjs_eliminar`. El flag
`puede_anadir` indica si el usuario puede dar de alta nuevos grupos.

## Endpoint

- URL: `/src/casas/grupo_lista_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/casas/infrastructure/ui/http/controllers/grupo_lista_data.php`

## Entrada

Sin parámetros; el controller invoca el caso de uso sin `$input`.

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse` en el front).
- Forma: `standard_envelope_string_data`.
- Payload en `data`:
  - `a_cabeceras` (`array<int,string>`): `casa padre`, `casa hijo`, y dos columnas vacías (acciones).
  - `a_valores` (`array`): filas indexadas; columnas 3–4 son celdas `{script, valor}` para editar/eliminar.
  - `puede_anadir` (`boolean`): `true` si `$_SESSION['oPerm']->have_perm_oficina('adl')`.

## Permisos

- `puede_anadir` depende de permiso oficina `adl` vía `XPermisos`. El listado en sí no filtra por permiso.

## Casos De Uso

- `src\casas\application\GrupoCasaListaData`

## Frontend Relacionado

- `frontend/casas/controller/grupo_lista.php`: `fnjs_ver` carga la tabla; las acciones de fila llaman a
  `grupo_form` / `grupo_update` / `grupo_eliminar`.
