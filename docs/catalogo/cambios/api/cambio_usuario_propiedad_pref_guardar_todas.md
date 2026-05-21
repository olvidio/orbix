---
id: "cambios.cambio_usuario_propiedad_pref_guardar_todas"
tipo: "endpoint"
modulo: "cambios"
url: "/src/cambios/cambio_usuario_propiedad_pref_guardar_todas"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/cambios/infrastructure/ui/http/controllers/cambio_usuario_propiedad_pref_guardar_todas.php"
entrada: ["post.id_item_usuario_objeto_prop:integer", "post.objeto_prop:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "cambios_CambioUsuarioPropiedadPrefGuardarTodasData"
respuesta_data: ["error:string"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\cambios\\application\\CambioUsuarioPropiedadPrefGuardarTodas"]
tags: ["cambios", "cambio", "usuario", "propiedad", "pref", "guardar", "todas"]
estado_revision: "generado"
---

# Cambio Usuario Propiedad Pref Guardar Todas

Endpoint JSON: sincroniza las `CambioUsuarioPropiedadPref` para un `CambioUsuarioObjetoPref`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/cambios/cambio_usuario_propiedad_pref_guardar_todas`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_propiedad_pref_guardar_todas.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item_usuario_objeto_prop` | `integer` | application | No | application |
| `objeto_prop` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `cambios_CambioUsuarioPropiedadPrefGuardarTodasData`):
  - `error` (`string`)

## Efectos colaterales

- Mutacion: sincroniza las propiedades vigiladas (`CambioUsuarioPropiedadPref`) para un `CambioUsuarioObjetoPref`.
- Crea, actualiza o elimina segun la seleccion (`objeto[]`) y los metadatos (`id_cond`, `id_item`) presentes en el POST.

## Casos De Uso

- `src\cambios\application\CambioUsuarioPropiedadPrefGuardarTodas`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.