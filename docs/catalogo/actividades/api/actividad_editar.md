---
id: "actividades.actividad_editar"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_editar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_editar.php"
entrada: ["post.desc_activ:string", "post.dl_org:string", "post.f_fin:string", "post.f_ini:string", "post.h_fin:string", "post.h_ini:string", "post.iactividad_val:integer", "post.iasistentes_val:integer", "post.id_activ:integer", "post.id_repeticion:integer", "post.id_tarifa:integer", "post.id_tipo_activ:integer", "post.id_ubi:integer", "post.idioma:string", "post.inom_tipo_val:string", "post.isfsv_val:integer", "post.lugar_esp:string", "post.nivel_stgr:integer", "post.nom_activ:string", "post.num_asistentes:integer", "post.observ:string", "post.observ_material:string", "post.plazas:integer", "post.precio:mixed", "post.publicado:string", "post.status:integer"]
entrada_obligatoria: ["post.id_activ"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["sesión de permisos no disponible", "debe seleccionar un tipo de actividad", "actividad no encontrada", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/actividades/view/_actividad_form.js.html.twig", "frontend/actividades/view/_calendario_form.js.html.twig"]
casos_uso: ["src\\actividades\\application\\ActividadEditar"]
tags: ["actividades", "actividad", "editar"]
estado_revision: "revisado"
---

# Actividad Editar

Guarda la edicion de una actividad existente (`actividad_ver` con `mod=editar`,
boton "guardar cambios"). Sustituye el case `editar` del dispatcher legacy
`actividad_update.php`.

Resolucion del tipo (`ActividadEditar::execute`):

- Si `procesos` esta instalada **y** el usuario tiene permiso `crear`, el tipo se
  recompone con los desplegables `isfsv_val + iasistentes_val + iactividad_val +
  inom_tipo_val`; si la concatenacion contiene `.` (algun nivel sin concretar)
  responde error `tipo`.
- En otro caso se usa `id_tipo_activ` tal cual. Solo se aplica si el valor es
  menor que 100000 (guarda contra tipos malformados).

Efectos colaterales tras guardar:

- **procesos**: si `dl_org` cambia desde o hacia la propia dl, regenera el
  proceso de la actividad (`generarProceso`).
- **actividadplazas**: si `plazas` cambia, no es 0 y `dl_org` es la propia dl,
  propaga el valor al registro de plazas de la dl (salvo que la dl ya tuviera
  un valor propio distinto del antiguo total).

## Endpoint

- URL: `/src/actividades/actividad_editar`
- Metodos registrados: `GET, POST` (solo lee POST)
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_editar.php`

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `id_activ` | `integer` | Si | Id de la actividad a editar. |
| `isfsv_val`, `iasistentes_val`, `iactividad_val`, `inom_tipo_val` | varios | Condicional | Solo se usan con `procesos` + permiso `crear` (recomposicion del tipo). |
| `id_tipo_activ` | `integer` | Condicional | Usado cuando no se recompone desde los desplegables. |
| `dl_org` | `string` | No | Se trunca en `#` (formato `dl#extra` de algunos desplegables). Si no llega, se vacia en la entidad. |
| `id_ubi` / `lugar_esp` | `integer` / `string` | No | Misma semantica que en `actividad_nuevo` (`1` = lugar especial). |
| `publicado` | `string` | No | Booleano `is_true` (`t`/`true`/`1`...). |
| `idioma` | `string` | No | Vacio ⇒ null. |
| resto (`nom_activ`, fechas, horas, `precio`, `status`, `observ*`, `nivel_stgr`, `id_repeticion`, `id_tarifa`, `num_asistentes`, `plazas`, `desc_activ`) | varios | No | Copia directa a la entidad. |

## Salida

- Helper: `ContestarJson::enviar`
- Exito: `success: true` (sin payload).
- Error: `success: false`, `mensaje` con el texto (el `tipo_error: 'tipo'` interno
  no cambia la forma de la respuesta).

## Permisos

- Exige `$_SESSION['oPermActividades']` (`PermisosActividades`); sin ella responde error.
- **No valida el permiso `modificar` en servidor**: el control esta en la UI
  (el boton "guardar cambios" solo se pinta si `oPermActiv.have_perm_action('modificar')`,
  ver `_actividad_form_botones.html.twig`). El permiso `crear` solo se consulta
  para decidir como resolver el tipo.

## Errores conocidos

- `sesión de permisos no disponible`
- `debe seleccionar un tipo de actividad`
- `actividad no encontrada`
- `hay un error, no se ha guardado` + detalle

## Casos De Uso

- `src\actividades\application\ActividadEditar`

## Frontend Relacionado

- `frontend/actividades/view/_actividad_form.js.html.twig` — `fnjs_guardar('editar')`
- `frontend/actividades/view/_calendario_form.js.html.twig` — formulario de calendario (planning)

## Revision Manual

- Revisado jun 2026 (lectura de controller + `ActividadEditar`): recomposicion del tipo,
  efectos sobre procesos/plazas y ausencia de check `modificar` en servidor verificados.
- Pendiente: ejemplos reales de request/response.
