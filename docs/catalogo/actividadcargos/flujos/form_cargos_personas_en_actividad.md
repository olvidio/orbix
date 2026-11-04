---
id: "actividadcargos.form_cargos_personas_en_actividad.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadcargos"
nombre: "Flujo - Abrir el formulario de cargo de una persona"
capacidad: "actividadcargos.form_cargos_personas_en_actividad.gestionar"
pantallas_principales: ["actividadcargos.pantalla.form_cargos_personas_en_actividad"]
fragmentos: ["actividadcargos.pantalla.form_cargos_personas_en_actividad"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadcargos/form_cargos_personas_en_actividad_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Form Cargos Personas En Actividad

Bootstrap del formulario de cargo en la vista por persona (dossier 1302).

## Objetivo De Usuario

Abrir el formulario para dar de alta o modificar un cargo de una persona en una actividad, con las actividades candidatas en las altas.

## Punto De Entrada

Relación de cargos de una persona: enlaces **añadir cargo de la dl**, **añadir cargo de otra dl** o **modificar cargo**.

## Fragmentos O Pantallas Auxiliares

- `actividadcargos.pantalla.form_cargos_personas_en_actividad`

## Escenarios Inferidos

### Abrir el formulario de cargo de una persona

Pasos propuestos:
1. Desde la relación de cargos de la persona, pulsar **añadir cargo de la dl**, **añadir cargo de otra dl** o seleccionar un cargo y pulsar **modificar cargo**.
2. Al modificar, aparecen los datos del cargo y la actividad no se puede cambiar.
3. Al añadir, el desplegable de actividades solo ofrece las del tipo elegido y de la propia dl (o de otras delegaciones, según el enlace).
4. Completar el formulario y guardar.

Endpoints asociados:
- `/src/actividadcargos/form_cargos_personas_en_actividad_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.asis`
- `html.asis_presente`
- `html.guardar`
- `html.id_activ`
- `html.observ`
- `html.puede_agd`

Acciones JavaScript:
- `fnjs_cargos_pers_datos_ok`
- `fnjs_guardar_cargo_pers`

## Endpoints Del Flujo

- `/src/actividadcargos/form_cargos_personas_en_actividad_data`

## Errores Conocidos

- `no encuentro el cargo` (edición)
- `actividad no encontrada`

## Ruta de menú

- sin entrada de menú en el índice (fragmento AJAX del dossier 1302; entrada habitual vía ficha de persona).
