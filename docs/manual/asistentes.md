---
tipo: "manual_usuario"
modulo: "asistentes"
flujos: 14
estado_revision: "generado"
---

# Manual De Usuario - asistentes

Este manual es un borrador generado desde `docs/catalogo`. Debe revisarse para ajustar nombres de menu, permisos, validaciones y lenguaje final de usuario.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas de menu y nombres visibles pueden necesitar revision manual.

## Activ Pendientes Select

### Para Que Sirve

Identificar personas sin ca/crt en el curso.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio; listado de menú: frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `asistentes.activ_pendientes_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/activ_pendientes_select.md`

## Asistente

### Para Que Sirve

Alta, edición, eliminación y movimiento de asistencia a actividades.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

#### Guardar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `falta id_activ_old`
- `faltan parametros id_activ / id_nom`
- `hay un error, no se ha eliminado`
- `hay un error, no se ha guardado`
- `los datos de asistencia los modifica la dl del asistente`

### Permisos

- Comprueba `Asistente::perm_modificar()` antes de eliminar.
- Invocación desde listados/forms: autorización en frontend + `$_SESSION['oPerm']`.
- `editar` y `mover` comprueban `Asistente::perm_modificar()` en el caso de uso.
- Alta/baja desde formularios: autorización de oficina en frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `asistentes.asistente.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/asistente.md`

## Asistente Mover

### Para Que Sirve

Mover asistente de una actividad a otra del mismo tipo.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Comprueba `perm_modificar()`; si falla, devuelve `aviso_txt` sin opciones.

### Referencias Internas

- Flujo: `asistentes.asistente_mover.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/asistente_mover.md`

## Asistente Plaza Asignar

### Para Que Sirve

Asignar plaza común a varios asistentes seleccionados.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `falta id_activ`
- `falta lista de seleccion`

### Permisos

- Por asistente: `perm_modificar()` en el caso de uso.
- Lote desde listado de actividad: autorización en frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `asistentes.asistente_plaza_asignar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/asistente_plaza_asignar.md`

## Form Actividades De Una Persona

### Para Que Sirve

Gestionar asistencias en dossier 1301 (persona).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `No se ha encontrado la actividad con id: %s`
- `No se ha encontrado el asistente (id_nom: %s, id_activ: %s)`
- `los datos de asistencia los modifica el propietario de la plaza: %s`

### Permisos

- Bloqueo por propietario de plaza en edición.
- Dossier persona: frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `asistentes.form_actividades_de_una_persona.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/form_actividades_de_una_persona.md`

## Form Asistentes A Una Actividad

### Para Que Sirve

Gestionar asistente en dossier 3101 (actividad).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `No se encontró el asistente para esta actividad.`
- `los datos de asistencia los modifica el propietario de la plaza: %s`

### Permisos

- Bloqueo por propietario de plaza en edición (no `perm_modificar` de entidad).
- Acceso al dossier: frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `asistentes.form_asistentes_a_una_actividad.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/form_asistentes_a_una_actividad.md`

## Lista Activ Ctr

### Para Que Sirve

Ver actividades asistidas agrupadas por centro.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio en el caso de uso; `ssfsv` se ajusta con `$_SESSION['oPerm']` solo para filtrar datos.

### Referencias Internas

- Flujo: `asistentes.lista_activ_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/lista_activ_ctr.md`

## Lista Asis Conjunto Activ

### Para Que Sirve

Listado conjunto de plazas en varias actividades.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio; desde `actividad_que` (`que=list_cjto`): frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `asistentes.lista_asis_conjunto_activ.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/lista_asis_conjunto_activ.md`

## Lista Asistentes

### Para Que Sirve

Consultar listado de asistentes de una actividad.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio; acceso desde ficha actividad: frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `asistentes.lista_asistentes.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/lista_asistentes.md`

## Lista Est Ctr

### Para Que Sirve

Ver estudios matriculados por centro.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio; listado de menú: frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `asistentes.lista_est_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/lista_est_ctr.md`

## Lista Ultim Que Ctr

### Para Que Sirve

Elegir centro para informe de última asistencia.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio; entrada menú vsg: frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `asistentes.lista_ultim_que_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/lista_ultim_que_ctr.md`

## Lista Ultima Activ

### Para Que Sirve

Informe de personas s sin asistencia reciente.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `No sé en que tipo de actividad hay que mirar las asistencias`

### Permisos

- Sin control propio; informes de menú vsg: frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `asistentes.lista_ultima_activ.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/lista_ultima_activ.md`

## Que Ctr

### Para Que Sirve

Filtrar por centro y periodo antes de listados por centros.

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

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio; listados de menú ACTIVIDADES: frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `asistentes.que_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/que_ctr.md`

## Tabla Peticiones

### Para Que Sirve

Ver peticiones de plaza y mover asistente a actividad preferida.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio; requiere `actividadplazas`; acceso desde actividad: frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `asistentes.tabla_peticiones.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/tabla_peticiones.md`

## Revision Pendiente

- Sustituir nombres tecnicos por nombres visibles en la aplicacion.
- Completar rutas de menu.
- Confirmar permisos necesarios.
- Anadir capturas o ejemplos si se quiere publicar para usuarios finales.
