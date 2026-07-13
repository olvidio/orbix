---
id: "dossiers.tipo_dossier_eliminar"
tipo: "endpoint"
modulo: "dossiers"
url: "/src/dossiers/tipo_dossier_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dossiers/infrastructure/ui/http/controllers/tipo_dossier_eliminar.php"
entrada: ["post.id_tipo_dossier:integer"]
entrada_obligatoria: ["id_tipo_dossier"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["falta id_tipo_dossier", "No se encuentra el dossier: <id>", "Hay un error, no se ha eliminado."]
frontend_referencias: []
casos_uso: ["src\\dossiers\\application\\TipoDossierEliminar"]
tags: ["dossiers", "tipo", "dossier", "eliminar"]
estado_revision: "revisado"
---

# Tipo Dossier Eliminar

Elimina un `TipoDossier`. Sustituye al case `eliminar` del antiguo `apps/dossiers/controller/perm_dossier_update.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Borra la definición de un tipo de dossier localizado por `id_tipo_dossier`. Valida que el `id` sea
`> 0` y que el registro exista antes de eliminarlo; si el borrado falla en el repositorio, devuelve error.

## Endpoint

- URL: `/src/dossiers/tipo_dossier_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dossiers/infrastructure/ui/http/controllers/tipo_dossier_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_dossier` | `integer` | application | Si | Debe ser `> 0` y existir; si no, error |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Error de negocio: `success: false`, `mensaje` con el texto del error.

## Errores conocidos

- `falta id_tipo_dossier` (cuando `id_tipo_dossier <= 0`)
- `No se encuentra el dossier: <id>` (el `id_tipo_dossier` no existe)
- `Hay un error, no se ha eliminado.` (fallo al eliminar en el repositorio)

## Permisos

- El caso de uso no aplica un control de permisos propio. La autorización se resuelve en el frontend
  (`perm_dossier_ver.php`) y en `$_SESSION['oPerm']`: el botón de eliminar solo se habilita
  (`perm_admin`) si el usuario tiene permiso de oficina `admin_sv` o `admin_sf`
  (ver `PermDossierVerFormData`). No inferir permisos concretos aquí.

## Casos De Uso

- `src\dossiers\application\TipoDossierEliminar`

## Frontend Relacionado

- Invocado desde el formulario `perm_dossier_ver` (URL emitida en su payload como `url_eliminar`).
  No hay referencia literal a la URL en `frontend/`.
