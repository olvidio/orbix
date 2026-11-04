---
tipo: "manual_usuario"
modulo: "ubiscamas"
flujos: 5
estado_revision: "revisado_parcial"
---

# Manual De Usuario - ubiscamas

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Asignar camas a los asistentes

### Para Que Sirve

Ver las camas de la casa de una actividad y asignar a cada asistente su cama.

### Donde Entrar

- Lista Habitaciones (frontend/ubiscamas/controller/lista_habitaciones.php)
- Lista Habitaciones Distribucion (frontend/ubiscamas/controller/lista_habitaciones_distribucion.php)
- Lista Habitaciones Nombres (frontend/ubiscamas/controller/lista_habitaciones_nombres.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Asignar camas a los asistentes

1. Abrir las habitaciones de la actividad; se ofrece **lista por habitaciones** o **lista por nombre**.
2. Arrastrar al asistente a la cama o usar las flechas de asignar y desasignar.
3. Marcar **solo VIP** para ver únicamente las camas VIP.

### Errores O Avisos Frecuentes

- `Actividad not found`
- `No Ubi assigned to activity`

### Referencias Internas

- Flujo: `ubiscamas.actividad_habitaciones.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubiscamas/flujos/actividad_habitaciones.md`

## camas

### Para Que Sirve

Crear, editar o eliminar camas individuales de una habitación.

### Donde Entrar

- Cama Form (frontend/ubiscamas/controller/cama_form.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Gestionar camas

1. Guardar antes la habitación y pulsar **Añadir cama**.
2. Rellenar **Descripción** y marcar **VIP** o **Larga**; pulsar **Guardar**.
3. Para cambiar o quitar una cama, usar **Editar** o **Eliminar** en su fila.

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

## Ver las habitaciones de una casa

### Para Que Sirve

Dar de alta, modificar o eliminar las habitaciones de una casa, y crear sus camas.

### Donde Entrar

- Habitacion Form (frontend/ubiscamas/controller/habitacion_form.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ver las habitaciones de una casa

1. Buscar la casa en **direcciones > buscar** y abrir su ficha.
2. En «Relación de dossiers» pulsar **Habitaciones (camas) de una cdc**.
3. Aparece la **Relación de habitaciones** de la casa (o «no hay ninguna fila» si aún no hay).

#### Crear una habitación

1. En la relación de habitaciones, pulsar **crear una nueva habitación**.
2. Se abre «Nueva habitación» con los campos **Orden**, **Nombre**, **Número de camas**, **Número de camas VIP**, **Planta**, **Tipo de lavabo** (sin especificar, NO, completo, sin ducha o exterior), **Sillón**, **Adaptada**, **Observaciones** y **Despacho**.
3. Debajo, en **Camas**, el botón **Añadir cama** permite añadir camas una a una.
4. Pulsar **Guardar *** para guardar: el asterisco indica que se añaden automáticamente las camas según el **Número de camas** puesto arriba. **Cancelar** sale sin guardar.

#### Modificar o eliminar una habitación

1. Desde la relación de habitaciones, seleccionar la habitación para modificarla o eliminarla (no probado en pantalla: la casa de prueba no tenía habitaciones).

### Errores O Avisos Frecuentes

- `No se encontró la habitación a eliminar`
- `hay un error, no se ha eliminado la habitación`
- `Error al eliminar la habitación`
- `Habitación no válida`
- `Error al guardar la habitación`

### Referencias Internas

- Flujo: `ubiscamas.habitacion.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubiscamas/flujos/habitacion.md`

## Asignar una cama a un asistente

### Para Que Sirve

Guardar la asignación de cama a un asistente en la distribución de habitaciones.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Asignar una cama a un asistente

1. En la lista de habitaciones, usar las flechas de asignar o desasignar cama.
2. La asignación se guarda al momento.

### Errores O Avisos Frecuentes

- `Operación no autorizada`
- `Asistencia no encontrada para id_nom`
- `Error al guardar la asignación de la cama`

### Referencias Internas

- Flujo: `ubiscamas.update_cama_asistente.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubiscamas/flujos/update_cama_asistente.md`

## Filtrar las camas VIP

### Para Que Sirve

Mostrar solo las camas VIP en la distribución de habitaciones.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Filtrar las camas VIP

1. Marcar la casilla «solo VIP».
2. La lista muestra solo las camas VIP; desmarcarla para ver todas.

### Errores O Avisos Frecuentes

- `Operación no autorizada`
- `Actividad no encontrada`
- `Error al guardar el estado VIP de la actividad`

### Referencias Internas

- Flujo: `ubiscamas.update_solo_vip.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubiscamas/flujos/update_solo_vip.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/ubiscamas/flujos/`.
