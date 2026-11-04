---
id: "actividades.actividad_editar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Modificar una actividad"
capacidad: "actividades.actividad_editar.gestionar"
pantallas_principales: ["actividades.pantalla.actividad_ver"]
fragmentos: ["actividades.pantalla.planning_casa_modificar"]
acciones: ["ejecutar"]
endpoints: ["/src/actividades/actividad_editar"]
estado_revision: "revisado"
---

# Flujo - Guardar edición de actividad

Persistir cambios de una actividad existente desde la ficha o el planning.

## Objetivo De Usuario

Modificar los datos de una actividad existente (fechas, lugar, plazas, observaciones, etc.) sin cambiar su tipo.

## Punto De Entrada

Lista de resultados de **Buscar actividad** (ACTIVIDADES > Buscar actividad, o la entrada del colectivo).

## Escenarios Inferidos

### Modificar una actividad

Pasos propuestos:
1. Marcar la actividad en la lista y pulsar **datos**.
2. Cambiar los campos que hagan falta (fechas, horas, plazas, observaciones, publicado…).
3. Pulsar **Guardar cambios**.
4. Si se guarda bien, se vuelve a la lista de la búsqueda; no hay mensaje de confirmación.

Endpoints asociados:
- `/src/actividades/actividad_editar`

## Endpoints Del Flujo

- `/src/actividades/actividad_editar`

## Errores Conocidos

- `sesión de permisos no disponible`
- `debe seleccionar un tipo de actividad`
- `actividad no encontrada`
- `hay un error, no se ha guardado` + detalle

## Ruta de menú

sin entrada de menú en el índice (se llega desde ficha abierta vía buscar activ o planning).
