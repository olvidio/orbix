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
estado_revision: "revisado"
---

# Flujo - Proceso de actividad

## Objetivo De Usuario

Consulta y edición del proceso de una actividad: ver tareas por fase, marcar completado, guardar observaciones y actualizar el estado de cada tarea.

## Punto De Entrada

Sin entrada directa de menú; se abre embebido desde otras pantallas (p. ej. cambio de fase o dossier de actividad).

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

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
