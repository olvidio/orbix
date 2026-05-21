---
id: "cartaspresentacion.cartas_presentacion_buscar_data"
tipo: "endpoint"
modulo: "cartaspresentacion"
url: "/src/cartaspresentacion/cartas_presentacion_buscar_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/cartaspresentacion/infrastructure/ui/http/controllers/cartas_presentacion_buscar_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "cartaspresentacion_CartasPresentacionBuscarOpcionesDataData"
respuesta_data: ["opciones_region:array", "opciones_pais:array", "opciones_delegacion:array", "paths:array", "hash_lista:array"]
requiere_hashb: false
frontend_referencias: ["frontend/cartaspresentacion/controller/cartas_presentacion_buscar.php"]
casos_uso: ["src\\cartaspresentacion\\application\\CartasPresentacionBuscarOpcionesData"]
tags: ["cartaspresentacion", "cartas", "presentacion", "buscar", "data"]
estado_revision: "generado"
---

# Cartas Presentacion Buscar Data

Endpoint backend: opciones del formulario de busqueda de cartas de presentacion (region, pais, delegacion).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/cartaspresentacion/cartas_presentacion_buscar_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/cartaspresentacion/infrastructure/ui/http/controllers/cartas_presentacion_buscar_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `cartaspresentacion_CartasPresentacionBuscarOpcionesDataData`):
  - `opciones_region` (`array`)
  - `opciones_pais` (`array`)
  - `opciones_delegacion` (`array`)
  - `paths` (`array`)
  - `hash_lista` (`array`)

## Casos De Uso

- `src\cartaspresentacion\application\CartasPresentacionBuscarOpcionesData`

## Frontend Relacionado

- `frontend/cartaspresentacion/controller/cartas_presentacion_buscar.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.