---
id: "configuracion.modulos_select_data"
tipo: "endpoint"
modulo: "configuracion"
url: "/src/configuracion/modulos_select_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/configuracion/infrastructure/ui/http/controllers/modulos_select_data.php"
entrada: ["post.id_sel:string", "post.restored_id_sel:string", "post.restored_scroll_id:string", "post.scroll_id:string", "post.stack:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/configuracion/controller/modulos_select.php"]
casos_uso: ["src\\configuracion\\application\\ModulosSelectData"]
tags: ["configuracion", "modulos", "select", "data"]
estado_revision: "revisado"
---

# Modulos Select Data

Construye los datos tabulares del listado de módulos (cabeceras, botones y filas) para
la pantalla `modulos_select`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lista todos los `Modulo` ordenados por nombre. Por cada módulo compone la fila con
nombre, descripción y las listas de módulos y aplicaciones requeridos (resolviendo los
ids contra los catálogos completos de módulos y apps). Además soporta la restauración
de selección/scroll al volver de un formulario: si llega `stack` con valor numérico
distinto de `0`, usa `restored_id_sel` / `restored_scroll_id` en lugar de `id_sel` /
`scroll_id` para reposicionar el listado.

## Endpoint

- URL: `/src/configuracion/modulos_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/configuracion/infrastructure/ui/http/controllers/modulos_select_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_sel` | `string` | application | No | Id a preseleccionar en el listado (clave `select` del payload) |
| `scroll_id` | `string` | application | No | Ancla de scroll a restaurar |
| `stack` | `string` | application | No | Si es numérico `!= 0`, activa la restauración desde `restored_*` |
| `restored_id_sel` | `string` | application | No | Selección restaurada al volver del form (solo si `stack` aplica) |
| `restored_scroll_id` | `string` | application | No | Scroll restaurado al volver del form (solo si `stack` aplica) |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- `data` es el payload del listado, con claves:
  - `a_cabeceras`: nombre, descripción, módulos requeridos, aplicaciones requeridas.
  - `a_botones`: acciones `modificar` (`fnjs_modificar`) y `eliminar` (`fnjs_eliminar`).
  - `a_valores`: filas `{sel: "<id_mod>#", 1: nom, 2: descripcion, 3: lista_mods, 4: lista_apps}`; opcionalmente `select` (`id_sel`) y `scroll_id`.
  - `hash_lista`: `campos_form` (`sel!mod`) / `campos_no` (`scroll_id!sel!refresh`).
  - `txt_eliminar`: texto de confirmación `¿Está seguro?`.
  - `txt_anadir_modulo`: etiqueta del botón de alta.

## Permisos

- El caso de uso no aplica control de permisos propio; la autorización de oficina se
  resuelve en el frontend (`modulos_select.php`) y en `$_SESSION['oPerm']`. No inferir
  permisos concretos aquí.

## Casos De Uso

- `src\configuracion\application\ModulosSelectData`

## Frontend Relacionado

- `frontend/configuracion/controller/modulos_select.php`
- `frontend/configuracion/helpers/ModulosSelectRender.php` (monta el HTML de hash del listado)
