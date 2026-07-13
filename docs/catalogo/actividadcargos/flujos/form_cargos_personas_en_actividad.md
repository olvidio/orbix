---
id: "actividadcargos.form_cargos_personas_en_actividad.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadcargos"
nombre: "Flujo - Gestionar Form Cargos Personas En Actividad"
capacidad: "actividadcargos.form_cargos_personas_en_actividad.gestionar"
pantallas_principales: []
fragmentos: ["actividadcargos.pantalla.form_cargos_personas_en_actividad"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadcargos/form_cargos_personas_en_actividad_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Form Cargos Personas En Actividad

Bootstrap del formulario de cargo en la vista por persona (dossier 1302).

## Objetivo De Usuario

Gestionar los cargos de una persona en distintas actividades: el sistema carga el listado de actividades candidatas (en altas), valores del cargo en edición y URLs de mutación.

## Punto De Entrada

Controller `frontend/actividadcargos/controller/form_cargos_personas_en_actividad.php`, invocado desde el widget 1302 (enlaces **añadir cargo de la dl** / **añadir cargo de otra dl** o **modificar cargo**).

## Fragmentos O Pantallas Auxiliares

- `actividadcargos.pantalla.form_cargos_personas_en_actividad`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. El usuario abre el formulario desde el widget de relación de cargos de la persona.
2. El controller POSTea a `form_cargos_personas_en_actividad_data` con `id_pau` (persona), `sel`, `mod`, `que_dl`, `id_tipo` según el enlace de alta.
3. En modo `editar`, carga datos del `ActividadCargo` y fija actividad en solo lectura.
4. En modo `nuevo`, filtra actividades por tipo y delegación (`que_dl` vacío = otras delegaciones).
5. El front pinta desplegables y hash; el usuario completa y guarda vía `cargo_nuevo`/`cargo_editar`.

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
