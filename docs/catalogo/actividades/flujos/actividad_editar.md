---
id: "actividades.actividad_editar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Guardar edición de actividad"
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

Modificar campos de la actividad (fechas, lugar, plazas, observaciones, etc.) y
guardar sin cambiar el tipo.

## Punto De Entrada

- `actividad_ver` (`mod=editar`, botón *guardar cambios*).
- `planning_casa_modificar` (mismo formulario incrustado).

## Escenarios

### Ejecutar

1. Abrir ficha con `id_activ` y permiso `modificar`.
2. Editar campos; validaciones JS de fechas/horas/tipo/organiza.
3. `fnjs_guardar('editar')` POST a `actividad_editar`.
4. Tras éxito, navegación atrás (`oPosicion`) o cierre del popup en planning.

## Endpoints Del Flujo

- `/src/actividades/actividad_editar`

## Errores Conocidos

- `sesión de permisos no disponible`
- `debe seleccionar un tipo de actividad`
- `actividad no encontrada`
- `hay un error, no se ha guardado` + detalle

## Ruta de menú

sin entrada de menú en el índice (se llega desde ficha abierta vía buscar activ o planning).
