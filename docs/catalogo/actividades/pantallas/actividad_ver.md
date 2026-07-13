---
id: "actividades.pantalla.actividad_ver"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "actividades"
nombre: "Ficha de actividad (ver/editar/nueva/cambiar tipo)"
controller: "frontend/actividades/controller/actividad_ver.php"
vistas: ["frontend/actividades/view/actividad_form.html.twig", "frontend/actividades/view/_actividad_form_head.html.twig", "frontend/actividades/view/_actividad_form_body.html.twig", "frontend/actividades/view/_actividad_form_botones.html.twig", "frontend/actividades/view/_actividad_form.js.html.twig"]
fragmentos_frontend: ["frontend/actividades/controller/actividad_select_ubi.php", "frontend/dossiers/controller/dossiers_ver.php"]
endpoints: ["/src/actividades/actividad_ver_datos", "/src/actividades/actividad_status_labels_datos", "/src/actividades/actividad_nivel_stgr_default_datos", "/src/actividades/actividad_permiso_crear_datos", "/src/actividades/actividad_que_datos", "/src/actividades/actividad_fases_completadas_datos", "/src/actividades/actividad_nuevo", "/src/actividades/actividad_editar", "/src/actividades/actividad_cambiar_tipo", "/src/actividades/actividad_tipo_get"]
capacidades: ["actividades.actividad_ver.gestionar", "actividades.actividad_permiso_crear.gestionar", "actividades.actividad_status_labels.gestionar", "actividades.actividad_nivel_stgr_default.gestionar", "actividades.actividad_que.gestionar"]
campos: ["post.id_activ", "post.mod", "post.obj_pau", "post.refresh", "post.id_tipo_activ", "post.sasistentes", "post.sactividad", "post.sel", "form.nom_activ", "form.f_ini", "form.h_ini", "form.f_fin", "form.h_fin", "form.dl_org", "form.plazas", "form.id_ubi", "form.lugar_esp", "form.id_tarifa", "form.precio", "form.observ", "form.id_repeticion", "form.nivel_stgr", "form.publicado", "form.idioma", "form.status"]
acciones: ["fnjs_guardar", "fnjs_cambiar_ubi", "fnjs_generarNomActiv", "fnjs_asistentes", "fnjs_actividad", "fnjs_nom_tipo", "fnjs_act_id_activ", "fnjs_actualizar_nivel_stgr"]
estado_revision: "revisado"
---

# Ficha de actividad

Formulario de la **ficha de actividad**, con tres modos segun `mod` / `id_activ`:

- **editar** (`id_activ` presente): muestra los datos reales; cabecera con
  enlace a dossiers de la actividad; el boton "guardar cambios" solo aparece con
  permiso `modificar`.
- **nuevo** (`id_activ` ausente): cascada de tipo editable; con `procesos`
  comprueba el permiso de crear (propia dl y, si la oficina responsable no es la
  del usuario, dl externa) y fija el `status` inicial segun el proceso.
- **cambiar_tipo**: como editar pero con la cascada de tipo activa; al guardar
  el proceso se regenera y la actividad vuelve a empezar fases.

El controller frontend no usa `src\` directamente: pide entidad y desplegables a
`actividad_ver_datos`, etiquetas de status a `actividad_status_labels_datos`,
nivel STGR por defecto a `actividad_nivel_stgr_default_datos`, permisos de
crear a `actividad_permiso_crear_datos`, el bloque de tipo a
`actividad_que_datos` y prefilla fases con `actividad_fases_completadas_datos`
(helper `PrefillPermActividadesFases`).

## Tipo

- Subtipo: `pantalla_principal` (formulario completo en `#main`; tambien lo
  incrusta planning, por eso el contenedor se llama `exportar2`)
- Controller: `frontend/actividades/controller/actividad_ver.php`
- Vistas: `actividad_form.html.twig` + parciales `_actividad_form_*`

## Acciones (revisadas)

