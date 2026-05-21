---
id: "cambios.cambio_usuario_objeto_pref_eliminar"
tipo: "endpoint"
modulo: "cambios"
url: "/src/cambios/cambio_usuario_objeto_pref_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/cambios/infrastructure/ui/http/controllers/cambio_usuario_objeto_pref_eliminar.php"
entrada: ["post.id_item_usuario_objeto:integer", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "cambios_CambioUsuarioObjetoPrefEliminarData"
respuesta_data: ["error:string"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\cambios\\application\\CambioUsuarioObjetoPrefEliminar"]
tags: ["cambios", "cambio", "usuario", "objeto", "pref", "eliminar"]
estado_revision: "generado"
---

# Cambio Usuario Objeto Pref Eliminar

Endpoint JSON: elimina un `CambioUsuarioObjetoPref`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/cambios/cambio_usuario_objeto_pref_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_objeto_pref_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item_usuario_objeto` | `integer` | controller+application | No | controller+application |
| `sel` | `array` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `cambios_CambioUsuarioObjetoPrefEliminarData`):
  - `error` (`string`)

## Efectos colaterales

- Mutacion: elimina un `CambioUsuarioObjetoPref` por id.
- Sucesor de la rama `eliminar` del dispatcher legacy `apps/cambios/controller/usuario_avisos_pref_ajax.php`.

## Casos De Uso

- `src\cambios\application\CambioUsuarioObjetoPrefEliminar`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.