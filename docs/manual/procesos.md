---
tipo: "manual_usuario"
modulo: "procesos"
flujos: 17
estado_revision: "generado"
---

# Manual De Usuario - procesos

Este manual es un borrador generado desde `docs/catalogo`. Debe revisarse para ajustar nombres de menu, permisos, validaciones y lenguaje final de usuario.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas de menu y nombres visibles pueden necesitar revision manual.

## Actividad Proceso

### Para Que Sirve

Consulta y edición del proceso de una actividad: ver tareas por fase, marcar completado, guardar observaciones y actualizar el estado de cada tarea.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

#### Obtener

1. Revisar manualmente los pasos de esta accion.

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización se resuelve en el
- No hay `perm_*` en el caso de uso; `puede_editar` por fila según oficina responsable y
- Sin control de permisos propio; el frontend solo habilita edición en filas con `puede_editar`.

### Referencias Internas

- Flujo: `procesos.actividad_proceso.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/actividad_proceso.md`

## Actividad Proceso Generar

### Para Que Sirve

Regenerar las tareas del proceso asociado a una actividad, conservando o no el estado actual según el flag «forzar».

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- Sin control de permisos propio; el frontend solo muestra la acción si `permiso_calendario`.

### Referencias Internas

- Flujo: `procesos.actividad_proceso_generar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/actividad_proceso_generar.md`

## Actividad Que Fases Ajax

### Para Que Sirve

Devolver las fases aplicables a un tipo de actividad para construir los checkboxes de filtro `fases_on` / `fases_off` en la búsqueda de actividades.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- Sin control de permisos propio; autorización en `actividad_que.php` y `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `procesos.actividad_que_fases_ajax.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/actividad_que_fases_ajax.md`

## Fases Activ Cambio

### Para Que Sirve

Cambio masivo de fase en actividades: filtrar por tipo, periodo y fase destino; listar candidatas; marcar o desmarcar la tarea de la fase nueva en las actividades seleccionadas.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

#### Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

#### Obtener

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- Sin control de permisos propio; autorización en `fases_activ_cambio.php` y `$_SESSION['oPerm']`.
- Sin control de permisos propio; autorización en frontend y `$_SESSION['oPerm']`.
- Por actividad: exige permiso de oficina responsable (`have_perm_oficina`) salvo oficina vacía.

### Referencias Internas

- Flujo: `procesos.fases_activ_cambio.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/fases_activ_cambio.md`

## Fases Activ Cambio Tipo Html

### Para Que Sirve

Generar el HTML del selector de tipo de actividad usado en la pantalla de cambio de fase masivo.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- No llama a `perm_*` explícito; restringe tipos visibles según oficinas `vcsd`/`des`/`calendario`

### Referencias Internas

- Flujo: `procesos.fases_activ_cambio_tipo_html.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/fases_activ_cambio_tipo_html.md`

## Procesos

### Para Que Sirve

Administración del árbol de fases/tareas de un tipo de proceso: visualizar estructura, crear o editar tareas con dependencias y eliminar tareas existentes.

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

#### Obtener

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `hay un error, no se ha eliminado`
- `hay un error, no se ha guardado`
- `no se encuentra la tarea a borrar`
- `no sé cuál he de borar`

### Permisos

- Sin control de permisos propio; autorización en `procesos_select.php` y `$_SESSION['oPerm']`.
- Filtrado implícito por SFSV/rol (SuperAdmin ve SF+SV); no usa `perm_*` de oficina.
- Sin control de permisos propio; autorización en frontend y `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `procesos.procesos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/procesos.md`

## Procesos Clonar

### Para Que Sirve

Clonar las tareas de un tipo de proceso de referencia sobre el proceso seleccionado, sustituyendo las tareas existentes.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `no se ha indicado el proceso a clonar`

### Permisos

- Sin control de permisos propio; autorización en `procesos_select.php` y `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `procesos.procesos_clonar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/procesos_clonar.md`

## Procesos Depende

### Para Que Sirve

Actualización dinámica del desplegable de tareas dependientes al cambiar la fase o fase previa en el formulario de edición de una tarea de proceso.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- Sin control de permisos propio; autorización en `procesos_ver.php` y `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `procesos.procesos_depende.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/procesos_depende.md`

