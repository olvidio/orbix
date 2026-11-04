---
tipo: "manual_usuario"
modulo: "cambios"
flujos: 11
estado_revision: "revisado_parcial"
---

# Manual De Usuario - cambios

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Consultar y purgar cambios

### Para Que Sirve

- Ver los cambios registrados pendientes de avisar y eliminar los que ya no interesan (por fila o por fecha límite).
- Opcionalmente regenerar la tabla de avisos desde el menú de administración.

### Donde Entrar

- Lista de cambios (frontend/cambios/controller/avisos_generar.php)
- **Legacy:** Calendario > actividades > ver lista cambios; sistema > usuarios web > ver lista cambios;
- Utilidades > Utilidades > lista de cambios
- **Pills2:** mismas rutas (+ ADMIN LOCAL en usuarios web)
- Generar tabla: **Legacy/Pills2:** sistema > usuarios web > generar tabla avisos (+ ADMIN LOCAL)

### Tareas Habituales

#### Generar tabla (batch)

1. Abrir **generar tabla avisos** (usuarios web / ADMIN LOCAL).
2. El sistema cruza cambios nuevos con preferencias y crea avisos pendientes; muestra HTML de
3. Revisar el listado de avisos que aparece.

#### Consultar el listado

1. Abrir la pantalla.
2. Si es admin, elegir usuario y tipo de aviso.
3. Se muestra la tabla con los avisos anotados.

#### Eliminar filas

1. Marcar filas en la tabla.
2. Pulsar **borrar**.

#### Eliminar hasta fecha

1. Indicar la fecha límite.
2. Pulsar **purga**: se borran los avisos anotados hasta esa fecha.

### Errores O Avisos Frecuentes

- `debe indicar la fecha`
- `Hay un error, no se ha eliminado`
- `Hay un error al eliminar los cambios hasta la fecha indicada`
- `Generar tabla: Algo falla; HTML de incidencias por cambio no procesado`

### Permisos

- Sin control propio; `is_admin` lo calcula `CambiosPermSupport::isAdmin()` en el frontend.
- Sin `perm_*` en el caso de uso. Entrada de menú restringida a perfiles con acceso a
- Sin control propio; la autorización se resuelve en frontend (`avisos_generar`) + `$_SESSION['oPerm']`.
- Sin control propio; autorización en frontend (`avisos_generar`) + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `cambios.avisos_generar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cambios/flujos/avisos_generar.md`

## Eliminar cambio anotado

### Para Que Sirve

Quitar de la cola de avisos un `CambioUsuario` concreto seleccionado en la lista de cambios.

### Donde Entrar

- Lista de cambios (frontend/cambios/controller/avisos_generar.php)
- (vía pantalla lista de cambios — ver flujo `avisos_generar`)

### Tareas Habituales

#### Eliminar

1. Marcar una o más filas del listado de avisos.
2. Confirmar borrado.
3. El listado se refresca sin las filas eliminadas.

### Errores O Avisos Frecuentes

- `Hay un error, no se ha eliminado`

### Permisos

- Sin control propio; la autorización se resuelve en frontend (`avisos_generar`) + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `cambios.cambio_usuario.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cambios/flujos/cambio_usuario.md`

## Purgar cambios hasta fecha

### Para Que Sirve

Eliminar en bloque todos los cambios anotados con fecha anterior o igual a la indicada.

### Donde Entrar

- Lista de cambios (frontend/cambios/controller/avisos_generar.php)
- (vía pantalla lista de cambios)

### Tareas Habituales

#### Eliminar hasta fecha

1. Indicar fecha límite (`f_fin`).
2. Ejecutar purga.
3. Refrescar listado.

### Errores O Avisos Frecuentes

- `debe indicar la fecha`
- `Hay un error al eliminar los cambios hasta la fecha indicada`

### Permisos

- Sin control propio; autorización en frontend (`avisos_generar`) + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `cambios.cambio_usuario_eliminar_hasta_fecha.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cambios/flujos/cambio_usuario_eliminar_hasta_fecha.md`

## Crear un aviso de cambios

### Para Que Sirve

Guardar el objeto, tipo de actividad, fase y avisos de una preferencia de aviso de cambios.

### Donde Entrar