| Accion | Funcion JS | Llama a | Parametros |
|--------|-----------|---------|------------|
| Crear ficha | `fnjs_guardar('nuevo')` | `/src/actividades/actividad_nuevo` | formulario serializado; al exito resetea el formulario (se queda en la pagina) |
| Guardar cambios | `fnjs_guardar('editar')` | `/src/actividades/actividad_editar` | formulario serializado; al exito vuelve atras (`oPosicion`) |
| Guardar cambio de tipo | `fnjs_guardar('cambiar_tipo')` | `/src/actividades/actividad_cambiar_tipo` | idem editar; antes pide confirmacion y avisa de la vuelta a proyecto |
| Cambiar lugar | `fnjs_cambiar_ubi()` (span lugar) | `frontend/actividades/controller/actividad_select_ubi.php` (popup) | `dl_org`, `ssfsv`, `isfsv` |
| Generar nombre | `fnjs_generarNomActiv('#modifica')` | JS local (`scripts/selects.js.php`) | compone el nombre segun tipo/fechas |
| Ver dossiers | enlace cabecera (modo editar) | `frontend/dossiers/controller/dossiers_ver.php` | `pau=a`, `id_pau=id_activ`, `obj_pau` |
| Cascada de tipo (nuevo/cambiar_tipo) | `fnjs_asistentes/actividad/nom_tipo` | `/src/actividades/actividad_tipo_get` | ver ficha del endpoint |
| Tipo concretado (nuevo/cambiar_tipo) | `fnjs_act_id_activ()` | `actividad_tipo_get` (`salida=id_tarifa`) y, con `procesos`, recarga `actividad_ver.php?refresh=1` | `id_tipo_activ` compuesto |

## Validaciones en cliente (`fnjs_guardar`)

- Fechas `f_ini`/`f_fin` y horas `h_ini`/`h_fin` con formato valido.
- Tipo de actividad completo: ningun nivel puede quedar en `.`/`...`.
- `dl_org` (organiza) obligatorio.
- `cambiar_tipo`: confirmacion explicita del usuario.

## Permisos

- **editar**: corta (`die`) si el permiso actual es solo `ocupado`; el boton
  guardar requiere `have_perm_action('modificar')`. Con `procesos`, `Bdl`
  (incluir la propia dl en organiza) depende de `have_perm_activ('ver')`.
- **nuevo** con `procesos`: exige `getPermisoCrear` para el tipo (propia dl o
  externa segun oficina responsable); si no, corta con mensaje.
- El backend solo re-valida en `actividad_nuevo` (crear); editar y cambiar_tipo
  confian en este control de UI (ver fichas API).

## Manual De Usuario

Ver [`manual/actividades.md`](../../../manual/actividades.md), seccion
*Crear, Editar, Eliminar*.

## Ruta de menú

- **Legacy:** dre > actividades > nueva activ; Calendario > actividades > nueva activ.
- **Pills2:** Calendario > actividades > nueva activ; dre > actividades > nueva activ;
  ATENCIÓN SACD > Actividades > Nueva actividad; ACTIVIDADES > Buscar actividad >
  Nueva actividad.

También se abre al editar desde listados (`actividad_select`) o planning — sin
entrada de menú propia en esos casos.

## Revision Manual

- Revisado jun 2026 (lectura de controller + 5 plantillas + JS): subtipo corregido,
  modos, tabla de acciones, validaciones y mapa de endpoints documentados.
- **Hallazgo (regresion latente)**: `_actividad_form_body.html.twig` referencia
  `constant('actividades\\model\\value_objects\\StatusId::…')` en la rama
  `{% if not procesos_installed %}`; esa clase legacy ya no existe en el repo
  (la actual es `src\actividades\domain\value_objects\StatusId`), por lo que la
  ficha **fallaria sin la app `procesos` instalada**. No se ha tocado el codigo;
  pendiente de decision del usuario.
