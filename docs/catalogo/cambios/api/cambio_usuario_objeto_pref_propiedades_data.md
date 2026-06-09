---
id: "cambios.cambio_usuario_objeto_pref_propiedades_data"
tipo: "endpoint"
modulo: "cambios"
url: "/src/cambios/cambio_usuario_objeto_pref_propiedades_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/cambios/infrastructure/ui/http/controllers/cambio_usuario_objeto_pref_propiedades_data.php"
entrada: ["post.id_item_usuario_objeto:integer", "post.objeto:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/cambios/controller/usuario_avisos_pref_propiedades.php"]
casos_uso: ["src\\cambios\\application\\CambioUsuarioObjetoPrefPropiedadesData"]
tags: ["cambios", "cambio", "usuario", "objeto", "pref", "propiedades", "data"]
estado_revision: "generado"
---

# Cambio Usuario Objeto Pref Propiedades Data

Endpoint JSON: listado de propiedades configurables del objeto indicado, preseleccionadas segun las preferencias ya guardadas.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/cambios/cambio_usuario_objeto_pref_propiedades_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_objeto_pref_propiedades_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item_usuario_objeto` | `integer` | controller+application | No | controller+application |
| `objeto` | `string` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\cambios\application\CambioUsuarioObjetoPrefPropiedadesData`

## Frontend Relacionado

- `frontend/cambios/controller/usuario_avisos_pref_propiedades.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.