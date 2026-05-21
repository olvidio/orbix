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
frontend_referencias: ["frontend/cartaspresentacion/controller/cartas_presentacion_ubis_lista.php"]
casos_uso: ["src\\cartaspresentacion\\application\\CartasPresentacionUbisListaData"]
tags: ["cartaspresentacion", "ubis", "lista", "data"]
estado_revision: "generado"
---

# Ubis Lista Data

Endpoint backend: listado de centros con el estado de su carta de presentacion, en dos variantes (delegacion del usuario o centros extranjeros).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/cartaspresentacion/ubis_lista_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/cartaspresentacion/infrastructure/ui/http/controllers/ubis_lista_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `poblacion_sel` | `string` | controller+application | No | controller+application |
| `tipo_lista` | `string` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload en `data` (schema `cartaspresentacion_CartasPresentacionUbisListaDataData`):
  - `tipo_lista` (`string`)
  - `explicacion` (`string`)
  - `a_cabeceras` (`array`)
  - `a_valores` (`array`)

## Efectos colaterales

- `a_valores` contiene filas con columnas 1..4 donde las columnas interactivas usan `['script' => ..., 'valor' => ...]` para que las funciones JS (fnjs_modificar, fnjs_ver_ubi, fnjs_eliminar_cp) se invoquen desde los formatters del `Lista`.

## Casos De Uso

- `src\cartaspresentacion\application\CartasPresentacionUbisListaData`

## Frontend Relacionado

- `frontend/cartaspresentacion/controller/cartas_presentacion_ubis_lista.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.