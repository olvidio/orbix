---
id: "actividades.actividad.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Crear una actividad"
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

Dar de alta una actividad nueva o borrar una existente.

## Punto De Entrada

Lista de resultados de **Buscar actividad** (ACTIVIDADES > Buscar actividad, o la entrada del colectivo). El alta también está en la propia lista (**nueva actividad del mismo tipo**, **nueva actividad sv n**…).

## Escenarios Inferidos

### Crear una actividad

Pasos propuestos:
1. Pulsar **nueva actividad…** debajo de la lista (o la opción de nueva actividad del menú). Se abre «Nueva actividad» con el estado «proyecto».
2. Elegir el tipo: **sf/sv**, **asistentes**, **actividad** y **tipo de actividad** (es obligatorio concretar los dos últimos; si no, avisa «debe concretar la actividad en el tipo de actividad» o «debe concretar el tipo de actividad»).
3. Rellenar el nombre (o pulsar **generar** para que lo proponga), las fechas y los demás datos: **Estado**, **Nombre actividad** (con el enlace **generar**), **Fecha inicio**, **Hora inicio**, **Fecha fin**, **Hora fin**, **dl/r que organiza**, **Plazas totales**, **Lugar**, **Tarifa**, **Precio**, **Observaciones**, **Repetición anual por**, **Nivel de stgr**, **publicado** (sí/no) e **Idioma**.
4. Pulsar **Crear ficha**.
5. La actividad queda creada; el formulario se vacía y no muestra mensaje. Para verla, buscarla con **Buscar actividad** (estado «proyecto» o «cualquiera»).

Endpoints asociados:
- `/src/actividades/actividad_eliminar`
- `/src/actividades/actividad_nuevo`

### Borrar una actividad

Pasos propuestos:
1. En la lista, marcar **una** actividad y pulsar **borrar**.
2. Confirmar «¿Está seguro que desea borrar esta actividad?».
3. La actividad desaparece al volver a buscar.

Endpoints asociados:
- `/src/actividades/actividad_eliminar`
- `/src/actividades/actividad_nuevo`

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
