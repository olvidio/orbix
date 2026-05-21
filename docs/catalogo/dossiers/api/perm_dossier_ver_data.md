---
id: "dossiers.perm_dossier_ver_data"
tipo: "endpoint"
modulo: "dossiers"
url: "/src/dossiers/perm_dossier_ver_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dossiers/infrastructure/ui/http/controllers/perm_dossier_ver_data.php"
entrada: ["post.id_tipo_dossier:mixed", "post.tipo:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/dossiers/controller/perm_dossier_ver.php"]
casos_uso: ["src\\dossiers\\application\\PermDossierVerFormData"]
tags: ["dossiers", "perm", "dossier", "ver", "data"]
estado_revision: "generado"
---

# Perm Dossier Ver Data

Formulario "permisos de acceso" para un tipo de dossier. El backend devuelve sólo datos: - `go_to_link_spec` ({path, query}) para que el frontend firme con HashFront. - `hash_config` (campos_form, campos_no, campos_hidden) para que el frontend componga el bloque hidden con HashFront; el valor de `go_to` dentro de `campos_hidden` se inyecta firmado en el borde del frontend. - `permiso_dossier_bit_map` + enteros `permiso_lectura` / `permiso_escritura`; el HTML de checkboxes lo genera el controlador frontend con {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/dossiers/perm_dossier_ver_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dossiers/infrastructure/ui/http/controllers/perm_dossier_ver_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_dossier` | `mixed` | controller | No | controller |
| `tipo` | `mixed` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Formulario "permisos de acceso" para un tipo de dossier.
- - `permiso_dossier_bit_map` + enteros `permiso_lectura` / `permiso_escritura`; el HTML de checkboxes lo genera el controlador frontend con {@see \frontend\shared\permisos\MenuPermisoMenuHtml}.

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