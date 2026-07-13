---
id: "asistentes.form_asistentes_a_una_actividad_data"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/form_asistentes_a_una_actividad_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/asistentes/infrastructure/ui/http/controllers/form_asistentes_a_una_actividad_data.php"
entrada: ["post.id_activ:integer", "post.id_nom:integer", "post.id_pau:integer", "post.na:string", "post.obj_pau:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se encontró el asistente para esta actividad.", "los datos de asistencia los modifica el propietario de la plaza: %s"]
frontend_referencias: ["frontend/asistentes/controller/form_asistentes_a_una_actividad.php"]
casos_uso: ["src\\asistentes\\application\\FormAsistentesAUnaActividadData"]
tags: ["asistentes", "form", "a", "una", "actividad", "data"]
estado_revision: "revisado"
---

# Form Asistentes A Una Actividad Data

Dossier asistentes a una actividad (3101): datos del formulario alta/edición.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- `id_nom=0` → modo `nuevo`: desplegable de personas según `obj_pau` (`PersonaN`, `PersonaAgd`, …).
- `id_nom>0` → modo `editar`: carga asistente existente y bloquea si el propietario de plaza no es la DL actual.
- Integra opciones de plaza/propietario si `actividadplazas` está instalado.

## Endpoint

- URL: `/src/asistentes/form_asistentes_a_una_actividad_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/form_asistentes_a_una_actividad_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | No | Actividad; si 0 usa `id_pau` |
| `id_pau` | `integer` | application | No | Alias de `id_activ` |
| `id_nom` | `integer` | application | No | 0 = alta; >0 = edición |
| `sel` | `array` | application | No | Token `id_nom#...` alternativo |
| `obj_pau` | `string` | application | No | Clase persona en alta (`PersonaN`, …) |
| `na` | `string` | application | No | Para `PersonaEx` (`p`+`na`) |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload en `data` (o clave `error`):
  - `obj`, `id_activ`, `id_nom_real`, `ape_nom`, `propio_chk`, `falta_chk`, `est_chk`, `observ`, `observ_est`
  - `plazas_installed`, `plaza_opciones`, `plaza_selected`, `propietario_*`
  - `hash_main`, `paths` (`asistente_guardar`, `form_self`, `posibles_propietarios_data`)
  - `personas_opciones`, `personas_onchange` (solo alta)
  - `ajax_propietarios` (si plazas instaladas)

## Errores conocidos

- `error` HTML si persona no encontrada en global.
- `No se encontró el asistente para esta actividad.`
- `los datos de asistencia los modifica el propietario de la plaza: %s`

## Permisos

- Bloqueo por propietario de plaza en edición (no `perm_modificar` de entidad).
- Acceso al dossier: frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\asistentes\application\FormAsistentesAUnaActividadData`

## Frontend Relacionado

- `frontend/asistentes/controller/form_asistentes_a_una_actividad.php` +
  `FormAsistentesAUnaActividadRender`.
