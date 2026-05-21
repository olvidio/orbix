---
id: "cambios.cambio_usuario_propiedad_pref_preview"
tipo: "endpoint"
modulo: "cambios"
url: "/src/cambios/cambio_usuario_propiedad_pref_preview"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/cambios/infrastructure/ui/http/controllers/cambio_usuario_propiedad_pref_preview.php"
entrada: ["post.id_item:integer", "post.id_ubi:array", "post.objeto:string", "post.operador:string", "post.propiedad:string", "post.valor:string", "post.valor_new:string", "post.valor_old:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "cambios_CambioUsuarioPropiedadPrefPreviewData"
respuesta_data: ["error:string", "id_item:integer", "objeto:string", "propiedad:string", "condicion:string", "cambio_prop:string"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\cambios\\application\\CambioUsuarioPropiedadPrefPreview"]
tags: ["cambios", "cambio", "usuario", "propiedad", "pref", "preview"]
estado_revision: "generado"
---

# Cambio Usuario Propiedad Pref Preview

Endpoint JSON: construye el texto de preview de la condicion y el array serializado (cambio_prop) sin persistir nada.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/cambios/cambio_usuario_propiedad_pref_preview`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_propiedad_pref_preview.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `integer` | controller+application | No | controller+application |
| `id_ubi` | `array` | controller+application | No | controller+application |
| `objeto` | `string` | controller+application | No | controller+application |
| `operador` | `string` | controller+application | No | controller+application |
| `propiedad` | `string` | controller+application | No | controller+application |
| `valor` | `string` | controller+application | No | controller+application |
| `valor_new` | `string` | controller+application | No | controller+application |
| `valor_old` | `string` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `cambios_CambioUsuarioPropiedadPrefPreviewData`):
  - `error` (`string`)
  - `id_item` (`integer`)
  - `objeto` (`string`)
  - `propiedad` (`string`)
  - `condicion` (`string`)
  - `cambio_prop` (`string`)

## Casos De Uso

- `src\cambios\application\CambioUsuarioPropiedadPrefPreview`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.