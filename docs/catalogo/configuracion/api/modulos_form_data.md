---
id: "configuracion.modulos_form_data"
tipo: "endpoint"
modulo: "configuracion"
url: "/src/configuracion/modulos_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/configuracion/infrastructure/ui/http/controllers/modulos_form_data.php"
entrada: ["post.id_mod:integer", "post.mod:string", "post.sel:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/configuracion/controller/modulos_form.php"]
casos_uso: ["src\\configuracion\\application\\ModulosFormData"]
tags: ["configuracion", "modulos", "form", "data"]
estado_revision: "revisado"
---

# Modulos Form Data

Construye los datos del formulario de alta/edición de un `Modulo` para la pantalla
`modulos_form`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Prepara el formulario según el modo:

- **Alta** (`mod = "nuevo"`): devuelve campos vacíos y los catálogos completos de módulos
  y aplicaciones disponibles.
- **Edición** (resto): resuelve el `id_mod` (del token `sel[0]` antes de `#`, o del campo
  `id_mod`), carga el `Modulo` y precarga nombre, descripción y los ids de módulos
  (`a_mods_req`) y aplicaciones (`a_apps_req`) requeridos, más las apps derivadas de los
  módulos requeridos (`a_apps_mod`).

## Endpoint

- URL: `/src/configuracion/modulos_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/configuracion/infrastructure/ui/http/controllers/modulos_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `mod` | `string` | application | No | `nuevo` fuerza modo alta; cualquier otro valor → edición |
| `sel` | `mixed` | application | No | Array del listado; se toma `sel[0]` y su token antes de `#` como `id_mod` |
| `id_mod` | `integer` | application | No | Id del módulo a editar cuando no llega por `sel` |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- `data` es el payload del formulario, con claves:
  - `hash_main`: `campos_form` (`nom!descripcion!`), `campos_no` (`sel_mods!sel_apps`) y `campos_hidden` (`campos_chk`, `id_mod`, `mod`).
  - `hash_actualizar`: `campos_no` (`refresh`) y `campos_hidden` (`id_mod`).
  - `id_mod`, `nom`, `descripcion`: valores actuales del módulo (vacíos en alta).
  - `a_mods_todos`, `a_apps_todas`: catálogos completos disponibles.
  - `a_mods_req`, `a_apps_req`: ids requeridos por el módulo editado.
  - `a_apps_mod`: apps derivadas (unión única) de los módulos requeridos.

## Permisos

- El caso de uso no aplica control de permisos propio; la autorización de oficina se
  resuelve en el frontend (`modulos_form.php`) y en `$_SESSION['oPerm']`. No inferir
  permisos concretos aquí.

## Casos De Uso

- `src\configuracion\application\ModulosFormData`

## Frontend Relacionado

- `frontend/configuracion/controller/modulos_form.php`
- `frontend/configuracion/helpers/ModulosFormRender.php` (monta el HTML de hash del formulario)
