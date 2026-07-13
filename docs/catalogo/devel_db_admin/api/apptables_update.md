---
id: "devel_db_admin.apptables_update"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/apptables_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/apptables_update.php"
entrada: ["post.accion:string", "post.esquema:string", "post.id_app:integer"]
entrada_obligatoria: ["id_app", "accion"]
respuesta: "standard_envelope_string_data"
requiere_hashb: true
errores: ["No hay aplicaciones configuradas en la sesión.", "Aplicación no válida.", "Acción no indicada.", "Debe elegir un esquema.", "Acción no reconocida.", "La aplicación %s no define clases DB para esta operación."]
frontend_referencias: ["frontend/devel_db_admin/controller/apptables.php", "frontend/devel_db_admin/controller/apptables_update.php"]
casos_uso: ["src\\devel_db_admin\\application\\ApptablesUpdate"]
tags: ["devel_db_admin", "apptables", "update"]
estado_revision: "revisado"
---

# Apptables Update

Crea, elimina o rellena tablas globales o de esquema invocando las clases `DB`/`DBEsquema` del módulo
de aplicación seleccionado.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Recibe `id_app`, `accion` y opcionalmente `esquema`. Resuelve el nombre de app desde
`$_SESSION['config']['a_apps']` y delega en `{app}\db\DB` o `{app}\db\DBEsquema` (legacy o `src\`).
Acciones: `crear_global`, `eliminar_global`, `crear_esquema`, `eliminar_esquema`, `llenar_esquema`.
Tras `crear_global` ejecuta verificación adicional (`ApptablesVerificarGlobal`).

## Endpoint

- URL: `/src/devel_db_admin/apptables_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/apptables_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_app` | `integer` | application | Si | Debe existir en sesión |
| `accion` | `string` | application | Si | Ver acciones arriba |
| `esquema` | `string` | application | Condicional | Obligatorio para acciones `*_esquema` |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload en `data`:
  - `ok` (`true`)
  - `mensaje` (`string`): texto de éxito traducido
  - `bases` (`list<string>`): BDs lógicas afectadas (`comun`, `sv`, `sv-e`, réplicas)
  - `replica` (`bool`): si `ReplicaSelectPolicy::incluirSelect()`
  - `verificado` (`array`): resultado de verificación global (solo `crear_global`)

## Errores conocidos

- `No hay aplicaciones configuradas en la sesión.`
- `Aplicación no válida.`
- `Acción no indicada.`
- `Debe elegir un esquema.`
- `Acción no reconocida.`
- `La aplicación %s no define clases DB para esta operación.`

## Permisos

- Sin control propio; `HashFront` en `apptables.php` firma el POST.

## Casos De Uso

- `src\devel_db_admin\application\ApptablesUpdate`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/apptables.php` (formulario)
- `frontend/devel_db_admin/controller/apptables_update.php` (proxy JSON con hash)
