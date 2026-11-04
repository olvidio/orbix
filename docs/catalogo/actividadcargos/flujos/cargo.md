---
id: "actividadcargos.cargo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadcargos"
nombre: "Flujo - Añadir un cargo"
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

Ver, añadir y quitar cargos de personas en actividades. Se consulta y se pulsa el enlace de alta en la relación de cargos; el alta se hace en un formulario y la baja es directa.

## Punto De Entrada

Relación de cargos del dossier de una actividad (vista por actividad) o de una persona (vista por persona).

## Fragmentos O Pantallas Auxiliares

- `actividadcargos.pantalla.select_cargos_de_actividad`
- `actividadcargos.pantalla.select_cargos_personas_en_actividad`
- Formularios de alta: `form_cargos_de_actividad` (3102) y `form_cargos_personas_en_actividad` (1302), que invocan `cargo_nuevo`.

## Escenarios Inferidos

### Añadir un cargo

Pasos propuestos:
1. Abrir el dossier de cargos, de la actividad o de la persona.
2. Pulsar el enlace **añadir …** del colectivo o tipo de actividad que corresponda.
3. Completar el formulario **Cargo de una actividad**: persona o actividad, tipo de cargo, agd, observaciones y, en las altas, **¿asiste?** si procede.
4. Pulsar **Guardar datos del cargo**.
5. Comprobar que la fila aparece en la relación de cargos (y en asistentes, si se marcó **¿asiste?**).

Endpoints asociados:
- `/src/actividadcargos/cargo_eliminar`
- `/src/actividadcargos/cargo_nuevo`

### Quitar un cargo

Pasos propuestos:
1. En la relación de cargos, marcar **una sola fila**.
2. Pulsar **quitar cargo**.
3. Leer el aviso de confirmación; puede avisar de que también se borra al asistente si se trata del tipo s o sg y el usuario es de des o vcsd.
4. Confirmar. El listado se actualiza solo.

Endpoints asociados:
- `/src/actividadcargos/cargo_eliminar`
- `/src/actividadcargos/cargo_nuevo`

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
