---
id: "procesos.fases_activ_cambio_tipo_html.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Elegir el tipo de actividad para cambiar de fase"
capacidad: "procesos.fases_activ_cambio_tipo_html.gestionar"
pantallas_principales: ["procesos.pantalla.fases_activ_cambio"]
fragmentos: ["procesos.pantalla.fases_activ_cambio"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/fases_activ_cambio_tipo_html"]
estado_revision: "revisado"
---

# Flujo - Selector tipo actividad (cambio de fase)

## Objetivo De Usuario

Mostrar el selector de tipo de actividad en la pantalla de cambio de fase masivo.

## Punto De Entrada

Menú **Calendario > actividades > cambiar de fase** (Pills2: ACTIVIDADES > Herramientas de calendario > Cambio de fase actividades).

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.fases_activ_cambio`

## Escenarios Inferidos

### Elegir el tipo de actividad para cambiar de fase

Pasos propuestos:
1. Entrar en **cambiar de fase**.
2. Elegir el tipo de actividad; el resto de opciones de la pantalla se adaptan a él.

Endpoints asociados:
- `/src/procesos/fases_activ_cambio_tipo_html`

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
