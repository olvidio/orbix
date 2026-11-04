---
id: "ubis.calendario_periodos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Consultar los periodos de una casa"
capacidad: "ubis.calendario_periodos.gestionar"
pantallas_principales: ["ubis.pantalla.calendario_periodos"]
fragmentos: []
acciones: ["eliminar", "guardar"]
endpoints: ["/src/ubis/calendario_periodos_eliminar", "/src/ubis/calendario_periodos_guardar"]
estado_revision: "revisado"
---

# Flujo - Calendario Periodos

## Objetivo De Usuario

Definir los periodos del calendario de una casa en un año y borrar los que sobran.

## Punto De Entrada

Menú **adl > Nuevo Calendario > Definir periodos** (Pills2: ACTIVIDADES > Herramientas de calendario > Definir periodos).

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Consultar los periodos de una casa

Pasos propuestos:
1. Entrar en **Definir periodos**.
2. Elegir la casa y el año y pulsar **buscar**.
3. Se listan los periodos ordenados por fecha de inicio y se avisa si alguno se solapa con otro.

Endpoints asociados:
- `/src/ubis/calendario_periodos_eliminar`
- `/src/ubis/calendario_periodos_guardar`

### Borrar un periodo

Pasos propuestos:
1. En la lista, pulsar eliminar en el periodo.
2. Confirmar «¿Está seguro de borrar este periodo?».

Endpoints asociados:
- `/src/ubis/calendario_periodos_eliminar`
- `/src/ubis/calendario_periodos_guardar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_item`
- `form.id_ubi`
- `form.year`
- `html.buscar`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_guardar`
- `fnjs_modificar`
- `fnjs_update_div`
- `fnjs_ver`

## Endpoints Del Flujo

- `/src/ubis/calendario_periodos_eliminar`
- `/src/ubis/calendario_periodos_guardar`

## Errores Conocidos

- `no sé cuál he de borar`
- `no se encuentra el periodo a borrar`
- `hay un error, no se ha eliminado`

## Ruta de menú

- **Legacy:** adl > Nuevo Calendario > Definir periodos
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Definir periodos
