---
id: "procesos.procesos_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Crear o modificar una tarea de un proceso"
capacidad: "procesos.procesos_ver.gestionar"
pantallas_principales: ["procesos.pantalla.procesos_ver"]
fragmentos: ["procesos.pantalla.procesos_ver"]
acciones: ["obtener_datos"]
endpoints: ["/src/procesos/procesos_ver_data"]
estado_revision: "revisado"
---

# Flujo - Editar tarea de proceso

## Objetivo De Usuario

Crear o modificar una tarea dentro de un tipo de proceso: fase, status, oficina y tareas de las que depende.

## Punto De Entrada

Ventana modal que se abre desde **ver listado** de un proceso.

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.procesos_ver`

## Escenarios Inferidos

### Crear o modificar una tarea de un proceso

Pasos propuestos:
1. Desde el listado del proceso, pulsar **nuevo** en la pantalla de selección de proceso (o una tarea existente del listado).
2. Elegir la fase, la tarea, el status, la oficina y la fase o tarea previa de la que depende.
3. Guardar. Al guardar, las actividades que usen este proceso deberán regenerarse para recoger el cambio.

Endpoints asociados:
- `/src/procesos/procesos_ver_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.acc`
- `form.dep_num`
- `form.id_fase`
- `form.id_fase_previa`
- `form.id_of_responsable`
- `form.id_tarea`
- `form.id_tarea_previa`
- `form.mensaje_requisito`
- `form.status`
- `form.valor_depende`
- `post.id_item`
- `post.id_tipo_proceso`
- `post.mod`

Acciones JavaScript:
- `fnjs_get_depende`

## Endpoints Del Flujo

- `/src/procesos/procesos_ver_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
