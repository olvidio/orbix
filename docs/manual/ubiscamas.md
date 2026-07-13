---
tipo: "manual_usuario"
modulo: "ubiscamas"
flujos: 5
estado_revision: "generado"
---

# Manual De Usuario - ubiscamas

Este manual es un borrador generado desde `docs/catalogo`. Debe revisarse para ajustar nombres de menu, permisos, validaciones y lenguaje final de usuario.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas de menu y nombres visibles pueden necesitar revision manual.

## Actividad Habitaciones

### Para Que Sirve

Listar camas de la ubi de una actividad, asignar o reasignar asistentes (drag-and-drop), activar modo solo VIP y abrir vistas de distribución o nombres.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

### Errores O Avisos Frecuentes

- `Actividad not found`
- `No Ubi assigned to activity`

### Referencias Internas

- Flujo: `ubiscamas.actividad_habitaciones.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubiscamas/flujos/actividad_habitaciones.md`

## Cama

### Para Que Sirve

Crear, editar o eliminar camas individuales asociadas a una habitación.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

#### Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

#### Abrir el formulario

1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

### Errores O Avisos Frecuentes

- `ID de cama no proporcionado`
- `No se encontró la cama a eliminar`
- `hay un error, no se ha eliminado la cama`
- `Error al eliminar la cama`
- `Habitación no válida`
- `Cama no válida`
- `Error al guardar la cama`

### Referencias Internas

- Flujo: `ubiscamas.cama.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubiscamas/flujos/cama.md`

## Habitacion

### Para Que Sirve

Dar de alta, modificar o eliminar habitaciones de un ubi CDC, incluyendo creación automática de camas según número indicado.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

#### Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

#### Abrir el formulario

1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

### Errores O Avisos Frecuentes

- `No se encontró la habitación a eliminar`
- `hay un error, no se ha eliminado la habitación`
- `Error al eliminar la habitación`
- `Habitación no válida`
- `Error al guardar la habitación`

### Referencias Internas

- Flujo: `ubiscamas.habitacion.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubiscamas/flujos/habitacion.md`

## Update Cama Asistente

### Para Que Sirve

Persistir la asignación cama↔asistente en la actividad actual (requiere token HashB).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Operación no autorizada`
- `Asistencia no encontrada para id_nom`
- `Error al guardar la asignación de la cama`

### Referencias Internas

- Flujo: `ubiscamas.update_cama_asistente.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubiscamas/flujos/update_cama_asistente.md`

## Update Solo Vip

### Para Que Sirve

Alternar el filtro de solo camas VIP en la actividad (`desc_activ=camasVIP`).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Operación no autorizada`
- `Actividad no encontrada`
- `Error al guardar el estado VIP de la actividad`

### Referencias Internas

- Flujo: `ubiscamas.update_solo_vip.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubiscamas/flujos/update_solo_vip.md`

## Revision Pendiente

- Sustituir nombres tecnicos por nombres visibles en la aplicacion.
- Completar rutas de menu.
- Confirmar permisos necesarios.
- Anadir capturas o ejemplos si se quiere publicar para usuarios finales.
