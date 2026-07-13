---
id: "actividadcargos.pantalla.form_cargos_personas_en_actividad"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadcargos"
nombre: "Form Cargos Personas En Actividad"
controller: "frontend/actividadcargos/controller/form_cargos_personas_en_actividad.php"
vistas: ["frontend/actividadcargos/view/form_cargos_personas_en_actividad.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividadcargos/form_cargos_personas_en_actividad_data"]
capacidades: ["actividadcargos.form_cargos_personas_en_actividad.gestionar"]
campos: ["html.asis", "html.asis_presente", "html.guardar", "html.id_activ", "html.observ", "html.puede_agd"]
acciones: ["fnjs_cargos_pers_datos_ok", "fnjs_guardar_cargo_pers"]
estado_revision: "revisado"
---

# Form Cargos Personas En Actividad

Formulario dossier **1302**: alta o edición de un cargo de una persona en una actividad (vista por persona).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadcargos/controller/form_cargos_personas_en_actividad.php`

## Vistas Relacionadas

- `frontend/actividadcargos/view/form_cargos_personas_en_actividad.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadcargos/form_cargos_personas_en_actividad_data` — bootstrap del formulario
- `/src/actividadcargos/cargo_nuevo` — guardar en modo `nuevo` (`fnjs_guardar_cargo_pers`)
- `/src/actividadcargos/cargo_editar` — guardar en modo `editar` (`fnjs_guardar_cargo_pers`)

## Capacidades Relacionadas

- `actividadcargos.form_cargos_personas_en_actividad.gestionar`

## Campos Detectados

- `html.asis` — checkbox **¿asiste?** (solo en altas nuevas sin actividad fija)
- `html.asis_presente` — hidden de presencia del checkbox `asis`
- `html.guardar` — botón **guardar datos del cargo**
- `html.id_activ` — desplegable de actividad (altas) o nombre fijo (edición)
- `html.observ` — textarea observaciones
- `html.puede_agd` — checkbox **¿Puede ser agd?**
- Desplegable `id_cargo` (tipo de cargo)
- Hidden vía hash: `id_nom`, `id_item`, `mod`

## Acciones Detectadas

- `fnjs_cargos_pers_datos_ok` — validación cliente (`id_activ`, `id_nom`, `id_cargo` > 0)
- `fnjs_guardar_cargo_pers` — POST AJAX a `cargo_nuevo` o `cargo_editar` según `mod` (sin botón cancelar explícito; navegación atrás vía barra del dossier)

## Manual De Usuario

Pantalla revisada contra `frontend/actividadcargos/` y `FormCargosPersonasEnActividadData`.

El controller carga `form_cargos_personas_en_actividad_data` con el contexto del dossier persona (`id_pau` = `id_nom`). En modo `nuevo` sin fila seleccionada, el builder filtra actividades por `id_tipo` / `que_dl` del enlace de alta.

Flujo habitual:

1. Desde el widget **Relación de cargos** (dossier 1302), pulsar **añadir cargo de la dl** / **añadir cargo de otra dl** o **modificar cargo**.
2. El formulario muestra **Cargo de una actividad**:
   - **Actividad**: desplegable en altas o nombre fijo en edición.
   - **Cargo**, **¿Puede ser agd?**, **Observaciones** y, en altas sin actividad fija, **¿asiste?** (marcada por defecto).
3. **Guardar datos del cargo** valida y envía por AJAX. En éxito, vuelve al dossier padre.

No incluye botón **Cancelar** (a diferencia del form 3102); el usuario usa la navegación atrás del dossier.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde dossier de persona 1302; entrada habitual vía ficha de persona o listados de personas).
