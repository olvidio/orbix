---
tipo: "manual_usuario"
modulo: "cambios"
flujos: 11
estado_revision: "generado"
---

# Manual De Usuario - cambios

Este manual es un borrador generado desde `docs/catalogo`. Debe revisarse para ajustar nombres de menu, permisos, validaciones y lenguaje final de usuario.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas de menu y nombres visibles pueden necesitar revision manual.

## Consultar y purgar cambios

### Para Que Sirve

Ver los cambios registrados pendientes de avisar y eliminar los que ya no interesan (por fila o por fecha límite).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `debe indicar la fecha`
- `Hay un error, no se ha eliminado`
- `Hay un error al eliminar los cambios hasta la fecha indicada`

### Permisos

- Sin control propio; `is_admin` lo calcula `CambiosPermSupport::isAdmin()` en el frontend.
- Sin control propio; la autorización se resuelve en frontend (`avisos_generar`) + `$_SESSION['oPerm']`.
- Sin control propio; autorización en frontend (`avisos_generar`) + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `cambios.avisos_generar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cambios/flujos/avisos_generar.md`

## Eliminar cambio anotado

### Para Que Sirve

Quitar de la cola de avisos un `CambioUsuario` concreto seleccionado en la lista de cambios.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

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

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `debe indicar la fecha`
- `Hay un error al eliminar los cambios hasta la fecha indicada`

### Permisos

- Sin control propio; autorización en frontend (`avisos_generar`) + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `cambios.cambio_usuario_eliminar_hasta_fecha.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cambios/flujos/cambio_usuario_eliminar_hasta_fecha.md`

## Guardar objeto de aviso

### Para Que Sirve

Persistir la parte «objeto + tipo de actividad + fase + flags de aviso» de una preferencia.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `falta id_usuario, id_tipo_activ invalido, Hay un error, no se ha guardado`

### Permisos

- Sin control propio; formulario `usuario_avisos_pref` + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `cambios.cambio_usuario_objeto_pref.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cambios/flujos/cambio_usuario_objeto_pref.md`

## Actualizar fases de referencia

### Para Que Sirve

Refrescar el desplegable de fase/estado al cambiar objeto o tipo de actividad en el formulario de aviso.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `primero debe elegir un objeto sobre el que mirar los cambios`

### Permisos

- Sin control propio; invocado desde el formulario de preferencias del usuario.

### Referencias Internas

- Flujo: `cambios.cambio_usuario_objeto_pref_fases.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cambios/flujos/cambio_usuario_objeto_pref_fases.md`

## Cargar propiedades vigilables

### Para Que Sirve

Mostrar la tabla de campos del objeto que pueden vigilarse, con el estado guardado preseleccionado.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `Usuario no encontrado, Usuario sin rol asignado, objeto %s no encontrado`

### Permisos

- Filtrado implícito por rol del usuario de sesión (`PAU_CDC` preselecciona `id_ubi`).

### Referencias Internas

- Flujo: `cambios.cambio_usuario_objeto_pref_propiedades.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cambios/flujos/cambio_usuario_objeto_pref_propiedades.md`

## Sincronizar propiedades

### Para Que Sirve

Tras guardar el objeto-pref, crear/actualizar/eliminar las `CambioUsuarioPropiedadPref` según los checkboxes y condiciones del formulario.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `faltan parametros, Hay un error, no se ha guardado, Hay un error, no se ha eliminado`

### Permisos

- Sin control propio; segundo paso de `fnjs_grabar_todo` en `usuario_avisos_pref`.

### Referencias Internas

- Flujo: `cambios.cambio_usuario_propiedad_pref_guardar_todas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cambios/flujos/cambio_usuario_propiedad_pref_guardar_todas.md`

## Editar condición de propiedad

### Para Que Sirve

Abrir el modal para definir operador, valor y alcance de un cambio en una propiedad concreta.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Filtro de casas: rol `PAU_CDC` (solo sus ubicaciones), permisos oficina `des`/`vcsd` (todas

### Referencias Internas

- Flujo: `cambios.cambio_usuario_propiedad_pref_item.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cambios/flujos/cambio_usuario_propiedad_pref_item.md`

## Preview de condición

### Para Que Sirve

Ver el texto de la condición y guardar el JSON en la fila de propiedades sin persistir aún en base de datos (la persistencia ocurre al grabar todo).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio; invocado desde el modal de condición antes de grabar el conjunto.

### Referencias Internas

- Flujo: `cambios.cambio_usuario_propiedad_pref_preview.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cambios/flujos/cambio_usuario_propiedad_pref_preview.md`

## Configurar preferencia de aviso

### Para Que Sirve

Definir qué cambios debe recibir un usuario o grupo: objeto, ámbito (tipo/fase/casas) y propiedades con condiciones opcionales.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

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

## avisos del usuario

### Para Que Sirve

Consultar y mantener las reglas de aviso configuradas para un usuario web.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `No tiene permiso`

### Permisos

- Gate en el caso de uso: requiere app `cambios` y `quien=usuario`; no comprueba permisos de oficina
- Sin control propio; autorización en `usuario_form_avisos` + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `cambios.usuario_form_avisos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cambios/flujos/usuario_form_avisos.md`

## Revision Pendiente

- Sustituir nombres tecnicos por nombres visibles en la aplicacion.
- Completar rutas de menu.
- Confirmar permisos necesarios.
- Anadir capturas o ejemplos si se quiere publicar para usuarios finales.
