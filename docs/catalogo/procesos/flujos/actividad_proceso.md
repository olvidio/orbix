---
id: "procesos.actividad_proceso.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Gestionar Actividad Proceso"
capacidad: "procesos.actividad_proceso.gestionar"
pantallas_principales: []
fragmentos: ["procesos.pantalla.actividad_proceso", "procesos.pantalla.actividad_proceso_get"]
acciones: ["crear_actualizar", "obtener", "obtener_datos"]
endpoints: ["/src/procesos/actividad_proceso_data", "/src/procesos/actividad_proceso_get", "/src/procesos/actividad_proceso_update"]
estado_revision: "generado"
---

# Flujo - Gestionar Actividad Proceso

Propuesta generada automaticamente desde la capacidad `procesos.actividad_proceso.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ActividadProceso, ActividadProcesoGet. Caso de uso: datos para la pantalla actividad_proceso (vista de las fases del proceso de una actividad concreta). Caso de uso: devuelve las tareas del proceso para un id_activ como estructura (completado, fase, tarea, responsable, observ) + flag de permiso de edicion. El render HTML se hace en el frontend. Caso de uso: guarda el estado (completado/observaciones) de una tarea concreta (id_item) del proceso de una actividad.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.actividad_proceso`
- `procesos.pantalla.actividad_proceso_get`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/procesos/actividad_proceso_update`

### Obtener

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.completado`
- `form.force`
- `form.id_item`
- `form.observ`
- `html.b_guardar`
- `html.completado`
- `html.observ`
- `post.id_activ`
- `post.sel`

Acciones JavaScript:
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/procesos/actividad_proceso_data`
- `/src/procesos/actividad_proceso_get`
- `/src/procesos/actividad_proceso_update`

## Errores Conocidos

- ``hay un error, no se ha guardado``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
