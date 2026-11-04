---
tipo: "manual_usuario"
modulo: "procesos"
flujos: 17
estado_revision: "revisado"
---

# Manual De Usuario - procesos

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Ver y avanzar el proceso de una actividad

### Para Que Sirve

Ver y avanzar las fases del proceso de una actividad (tareas completadas, observaciones).

### Donde Entrar

- Actividad Proceso (frontend/procesos/controller/actividad_proceso.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ver y avanzar el proceso de una actividad

1. Marcar la actividad y pulsar **proceso**.
2. Se muestra el título, el enlace **(dossiers)** y «Fases de la actividad», con la opción **forzar** y una fila por fase y tarea.
3. Marcar las tareas hechas, escribir observaciones y guardar.
4. Si el proceso de la actividad no se ha generado bien, aparece un error del tipo «La fase del proceso tipo: 6, fase: 2, tarea: 0»; los procesos se generan al crear la actividad, así que este error indica un dato incompleto de esa actividad; en ese caso hay que regenerar el proceso.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización se resuelve en el
- No hay `perm_*` en el caso de uso; `puede_editar` por fila según oficina responsable y
- Sin control de permisos propio; el frontend solo habilita edición en filas con `puede_editar`.

### Referencias Internas

- Flujo: `procesos.actividad_proceso.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/actividad_proceso.md`

## Regenerar el proceso de una actividad

### Para Que Sirve

Regenerar las tareas del proceso de una actividad, conservando o no su estado actual.

### Donde Entrar

- Actividad Proceso (frontend/procesos/controller/actividad_proceso.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Regenerar el proceso de una actividad

1. Abrir la ficha de la actividad y su pestaña de proceso.
2. Pulsar regenerar.
3. Marcar «forzar» si se quiere descartar el estado actual de las tareas; si no, se conserva.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- Sin control de permisos propio; el frontend solo muestra la acción si `permiso_calendario`.

### Referencias Internas

- Flujo: `procesos.actividad_proceso_generar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/actividad_proceso_generar.md`

## Filtrar actividades por fases

### Para Que Sirve

Mostrar las fases de un tipo de actividad para marcarlas en el filtro de búsqueda de actividades.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Filtrar actividades por fases

1. En la búsqueda de actividades, elegir el tipo.
2. Aparecen las fases posibles como casillas para marcar las que deben estar completadas (fases_on) o no (fases_off).

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- Sin control de permisos propio; autorización en `actividad_que.php` y `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `procesos.actividad_que_fases_ajax.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/actividad_que_fases_ajax.md`

## Cambiar de fase varias actividades

### Para Que Sirve

Cambiar de fase varias actividades a la vez.

### Donde Entrar

- Fases Activ Cambio (frontend/procesos/controller/fases_activ_cambio.php)
- **Legacy:** Calendario > actividades > cambiar de fase; dre > actividades > cambiar de fase
- **Pills2:** ATENCIÓN SACD > Actividades > cambiar de fase; dre > actividades > cambiar de fase; Calendario > actividades > cambiar de fase; ACTIVIDADES > Herramientas de calendario > Cambio de fase actividades

### Tareas Habituales

#### Cambiar de fase varias actividades

1. Elegir el tipo de actividad, el periodo y la fase destino y listar.
2. Marcar las actividades que pasan a la fase nueva.
3. Pulsar para aplicar el cambio.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- Sin control de permisos propio; autorización en `fases_activ_cambio.php` y `$_SESSION['oPerm']`.
- Sin control de permisos propio; autorización en frontend y `$_SESSION['oPerm']`.
- Por actividad: exige permiso de oficina responsable (`have_perm_oficina`) salvo oficina vacía.

### Referencias Internas

- Flujo: `procesos.fases_activ_cambio.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/fases_activ_cambio.md`

## Elegir el tipo de actividad para cambiar de fase

### Para Que Sirve

Mostrar el selector de tipo de actividad en la pantalla de cambio de fase masivo.

### Donde Entrar

- Fases Activ Cambio (frontend/procesos/controller/fases_activ_cambio.php)
- **Legacy:** Calendario > actividades > cambiar de fase; dre > actividades > cambiar de fase
- **Pills2:** ATENCIÓN SACD > Actividades > cambiar de fase; dre > actividades > cambiar de fase; Calendario > actividades > cambiar de fase; ACTIVIDADES > Herramientas de calendario > Cambio de fase actividades

### Tareas Habituales

#### Elegir el tipo de actividad para cambiar de fase

1. Entrar en **cambiar de fase**.
2. Elegir el tipo de actividad; el resto de opciones de la pantalla se adaptan a él.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- No llama a `perm_*` explícito; restringe tipos visibles según oficinas `vcsd`/`des`/`calendario`

### Referencias Internas

- Flujo: `procesos.fases_activ_cambio_tipo_html.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/fases_activ_cambio_tipo_html.md`

## Administrar las tareas de un proceso

### Para Que Sirve

Administrar las fases y tareas de un tipo de proceso: verlas, crear o editar tareas y eliminarlas.

### Donde Entrar

- Procesos Select (frontend/procesos/controller/procesos_select.php)
- **Legacy:** sistema > procesos activ. > procesos
- **Pills2:** ADMIN LOCAL > procesos activ. > procesos

### Tareas Habituales

#### Administrar las tareas de un proceso

1. Entrar en **procesos**, elegir el proceso y pulsar **ver listado**.
2. Crear o modificar una tarea eligiendo fase, status, oficina y de qué tarea depende; guardar.
3. Para quitar una tarea, eliminarla y confirmar.
4. Después, regenerar las actividades si se quiere que recojan el cambio.

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

## Clonar un proceso

### Para Que Sirve

Copiar las tareas de un proceso de referencia sobre el proceso elegido, sustituyendo las que tenía.

### Donde Entrar

- Procesos Select (frontend/procesos/controller/procesos_select.php)
- **Legacy:** sistema > procesos activ. > procesos
- **Pills2:** ADMIN LOCAL > procesos activ. > procesos

### Tareas Habituales

#### Clonar un proceso

1. En **procesos**, elegir en **Proceso** el que se quiere rellenar.
2. En **clonar de** elegir el proceso de referencia.
3. Pulsar **clonar**. Atención: las tareas existentes del proceso destino se sustituyen por las del de referencia.

### Errores O Avisos Frecuentes

- `no se ha indicado el proceso a clonar`

### Permisos

- Sin control de permisos propio; autorización en `procesos_select.php` y `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `procesos.procesos_clonar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/procesos_clonar.md`

## Elegir de qué tarea depende

### Para Que Sirve

Actualizar la lista de tareas de las que puede depender una tarea al cambiar la fase.

### Donde Entrar

- Procesos Ver (frontend/procesos/controller/procesos_ver.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Elegir de qué tarea depende

1. En el formulario de tarea, cambiar la fase o la fase previa.
2. El desplegable de tareas dependientes se actualiza con las tareas posibles; elegir una.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- Sin control de permisos propio; autorización en `procesos_ver.php` y `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `procesos.procesos_depende.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/procesos_depende.md`

## Consultar y modificar las tareas de un proceso

### Para Que Sirve

Ver las fases y tareas de un tipo de proceso en forma de tabla, con acciones para modificar o eliminar cada tarea.

### Donde Entrar

- Procesos Get Listado (frontend/procesos/controller/procesos_get_listado.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Consultar y modificar las tareas de un proceso

1. En **procesos**, elegir el proceso y pulsar **ver listado**.
2. Se muestra una tabla con cada fase y tarea.
3. Pulsar una tarea para modificarla, o su botón de eliminar para quitarla (con confirmación).
4. Para añadir una tarea, elegir el proceso y pulsar **nuevo**.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- Filtrado implícito por SFSV/rol (SuperAdmin ve SF+SV); no usa `perm_*` de oficina.

### Referencias Internas

- Flujo: `procesos.procesos_get_listado.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/procesos_get_listado.md`

## Regenerar las actividades de un proceso

### Para Que Sirve

Regenerar las tareas de todas las actividades que usan un tipo de proceso, tras cambiar su definición.

### Donde Entrar

- Procesos Select (frontend/procesos/controller/procesos_select.php)
- **Legacy:** sistema > procesos activ. > procesos
- **Pills2:** ADMIN LOCAL > procesos activ. > procesos

### Tareas Habituales

#### Regenerar las actividades de un proceso

1. En **procesos**, elegir el proceso.
2. Pulsar **rehacer los procesos de las actividades**. Las tareas de las actividades asociadas se recalculan a partir de la definición actual de fases y tareas.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- Sin control de permisos propio; autorización en `procesos_select.php` y `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `procesos.procesos_regenerar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/procesos_regenerar.md`

## Elegir el proceso que se quiere administrar

### Para Que Sirve

Elegir el tipo de proceso de actividades que se quiere revisar o modificar, y clonar sus tareas desde otro.

### Donde Entrar

- Procesos Select (frontend/procesos/controller/procesos_select.php)
- **Legacy:** sistema > procesos activ. > procesos
- **Pills2:** ADMIN LOCAL > procesos activ. > procesos

### Tareas Habituales

#### Elegir el proceso que se quiere administrar

1. Entrar en **procesos**.
2. En **Proceso** elegir el tipo (actividad n, agd, sacd, sg, sr, estudios, general…; hay variantes «(no dl)»).
3. Pulsar **ver listado** para abrir sus fases y tareas.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- Sin control de permisos propio; autorización en `procesos_select.php` y `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `procesos.procesos_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/procesos_select.md`

## Crear o modificar una tarea de un proceso

### Para Que Sirve

Crear o modificar una tarea dentro de un tipo de proceso: fase, status, oficina y tareas de las que depende.

### Donde Entrar

- Procesos Ver (frontend/procesos/controller/procesos_ver.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Crear o modificar una tarea de un proceso

1. Desde el listado del proceso, pulsar **nuevo** en la pantalla de selección de proceso (o una tarea existente del listado).
2. Elegir la fase, la tarea, el status, la oficina y la fase o tarea previa de la que depende.
3. Guardar. Al guardar, las actividades que usen este proceso deberán regenerarse para recoger el cambio.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- Sin control de permisos propio; autorización en `procesos_ver.php` y `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `procesos.procesos_ver.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/procesos_ver.md`

## Consultar los procesos de cada tipo de actividad

### Para Que Sirve

Ver los tipos de actividad con el proceso que tienen asignado.

### Donde Entrar

- Tipo Activ Proceso (frontend/procesos/controller/tipo_activ_proceso.php)
- **Legacy:** sistema > procesos activ. > tipo activ - proceso
- **Pills2:** ADMIN LOCAL > procesos activ. > tipo activ - proceso

### Tareas Habituales

#### Consultar los procesos de cada tipo de actividad

1. Entrar en **tipo activ - proceso**.
2. Se lista cada tipo de actividad con su proceso (propio y no propio); pulsar uno para cambiarlo.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- Sin control de permisos propio; autorización en frontend y `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `procesos.tipo_activ_proceso.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/tipo_activ_proceso.md`

## Asignar un proceso a un tipo de actividad

### Para Que Sirve

Asignar un tipo de proceso a un tipo de actividad (propio de la dl o no propio).

### Donde Entrar

- Tipo Activ Proceso (frontend/procesos/controller/tipo_activ_proceso.php)
- **Legacy:** sistema > procesos activ. > tipo activ - proceso
- **Pills2:** ADMIN LOCAL > procesos activ. > tipo activ - proceso

### Tareas Habituales

#### Asignar un proceso a un tipo de actividad

1. Entrar en **tipo activ - proceso** y elegir el tipo de actividad.
2. Elegir el proceso de la lista de posibles.
3. Guardar. Las actividades nuevas de ese tipo usarán el proceso asignado.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado el proceso`

### Permisos

- Sin control de permisos propio; autorización en `tipo_activ_proceso.php` y `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `procesos.tipo_activ_proceso_asignar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/tipo_activ_proceso_asignar.md`

## Ver los procesos asignables a un tipo de actividad

### Para Que Sirve

Mostrar los procesos que se pueden asignar a un tipo de actividad.

### Donde Entrar

- Tipo Activ Proceso (frontend/procesos/controller/tipo_activ_proceso.php)
- **Legacy:** sistema > procesos activ. > tipo activ - proceso
- **Pills2:** ADMIN LOCAL > procesos activ. > tipo activ - proceso

### Tareas Habituales

#### Ver los procesos asignables a un tipo de actividad

1. Entrar en **tipo activ - proceso**.
2. Elegir el tipo de actividad (propio de la dl o no propio).
3. Se muestra la mini-tabla de procesos posibles.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- Sin control de permisos propio; autorización en `tipo_activ_proceso.php` y `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `procesos.tipo_activ_proceso_lst_posibles.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/tipo_activ_proceso_lst_posibles.md`

## Definir un permiso de actividades de un usuario

### Para Que Sirve

Dar de alta o modificar un permiso de actividades de un usuario: tipo de actividad, ámbito, fase y permiso.

### Donde Entrar

- Usuario Perm Activ (frontend/procesos/controller/usuario_perm_activ.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Definir un permiso de actividades de un usuario

1. En la ficha del usuario, pulsar **añadir permiso**.
2. Elegir el tipo de actividad y el ámbito (propia dl u otras).
3. Elegir la fase de referencia y el permiso.
4. Guardar. Manda el nivel más concreto («sv n crt» sobre «sv n»).

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- `perm_jefe` según `is_jefeCalendario`, oficinas `des`/`vcsd` (con SFSV=1) o `calendario` en

### Referencias Internas

- Flujo: `procesos.usuario_perm_activ.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/usuario_perm_activ.md`

## Elegir la fase de referencia

### Para Que Sirve

Actualizar las fases disponibles al cambiar el tipo de actividad o la delegación en el permiso de un usuario.

### Donde Entrar

- Usuario Perm Activ (frontend/procesos/controller/usuario_perm_activ.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Elegir la fase de referencia

1. En el permiso, cambiar el tipo de actividad o la delegación.
2. El desplegable de fase de referencia se actualiza con las fases válidas.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Permisos

- Sin control de permisos propio; autorización en `usuario_perm_activ.php` y `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `procesos.usuario_perm_activ_ajax.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/procesos/flujos/usuario_perm_activ_ajax.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/procesos/flujos/`.
