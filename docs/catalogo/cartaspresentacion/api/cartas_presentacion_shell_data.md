---
id: "cartaspresentacion.cartas_presentacion_shell_data"
tipo: "endpoint"
modulo: "cartaspresentacion"
url: "/src/cartaspresentacion/cartas_presentacion_shell_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/cartaspresentacion/infrastructure/ui/http/controllers/cartas_presentacion_shell_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "cartaspresentacion_CartasPresentacionShellDataData"
respuesta_data: ["mi_dele:string", "paths:array", "hash_ctr:array", "hash_lista:array", "hash_form:array", "hash_poblaciones:array", "hash_eliminar:array"]
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/cartaspresentacion/controller/cartas_presentacion.php"]
casos_uso: ["src\\cartaspresentacion\\application\\CartasPresentacionShellData"]
tags: ["cartaspresentacion", "cartas", "presentacion", "shell", "data"]
estado_revision: "revisado"
---

# Cartas Presentacion Shell Data

Bootstrap de la pantalla principal `cartas_presentacion`: devuelve la delegación del usuario y las
rutas relativas de los demás endpoints del módulo, junto con las especificaciones `hash_*` que el
frontend firma con `HashFront`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

No toca base de datos. Emite `mi_dele` (`ConfigGlobal::mi_delef()`) y, para cada acción AJAX de la
shell (listado de centros, formulario, poblaciones, update, eliminar, ver ficha de centro), el `path`
relativo y los `campos_form` / `campos_no` que `HashFront` debe cubrir. La firma se aplica en
`CartasPresentacionShellRender`, no en `src/`.

## Endpoint

- URL: `/src/cartaspresentacion/cartas_presentacion_shell_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/cartaspresentacion/infrastructure/ui/http/controllers/cartas_presentacion_shell_data.php`

## Entrada

Sin parámetros POST. El caso de uso no recibe input.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data`:
  - `mi_dele` (`string`): delegación del usuario.
  - `paths` (`array`): rutas relativas — `ctr`, `lista`, `form`, `poblaciones`, `update`, `eliminar`.
  - `hash_ctr` (`array`): `campos_form` = `bloque!pau!id_ubi` (ficha de centro en `#ficha2`).
  - `hash_lista` (`array`): `campos_form` = `tipo_lista`, `campos_no` = `scroll_id!sel!poblacion_sel`.
  - `hash_form` (`array`): `campos_form` = `id_direccion!id_ubi`.
  - `hash_poblaciones` (`array`): `campos_form` = `filtro`.
  - `hash_eliminar` (`array`): `campos_form` = `id_ubi!id_direccion`.

## Permisos

- Sin control de permisos propio; solo compone rutas. La autorización la ejercen los endpoints destino
  y se resuelve en frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\cartaspresentacion\application\CartasPresentacionShellData`

## Frontend Relacionado

- `frontend/cartaspresentacion/controller/cartas_presentacion.php`: llama vía `PostRequest::getDataFromUrl`,
  enriquece con `CartasPresentacionShellRender` y expone las URLs firmadas a `cartas_presentacion.phtml`.
