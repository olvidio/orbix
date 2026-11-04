---
tipo: "manual_usuario"
modulo: "profesores"
flujos: 6
estado_revision: "revisado"
---

# Manual De Usuario - profesores

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Consultar la asistencia a congresos

### Para Que Sirve

Consultar la asistencia de los profesores a congresos, claustros, reuniones y otros eventos.

### Donde Entrar

- Asistencia a congresos (frontend/profesores/controller/congresos.php)
- **Legacy:** vest > actas... > asitencia a congresos
- **Pills2:** sin entrada en el índice

### Tareas Habituales

#### Consultar la asistencia a congresos

1. Entrar en **asitencia a congresos**.
2. Se muestra una tabla con «apellidos, nombre», «tipo» (claustro, reunión, congreso, cv…), «lugar», «inicio», «fin» y «organiza».
3. Arriba a la derecha de la tabla hay dos iconos (lupa y guardar) para buscar en ella y exportarla.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin `perm_*` en caso de uso; autorización en frontend + `$_SESSION['oPerm']` (menú `stgr2`).

### Referencias Internas

- Flujo: `profesores.congresos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/profesores/flujos/congresos.md`

## Consultar la docencia

### Para Que Sirve

Consultar la docencia impartida por los profesores: curso, asignatura, modo y acta.

### Donde Entrar

- Ver docencia (frontend/profesores/controller/docencia.php)
- **Legacy:** vest > actas... > ver docencia
- **Pills2:** sin entrada en el índice

### Tareas Habituales

#### Consultar la docencia

1. Entrar en **ver docencia**.
2. Se muestra una tabla con «apellidos, nombre», «inicio curso», «asignatura», «modo» (por ejemplo «sem. invierno» o «ca/cv») y «acta».
3. Arriba a la derecha de la tabla hay dos iconos (lupa y guardar) para buscar en ella y exportarla.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin `perm_*` en caso de uso; autorización en frontend + `$_SESSION['oPerm']` (menú `stgr2`).

### Referencias Internas

- Flujo: `profesores.docencia.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/profesores/flujos/docencia.md`

## Consultar la ficha de un profesor

### Para Que Sirve

Consultar la ficha stgr de un profesor (nombramientos, curriculum, docencia, congresos…) y, con permiso, imprimirla o modificarla.

### Donde Entrar

- Ficha profesor STGR (frontend/profesores/controller/ficha_profesor_stgr.php)
- sin entrada de menú en el índice (desde búsqueda de personas / `personas_select`)

### Tareas Habituales

#### Consultar la ficha de un profesor

1. Buscar a la persona y pulsar **ficha profesor stgr**.
2. Se muestran los bloques de la ficha que el usuario tiene permiso para ver.

#### Imprimir la ficha

1. Pulsar **[imprimir]**: la ficha se vuelve a mostrar en formato de impresión (en las regiones del stgr siempre sale así).

#### Modificar un bloque de la ficha

1. Con permiso de escritura, pulsar **[modificar]** en el bloque que se quiere cambiar.
2. Se abre la tabla de ese bloque para editarla.

### Errores O Avisos Frecuentes

- `No encuentro a nadie con id_nom: %s`

### Permisos

- Frontend: si `$_SESSION['oPerm']->have_perm_oficina('est')`, fuerza `permiso=3`.
- Lectura/escritura por sección: `PermDossier` según tipo dossier (1012–1025).
- Entrada habitual desde `personas_select` (botón **ficha profesor stgr**); sin menú directo.

### Referencias Internas

- Flujo: `profesores.ficha_profesor_stgr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/profesores/flujos/ficha_profesor_stgr.md`

## Ver el claustro por departamentos

### Para Que Sirve

Ver el claustro de profesores agrupado por departamento y categoría.

### Donde Entrar

- Claustro por departamentos (frontend/profesores/controller/lista_por_departamentos.php)
- **Legacy:** vest > buscar persona > claustro; stgr > personas > claustro
- **Pills2:** ESTUDIOS > Datos e informes > Claustro

### Tareas Habituales

#### Ver el claustro por departamentos

1. Entrar en **claustro**.
2. Se muestra cada departamento (por ejemplo «Música») con sus categorías: director, Ordinario, Extraordinario, Adjunto, Encargado, Ayudante y Asociado.
3. Bajo cada categoría se listan los profesores que la tienen. Si una categoría sale sin nombres es que ahora mismo no hay nadie en ella (en la instancia de desarrollo solo se ven los títulos).

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin `perm_*` en caso de uso; autorización en frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `profesores.lista_por_departamentos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/profesores/flujos/lista_por_departamentos.md`

## Ver los profesores de una asignatura

### Para Que Sirve

Cargar la lista de profesores de la asignatura elegida.

### Donde Entrar

- Tabla profesores por asignatura (frontend/profesores/controller/profesor_asignatura_ajax.php)
- sin entrada de menú en el índice (fragmento AJAX de `profesor_asignatura_que`)

### Tareas Habituales

#### Ver los profesores de una asignatura

1. Elegir la asignatura en el desplegable.
2. La lista de profesores se actualiza en la misma pantalla.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin `perm_*` en caso de uso; autorización en frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `profesores.profesor_asignatura_ajax.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/profesores/flujos/profesor_asignatura_ajax.md`

## Buscar profesores para una asignatura

### Para Que Sirve

Saber qué profesores pueden impartir una asignatura.

### Donde Entrar

- Profesor para asignatura (frontend/profesores/controller/profesor_asignatura_que.php)
- **Legacy:** vest > buscar persona > profesor para asignatura; stgr > personas > profesor para asignatura
- **Pills2:** ESTUDIOS > Preparación planes estudio > Profesor para asignatura

### Tareas Habituales

#### Buscar profesores para una asignatura

1. Entrar en «Profesores que pueden impartir una asignatura».
2. Elegir la **Asignatura** en el desplegable.
3. Debajo aparece la lista de profesores que pueden darla, o «no hay ninguna fila» si no hay ninguno registrado.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin `perm_*` en caso de uso; autorización en frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `profesores.profesor_asignatura_que.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/profesores/flujos/profesor_asignatura_que.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/profesores/flujos/`.
