---
id: "dossiers.perm_dossier_ver_data"
tipo: "endpoint"
modulo: "dossiers"
url: "/src/dossiers/perm_dossier_ver_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dossiers/infrastructure/ui/http/controllers/perm_dossier_ver_data.php"
entrada: ["post.id_tipo_dossier:integer", "post.tipo:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "dossiers_PermDossierVerFormDataData"
respuesta_data: ["path:string, query: array<string, string>"]
requiere_hashb: false
frontend_referencias: ["frontend/dossiers/controller/perm_dossier_ver.php"]
casos_uso: ["src\\dossiers\\application\\PermDossierVerFormData"]
tags: ["dossiers", "perm", "dossier", "ver", "data"]
estado_revision: "generado"
---

# Perm Dossier Ver Data

Formulario "permisos de acceso" para un tipo de dossier.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/dossiers/perm_dossier_ver_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dossiers/infrastructure/ui/http/controllers/perm_dossier_ver_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_dossier` | `integer` | controller | No | controller |
| `tipo` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `dossiers_PermDossierVerFormDataData`):
  - `path` (`string, query: array<string, string>`)

## Efectos colaterales

- Formulario "permisos de acceso" para un tipo de dossier.

## Permisos

- Permiso oficina `admin_sv`
- Permiso oficina `admin_sf`

## Casos De Uso

- `src\dossiers\application\PermDossierVerFormData`

## Frontend Relacionado

- `frontend/dossiers/controller/perm_dossier_ver.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.