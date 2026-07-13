---
id: "dossiers.perm_dossier_ver_data"
tipo: "endpoint"
modulo: "dossiers"
url: "/src/dossiers/perm_dossier_ver_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/dossiers/infrastructure/ui/http/controllers/perm_dossier_ver_data.php"
entrada: ["post.id_tipo_dossier:integer", "post.tipo:string"]
entrada_obligatoria: ["id_tipo_dossier"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/dossiers/controller/perm_dossier_ver.php"]
casos_uso: ["src\\dossiers\\application\\PermDossierVerFormData"]
tags: ["dossiers", "perm", "dossier", "ver", "data"]
estado_revision: "revisado"
---

# Perm Dossier Ver Data

Construye los datos del formulario "permisos de acceso" de un tipo de dossier concreto.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Prepara el formulario de edición de un `TipoDossier` (pantalla de permisos): carga sus atributos
actuales (descripción, tablas, campo, tipo relacionado, `app`/`class`/`codigo`), las máscaras de
permiso de lectura/escritura y la configuración del bloque HashB del formulario. Emite las URLs de
guardar/eliminar y el `go_to_link_spec` de vuelta al listado. Marca `perm_admin`/`botones` según el
permiso de oficina del usuario. Si el `id_tipo_dossier` no existe, el caso de uso lanza una excepción.

## Endpoint

- URL: `/src/dossiers/perm_dossier_ver_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/dossiers/infrastructure/ui/http/controllers/perm_dossier_ver_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_dossier` | `integer` | controller | Si | Tipo de dossier a editar; si no existe, excepción |
| `tipo` | `string` | controller | No | Ámbito (`tabla_from`); se propaga al `go_to_link_spec` del listado |

El controller lee `id_tipo_dossier` y `tipo` de `$_POST` y llama a `PermDossierVerFormData::build()`.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace un segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- `data` es el payload del formulario, con claves como:
  - `hash_config` (`campos_form` / `campos_no` / `campos_hidden` con `campos_chk`)
  - `go_to_link_spec` (link spec de vuelta a `perm_dossiers.php`, se firma en el frontend)
  - `permiso_dossier_bit_map` (mapa etiquetado de bits `PermisoDossierBits::labeledMap()`)
  - `url_guardar` (`/src/dossiers/tipo_dossier_guardar`), `url_eliminar` (`/src/dossiers/tipo_dossier_eliminar`)
  - `txt_eliminar` (texto de confirmación)
  - `perm_admin` (bool), `botones` (`'1,2'` si es admin, `0` si no)
  - `id_tipo_dossier`, `descripcion`, `tabla_from`, `tabla_to`, `campo_to`, `id_tipo_dossier_rel`,
    `permiso_lectura`, `permiso_escritura`, `app`, `class`, `codigo`, `chk`

## Errores conocidos

- Si el `id_tipo_dossier` no existe, `PermDossierVerFormData::build()` lanza
  `RuntimeException("No se encuentra el dossier: <id>")` (no es un error de negocio del envelope).

## Permisos

- El caso de uso consulta `$_SESSION['oPerm']`: si el usuario tiene permiso de oficina `admin_sv`
  o `admin_sf` marca `perm_admin = true` y `botones = '1,2'` (habilita guardar/eliminar). En caso
  contrario el formulario se muestra en modo solo lectura. No inferir permisos adicionales.

## Casos De Uso

- `src\dossiers\application\PermDossierVerFormData`

## Frontend Relacionado

- `frontend/dossiers/controller/perm_dossier_ver.php` (firma `go_to_link_spec`, compone el bloque
  HashB y pinta `perm_dossier_pres.phtml`).
