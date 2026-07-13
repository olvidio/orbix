---
id: "cambios.cambio_usuario_objeto_pref_propiedades_data"
tipo: "endpoint"
modulo: "cambios"
url: "/src/cambios/cambio_usuario_objeto_pref_propiedades_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/cambios/infrastructure/ui/http/controllers/cambio_usuario_objeto_pref_propiedades_data.php"
entrada: ["post.id_item_usuario_objeto:integer", "post.objeto:string"]
entrada_obligatoria: ["objeto"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/cambios/controller/usuario_avisos_pref_propiedades.php"]
casos_uso: ["src\\cambios\\application\\CambioUsuarioObjetoPrefPropiedadesData"]
tags: ["cambios", "cambio", "usuario", "objeto", "pref", "propiedades", "data"]
estado_revision: "revisado"
---

# Cambio Usuario Objeto Pref Propiedades Data

Listado de propiedades configurables del objeto, preseleccionadas según preferencias guardadas.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Para un `objeto` del catálogo de avisos, devuelve las propiedades vigilables (`CambioObjetoDatosCampos`)
con checkbox, condición y payload JSON (`cambio_prop`) de las ya guardadas. Preselecciona `id_ubi` para
roles PAU_CDC.

## Endpoint

- URL: `/src/cambios/cambio_usuario_objeto_pref_propiedades_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_objeto_pref_propiedades_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `objeto` | `string` | controller+application | Sí | Nombre del objeto |
| `id_item_usuario_objeto` | `integer` | controller+application | No | `0` = sin preferencias previas |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload en `data`:
  - `error` (`string`)
  - `objeto`, `id_item_usuario_objeto`
  - `propiedades` (`array`): `nom_prop`, `etiqueta`, `chk_prop`, `id_item`, `cambio_prop`, `condicion`

## Errores conocidos

- `Usuario no encontrado`
- `Usuario sin rol asignado`
- `objeto %s no encontrado`

## Permisos

- Filtrado implícito por rol del usuario de sesión (`PAU_CDC` preselecciona `id_ubi`).

## Casos De Uso

- `src\cambios\application\CambioUsuarioObjetoPrefPropiedadesData`

## Frontend Relacionado

- `frontend/cambios/controller/usuario_avisos_pref_propiedades.php`: fragmento HTML de la tabla de
  propiedades; `fnjs_actualizar_propiedades` en `usuario_avisos_pref`.
