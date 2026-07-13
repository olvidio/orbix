---
id: "procesos.fases_activ_cambio_tipo_html.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Gestionar Fases Activ Cambio Tipo Html"
capacidad: "procesos.fases_activ_cambio_tipo_html.gestionar"
pantallas_principales: []
fragmentos: ["procesos.pantalla.fases_activ_cambio"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/fases_activ_cambio_tipo_html"]
estado_revision: "revisado"
---

# Flujo - Selector tipo actividad (cambio de fase)

## Objetivo De Usuario

Generar el HTML del selector de tipo de actividad usado en la pantalla de cambio de fase masivo.

## Punto De Entrada

Menú Legacy: Calendario > actividades > cambiar de fase (también dre y variantes vest/vsm/dagd/vsg). Pills2: ACTIVIDADES > Herramientas de calendario > Cambio de fase actividades.

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.fases_activ_cambio`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.accion`
- `form.dl_propia`
- `form.empiezamax`
- `form.empiezamin`
- `form.entrada`
- `form.extendida`
- `form.id_fase_nueva`
- `form.id_fase_sel`
- `form.id_tipo_activ`
- `form.modo`
- `form.periodo`
- `form.salida`
- `form.year`
- `post.dl_propia`
- `post.empiezamax`
- `post.empiezamin`
- `post.fin`
- `post.id_fase_nueva`
- `post.id_tipo_activ`
- `post.inicio`
- `post.periodo`
- `post.sactividad`
- `post.sactividad2`
- `post.sasistentes`
- `post.stack`
- `post.year`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/procesos/fases_activ_cambio_tipo_html`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** Calendario > actividades > cambiar de fase; dre > actividades > cambiar de fase
- **Pills2:** ATENCIÓN SACD > Actividades > cambiar de fase; dre > actividades > cambiar de fase; Calendario > actividades > cambiar de fase; ACTIVIDADES > Herramientas de calendario > Cambio de fase actividades
