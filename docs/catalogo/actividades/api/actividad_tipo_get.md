---
id: "actividades.actividad_tipo_get"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_tipo_get"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_tipo_get.php"
entrada: ["post.salida:string", "post.entrada:string", "post.extendida:string", "post.modo:string", "post.opcion_sel:string", "post.isfsv:integer", "post.ssfsv:string"]
entrada_obligatoria: ["post.salida"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividades_ActividadTipoGetData"
respuesta_data: ["id:string", "opciones:array<int|string,string>", "selected:string", "blanco:bool", "val_blanco:string", "action:string", "content:string"]
requiere_hashb: false
errores: ["opción no definida: salida=<valor>"]
frontend_referencias: ["frontend/actividades/view/_actividad_tipo.js.html.twig", "frontend/actividades/view/_actividad_tipo_gestion.js.html.twig", "frontend/actividades/view/actividad_que.html.twig", "frontend/actividades/controller/actividad_select_ubi.php", "frontend/actividades/view/actividad_select_ubi.phtml", "frontend/pasarela/controller/nombre_form.php", "frontend/procesos/controller/fases_activ_cambio.php"]
casos_uso: ["src\\actividades\\application\\ActividadTipoGetActividad", "src\\actividades\\application\\ActividadTipoGetAsistentes", "src\\actividades\\application\\ActividadTipoGetDlOrg", "src\\actividades\\application\\ActividadTipoGetFiltroLugar", "src\\actividades\\application\\ActividadTipoGetIdTarifa", "src\\actividades\\application\\ActividadTipoGetLugar", "src\\actividades\\application\\ActividadTipoGetNivelStgrDefecto", "src\\actividades\\application\\ActividadTipoGetNomTipo", "src\\actividades\\application\\ActividadTipoGetNomTipoTabla"]
tags: ["actividades", "actividad", "tipo", "get"]
estado_revision: "revisado"
---

# Actividad Tipo Get

Endpoint multiplexado de la **cascada del selector de tipo de actividad** y de
algunos valores derivados. El parametro `salida` selecciona el caso de uso; el
parametro `entrada` lleva el prefijo del id de tipo acumulado hasta ese nivel
(los niveles sin concretar se rellenan con `.` como comodin).

## Salidas tipo desplegable (payload estructurado en `data`)

`data = {id, opciones, selected, blanco, val_blanco?, action?}`; el frontend
construye el `<select>` con `fnjs_construir_desplegable`.

| `salida` | `entrada` | Devuelve | Notas |
|----------|-----------|----------|-------|
| `asistentes` | `isfsv` (1 digito) | asistentes posibles (`iasistentes_val`) | `blanco=true` solo con perm oficina `des` o `calendario`. |
| `actividad` | `isfsv+iasistentes` | actividades posibles (`iactividad_val`) | `extendida='t'` ⇒ codigos de 2 digitos (blanco `..`). |
| `nom_tipo` | `isfsv+iasistentes+iactividad` | tipos posibles (`inom_tipo_val`) | `modo='buscar'` ⇒ onchange `fnjs_id_activ()`; otro ⇒ `fnjs_act_id_activ()`. |
| `lugar` | `filtro_lugar` (dl o region) | casas posibles (`id_ubi`) | Usa tambien `isfsv`, `ssfsv`, `opcion_sel`. |
| `dl_org` | `sfsv` | delegaciones y regiones organizadoras | `selected` = mi delegacion. |
| `filtro_lugar` | `sfsv` | delegaciones + regiones para filtrar lugar | onchange `fnjs_lugar()`. |

## Salidas tipo contenido (`data = {content: string}`)

| `salida` | `entrada` | `content` |
|----------|-----------|-----------|
| `nom_tipo_tabla` | prefijo 4 digitos | tabla HTML (id, nombre) de los tipos posibles. |
| `id_tarifa` | `id_tipo_activ` completo | id de la primera tarifa asociada al tipo (orden `id_serie`) o `''`. |
| `nivel_stgr_defecto` | `id_tipo_activ` completo | nivel STGR por defecto (misma regla que `actividad_nivel_stgr_default_datos`). |

Cualquier otro valor de `salida` responde error `opción no definida: salida=…`.

## Endpoint

- URL: `/src/actividades/actividad_tipo_get`
- Metodos registrados: `GET, POST` (solo lee POST)
- Operacion: `consulta` (sin efectos)
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_tipo_get.php`

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Permisos

- No exige permiso para llamar; en `salida=asistentes` los permisos de oficina
  de la sesion solo deciden si hay opcion en blanco.

## Casos De Uso

- `src\actividades\application\ActividadTipoGet*` (uno por salida; ver frontmatter)

## Frontend Relacionado

- `frontend/actividades/view/_actividad_tipo.js.html.twig` — cascada
  (`fnjs_asistentes`, `fnjs_actividad`, `fnjs_nom_tipo`, `fnjs_id_activ`,
  `fnjs_act_id_activ`, `fnjs_actualizar_nivel_stgr`)
- `frontend/actividades/view/_actividad_tipo_gestion.js.html.twig` — variante gestion
- `frontend/actividades/view/actividad_que.html.twig` — `fnjs_lugar()`
- `frontend/actividades/controller/actividad_select_ubi.php` + `actividad_select_ubi.phtml`
- `frontend/pasarela/controller/nombre_form.php`
- `frontend/procesos/controller/fases_activ_cambio.php`

## Revision Manual

- Revisado jun 2026 (lectura del controller multiplexado y los 9 casos de uso):
  tabla de salidas, forma de cada payload y semantica de `entrada` (comodin `.`)
  verificadas.
- Nota historica: en el legacy el case `id_tarifa` salia sin emitir respuesta;
  el comportamiento se corrigio en la migracion (ahora devuelve `content`).
- Pendiente: ejemplos reales de request/response.
