---
id: "procesos.procesos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "procesos"
nombre: "Flujo - Gestionar Procesos"
capacidad: "procesos.procesos.gestionar"
pantallas_principales: []
fragmentos: ["procesos.pantalla.procesos_get", "procesos.pantalla.procesos_select", "procesos.pantalla.procesos_ver"]
acciones: ["crear_actualizar", "eliminar", "obtener"]
endpoints: ["/src/procesos/procesos_eliminar", "/src/procesos/procesos_get", "/src/procesos/procesos_update"]
estado_revision: "generado"
---

# Flujo - Gestionar Procesos

Propuesta generada automaticamente desde la capacidad `procesos.procesos.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona Procesos, ProcesosGet. Caso de uso: devuelve la estructura de padres/hijos del arbol de fases del proceso filtrando segun el sfsv/role del usuario. Retorna un array donde cada clave es el id de fase padre (0 = raiz) y cada valor es una lista de ['id', 'nom']. El HTML del árbol lo genera {. Caso de uso: elimina una tarea_proceso por su id_item. Caso de uso: guarda una tarea_proceso (fase/tarea/responsable/status y fases_previas) del proceso.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `procesos.pantalla.procesos_get`
- `procesos.pantalla.procesos_select`
- `procesos.pantalla.procesos_ver`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/procesos/procesos_update`

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/procesos/procesos_eliminar`

### Obtener

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

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
