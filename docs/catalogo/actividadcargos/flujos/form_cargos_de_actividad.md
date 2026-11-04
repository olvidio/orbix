---
id: "actividadcargos.form_cargos_de_actividad.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadcargos"
nombre: "Flujo - Añadir o modificar un cargo en una actividad"
capacidad: "actividadcargos.form_cargos_de_actividad.gestionar"
pantallas_principales: ["actividadcargos.pantalla.form_cargos_de_actividad"]
fragmentos: ["actividadcargos.pantalla.form_cargos_de_actividad"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadcargos/form_cargos_de_actividad_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Form Cargos De Actividad

Bootstrap del formulario de cargo en la vista por actividad (dossier 3102).

## Objetivo De Usuario

Añadir o modificar un cargo de una persona en una actividad.

## Punto De Entrada

«Relación de cargos» de la actividad (botón **cargos** en la lista) o «Relación de asistentes» (botón **añadir cargo** o **modificar cargo**).

## Fragmentos O Pantallas Auxiliares

- `actividadcargos.pantalla.form_cargos_de_actividad`

## Escenarios Inferidos

### Añadir o modificar un cargo en una actividad

Pasos propuestos:
1. Pulsar **añadir n** / **añadir agd** en «Relación de cargos», o marcar a un asistente y pulsar **añadir cargo** / **modificar cargo**.
2. Se abre «Cargo de una actividad» con el nombre del asistente y los campos **Cargo** (desplegable, por ejemplo «d»), **¿Puede ser agd?** y **Observaciones**.
3. Pulsar **Guardar datos del cargo**, o **Cancelar** para salir sin guardar.

Endpoints asociados:
- `/src/actividadcargos/form_cargos_de_actividad_data`

### Ver los cargos de una actividad

Pasos propuestos:
1. Marcar la actividad en la lista y pulsar **cargos**.
2. Se abre «Relación de cargos» con el enlace **( dossiers )**; sin cargos indica «no hay ninguna fila».
3. Los botones **añadir n** y **añadir agd** abren el formulario de cargo.

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
