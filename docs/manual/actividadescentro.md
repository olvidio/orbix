---
tipo: "manual_usuario"
modulo: "actividadescentro"
flujos: 7
estado_revision: "generado"
---

# Manual De Usuario - actividadescentro

Este manual es un borrador generado desde `docs/catalogo`. Debe revisarse para ajustar nombres de menu, permisos, validaciones y lenguaje final de usuario.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas de menu y nombres visibles pueden necesitar revision manual.

## Activ Ctr Shell

### Para Que Sirve

- Al abrir la pantalla de asignación de centros encargados, el sistema resuelve el colectivo (`tipo`, que puede remaparse a `sf*` en el semestre de formación) y firma las URLs AJAX que usará el resto de acciones.
- Es un paso transparente para el usuario, previo a mostrar los filtros de periodo.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- El caso de uso no aplica control de permisos propio: solo compone rutas. La autorización real la

### Referencias Internas

- Flujo: `actividadescentro.activ_ctr_shell.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadescentro/flujos/activ_ctr_shell.md`

## Centro Encargado

### Para Que Sirve

El usuario quita un centro de la lista de encargados de una actividad.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Eliminar

1. Pulsar un centro encargado ya asignado para abrir el popup de orden.
2. Elegir **borrar**.
3. El sistema lo elimina y refresca la celda de centros de la actividad.

### Errores O Avisos Frecuentes

- `el centro encargado ya no existe`
- `hay un error, no se ha eliminado el centro`
- `no se sabe cual borrar`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización se resuelve en el frontend (la

### Referencias Internas

- Flujo: `actividadescentro.centro_encargado.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadescentro/flujos/centro_encargado.md`

## Centro Encargado Asignar

### Para Que Sirve

- El usuario asigna un centro (elegido en el desplegable de candidatos) como encargado de una actividad.
- El centro queda al final del listado (`num_orden = max + 1`) con `encargo = 'organizador'`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. En una actividad, pulsar **nuevo** para ver los centros candidatos.
2. Pulsar el centro deseado.
3. El sistema lo guarda como encargado y refresca la celda de centros de la actividad.

### Errores O Avisos Frecuentes

- `faltan parametros id_activ / id_ubi`
- `hay un error, no se ha guardado el centro encargado`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización se resuelve en el frontend

### Referencias Internas

- Flujo: `actividadescentro.centro_encargado_asignar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadescentro/flujos/centro_encargado_asignar.md`

## Centro Encargado Reordenar

### Para Que Sirve

- El usuario sube (**+ prioridad**) o baja (**- prioridad**) un centro encargado en el listado de una actividad.
- Internamente se intercambia el `num_orden` con el centro vecino.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Pulsar un centro encargado ya asignado para abrir el popup de orden.
2. Elegir **+ prioridad** o **- prioridad**.
3. El sistema reordena y refresca la celda de centros de la actividad.

### Errores O Avisos Frecuentes

- `direccion de orden incorrecta (mas / menos)`
- `faltan parametros id_activ / id_ubi`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización se resuelve en el frontend (la

### Referencias Internas

- Flujo: `actividadescentro.centro_encargado_reordenar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadescentro/flujos/centro_encargado_reordenar.md`

## Centros Disponibles

### Para Que Sirve

- Al pulsar **nuevo** en una actividad, el usuario ve la lista de centros candidatos (filtrada por el colectivo `tipo`) para elegir cuál asignar como encargado.
- Para el tipo `sg` la lista incluye, por centro, el número de actividades en el periodo y la diferencia de días con su actividad más próxima, para ayudar a repartir la carga.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `tipo no valido`

### Permisos

- El caso de uso no aplica control de permisos propio; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadescentro.centros_disponibles.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadescentro/flujos/centros_disponibles.md`

## Centros Encargados

### Para Que Sirve

- Tras asignar, reordenar o eliminar un centro encargado, la celda de esa actividad se refresca con la lista actualizada de centros y el flag `permite_modificar` (que decide si cada centro se pinta como enlace o como texto plano).
- Es un paso automático, no una acción explícita del usuario.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Resuelve el permiso `ctr` con `PermisosActividades` (`$_SESSION['oPermActividades']`) cuando

### Referencias Internas

- Flujo: `actividadescentro.centros_encargados.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadescentro/flujos/centros_encargados.md`

## Lista Actividades Ctr

### Para Que Sirve

El usuario elige un periodo y pulsa **buscar**: el sistema muestra la tabla de actividades del colectivo (`tipo`) en ese periodo y, por cada una, los centros encargados actuales y los flags de permiso (modificar / crear centros) que deciden qué acciones se ofrecen.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Se apoya en `PermisosActividades` (`$_SESSION['oPermActividades']`) cuando `procesos` está instalado;
- Por actividad exige `have_perm_activ('ocupado')` y `have_perm_activ('ver')` en la faceta `datos`;

### Referencias Internas

- Flujo: `actividadescentro.lista_actividades_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadescentro/flujos/lista_actividades_ctr.md`

## Revision Pendiente

- Sustituir nombres tecnicos por nombres visibles en la aplicacion.
- Completar rutas de menu.
- Confirmar permisos necesarios.
- Anadir capturas o ejemplos si se quiere publicar para usuarios finales.
