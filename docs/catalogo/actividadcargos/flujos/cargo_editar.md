---
id: "actividadcargos.cargo_editar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadcargos"
nombre: "Flujo - Gestionar Cargo Editar"
capacidad: "actividadcargos.cargo_editar.gestionar"
pantallas_principales: []
fragmentos: ["actividadcargos.pantalla.form_cargos_de_actividad", "actividadcargos.pantalla.form_cargos_personas_en_actividad"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadcargos/cargo_editar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Cargo Editar

Persistencia de cambios en un cargo existente (o alta implícita si `id_item` vacío en edición legacy).

## Objetivo De Usuario

Guardar cambios en un cargo existente: tipo de cargo, flag AGD, observaciones y, cuando el formulario incluye **¿asiste?**, sincronizar el registro de asistente (alta/baja).

## Punto De Entrada

- Formulario **Cargo de una actividad** abierto en modo `editar` desde **modificar cargo** en el widget de relación (dossier 3102 o 1302).
- Invocado al pulsar **Guardar datos del cargo** (`fnjs_guardar_cargo_act` / `fnjs_guardar_cargo_pers`) cuando `mod === 'editar'`.

## Fragmentos O Pantallas Auxiliares

- `actividadcargos.pantalla.form_cargos_de_actividad`
- `actividadcargos.pantalla.form_cargos_personas_en_actividad`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. En la relación de cargos, seleccionar una fila y pulsar **modificar cargo**.
2. Ajustar **Cargo**, **¿Puede ser agd?** u **Observaciones** (persona y actividad suelen venir fijas).
3. Si aparece **¿asiste?**, marcar o desmarcar según corresponda.
4. Pulsar **Guardar datos del cargo**.
5. En éxito, el panel se cierra y el listado refleja los cambios.

Endpoints asociados:
- `/src/actividadcargos/cargo_editar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `id_cargo`, `puede_agd`, `observ`
- Hidden: `id_item`, `id_activ`, `id_nom`, `mod`, `asis_presente`
- `asis` (si el form lo muestra)

Acciones JavaScript:
- `fnjs_guardar_cargo_act` (form 3102)
- `fnjs_guardar_cargo_pers` (form 1302)
- `fnjs_cargo_de_actividad_datos_ok` / `fnjs_cargos_pers_datos_ok` (validación previa)

## Endpoints Del Flujo

- `/src/actividadcargos/cargo_editar`

## Errores Conocidos

- `faltan parametros id_activ / id_nom / id_cargo`
- `hay un error, no se ha eliminado el asistente`
- `hay un error, no se ha guardado`
- `hay un error, no se ha guardado el asistente`
- `no encuentro el cargo`
- `ya existe este cargo para esta actividad`

## Ruta de menú

- sin entrada de menú en el índice (subflujo del formulario de cargo, accesible desde dossiers 3102/1302).
