---
id: "actividades.actividad.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Crear y eliminar actividad"
capacidad: "actividades.actividad.gestionar"
pantallas_principales: ["actividades.pantalla.actividad_ver"]
fragmentos: ["actividades.pantalla.actividad_select"]
acciones: ["crear", "eliminar"]
endpoints: ["/src/actividades/actividad_eliminar", "/src/actividades/actividad_nuevo"]
estado_revision: "revisado"
---

# Flujo - Crear y eliminar actividad

Alta y baja de fichas de actividad desde la UI (ficha nueva, listados, planning).

## Objetivo De Usuario

- **Crear:** rellenar la ficha en modo *nuevo* y guardar (`actividad_nuevo`).
- **Eliminar:** seleccionar actividad(es) en un listado y confirmar borrado (`actividad_eliminar`).

## Punto De Entrada

- Crear: `actividad_ver` (`mod=nuevo`) o `planning_casa_nueva`.
- Eliminar: acciones de listado en `actividad_select` / `lista_actividades_sg` (según permisos).

## Escenarios

### Crear

1. Abrir ficha nueva (menú *nueva activ* o planning).
2. Completar tipo, fechas, lugar, organiza y demás campos obligatorios.
3. Pulsar guardar (`fnjs_guardar('nuevo')` → `actividad_nuevo`).
4. Comprobar mensaje de éxito; la ficha puede resetearse en la misma página.

### Eliminar

1. En listado, marcar actividad(es) y elegir borrar.
2. Confirmar diálogo.
3. El backend elimina vía `actividad_eliminar`; refrescar listado.

## Endpoints Del Flujo

- `/src/actividades/actividad_nuevo`
- `/src/actividades/actividad_eliminar`

## Errores Conocidos

Crear: `debe seleccionar un tipo de actividad`, `No tiene permiso para crear…`,
`hay un error, no se ha guardado`. Eliminar: `actividad no encontrada`,
`No tiene permiso para borrar esta actividad`, `hay un error, no se ha eliminado`.

## Ruta de menú

- **Legacy:** dre/Calendario > actividades > nueva activ (crear).
- **Pills2:** ACTIVIDADES > Buscar actividad > Nueva actividad; ATENCIÓN SACD >
  Actividades > Nueva actividad. Borrado desde listados de búsqueda (sin menú propio).
