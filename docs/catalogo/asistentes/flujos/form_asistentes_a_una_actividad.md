---
id: "asistentes.form_asistentes_a_una_actividad.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Gestionar Form Asistentes A Una Actividad"
capacidad: "asistentes.form_asistentes_a_una_actividad.gestionar"
pantallas_principales: []
fragmentos: ["asistentes.pantalla.form_asistentes_a_una_actividad"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/form_asistentes_a_una_actividad_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Form Asistentes A Una Actividad

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Gestionar asistente en dossier 3101 (actividad).


## Punto De Entrada

Pantalla `form_asistentes_a_una_actividad` (`frontend/asistentes/controller/`).


## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.form_asistentes_a_una_actividad`

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
- `html.guardar2`
- `html.observ`
- `html.observ_est`
- `html.propio`
- `post.actualizar`

Acciones JavaScript:
- `fnjs_cmb_propietario`
- `fnjs_construir_desplegable_propietario`
- `fnjs_enviar_formulario`
- `fnjs_guardar`
- `fnjs_nuevo`

## Endpoints Del Flujo

- `/src/asistentes/form_asistentes_a_una_actividad_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice (acceso desde dossier actividad/persona, `actividad_que` o navegación embebida).
