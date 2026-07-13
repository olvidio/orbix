---
id: "actividadcargos.pantalla.form_cargos_de_actividad"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadcargos"
nombre: "Form Cargos De Actividad"
controller: "frontend/actividadcargos/controller/form_cargos_de_actividad.php"
vistas: ["frontend/actividadcargos/view/form_cargos_de_actividad.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividadcargos/form_cargos_de_actividad_data"]
capacidades: ["actividadcargos.form_cargos_de_actividad.gestionar"]
campos: ["html.asis", "html.asis_presente", "html.cancel", "html.guardar", "html.observ", "html.puede_agd"]
acciones: ["fnjs_cancelar", "fnjs_cargo_de_actividad_datos_ok", "fnjs_guardar_cargo_act"]
estado_revision: "revisado"
---

# Form Cargos De Actividad

Formulario dossier **3102** (y **3101** con persona fija): alta o edición del cargo de una persona en una actividad.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadcargos/controller/form_cargos_de_actividad.php`

## Vistas Relacionadas

- `frontend/actividadcargos/view/form_cargos_de_actividad.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadcargos/form_cargos_de_actividad_data` — bootstrap del formulario (payload con desplegables, hash y URLs de mutación)
- `/src/actividadcargos/cargo_nuevo` — guardar en modo `nuevo` (`fnjs_guardar_cargo_act`)
- `/src/actividadcargos/cargo_editar` — guardar en modo `editar` (`fnjs_guardar_cargo_act`)

## Capacidades Relacionadas

- `actividadcargos.form_cargos_de_actividad.gestionar`

## Campos Detectados

- `html.asis` — checkbox **¿asiste?** (solo en altas nuevas sin persona fija)
- `html.asis_presente` — hidden que marca presencia del checkbox (evita ambigüedad al desmarcar)
- `html.cancel` — botón **cancelar**
- `html.guardar` — botón **guardar datos del cargo**
- `html.observ` — textarea observaciones
- `html.puede_agd` — checkbox **¿Puede ser agd?**
- Desplegables generados en servidor: `id_nom` (persona), `id_cargo` (tipo de cargo)
- Hidden vía hash: `id_activ`, `id_item`, `mod`, `obj_pau`, `permiso`

## Acciones Detectadas

- `fnjs_cancelar` — vuelve al dossier padre sin guardar
- `fnjs_cargo_de_actividad_datos_ok` — validación cliente (`id_activ`, `id_nom`, `id_cargo` > 0)
- `fnjs_guardar_cargo_act` — POST AJAX a `cargo_nuevo` o `cargo_editar` según `mod`

## Manual De Usuario

Pantalla revisada contra `frontend/actividadcargos/` y `FormCargosDeActividadData`.

El controller carga `form_cargos_de_actividad_data` con el payload del dossier (`pau`, `id_pau`, `obj_pau`, `sel`, `mod`). Si devuelve `redir: go_atras`, navega hacia atrás. El front compone desplegables de personas y cargos con `FormCargosDeActividadHashCompose`.

Flujo habitual:

1. Desde el widget **Relación de cargos** (dossier 3102), pulsar un enlace **añadir …** (modo `nuevo`) o **modificar cargo** (modo `editar` con fila seleccionada).
2. El formulario muestra **Cargo de una actividad**:
   - **Asistente**: desplegable en altas (según `obj_pau` / colectivo) o nombre fijo en edición.
   - **Cargo**: desplegable con todos los cargos del catálogo (`xd_orden_cargo`).
   - **¿Puede ser agd?**, **Observaciones** y, en altas nuevas sin persona fija, **¿asiste?** (marcada por defecto).
3. **Guardar datos del cargo** valida en cliente y envía el formulario serializado. En éxito, `jsNavAtrasToDossiersParent()` cierra el panel.
4. **Cancelar** vuelve al dossier sin persistir.

Si **¿asiste?** está marcado al guardar en modo `nuevo`, el backend crea también el registro de asistente (`ActividadCargoNuevo`).

## Ruta de menú

- sin entrada de menú en el índice (acceso desde dossier de actividad 3102, enlace **añadir …** o **modificar cargo**; entrada habitual vía buscador de actividades, p. ej. **Legacy:** vsm > ca > buscar ca · **Pills2:** ACTIVIDADES > Buscar actividad > ca n).
