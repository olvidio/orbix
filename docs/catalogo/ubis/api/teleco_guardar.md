---
id: "ubis.teleco_guardar"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/teleco_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/teleco_guardar.php"
entrada: ["post.obj_pau:string", "post.id_ubi:integer", "post.id_tipo_teleco:integer", "post.id_desc_teleco:integer", "post.num_teleco:string", "post.observ:string", "post.sel:string", "post.s_pkey:string"]
entrada_obligatoria: ["obj_pau", "id_ubi"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_TelecoGuardarData"
respuesta_data: ["ok:true"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\ubis\\application\\TelecoGuardar"]
tags: ["ubis", "teleco", "guardar"]
estado_revision: "revisado"
errores: ["No se encuentra teleco id %s"]
---

# Teleco Guardar

Crea o actualiza un registro de telecomunicación asociado a un ubi.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Crea o actualiza un registro de telecomunicación asociado a un ubi.

## Endpoint

- URL: `/src/ubis/teleco_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/teleco_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `obj_pau` | `string` | application | Si | |
| `id_ubi` | `integer` | application | Si | |
| `id_tipo_teleco` | `integer` | application | No | |
| `id_desc_teleco` | `integer` | application | No | |
| `num_teleco` | `string` | application | No | |
| `observ` | `string` | application | No | |
| `sel` | `mixed` | application | No | |
| `s_pkey` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: payload en `data`:
  - `ok`: 1

## Errores conocidos
- `No se encuentra teleco id %s`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `UbiPermisos` (`puedeModificarPorObjeto`, `dlPerteneceAMiDelegacion`), `have_perm_oficina(scdl|scl|vcsd|des|admin_sv)` y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\ubis\application\TelecoGuardar`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`[]`).
