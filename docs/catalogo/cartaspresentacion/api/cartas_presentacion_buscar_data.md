---
id: "cartaspresentacion.cartas_presentacion_buscar_data"
tipo: "endpoint"
modulo: "cartaspresentacion"
url: "/src/cartaspresentacion/cartas_presentacion_buscar_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/cartaspresentacion/infrastructure/ui/http/controllers/cartas_presentacion_buscar_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "cartaspresentacion_CartasPresentacionBuscarOpcionesDataData"
respuesta_data: ["opciones_region:array", "opciones_pais:array", "opciones_delegacion:array", "paths:array", "hash_lista:array"]
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/cartaspresentacion/controller/cartas_presentacion_buscar.php"]
casos_uso: ["src\\cartaspresentacion\\application\\CartasPresentacionBuscarOpcionesData"]
tags: ["cartaspresentacion", "cartas", "presentacion", "buscar", "data"]
estado_revision: "revisado"
---

# Cartas Presentacion Buscar Data

Opciones de los desplegables del formulario de búsqueda de cartas de presentación.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Carga las opciones de región (activas), país (todas las direcciones) y delegación (regiones `H`), más
la URL del listado y la especificación `hash_lista` para enviar `que=get` con los filtros del
formulario.

## Endpoint

- URL: `/src/cartaspresentacion/cartas_presentacion_buscar_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/cartaspresentacion/infrastructure/ui/http/controllers/cartas_presentacion_buscar_data.php`

## Entrada

Sin parámetros POST.

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse` en el cliente).
- Forma: `standard_envelope_string_data`.
- Payload en `data`:
  - `opciones_region` (`array<string,string>`): regiones activas ordenadas por nombre.
  - `opciones_pais` (`array<string,string>`): países de direcciones de centros.
  - `opciones_delegacion` (`array<string,string>`): delegaciones de regiones `H`.
  - `paths` (`array`): `lista` → `frontend/cartaspresentacion/controller/cartas_presentacion_lista.php`.
  - `hash_lista` (`array`): `campos_hidden` = `{que: get}`, `campos_form` = `que!poblacion!region!pais!dl`,
    `campos_no` = `scroll_id!sel`.

## Permisos

- Sin control de permisos propio; autorización en frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\cartaspresentacion\application\CartasPresentacionBuscarOpcionesData`

## Frontend Relacionado

- `frontend/cartaspresentacion/controller/cartas_presentacion_buscar.php`: carga este endpoint al
  abrir la pantalla; `CartasPresentacionBuscarOpcionesRender` firma `hash_lista` y monta los
  desplegables en `cartas_presentacion_buscar.phtml`.
