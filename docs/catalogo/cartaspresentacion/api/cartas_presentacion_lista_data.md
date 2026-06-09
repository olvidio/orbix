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
respuesta_data: ["html_lista:string, html_errores:string"]
requiere_hashb: false
frontend_referencias: ["frontend/cartaspresentacion/controller/cartas_presentacion_lista.php"]
casos_uso: ["src\\cartaspresentacion\\application\\CartasPresentacionListaData"]
tags: ["cartaspresentacion", "cartas", "presentacion", "lista", "data"]
estado_revision: "generado"
---

# Cartas Presentacion Lista Data

Endpoint backend: listado agrupado de cartas de presentacion (modo `lista_dl`, `lista_todo` o `get` con filtros).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/cartaspresentacion/cartas_presentacion_lista_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/cartaspresentacion/infrastructure/ui/http/controllers/cartas_presentacion_lista_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl` | `string` | controller+application | No | controller+application |
| `pais` | `string` | controller+application | No | controller+application |
| `poblacion` | `string` | controller+application | No | controller+application |
| `que` | `string` | controller+application | No | controller+application |
| `region` | `string` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload en `data` (schema `cartaspresentacion_CartasPresentacionListaDataData`):
  - `html_lista` (`string, html_errores:string`)

## Casos De Uso

- `src\cartaspresentacion\application\CartasPresentacionListaData`

## Frontend Relacionado

- `frontend/cartaspresentacion/controller/cartas_presentacion_lista.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.