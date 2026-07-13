---
id: "cambios.cambio_usuario_propiedad_pref_preview"
tipo: "endpoint"
modulo: "cambios"
url: "/src/cambios/cambio_usuario_propiedad_pref_preview"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/cambios/infrastructure/ui/http/controllers/cambio_usuario_propiedad_pref_preview.php"
entrada: ["post.id_item:integer", "post.id_ubi:array", "post.objeto:string", "post.operador:string", "post.propiedad:string", "post.valor:string", "post.valor_new:string", "post.valor_old:string"]
entrada_obligatoria: ["propiedad"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/cambios/controller/usuario_avisos_pref.php"]
casos_uso: ["src\\cambios\\application\\CambioUsuarioPropiedadPrefPreview"]
tags: ["cambios", "cambio", "usuario", "propiedad", "pref", "preview"]
estado_revision: "revisado"
---

# Cambio Usuario Propiedad Pref Preview

Calcula el texto de preview de una condición sin persistir (operación de lectura/cálculo).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Construye `condicion` (texto legible vía `getTextCambio`) y `cambio_prop` (JSON serializado) a partir
de los campos del modal. Si `propiedad=id_ubi`, concatena `id_ubi[]` en `valor`.

## Endpoint

- URL: `/src/cambios/cambio_usuario_propiedad_pref_preview`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_propiedad_pref_preview.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `propiedad` | `string` | controller+application | Sí | Campo vigilado |
| `objeto` | `string` | controller+application | No | |
| `id_item` | `integer` | controller+application | No | |
| `operador` | `string` | controller+application | No | |
| `valor` | `string` | controller+application | No | Sustituido por `id_ubi[]` si aplica |
| `valor_old` / `valor_new` | `string` | controller+application | No | Checkboxes del modal |
| `id_ubi` | `array` | controller+application | No | Solo si `propiedad=id_ubi` |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload en `data`:
  - `id_item`, `objeto`, `propiedad`
  - `condicion` (`string`): texto de la condición
  - `cambio_prop` (`string`): JSON con `iid_item`, `spropiedad`, `soperador`, `svalor`, `bvalor_old`, `bvalor_new`

## Permisos

- Sin control propio; invocado desde el modal de condición antes de grabar el conjunto.

## Casos De Uso

- `src\cambios\application\CambioUsuarioPropiedadPrefPreview`

## Frontend Relacionado

- `frontend/cambios/controller/usuario_avisos_pref.php`: `fnjs_guardar_cond` llama a
  `url_preview_cond` y actualiza la celda de condición en la tabla de propiedades.
