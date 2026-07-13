---
id: "actividadcargos.form_cargos_de_actividad.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadcargos"
nombre: "Flujo - Gestionar Form Cargos De Actividad"
capacidad: "actividadcargos.form_cargos_de_actividad.gestionar"
pantallas_principales: []
fragmentos: ["actividadcargos.pantalla.form_cargos_de_actividad"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadcargos/form_cargos_de_actividad_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Form Cargos De Actividad

Bootstrap del formulario de cargo en la vista por actividad (dossier 3102).

## Objetivo De Usuario

Asignar o editar el cargo de una persona en una actividad: el sistema carga desplegables, valores actuales, hash de campos y URLs de mutación antes de mostrar el formulario.

## Punto De Entrada

Controller `frontend/actividadcargos/controller/form_cargos_de_actividad.php`, invocado desde el widget 3102 (enlace **añadir …** o botón **modificar cargo**) vía `fnjs_enviar_formulario` al bloque del dossier.

## Fragmentos O Pantallas Auxiliares

- `actividadcargos.pantalla.form_cargos_de_actividad`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. El usuario abre el formulario desde el widget de relación de cargos (modo `nuevo` o `editar`).
2. El controller POSTea a `form_cargos_de_actividad_data` con contexto del dossier (`pau`, `id_pau`, `obj_pau`, `sel`, `mod`, `permiso`).
3. El backend devuelve payload con desplegables (`personas_select`, `cargos_select`), valores (`observ`, `chk`), flags (`show_asis`, `id_nom_real`) y `hash_form_config`.
4. El front compone HTML de desplegables y hash; pinta el formulario en el bloque AJAX.
5. Si falta contexto válido, puede devolver `redir: go_atras` o `error`.

Endpoints asociados:
- `/src/actividadcargos/form_cargos_de_actividad_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.asis`
- `html.asis_presente`
- `html.cancel`
- `html.guardar`
- `html.observ`
- `html.puede_agd`

Acciones JavaScript:
- `fnjs_cancelar`
- `fnjs_cargo_de_actividad_datos_ok`
- `fnjs_guardar_cargo_act`

## Endpoints Del Flujo

- `/src/actividadcargos/form_cargos_de_actividad_data`

## Errores Conocidos

- `no encuentro el cargo` (edición con `sel` inválido)
- Mensajes HTML de persona no encontrada (`No encuentro a nadie con id_nom: …`)
- `redir: go_atras` cuando falta `obj_pau` en altas

## Ruta de menú

- sin entrada de menú en el índice (fragmento AJAX del dossier 3102; entrada habitual vía ficha de actividad).
