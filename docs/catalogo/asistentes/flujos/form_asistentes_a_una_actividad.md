---
id: "asistentes.form_asistentes_a_una_actividad.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Modificar los datos de un asistente en una actividad"
capacidad: "asistentes.form_asistentes_a_una_actividad.gestionar"
pantallas_principales: ["asistentes.pantalla.form_asistentes_a_una_actividad"]
fragmentos: ["asistentes.pantalla.form_asistentes_a_una_actividad"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/form_asistentes_a_una_actividad_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Form Asistentes A Una Actividad

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Editar los datos de un asistente en una actividad (plaza, observaciones, falta, estudios confirmados).

## Punto De Entrada

Lista de resultados de **Buscar actividad**: marcar la actividad y pulsar el botón **asistentes**.

## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.form_asistentes_a_una_actividad`

## Escenarios Inferidos

### Modificar los datos de un asistente en una actividad

Pasos propuestos:
1. En «Relación de asistentes», marcar a la persona y pulsar **modificar asistencia** (o **+ n** / **+ agd** para añadir a alguien).
2. Se abre «Asistente a una actividad» con el nombre y los campos **propio**, **falta**, **estudios confirmados**, **Observaciones actividad**, **Observaciones estudios**, **plaza** (pedida, en espera, asignada o confirmada) y **propiedad de** (la dl dueña de la plaza, con «n de m» plazas).
3. Pulsar **Guardar datos del asistente**, o **Guardar y añadir otro asistente** para seguir con otra persona.

Endpoints asociados:
- `/src/asistentes/form_asistentes_a_una_actividad_data`

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
