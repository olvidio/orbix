---
id: "asistentes.form_actividades_de_una_persona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Gestionar Form Actividades De Una Persona"
capacidad: "asistentes.form_actividades_de_una_persona.gestionar"
pantallas_principales: []
fragmentos: ["asistentes.pantalla.form_actividades_de_una_persona"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/form_actividades_de_una_persona_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Form Actividades De Una Persona

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Gestionar asistencias en dossier 1301 (persona).


## Punto De Entrada

Pantalla `form_actividades_de_una_persona` (`frontend/asistentes/controller/`).


## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.form_actividades_de_una_persona`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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
