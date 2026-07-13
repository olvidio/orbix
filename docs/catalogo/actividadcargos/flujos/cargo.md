---
id: "actividadcargos.cargo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadcargos"
nombre: "Flujo - Gestionar Cargo"
capacidad: "actividadcargos.cargo.gestionar"
pantallas_principales: []
fragmentos: ["actividadcargos.pantalla.select_cargos_de_actividad", "actividadcargos.pantalla.select_cargos_personas_en_actividad"]
acciones: ["crear", "eliminar"]
endpoints: ["/src/actividadcargos/cargo_eliminar", "/src/actividadcargos/cargo_nuevo"]
estado_revision: "revisado"
---

# Flujo - Gestionar Cargo

Alta y baja de cargos desde los widgets de relación (dossiers 3102 y 1302).

## Objetivo De Usuario

Ver, añadir y quitar cargos de personas en actividades. La consulta y los enlaces de alta se hacen en el widget; la alta concreta pasa por el formulario (`cargo_nuevo`); la baja es directa vía `cargo_eliminar`.

## Punto De Entrada

- Widget **Relación de cargos** en dossier **3102** (vista por actividad): `select_cargos_de_actividad.phtml`.
- Widget **Relación de cargos** en dossier **1302** (vista por persona): `select_cargos_personas_en_actividad.phtml`.

Ambos se cargan embebidos en `frontend/dossiers/controller/dossiers_ver.php`.

## Fragmentos O Pantallas Auxiliares

- `actividadcargos.pantalla.select_cargos_de_actividad`
- `actividadcargos.pantalla.select_cargos_personas_en_actividad`
- Formularios de alta: `form_cargos_de_actividad` (3102) y `form_cargos_personas_en_actividad` (1302), que invocan `cargo_nuevo`.

## Escenarios Inferidos

### Crear

Pasos propuestos:
1. Abrir el dossier de cargos (actividad o persona).
2. Pulsar el enlace **añadir …** del colectivo o tipo de actividad permitido.
3. Completar el formulario **Cargo de una actividad** (persona/actividad, tipo de cargo, AGD, observaciones; en altas, **¿asiste?** si aplica).
4. Pulsar **Guardar datos del cargo**.
5. Comprobar que la fila aparece en la relación de cargos (y en asistentes si marcó **¿asiste?**).

Endpoints asociados:
- `/src/actividadcargos/cargo_nuevo` (vía formulario; no se invoca directamente desde el widget)

### Eliminar

Pasos propuestos:
1. En la relación de cargos, marcar **una sola fila**.
2. Pulsar **quitar cargo**.
3. Leer el aviso de confirmación (puede indicar borrado del asistente si `des`/`vcsd` y tipo `s`/`sg`).
4. Confirmar.
5. El listado se refresca automáticamente (`fnjs_actualizar`).

Endpoints asociados:
- `/src/actividadcargos/cargo_eliminar`

## Campos Y Acciones Detectadas En Pantalla

Campos (widget):
- `modo_curso` (solo dossier 1302)
- `sel`, `mod`, `id_sel` (hidden)

Acciones JavaScript:
- `fnjs_mod_cargo`, `fnjs_borrar_cargo`, `fnjs_actualizar` (widgets)
- Enlaces de alta vía `_links_insert_bar.phtml`

## Endpoints Del Flujo

- `/src/actividadcargos/cargo_eliminar`
- `/src/actividadcargos/cargo_nuevo`

## Errores Conocidos

- `falta id_item`
- `faltan parametros id_activ / id_nom / id_cargo`
- `hay un error, no se ha eliminado`
- `hay un error, no se ha eliminado el asistente`
- `hay un error, no se ha guardado el asistente`
- `no encuentro el cargo`
- `ya existe este cargo para esta actividad`

## Ruta de menú

- sin entrada de menú en el índice (acceso vía dossiers 3102/1302; actividad: buscador p. ej. **Legacy:** vsm > ca > buscar ca · **Pills2:** ACTIVIDADES > Buscar actividad > ca n; persona: ficha de persona).
