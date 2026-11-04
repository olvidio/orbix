---
id: "asistentes.form_actividades_de_una_persona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Anotar la asistencia de una persona a una actividad"
capacidad: "asistentes.form_actividades_de_una_persona.gestionar"
pantallas_principales: ["asistentes.pantalla.form_actividades_de_una_persona"]
fragmentos: ["asistentes.pantalla.form_actividades_de_una_persona"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/form_actividades_de_una_persona_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Form Actividades De Una Persona

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Añadir o modificar la asistencia de una persona a una actividad desde su ficha.

## Punto De Entrada

Ficha de la persona > dossier de actividades.

## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.form_actividades_de_una_persona`

## Escenarios Inferidos

### Anotar la asistencia de una persona a una actividad

Pasos propuestos:
1. Abrir el dossier de actividades de la persona.
2. Elegir la **actividad** (obligatoria: «debe indicar una actividad»), la **plaza**, quién es **propiedad de** la plaza, **estudios confirmados** y las **observaciones**.
3. Pulsar **guardar**.

Endpoints asociados:
- `/src/asistentes/form_actividades_de_una_persona_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.est_ok`
- `html.falta`
- `html.guardar`
- `html.observ`
- `html.propio`

Acciones JavaScript:
- `fnjs_cmb_propietario`
- `fnjs_construir_desplegable_propietario`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/asistentes/form_actividades_de_una_persona_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde dossier actividad/persona, `actividad_que` o navegación embebida).