## Procesos Get Listado

### Para Que Sirve

Visualización en formato tabla de las fases/tareas de un tipo de proceso, con acciones de modificar y eliminar cada tarea.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- Filtrado implícito por SFSV/rol (SuperAdmin ve SF+SV); no usa `perm_*` de oficina.

### Referencias Internas

- Flujo: `procesos.procesos_get_listado.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/procesos_get_listado.md`

## Procesos Regenerar

### Para Que Sirve

Regenerar masivamente las tareas de proceso de las actividades asociadas a un tipo de proceso, a partir de la definición de fases/tareas del proceso.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- Sin control de permisos propio; autorización en `procesos_select.php` y `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `procesos.procesos_regenerar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/procesos_regenerar.md`

## Procesos Select

### Para Que Sirve

Carga inicial de la pantalla de administración de procesos: opciones del desplegable de tipo de proceso y hashes de navegación.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- Sin control de permisos propio; autorización en `procesos_select.php` y `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `procesos.procesos_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/procesos_select.md`

## Procesos Ver

### Para Que Sirve

Carga del formulario modal de alta o edición de una tarea dentro de un tipo de proceso, con desplegables de fases, tareas, status, oficina y dependencias.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- Sin control de permisos propio; autorización en `procesos_ver.php` y `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `procesos.procesos_ver.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/procesos_ver.md`

## Tipo Activ Proceso

### Para Que Sirve

Listado de tipos de actividad con el proceso asignado (propio y no propio) para su gestión desde la pantalla de asignación.

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

- `_(ninguno documentado)_`

### Permisos

- Sin control de permisos propio; autorización en frontend y `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `procesos.tipo_activ_proceso.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/tipo_activ_proceso.md`

## Tipo Activ Proceso Asignar

### Para Que Sirve

Asignar un tipo de proceso a un tipo de actividad, distinguiendo entre proceso propio (DL) o no propio.

### Donde Entrar

- Tipo Activ Proceso (frontend/procesos/controller/tipo_activ_proceso.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado el proceso`

### Permisos

- Sin control de permisos propio; autorización en `tipo_activ_proceso.php` y `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `procesos.tipo_activ_proceso_asignar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/tipo_activ_proceso_asignar.md`

## Tipo Activ Proceso Lst Posibles

### Para Que Sirve

Obtener y mostrar la mini-tabla de procesos que el usuario puede asignar a un tipo de actividad concreto (propio o no propio).

### Donde Entrar

- Tipo Activ Proceso (frontend/procesos/controller/tipo_activ_proceso.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- Sin control de permisos propio; autorización en `tipo_activ_proceso.php` y `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `procesos.tipo_activ_proceso_lst_posibles.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/tipo_activ_proceso_lst_posibles.md`

## Usuario Perm Activ

### Para Que Sirve

Carga de la pantalla de alta o edición de permisos de actividad para un usuario: tipo de actividad, filas de ámbitos afectados y desplegables de fase y permisos.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- `perm_jefe` según `is_jefeCalendario`, oficinas `des`/`vcsd` (con SFSV=1) o `calendario` en

### Referencias Internas

- Flujo: `procesos.usuario_perm_activ.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/usuario_perm_activ.md`

## Usuario Perm Activ Ajax

### Para Que Sirve

Actualizar dinámicamente las opciones del desplegable `fase_ref[]` al cambiar el tipo de actividad o la delegación en la pantalla de permisos de actividad de usuario.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- Sin control de permisos propio; autorización en `usuario_perm_activ.php` y `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `procesos.usuario_perm_activ_ajax.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/usuario_perm_activ_ajax.md`

## Revision Pendiente

- Sustituir nombres tecnicos por nombres visibles en la aplicacion.
- Completar rutas de menu.
- Confirmar permisos necesarios.
- Anadir capturas o ejemplos si se quiere publicar para usuarios finales.
