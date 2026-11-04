---
id: "procesos.procesos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Administrar las tareas de un proceso"
capacidad: "procesos.procesos.gestionar"
pantallas_principales: ["procesos.pantalla.procesos_select"]
fragmentos: ["procesos.pantalla.procesos_get", "procesos.pantalla.procesos_select", "procesos.pantalla.procesos_ver"]
acciones: ["crear_actualizar", "eliminar", "obtener"]
endpoints: ["/src/procesos/procesos_eliminar", "/src/procesos/procesos_get", "/src/procesos/procesos_update"]
estado_revision: "revisado"
---

# Flujo - Gestionar procesos

## Objetivo De Usuario

Administrar las fases y tareas de un tipo de proceso: verlas, crear o editar tareas y eliminarlas.

## Punto De Entrada

Menú **sistema > procesos activ. > procesos**.

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.procesos_get`
- `procesos.pantalla.procesos_select`
- `procesos.pantalla.procesos_ver`

## Escenarios Inferidos

### Administrar las tareas de un proceso

Pasos propuestos:
1. Entrar en **procesos**, elegir el proceso y pulsar **ver listado**.
2. Crear o modificar una tarea eligiendo fase, status, oficina y de qué tarea depende; guardar.
3. Para quitar una tarea, eliminarla y confirmar.
4. Después, regenerar las actividades si se quiere que recojan el cambio.

Endpoints asociados:
- `/src/procesos/procesos_eliminar`
- `/src/procesos/procesos_get`
- `/src/procesos/procesos_update`

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
- `post.refresh`
- `post.stack`

Acciones JavaScript:
- `fnjs_get_depende`

## Endpoints Del Flujo

- `/src/procesos/procesos_eliminar`
- `/src/procesos/procesos_get`
- `/src/procesos/procesos_update`

## Errores Conocidos

- ``hay un error, no se ha eliminado``
- ``hay un error, no se ha guardado``
- ``no se encuentra la tarea a borrar``
- ``no sé cuál he de borar``

## Ruta de menú

- **Legacy:** sistema > procesos activ. > procesos
- **Pills2:** ADMIN LOCAL > procesos activ. > procesos
