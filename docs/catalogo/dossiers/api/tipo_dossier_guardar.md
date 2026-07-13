---
id: "dossiers.tipo_dossier_guardar"
tipo: "endpoint"
modulo: "dossiers"
url: "/src/dossiers/tipo_dossier_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dossiers/infrastructure/ui/http/controllers/tipo_dossier_guardar.php"
entrada: ["post.Permiso_escritura:array", "post.Permiso_lectura:array", "post.app:string", "post.campo_to:string", "post.class:string", "post.codigo:string", "post.depende_modificar:string", "post.descripcion:string", "post.id_tipo_dossier:integer", "post.id_tipo_dossier_rel:integer", "post.tabla_from:string", "post.tabla_to:string"]
entrada_obligatoria: ["id_tipo_dossier"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["falta id_tipo_dossier", "No se encuentra el dossier: <id>", "Hay un error, no se ha guardado."]
frontend_referencias: []
casos_uso: ["src\\dossiers\\application\\TipoDossierGuardar"]
tags: ["dossiers", "tipo", "dossier", "guardar"]
estado_revision: "revisado"
---

# Tipo Dossier Guardar

Guarda los cambios de un `TipoDossier` existente. Sustituye al case `guardar` del antiguo `apps/dossiers/controller/perm_dossier_update.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Actualiza la definición de un tipo de dossier ya existente (localizado por `id_tipo_dossier`): descripción, `tabla_from`/`tabla_to`/`campo_to`, tipo relacionado (`id_tipo_dossier_rel`), flag `depende_modificar`, `app`, `class`, `codigo` y las máscaras de bits de permisos (`Permiso_lectura`/`Permiso_escritura`). Fuerza `db = 1`. No crea tipos nuevos: si el `id_tipo_dossier` no corresponde a un registro existente, devuelve error.

Las máscaras de permiso llegan como arrays de bits (`Permiso_lectura[]`, `Permiso_escritura[]`); el caso de uso los suma para obtener el byte final y solo los actualiza si el array no viene vacío.

## Endpoint

- URL: `/src/dossiers/tipo_dossier_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dossiers/infrastructure/ui/http/controllers/tipo_dossier_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_dossier` | `integer` | application | Si | Debe ser `> 0` y existir; si no, error |
| `descripcion` | `string` | application | No | |
| `tabla_from` | `string` | application | No | Ámbito/tabla origen (p. ej. `p`, `a`, `u`) |
| `tabla_to` | `string` | application | No | |
| `campo_to` | `string` | application | No | |
| `id_tipo_dossier_rel` | `integer` | application | No | |
| `depende_modificar` | `string` | application | No | Se interpreta como booleano (`isTrue`) |
| `app` | `string` | application | No | |
| `class` | `string` | application | No | |
| `codigo` | `string` | application | No | Se guarda `null` si viene vacío tras `trim` |
| `Permiso_lectura` | `array` | application | No | Array de bits; se suman a un byte y solo se aplica si no viene vacío |
| `Permiso_escritura` | `array` | application | No | Array de bits; se suman a un byte y solo se aplica si no viene vacío |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Error de negocio: `success: false`, `mensaje` con el texto del error, `data: "ok"` (el controller pasa `'ok'` como `data` por defecto).

## Errores conocidos

- `falta id_tipo_dossier` (cuando `id_tipo_dossier <= 0`)
- `No se encuentra el dossier: <id>` (el `id_tipo_dossier` no existe)
- `Hay un error, no se ha guardado.` (fallo al persistir)

## Permisos

- El caso de uso no aplica un control de permisos propio. La autorización se resuelve en el frontend
  (`perm_dossier_ver.php`) y en `$_SESSION['oPerm']`: los botones de guardar/eliminar del formulario
  solo se habilitan (`perm_admin`) si el usuario tiene permiso de oficina `admin_sv` o `admin_sf`
  (ver `PermDossierVerFormData`). No inferir permisos concretos aquí.

## Casos De Uso

- `src\dossiers\application\TipoDossierGuardar`

## Frontend Relacionado

- Invocado desde el submit del formulario `perm_dossier_ver` (URL emitida en su payload como
  `url_guardar`). No hay referencia literal a la URL en `frontend/`.
