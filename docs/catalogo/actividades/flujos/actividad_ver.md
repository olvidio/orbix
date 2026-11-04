---
id: "actividades.actividad_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Abrir la ficha de una actividad"
capacidad: "actividades.actividad_ver.gestionar"
pantallas_principales: ["actividades.pantalla.actividad_ver"]
fragmentos: ["actividades.pantalla.planning_casa_modificar", "actividades.pantalla.planning_casa_nueva"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/actividad_ver_datos"]
estado_revision: "revisado"
---

# Flujo - Cargar datos de ficha actividad

Bootstrap del formulario de actividad: entidad, desplegables y HTML auxiliar.

## Objetivo De Usuario

Abrir la ficha de una actividad (o el formulario de una nueva) con todos sus datos.

## Punto De Entrada

Lista de resultados de **Buscar actividad** (ACTIVIDADES > Buscar actividad, o la entrada del colectivo). También desde el planning de una casa.

## Escenarios Inferidos

### Abrir la ficha de una actividad

Pasos propuestos:
1. Buscar la actividad y marcar su casilla en la lista.
2. Pulsar **datos** (o pulsar el nombre de la actividad).
3. Se abre «DATOS ACTIVIDAD» con el título de la actividad, el enlace **(dossiers)** y el tipo (sf/sv, asistentes, actividad y tipo de actividad, solo lectura).
4. Los campos son: **Estado**, **Nombre actividad** (con el enlace **generar**), **Fecha inicio**, **Hora inicio**, **Fecha fin**, **Hora fin**, **dl/r que organiza**, **Plazas totales**, **Lugar**, **Tarifa**, **Precio**, **Observaciones**, **Repetición anual por**, **Nivel de stgr**, **publicado** (sí/no) e **Idioma**.
5. Según los permisos, la ficha se puede modificar o es solo de consulta.

Endpoints asociados:
- `/src/actividades/actividad_ver_datos`

## Endpoints Del Flujo

- `/src/actividades/actividad_ver_datos`

## Errores Conocidos

Errores de permiso o actividad inexistente se gestionan en el controller frontend antes/después
del POST (ver ficha API).

## Ruta de menú

Misma que `actividad_ver` / planning (sin menú propio para el paso AJAX).
