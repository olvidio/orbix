---
id: "ubis.teleco_editar"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/teleco_editar"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/ubis/infrastructure/ui/http/controllers/teleco_editar.php"
entrada: ["post.obj_pau:string", "post.mod:string", "post.id_ubi:integer", "post.sel:string", "post.s_pkey:string"]
entrada_obligatoria: ["obj_pau", "id_ubi"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/teleco_editar.php"]
casos_uso: ["src\\ubis\\application\\TelecoEditarData"]
tags: ["ubis", "teleco", "editar"]
estado_revision: "revisado"
errores: []
---

# Teleco Editar

Carga el formulario de alta/edición de una telecomunicación de un ubi.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Carga el formulario de alta/edición de una telecomunicación de un ubi.

## Endpoint

- URL: `/src/ubis/teleco_editar`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/teleco_editar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `obj_pau` | `string` | application | Si | |
| `mod` | `string` | application | No | |
| `id_ubi` | `integer` | application | Si | |
| `sel` | `mixed` | application | No | |
| `s_pkey` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `obj`: clase repositorio teleco
  - `dl`: delegación ubi
  - `botones`: 1,3 o 0 según UbiPermisos
  - `id_tipo_teleco`: tipo seleccionado
  - `a_tipos`: tipos teleco
  - `a_desc`: descripciones

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

UbiPermisos.puedeModificarPorObjeto: controla botones.

## Casos De Uso

- `src\ubis\application\TelecoEditarData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/teleco_editar.php"]`).
