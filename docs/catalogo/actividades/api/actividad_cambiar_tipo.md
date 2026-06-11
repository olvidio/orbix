---
id: "actividades.actividad_cambiar_tipo"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_cambiar_tipo"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_cambiar_tipo.php"
entrada: ["post.desc_activ:string", "post.dl_org:string", "post.f_fin:string", "post.f_ini:string", "post.h_fin:string", "post.h_ini:string", "post.iactividad_val:integer", "post.iasistentes_val:integer", "post.id_activ:integer", "post.id_repeticion:integer", "post.id_tarifa:integer", "post.id_tipo_activ:integer", "post.id_ubi:integer", "post.inom_tipo_val:string", "post.isfsv_val:integer", "post.lugar_esp:string", "post.nivel_stgr:integer", "post.nom_activ:string", "post.num_asistentes:integer", "post.observ:string", "post.observ_material:string", "post.plazas:integer", "post.precio:mixed", "post.status:integer"]
entrada_obligatoria: ["post.id_activ"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["debe seleccionar un tipo de actividad", "actividad no encontrada", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/actividades/view/_actividad_form.js.html.twig", "frontend/actividades/view/_calendario_form.js.html.twig"]
casos_uso: ["src\\actividades\\application\\ActividadCambiarTipo"]
tags: ["actividades", "actividad", "cambiar", "tipo"]
estado_revision: "revisado"
---

# Actividad Cambiar Tipo

Cambia el tipo de una actividad existente **de la propia dl** (usa
`ActividadDlRepository`) y guarda a la vez el resto de campos del formulario.
Si la app `procesos` esta instalada, regenera el proceso asociado con reset
(la actividad vuelve a empezar sus fases; la UI avisa de que "pasará a proyecto").

Resolucion del tipo: usa `id_tipo_activ` si llega no nulo; si no, concatena
`isfsv_val + iasistentes_val + iactividad_val + inom_tipo_val`; si el resultado
contiene `.` (nivel sin concretar) responde error `tipo`.

A diferencia de `actividad_editar`, **no** toca `publicado`/`idioma` ni propaga
plazas a actividadplazas.

## Endpoint

- URL: `/src/actividades/actividad_cambiar_tipo`
- Metodos registrados: `GET, POST` (solo lee POST)
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_cambiar_tipo.php`

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `id_activ` | `integer` | Si | Actividad a modificar (debe ser de la propia dl). |
| `id_tipo_activ` o (`isfsv_val`+`iasistentes_val`+`iactividad_val`+`inom_tipo_val`) | varios | Si | Tipo nuevo completo (6 digitos sin `.`). |
| `dl_org` | `string` | No | Se trunca en `#`; si no llega, se vacia en la entidad. |
| `status` | `integer` | No | Llega del hidden del formulario (con procesos el status lo gobiernan las fases). |
| resto (campos de la ficha) | varios | No | Copia directa a la entidad, como en editar. |

## Salida

- Helper: `ContestarJson::enviar`
- Exito: `success: true` (sin payload).
- Error: `success: false`, `mensaje` con el texto.

## Permisos

- **No valida permisos en servidor**; el control esta en la UI (la accion
  "cambiar tipo" se ofrece desde la ficha/listados segun permisos, y el JS pide
  confirmacion al usuario).

## Errores conocidos

- `debe seleccionar un tipo de actividad`
- `actividad no encontrada`
- `hay un error, no se ha guardado` + detalle

## Casos De Uso

- `src\actividades\application\ActividadCambiarTipo`

## Frontend Relacionado

- `frontend/actividades/view/_actividad_form.js.html.twig` — `fnjs_guardar('cambiar_tipo')`
  (confirm + aviso de vuelta a proyecto si hay procesos)
- `frontend/actividades/view/_calendario_form.js.html.twig` — mismo mapa de URLs (planning)

## Revision Manual

- Revisado jun 2026 (lectura de controller + `ActividadCambiarTipo`): regeneracion de
  proceso con reset, restriccion a la propia dl y diferencias con editar verificadas.
- Pendiente: ejemplos reales de request/response.
