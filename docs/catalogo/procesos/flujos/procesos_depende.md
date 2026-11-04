---
id: "procesos.procesos_depende.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Elegir de qué tarea depende"
capacidad: "procesos.procesos_depende.gestionar"
pantallas_principales: ["procesos.pantalla.procesos_ver"]
fragmentos: ["procesos.pantalla.procesos_ver"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/procesos_depende"]
estado_revision: "revisado"
---

# Flujo - Dependencias de tarea de proceso

## Objetivo De Usuario

Actualizar la lista de tareas de las que puede depender una tarea al cambiar la fase.

## Punto De Entrada

Formulario de tarea de un proceso.

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.procesos_ver`

## Escenarios Inferidos

### Elegir de qué tarea depende

Pasos propuestos:
1. En el formulario de tarea, cambiar la fase o la fase previa.
2. El desplegable de tareas dependientes se actualiza con las tareas posibles; elegir una.

Endpoints asociados:
- `/src/procesos/procesos_depende`

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

- `/src/procesos/procesos_depende`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
