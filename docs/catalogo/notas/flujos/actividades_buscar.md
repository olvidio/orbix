---
id: "notas.actividades_buscar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Vincular una actividad al acta"
capacidad: "notas.actividades_buscar.gestionar"
pantallas_principales: ["notas.pantalla.actividad_buscar_form"]
fragmentos: ["notas.pantalla.actividad_buscar_form"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/actividades_buscar_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Actividades Buscar

Propuesta generada automaticamente desde la capacidad `notas.actividades_buscar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Elegir la actividad de estudios (CA) a la que corresponde el acta.

## Punto De Entrada

Ficha del acta, botón de elegir actividad.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.actividad_buscar_form`

## Escenarios Inferidos

### Vincular una actividad al acta

Pasos propuestos:
1. En la ficha del acta, pulsar para seleccionar la actividad.
2. Elegir la **organización** y la **actividad** y pulsar **seleccionar la actividad** y **guardar**.

Endpoints asociados:
- `/src/notas/actividades_buscar_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.observ`
- `form.pres_mail`
- `form.pres_nom`
- `form.pres_telf`
- `form.zona`
- `post.dl_org`
- `post.f_acta_iso`
- `post.id_activ`

Acciones JavaScript:
- `fnjs_buscar_ca`
- `fnjs_cerrar`
- `fnjs_update_activ`

## Endpoints Del Flujo

- `/src/notas/actividades_buscar_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
