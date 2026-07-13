---
id: "procesos.procesos_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Gestionar Procesos Ver"
capacidad: "procesos.procesos_ver.gestionar"
pantallas_principales: []
fragmentos: ["procesos.pantalla.procesos_ver"]
acciones: ["obtener_datos"]
endpoints: ["/src/procesos/procesos_ver_data"]
estado_revision: "revisado"
---

# Flujo - Editar tarea de proceso

## Objetivo De Usuario

Carga del formulario modal de alta o edición de una tarea dentro de un tipo de proceso, con desplegables de fases, tareas, status, oficina y dependencias.

## Punto De Entrada

Sin entrada directa de menú; se abre desde la pantalla de administración de procesos (`procesos_select`).

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.procesos_ver`

## Escenarios Inferidos

### Obtener Datos

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

- `/src/procesos/procesos_ver_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
