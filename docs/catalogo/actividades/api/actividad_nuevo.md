---
id: "actividades.actividad_nuevo"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_nuevo"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_nuevo.php"
entrada: ["post.desc_activ:string", "post.dl_org:string", "post.f_fin:string", "post.f_ini:string", "post.h_fin:string", "post.h_ini:string", "post.id_repeticion:integer", "post.id_tarifa:integer", "post.id_tipo_activ:integer", "post.id_ubi:integer", "post.idioma:string", "post.inom_tipo_val:string", "post.lugar_esp:string", "post.nivel_stgr:string", "post.nom_activ:string", "post.num_asistentes:integer", "post.observ:string", "post.observ_material:string", "post.plazas:integer", "post.precio:mixed", "post.publicado:string", "post.status:integer", "post.tipo_horario:string"]
entrada_obligatoria: ["post.inom_tipo_val", "post.nom_activ", "post.f_ini", "post.f_fin", "post.status", "post.dl_org"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["debe seleccionar un tipo de actividad", "No puede crear una actividad que organiza una dl/r que ya usa aquinate", "sesión de permisos no disponible", "No tiene permiso para crear una actividad de este tipo", "debe llenar todos los campos que tengan un (*)", "tipo de actividad incorrecto", "hay un error, no se ha guardado", "hay un error, no se ha importado"]
frontend_referencias: ["frontend/actividades/view/_actividad_form.js.html.twig", "frontend/actividades/view/_calendario_form.js.html.twig"]
casos_uso: ["src\\actividades\\application\\ActividadNueva"]
tags: ["actividades", "actividad", "nuevo"]
estado_revision: "revisado"
---

# Actividad Nuevo

Crea una actividad nueva a partir del formulario de ficha (`actividad_ver` con
`mod=nuevo`, y tambien desde el formulario de calendario en planning).

Comportamiento segun `dl_org`:

- **`dl_org` = mi delegacion** (`ConfigGlobal::mi_delef(isfsv)`): alta en la tabla
  propia (`id_tabla='dl'`) con el `status` recibido.
- **`dl_org` distinta**: alta en la tabla de externas (`id_tabla='ex'`) con
  `publicado=true` y `status` forzado a `ACTUAL`, y ademas se registra en
  `Importada` (queda importada automaticamente). Si la dl organizadora ya tiene
  esquema en Orbix se rechaza con error (debe crearla esa dl y esta importarla).

Otras reglas del caso de uso (`ActividadNueva::actividadNueva`):

- `id_ubi` distinto de `0`/`1` limpia `lugar_esp`; `id_ubi=1` significa "lugar
  especial" y conserva `lugar_esp`; `id_ubi=0` es "sin determinar".
- Si `nivel_stgr=0` se calcula con `NivelStgrId::generarNivelStgr(id_tipo_activ)`.
- Si la app `actividadplazas` esta instalada, `plazas > 0` y la actividad es de
  la propia dl, crea/actualiza el registro de plazas de la dl.

## Endpoint

- URL: `/src/actividades/actividad_nuevo`
- Metodos registrados: `GET, POST` (solo lee POST)
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_nuevo.php`

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `inom_tipo_val` | `string` | Si | Solo se usa como guarda: si esta vacio responde error sin crear. Puede ser `'000'` (= sin especificar). |
| `id_tipo_activ` | `integer` | Si (efectivo) | Tipo completo de 6 digitos; lo compone el JS (`fnjs_guardar`) antes de enviar. |
| `nom_activ`, `f_ini`, `f_fin`, `status`, `dl_org` | varios | Si | Validados en el caso de uso ("campos con (*)"). |
| `id_ubi` / `lugar_esp` | `integer` / `string` | No | `id_ubi=1` ⇒ lugar especial (`lugar_esp`); `0` ⇒ sin determinar. |
| `h_ini`, `h_fin` | `string` | No | Formato `hh:mm`; vacio ⇒ null. |
| `precio` | `float` | No | Saneado `FILTER_SANITIZE_NUMBER_FLOAT`. |
| `id_tarifa` | `integer` | No | El caso de uso lo recibe como `tarifa`. |
| `nivel_stgr` | `integer` | No | `0` ⇒ se autogenera segun tipo. |
| `publicado` | `string` | No | Booleano (`true`/`false`); forzado a `true` si la dl no es la propia. |
| `desc_activ`, `observ`, `observ_material`, `num_asistentes`, `id_repeticion`, `plazas`, `idioma` | varios | No | Copia directa a la entidad. |
| `tipo_horario` | `string` | No | **Entrada muerta**: el controller la lee pero `ActividadNueva` no la usa (el formulario tampoco la envia). Herencia del dispatcher legacy. |

## Salida

- Helper: `ContestarJson::enviar`
- Exito: `success: true` (sin payload; el id nuevo que devuelve el caso de uso
  no se expone en la respuesta).
- Error: `success: false`, `mensaje` con el texto.

## Permisos

- Si la app `procesos` esta instalada: exige `$_SESSION['oPermActividades']`
  (`PermisosActividades`) y valida `getPermisoCrear(dl_propia)` para el tipo;
  si no, error "No tiene permiso para crear una actividad de este tipo".
- Sin `procesos`: no hay validacion de permisos en servidor (control en UI).

## Errores conocidos

- `debe seleccionar un tipo de actividad` (controller, `inom_tipo_val` vacio)
- `No puede crear una actividad que organiza una dl/r que ya usa aquinate`
- `sesión de permisos no disponible`
- `No tiene permiso para crear una actividad de este tipo`
- `debe llenar todos los campos que tengan un (*)`
- `tipo de actividad incorrecto`
- `hay un error, no se ha guardado: <detalle>` / `hay un error, no se ha importado: <detalle>`

## Casos De Uso

- `src\actividades\application\ActividadNueva`

## Frontend Relacionado

- `frontend/actividades/view/_actividad_form.js.html.twig` — `fnjs_guardar('nuevo')` (pantalla `actividad_ver`)
- `frontend/actividades/view/_calendario_form.js.html.twig` — mismo mapa de URLs desde el formulario de calendario (planning)

## Revision Manual

- Revisado jun 2026 (lectura de controller + `ActividadNueva`): semantica dl propia/externa,
  obligatorios, permisos y efectos (Importada, plazas) verificados.
- Hallazgo: `tipo_horario` es entrada muerta (ver tabla); el legacy original si lo guardaba.
- Pendiente: ejemplos reales de request/response.
