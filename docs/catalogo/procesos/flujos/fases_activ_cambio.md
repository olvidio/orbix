---
id: "procesos.fases_activ_cambio.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Cambiar de fase varias actividades"
capacidad: "procesos.fases_activ_cambio.gestionar"
pantallas_principales: ["procesos.pantalla.fases_activ_cambio"]
fragmentos: ["procesos.pantalla.fases_activ_cambio", "procesos.pantalla.fases_activ_cambio_lista"]
acciones: ["crear_actualizar", "listar", "obtener"]
endpoints: ["/src/procesos/fases_activ_cambio_get", "/src/procesos/fases_activ_cambio_lista", "/src/procesos/fases_activ_cambio_update"]
estado_revision: "revisado"
---

# Flujo - Cambiar de fase

## Objetivo De Usuario

Cambiar de fase varias actividades a la vez.

## Punto De Entrada

Menú **Calendario > actividades > cambiar de fase** (Pills2: ACTIVIDADES > Herramientas de calendario > Cambio de fase actividades).

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.fases_activ_cambio`
- `procesos.pantalla.fases_activ_cambio_lista`

## Escenarios Inferidos

### Cambiar de fase varias actividades

Pasos propuestos:
1. Elegir el tipo de actividad, el periodo y la fase destino y listar.
2. Marcar las actividades que pasan a la fase nueva.
3. Pulsar para aplicar el cambio.

Endpoints asociados:
- `/src/procesos/fases_activ_cambio_get`
- `/src/procesos/fases_activ_cambio_lista`
- `/src/procesos/fases_activ_cambio_update`

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
- `form.sel`
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
- `fnjs_cambiar`
- `fnjs_selectAll`
- `fnjs_ver_activ`

## Endpoints Del Flujo

- `/src/procesos/fases_activ_cambio_get`
- `/src/procesos/fases_activ_cambio_lista`
- `/src/procesos/fases_activ_cambio_update`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** Calendario > actividades > cambiar de fase; dre > actividades > cambiar de fase
- **Pills2:** ATENCIÓN SACD > Actividades > cambiar de fase; dre > actividades > cambiar de fase; Calendario > actividades > cambiar de fase; ACTIVIDADES > Herramientas de calendario > Cambio de fase actividades
