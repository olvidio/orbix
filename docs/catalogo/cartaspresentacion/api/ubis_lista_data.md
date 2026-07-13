---
id: "cartaspresentacion.ubis_lista_data"
tipo: "endpoint"
modulo: "cartaspresentacion"
url: "/src/cartaspresentacion/ubis_lista_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/cartaspresentacion/infrastructure/ui/http/controllers/ubis_lista_data.php"
entrada: ["post.poblacion_sel:string", "post.tipo_lista:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "cartaspresentacion_CartasPresentacionUbisListaDataData"
respuesta_data: ["tipo_lista:string", "explicacion:string", "a_cabeceras:array", "a_valores:array"]
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/cartaspresentacion/controller/cartas_presentacion_ubis_lista.php"]
casos_uso: ["src\\cartaspresentacion\\application\\CartasPresentacionUbisListaData"]
tags: ["cartaspresentacion", "ubis", "lista", "data"]
estado_revision: "revisado"
---

# Ubis Lista Data

Listado de centros con el estado (sí/no) de su carta de presentación, para pintar con `Lista`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Dos variantes según `tipo_lista`:

- `get_dl` — centros de la delegación filtrados por `poblacion_sel` (dirección); muestra explicación
  «para añadir un centro… basta con poner el nombre del director».
- `get_r` — centros extranjeros (`tipo_ctr` `cr` o `dl`, activos); sin filtro de población.

Cada fila indica si tiene carta y ofrece scripts JS (`fnjs_modificar`, `fnjs_ver_ubi`,
`fnjs_eliminar_cp`) en las columnas interactivas.

## Endpoint

- URL: `/src/cartaspresentacion/ubis_lista_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/cartaspresentacion/infrastructure/ui/http/controllers/ubis_lista_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `tipo_lista` | `string` | controller+application | No | `get_dl` o `get_r`; otro valor → listado vacío |
| `poblacion_sel` | `string` | controller+application | No | Solo aplica en `get_dl`; vacío lista todos los de la dl |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse` en el cliente).
- Forma: `standard_envelope_string_data`.
- Payload en `data`:
  - `tipo_lista` (`string`): eco del modo (`get_dl` / `get_r`).
  - `explicacion` (`string`): HTML con texto de ayuda (solo en `get_dl`).
  - `a_cabeceras` (`array`): definición de columnas para `frontend\shared\web\Lista`.
  - `a_valores` (`array`): filas con columnas 1..4; las interactivas usan
    `['script' => ..., 'valor' => ...]` para los formatters `clickFormatter*`.

## Efectos colaterales

- Solo lectura; las mutaciones las ejecutan los endpoints `carta_presentacion_update` / `eliminar`
  invocados desde los scripts de las celdas.

## Permisos

- Sin control de permisos propio; autorización en frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\cartaspresentacion\application\CartasPresentacionUbisListaData`

## Frontend Relacionado

- `frontend/cartaspresentacion/controller/cartas_presentacion_ubis_lista.php`: invocado desde
  `fnjs_ver` de `cartas_presentacion.phtml`; pinta la tabla con `Lista::mostrar_tabla()`.
