---
id: "procesos.procesos_depende.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Gestionar Procesos Depende"
capacidad: "procesos.procesos_depende.gestionar"
pantallas_principales: []
fragmentos: ["procesos.pantalla.procesos_ver"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/procesos_depende"]
estado_revision: "revisado"
---

# Flujo - Dependencias de tarea de proceso

## Objetivo De Usuario

Actualización dinámica del desplegable de tareas dependientes al cambiar la fase o fase previa en el formulario de edición de una tarea de proceso.

## Punto De Entrada

Sin entrada directa de menú; se invoca desde el formulario modal de `procesos_ver`.

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.procesos_ver`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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
