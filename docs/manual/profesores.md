---
tipo: "manual_usuario"
modulo: "profesores"
flujos: 6
estado_revision: "generado"
---

# Manual De Usuario - profesores

Este manual es un borrador generado desde `docs/catalogo`. Debe revisarse para ajustar nombres de menu, permisos, validaciones y lenguaje final de usuario.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas de menu y nombres visibles pueden necesitar revision manual.

## Consultar congresos

### Para Que Sirve

Revisar congresos registrados por profesor (tipo, lugar, fechas, organizador).

### Donde Entrar

- Asistencia a congresos (frontend/profesores/controller/congresos.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Consultar

1. Abrir **asistencia a congresos** desde el menú `stgr2`.
2. Revisar la tabla `tabla_congreso`.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin `perm_*` en caso de uso; autorización en frontend + `$_SESSION['oPerm']` (menú `stgr2`).

### Referencias Internas

- Flujo: `profesores.congresos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/profesores/flujos/congresos.md`

## Ver docencia global

### Para Que Sirve

Revisar qué docencia consta registrada por profesor, curso, asignatura y acta.

### Donde Entrar

- Ver docencia (frontend/profesores/controller/docencia.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Consultar

1. Abrir **ver docencia** desde el menú `stgr2`.
2. Revisar la tabla `tabla_docencia`.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin `perm_*` en caso de uso; autorización en frontend + `$_SESSION['oPerm']` (menú `stgr2`).

### Referencias Internas

- Flujo: `profesores.docencia.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/profesores/flujos/docencia.md`

## Ver ficha profesor STGR

### Para Que Sirve

Consultar (e imprimir o modificar con permiso) la ficha STGR de un profesor: nombramientos, curriculum, docencia, congresos, etc.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Consultar

1. Buscar persona y abrir **ficha profesor stgr**.
2. Revisar bloques visibles según `aPerm`.

#### Imprimir

1. Pulsar **[imprimir]** → recarga con `print=1` (forzado en RSTGR).

#### Modificar bloque

1. Con permiso de escritura, pulsar **[modificar]** en un bloque → `tablaDB_lista_ver`.

### Errores O Avisos Frecuentes

- `No encuentro a nadie con id_nom: %s`

### Permisos

- Frontend: si `$_SESSION['oPerm']->have_perm_oficina('est')`, fuerza `permiso=3`.
- Lectura/escritura por sección: `PermDossier` según tipo dossier (1012–1025).
- Entrada habitual desde `personas_select` (botón **ficha profesor stgr**); sin menú directo.

### Referencias Internas

- Flujo: `profesores.ficha_profesor_stgr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/profesores/flujos/ficha_profesor_stgr.md`

## Consultar claustro

### Para Que Sirve

Ver quiénes integran el claustro vigente, opcionalmente filtrado por delegación en RSTGR.

### Donde Entrar

- Claustro por departamentos (frontend/profesores/controller/lista_por_departamentos.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Consultar

1. Abrir **claustro** desde el menú.
2. En RSTGR sin filtro: marcar delegaciones y pulsar **Aplicar filtro** (`filtro=1`, `dl[]`).
3. Revisar departamentos con subsecciones director y tipos de profesor.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin `perm_*` en caso de uso; autorización en frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `profesores.lista_por_departamentos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/profesores/flujos/lista_por_departamentos.md`

## Tabla AJAX profesores asignatura

### Para Que Sirve

Obtener la lista de profesores para la asignatura seleccionada sin recargar la pantalla principal.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Consultar

1. El usuario cambia la asignatura en el desplegable.
2. POST a `profesor_asignatura_ajax.php` con `id_asignatura`.
3. Se inserta HTML de tabla en el contenedor de la pantalla padre.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin `perm_*` en caso de uso; autorización en frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `profesores.profesor_asignatura_ajax.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/profesores/flujos/profesor_asignatura_ajax.md`

## Buscar profesor para asignatura

### Para Que Sirve

Elegir asignatura y ver candidatos (departamento + ampliación) con datos de contacto y docencia previa, como apoyo antes de asignar en el curso académico.

### Donde Entrar

- Profesor para asignatura (frontend/profesores/controller/profesor_asignatura_que.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Consultar

1. Abrir **profesor para asignatura** desde el menú.
2. Elegir asignatura en el desplegable (`fnjs_profes`).
3. Revisar la tabla AJAX con profesores, centro, docencia y contacto.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin `perm_*` en caso de uso; autorización en frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `profesores.profesor_asignatura_que.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/profesores/flujos/profesor_asignatura_que.md`

## Revision Pendiente

- Sustituir nombres tecnicos por nombres visibles en la aplicacion.
- Completar rutas de menu.
- Confirmar permisos necesarios.
- Anadir capturas o ejemplos si se quiere publicar para usuarios finales.
