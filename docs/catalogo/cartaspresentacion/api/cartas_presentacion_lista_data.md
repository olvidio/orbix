---
id: "cartaspresentacion.cartas_presentacion_lista_data"
tipo: "endpoint"
modulo: "cartaspresentacion"
url: "/src/cartaspresentacion/cartas_presentacion_lista_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/cartaspresentacion/infrastructure/ui/http/controllers/cartas_presentacion_lista_data.php"
entrada: ["post.dl:string", "post.pais:string", "post.poblacion:string", "post.que:string", "post.region:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "cartaspresentacion_CartasPresentacionListaDataData"
respuesta_data: ["html_lista:string", "html_errores:string"]
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/cartaspresentacion/controller/cartas_presentacion_lista.php"]
casos_uso: ["src\\cartaspresentacion\\application\\CartasPresentacionListaData"]
tags: ["cartaspresentacion", "cartas", "presentacion", "lista", "data"]
estado_revision: "revisado"
---

# Cartas Presentacion Lista Data

Listado agrupado de cartas de presentación en HTML ya formateado (tipo labor → delegación → población).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Agrupa las cartas por tipo de labor, delegación y población, y devuelve el HTML listo para imprimir.
Tres modos según `que`:

- `lista_dl` — solo cartas de la delegación del usuario.
- `lista_todo` — todas las delegaciones.
- `get` — filtrado por `poblacion`, `pais`, `region` y/o `dl` (pantalla buscar).

Si algún centro tiene `tipo_labor` vacío o 0, acumula su nombre en `html_errores` bajo el aviso
«Centros con el campo 'tipo labor' mal puesto».

## Endpoint

- URL: `/src/cartaspresentacion/cartas_presentacion_lista_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/cartaspresentacion/infrastructure/ui/http/controllers/cartas_presentacion_lista_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `que` | `string` | controller+application | No | `lista_dl`, `lista_todo` o `get`; si vacío devuelve listado vacío |
| `poblacion` | `string` | controller+application | No | Solo en modo `get`; también busca en campo `zona` |
| `pais` | `string` | controller+application | No | Solo en modo `get` |
| `region` | `string` | controller+application | No | Solo en modo `get` |
| `dl` | `string` | controller+application | No | Solo en modo `get` |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse` en el cliente).
- Forma: `standard_envelope_string_data`.
- Payload en `data`:
  - `html_lista` (`string`): tablas HTML agrupadas por tipo/delegación/población.
  - `html_errores` (`string`): aviso HTML con centros cuyo `tipo_labor` está mal; vacío si no hay.

## Errores conocidos

- No devuelve errores `_()` en el envelope; los centros con `tipo_labor` inválido aparecen en
  `html_errores`.

## Permisos

- Sin control de permisos propio en el caso de uso; autorización en frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\cartaspresentacion\application\CartasPresentacionListaData`

## Frontend Relacionado

- `frontend/cartaspresentacion/controller/cartas_presentacion_lista.php`: invocado desde el menú
  (lista dl / lista todo) o desde la pantalla buscar (`que=get`); concatena `html_lista` + `html_errores`
  y los devuelve como HTML vía `AjaxJsonSupport::html`.
