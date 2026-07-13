---
id: "actividadtarifas.tarifa_ubi_lista_data"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tarifa_ubi_lista_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_lista_data.php"
entrada: ["post.id_ubi:integer", "post.year:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadtarifas_TarifaUbiListaDataData"
respuesta_data: ["a_cabeceras:array", "a_valores:array", "any_anterior:integer", "any_actual:integer", "puede_anadir:boolean", "id_ubi:integer", "year:integer", "token_copiar:string"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_ubi_lista.php"]
casos_uso: ["src\\actividadtarifas\\application\\TarifaUbiListaData"]
tags: ["actividadtarifas", "tarifa", "ubi", "lista", "data"]
estado_revision: "revisado"
---

# Tarifa Ubi Lista Data

Listado de `TarifaUbi` filtrado por casa (`id_ubi`) y año (`year`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Construye la tabla de tarifas de una casa/año: sección, letra+serie (enlace JS si editable),
tipos de actividad aplicados, mínimo (siempre `0`), precio y método. Ordena por sección DESC y
letra ASC. Emite `token_copiar` (`HashB`) si el usuario puede añadir tarifas.

## Endpoint

- URL: `/src/actividadtarifas/tarifa_ubi_lista_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_lista_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | application | No | Casa; `0` devuelve listado vacío |
| `year` | `integer` | application | No | Año; `0` devuelve listado vacío |

## Salida

- Helper: `ContestarJson::enviar` → doble `JSON.parse` en cliente.
- Payload en `data`:
  - `a_cabeceras`: sección, tarifa, se aplica a, mínimo, precio, método
  - `a_valores`: filas indexadas; columna tarifa puede ser `{script, valor}` con `fnjs_modificar(id_item, letra)`
  - `any_anterior` (`year-1`), `any_actual`, `puede_anadir`, `id_ubi`, `year`
  - `token_copiar`: cápsula `HashB` para `tarifa_ubi_copiar` (vacía si no aplica)

## Permisos

- Enlace modificar: `mi_sfsv === seccion` y `have_perm_oficina('adl')`.
- `puede_anadir`: `have_perm_oficina('adl'|'pr'|'calendario')` con `id_ubi !== 0`.

## Casos De Uso

- `src\actividadtarifas\application\TarifaUbiListaData`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_ubi_lista.php`: renderiza HTML de tabla; pasa
  `token_copiar` a `fnjs_copiar_tarifas`.
