---
id: "cambios.cambio_usuario_propiedad_pref_item_data"
tipo: "endpoint"
modulo: "cambios"
url: "/src/cambios/cambio_usuario_propiedad_pref_item_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/cambios/infrastructure/ui/http/controllers/cambio_usuario_propiedad_pref_item_data.php"
entrada: ["post.id_item:integer", "post.objeto:string", "post.propiedad:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/cambios/controller/usuario_avisos_pref_condicion.php"]
casos_uso: ["src\\cambios\\application\\CambioUsuarioPropiedadPrefItemData"]
tags: ["cambios", "cambio", "usuario", "propiedad", "pref", "item", "data"]
estado_revision: "generado"
---

# Cambio Usuario Propiedad Pref Item Data

Endpoint JSON: devuelve los datos de una condicion por `id_item` (si existe) y la lista de casas cuando la propiedad es `id_ubi`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/cambios/cambio_usuario_propiedad_pref_item_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_propiedad_pref_item_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `integer` | controller+application | No | controller+application |
| `objeto` | `string` | controller+application | No | controller+application |
| `propiedad` | `string` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Permisos

- Permiso oficina `des`
- Permiso oficina `vcsd`

## Casos De Uso

- `src\cambios\application\CambioUsuarioPropiedadPrefItemData`

## Frontend Relacionado

- `frontend/cambios/controller/usuario_avisos_pref_condicion.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.