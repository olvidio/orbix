---
id: "procesos.actividad_que_fases_ajax"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/actividad_que_fases_ajax"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/procesos/infrastructure/ui/http/controllers/actividad_que_fases_ajax.php"
entrada: ["post.dl_propia:string", "post.id_tipo_activ:string", "post.selected:string"]
entrada_obligatoria: ["id_tipo_activ"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/actividades/controller/actividad_que.php"]
casos_uso: ["src\\procesos\\application\\ActividadQueFasesCuadro"]
tags: ["procesos", "actividad", "que", "fases", "ajax"]
estado_revision: "revisado"
---

# Actividad Que Fases Ajax

Fases aplicables al tipo de actividad para los checkboxes `fases_on` / `fases_off`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Dado un `id_tipo_activ` y si la actividad es de delegación propia (`dl_propia`), devuelve las
fases de los procesos asociados al tipo, marcando como `checked` las que vienen en `selected`.

## Endpoint

- URL: `/src/procesos/actividad_que_fases_ajax`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/procesos/infrastructure/ui/http/controllers/actividad_que_fases_ajax.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `string` | controller | Si | Tipo de actividad (p. ej. `1xxxxx`) |
| `dl_propia` | `string` | controller | No | `t`/`f`; delegación propia vs ajena |
| `selected` | `string` | controller | No | CSV de `id_fase` ya marcados (`1,2,3`) |

El controller parsea `selected` como lista de enteros antes de llamar al caso de uso.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Claves en `data` (doble `JSON.parse`):
  - `a_fases` (`list`): cada elemento con `id` (`int`), `nom` (`string`), `checked` (`bool`)

## Errores conocidos

- _(ninguno documentado en el caso de uso)_

## Permisos

- Sin control de permisos propio; autorización en `actividad_que.php` y `$_SESSION['oPerm']`.

## Casos De Uso

- `src\procesos\application\ActividadQueFasesCuadro`

## Frontend Relacionado

- `frontend/actividades/controller/actividad_que.php` (URL `url_actualizar_fases` al cambiar tipo/DL)
