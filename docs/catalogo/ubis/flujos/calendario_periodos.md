---
id: "ubis.calendario_periodos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Calendario Periodos"
capacidad: "ubis.calendario_periodos.gestionar"
pantallas_principales: ["ubis.pantalla.calendario_periodos"]
fragmentos: []
acciones: ["eliminar", "guardar"]
endpoints: ["/src/ubis/calendario_periodos_eliminar", "/src/ubis/calendario_periodos_guardar"]
estado_revision: "revisado"
---

# Flujo - Calendario Periodos

## Objetivo De Usuario

Elimina un periodo de calendario CDC identificado por id_item.

## Punto De Entrada

Menú Legacy: adl > Nuevo Calendario > Definir periodos. Pills2: ACTIVIDADES > Herramientas de calendario > Definir periodos.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/ubis/calendario_periodos_eliminar`

### Guardar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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