- Configurar aviso (frontend/cambios/controller/usuario_avisos_pref.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Crear un aviso de cambios

1. Entrar en **gestionar avisos en cambios actividades**.
2. Elegir el objeto a vigilar, el tipo de actividad y la fase.
3. Marcar los campos y condiciones y guardar todo.

### Errores O Avisos Frecuentes

- `falta id_usuario, id_tipo_activ invalido, Hay un error, no se ha guardado`

### Permisos

- Sin control propio; formulario `usuario_avisos_pref` + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `cambios.cambio_usuario_objeto_pref.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cambios/flujos/cambio_usuario_objeto_pref.md`

## Elegir la fase de un aviso

### Para Que Sirve

Actualizar el desplegable de fase/estado al cambiar el objeto o el tipo de actividad.

### Donde Entrar

- Desplegable de fases (frontend/cambios/controller/usuario_avisos_pref_fases.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Elegir la fase de un aviso

1. Cambiar el objeto o el tipo de actividad.
2. El desplegable de fase se actualiza con las fases posibles.

### Errores O Avisos Frecuentes

- `primero debe elegir un objeto sobre el que mirar los cambios`

### Permisos

- Sin control propio; invocado desde el formulario de preferencias del usuario.

### Referencias Internas

- Flujo: `cambios.cambio_usuario_objeto_pref_fases.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cambios/flujos/cambio_usuario_objeto_pref_fases.md`

## Elegir qué campos vigilar

### Para Que Sirve

Mostrar los campos del objeto que se pueden vigilar, con el estado guardado.

### Donde Entrar

- Tabla de propiedades (frontend/cambios/controller/usuario_avisos_pref_propiedades.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Elegir qué campos vigilar

1. Elegir el objeto.
2. Aparece la tabla de campos; marcar los que se quieren vigilar.

### Errores O Avisos Frecuentes

- `Usuario no encontrado, Usuario sin rol asignado, objeto %s no encontrado`

### Permisos

- Filtrado implícito por rol del usuario de sesión (`PAU_CDC` preselecciona `id_ubi`).

### Referencias Internas

- Flujo: `cambios.cambio_usuario_objeto_pref_propiedades.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cambios/flujos/cambio_usuario_objeto_pref_propiedades.md`

## Guardar los avisos

### Para Que Sirve

Crear, actualizar o quitar los campos vigilados según las casillas y condiciones marcadas.

### Donde Entrar

- Configurar aviso (frontend/cambios/controller/usuario_avisos_pref.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Guardar los avisos

1. Marcar y condicionar los campos.
2. Pulsar grabar todo: se guardan el aviso y todos sus campos vigilados.

### Errores O Avisos Frecuentes

- `faltan parametros, Hay un error, no se ha guardado, Hay un error, no se ha eliminado`

### Permisos

- Sin control propio; segundo paso de `fnjs_grabar_todo` en `usuario_avisos_pref`.

### Referencias Internas

- Flujo: `cambios.cambio_usuario_propiedad_pref_guardar_todas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cambios/flujos/cambio_usuario_propiedad_pref_guardar_todas.md`

## Poner condición a un campo

### Para Que Sirve

Abrir la ventana para definir operador, valor y alcance de un campo vigilado.

### Donde Entrar

- Modal de condición (frontend/cambios/controller/usuario_avisos_pref_condicion.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Poner condición a un campo

1. En la tabla de campos, pulsar modificar en el campo.
2. Elegir el operador y el valor y el alcance, y guardar la condición.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Filtro de casas: rol `PAU_CDC` (solo sus ubicaciones), permisos oficina `des`/`vcsd` (todas

### Referencias Internas

- Flujo: `cambios.cambio_usuario_propiedad_pref_item.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cambios/flujos/cambio_usuario_propiedad_pref_item.md`

## Previsualizar una condición

### Para Que Sirve

Ver el texto de la condición y dejarla en la tabla sin guardar todavía en la base de datos.

### Donde Entrar

- Configurar aviso (frontend/cambios/controller/usuario_avisos_pref.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Previsualizar una condición

1. En la ventana de condición, pulsar guardar.
2. Se muestra el texto de la condición en la fila; no se guarda definitivamente hasta grabar todo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio; invocado desde el modal de condición antes de grabar el conjunto.

### Referencias Internas

- Flujo: `cambios.cambio_usuario_propiedad_pref_preview.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cambios/flujos/cambio_usuario_propiedad_pref_preview.md`

## Configurar un aviso de cambios en actividades

### Para Que Sirve

Configurar un aviso de cambios: qué se vigila, en qué actividades y cómo se avisa.

### Donde Entrar

- Configurar aviso (frontend/cambios/controller/usuario_avisos_pref.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Configurar un aviso de cambios en actividades

1. En «Avisos para …», elegir **cambios en:** (el objeto a vigilar).
2. Elegir si es para actividades que organiza **la propia dl** u **otras dl**, y el tipo (**sf/sv**, **asistentes**, **actividad**, **tipo de actividad**).
3. En **fase de referencia** elegir la fase (salen todas las posibles, aunque no tengas permiso; el permiso se comprueba al generar el aviso).
4. Marcar cuándo avisar: **avisar antes de que esté marcada (off)**, **avisar si está marcada (on)** (viene marcada) y **avisar aunque la actividad haya finalizado**.
5. En «opciones» elegir **ver cambios sólo de las casas** (opcional) y **avisar por:** lista o email.
6. En «campos o propiedades» marcar los campos a vigilar (enlaces **[todos]**, **[ninguno]**, **[invertir]**) y, si hace falta, una **condición**.
7. Pulsar **guardar**.

### Errores O Avisos Frecuentes

- `falta id_usuario, usuario/grupo no encontrado, preferencia no encontrada`
- `id_tipo_activ invalido, Hay un error, no se ha guardado`
- `faltan parametros (propiedades)`

### Permisos

- `perm_jefe` según: `is_jefeCalendario()`, permisos oficina `des`/`vcsd` (sv), rol `PAU_CDC`/`PAU_SACD`,
- Sin control propio; formulario `usuario_avisos_pref` + `$_SESSION['oPerm']`.
- Sin control propio; segundo paso de `fnjs_grabar_todo` en `usuario_avisos_pref`.
- Sin control propio; invocado desde el modal de condición antes de grabar el conjunto.

### Referencias Internas

- Flujo: `cambios.usuario_avisos_pref.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cambios/flujos/usuario_avisos_pref.md`

## los avisos de cambios de un usuario

### Para Que Sirve

Ver y gestionar los avisos de cambios en actividades que recibe un usuario.

### Donde Entrar

- Avisos del usuario (frontend/cambios/controller/usuario_form_avisos.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Gestionar los avisos de cambios de un usuario

1. En la ficha del usuario, apartado «avisos de cambios», se ve la tabla de avisos (o «no hay ninguna fila»).
2. Pulsar **añadir aviso** para crear uno, o marcar una fila y modificarla o eliminarla.

### Errores O Avisos Frecuentes

- `No tiene permiso`

### Permisos

- Gate en el caso de uso: requiere app `cambios` y `quien=usuario`; no comprueba permisos de oficina
- Sin control propio; autorización en `usuario_form_avisos` + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `cambios.usuario_form_avisos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cambios/flujos/usuario_form_avisos.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/cambios/flujos/`.
