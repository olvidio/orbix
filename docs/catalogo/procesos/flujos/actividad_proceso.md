---
id: "procesos.actividad_proceso.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Ver y avanzar el proceso de una actividad"
capacidad: "procesos.actividad_proceso.gestionar"
pantallas_principales: ["procesos.pantalla.actividad_proceso"]
fragmentos: ["procesos.pantalla.actividad_proceso", "procesos.pantalla.actividad_proceso_get"]
acciones: ["crear_actualizar", "obtener", "obtener_datos"]
endpoints: ["/src/procesos/actividad_proceso_data", "/src/procesos/actividad_proceso_get", "/src/procesos/actividad_proceso_update"]
estado_revision: "revisado"
---

# Flujo - Proceso de actividad

## Objetivo De Usuario

Ver y avanzar las fases del proceso de una actividad (tareas completadas, observaciones).

## Punto De Entrada

Lista de resultados de **Buscar actividad**: marcar la actividad y pulsar el botón **proceso**.

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.actividad_proceso`
- `procesos.pantalla.actividad_proceso_get`

## Escenarios Inferidos

### Ver y avanzar el proceso de una actividad

Pasos propuestos:
1. Marcar la actividad y pulsar **proceso**.
2. Se muestra el título, el enlace **(dossiers)** y «Fases de la actividad», con la opción **forzar** y una fila por fase y tarea.
3. Marcar las tareas hechas, escribir observaciones y guardar.
4. Si el proceso de la actividad no se ha generado bien, aparece un error del tipo «La fase del proceso tipo: 6, fase: 2, tarea: 0»; los procesos se generan al crear la actividad, así que este error indica un dato incompleto de esa actividad; en ese caso hay que regenerar el proceso.

Endpoints asociados:
- `/src/procesos/actividad_proceso_data`
- `/src/procesos/actividad_proceso_get`
- `/src/procesos/actividad_proceso_update`

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
