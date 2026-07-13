---
id: "cartaspresentacion.poblaciones_data"
tipo: "endpoint"
modulo: "cartaspresentacion"
url: "/src/cartaspresentacion/poblaciones_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/cartaspresentacion/infrastructure/ui/http/controllers/poblaciones_data.php"
entrada: ["post.filtro:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "cartaspresentacion_CartasPresentacionPoblacionesDataData"
respuesta_data: ["id:string", "opciones:array", "selected:string", "blanco:boolean", "val_blanco:string", "action:string", "clase:string"]
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/cartaspresentacion/view/cartas_presentacion.phtml"]
casos_uso: ["src\\cartaspresentacion\\application\\CartasPresentacionPoblacionesData"]
tags: ["cartaspresentacion", "poblaciones", "data"]
estado_revision: "revisado"
---

# Poblaciones Data

Opciones del desplegable de poblaciones de la pantalla principal según el filtro elegido.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve el payload estándar de desplegable AJAX (`fnjs_construir_desplegable`) según `filtro`:

- `get_H` — poblaciones de direcciones con país España.
- `get_r` — poblaciones de direcciones con país distinto de España.
- `get_dl` — poblaciones de los centros de la delegación del usuario.
- Otro valor — opciones vacías.

El `id` del desplegable es siempre `poblacion_sel`.

## Endpoint

- URL: `/src/cartaspresentacion/poblaciones_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/cartaspresentacion/infrastructure/ui/http/controllers/poblaciones_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `filtro` | `string` | controller+application | No | Valor de `#tipo_lista`: `get_dl`, `get_H` o `get_r` |

Nota: en la vista principal el desplegable `tipo_lista` ofrece `get_dl` (mi delegación) y `get_r`
(regiones); al elegir `get_r` el JS oculta el desplegable de población sin llamar a este endpoint.

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse` en el cliente).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (contrato desplegable estándar):
  - `id` (`string`): `poblacion_sel`.
  - `opciones` (`array`): pares `[valor, etiqueta]` ordenados.
  - `selected` (`string`): vacío.
  - `blanco` (`boolean`): `true`.
  - `val_blanco` (`string`): vacío.
  - `action` (`string`): vacío.
  - `clase` (`string`): `contenido`.

## Permisos

- Sin control de permisos propio; autorización en frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\cartaspresentacion\application\CartasPresentacionPoblacionesData`

## Frontend Relacionado

- Invocado desde `fnjs_poblacion` en `cartas_presentacion.phtml` al cambiar `tipo_lista` o al cargar
  la pantalla. URL firmada como `URL_POBLACIONES` desde la shell.
