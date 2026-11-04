---
tipo: "manual_usuario"
modulo: "notas"
flujos: 31
estado_revision: "revisado"
---

# Manual De Usuario - notas

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Crear un acta

### Para Que Sirve

Ciclo completo de un acta: listarla, abrirla, crearla, modificarla o eliminarla, con PDF e impresión.

### Donde Entrar

- Acta Select (frontend/notas/controller/acta_select.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/dossier)

### Tareas Habituales

#### Crear un acta

1. Entrar en **actas** y pulsar nueva acta: se abre **DATOS DEL ACTA**.
2. El **Acta** viene propuesta con el siguiente número de la delegación (por ejemplo «dlb 39/26», con «última = dlb 38/26»); se puede cambiar, con formato num/aa.
3. Rellenar la **Fecha acta** (obligatoria), **Libro**, **Página**, **Línea**, **Lugar** y **Observaciones**.
4. Elegir la **Asignatura** escribiendo parte del nombre y eligiendo de la lista (es obligatoria aunque la pantalla no lo avise; si falta sale un error técnico, D-NOT-2).
5. En **Tribunal**, escribir el **Examinador** (con **Más** se añaden más).
6. Pulsar **Guardar cambios acta**. Después, abriendo el acta, aparece **Alumnos del acta** (al principio «Todavía no hay notas asociadas a este acta») y **Añadir alumno al acta**.

#### Eliminar un acta

1. En **actas**, marcar el acta y pulsar eliminar; confirmar «esto eliminará los datos del acta, pero no las notas que mantendrán el número de acta». Las notas ya puestas siguen existiendo con ese número de acta.

### Errores O Avisos Frecuentes

- `No se encuentra el acta`

### Permisos

- `have_perm_oficina('est')` en `acta_select` (DL).
- Frontend `acta_select`/`acta_ver`: `have_perm_oficina('est')` en ámbito DL; en `rstgr` solo lectura.

### Referencias Internas

- Flujo: `notas.acta.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/acta.md`

## Imprimir un acta

### Para Que Sirve

Preparar el acta para imprimirla (cara A delante y cara B detrás) o sacarla en PDF.

### Donde Entrar

- Acta Imprimir (frontend/notas/controller/acta_imprimir.php)
- Acta Imprimir Mpdf (frontend/notas/controller/acta_imprimir_mpdf.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Imprimir un acta

1. Abrir el acta en **actas**.
2. Pulsar imprimir: aparecen **Cara A (delante)** y **Cara B (detrás)**, o la opción **PDF**.
3. Imprimir antes de subir el PDF firmado: el acta debe imprimirse después de su último cambio.

### Errores O Avisos Frecuentes

- `Falta el acta`
- `No se encuentra el acta: %s`
- `El acta no tiene asignatura asociada`
- `No se ha encontrado la asignatura con id: %s`
- `La asignatura no tiene tipo`
- `No se ha encontrado el tipo de asignatura con id: %s`

### Permisos

- Desde `acta_imprimir` / selección en `acta_select`.

### Referencias Internas

- Flujo: `notas.acta_imprimir_presentacion.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/acta_imprimir_presentacion.md`

## Listar las actas de un periodo

### Para Que Sirve

Ver las actas de un periodo de fechas, por año o trimestre.

### Donde Entrar

- Acta Listado Anual (frontend/notas/controller/acta_listado_anual.php)
- **Legacy:** vest > actas... > listado actas
- **Pills2:** ESTUDIOS > Actas y certificados > Listado de actas; vest > actas... > listado actas

### Tareas Habituales

#### Listar las actas de un periodo

1. Entrar en **listado actas**.
2. En **Periodo de selección** elegir el trimestre (o todo el año) y el año, o escribir dos fechas en **entre … y …**.
3. Se muestra la lista de actas del periodo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Menú listado actas; `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `notas.acta_listado_anual.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/acta_listado_anual.md`

## Modificar un acta

### Para Que Sirve

Guardar los cambios de un acta existente.

### Donde Entrar

- Acta Ver (frontend/notas/controller/acta_ver.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Modificar un acta

1. Abrir el acta desde **actas**.
2. Cambiar la fecha, libro, página, línea, lugar, observaciones, la asignatura o los examinadores; el número de acta ya no se cambia. La fecha es obligatoria.
3. Pulsar **Guardar cambios acta**. Un acta que ya tiene el PDF firmado adjunto no se puede modificar.

### Errores O Avisos Frecuentes

- `No se encuentra el acta`

### Permisos

- Igual que `acta_nueva` (`est` en DL).

### Referencias Internas

- Flujo: `notas.acta_modificar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/acta_modificar.md`

## el PDF del acta

### Para Que Sirve

Gestionar el PDF escaneado del acta: subirlo, descargarlo o eliminarlo.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Gestionar el PDF del acta

1. Subir: elegir el PDF firmado y subirlo (el acta debe haberse impreso tras el último cambio).
2. Descargar: pulsar descargar PDF.
3. Eliminar: quitar el PDF para poder modificar el acta de nuevo.

### Errores O Avisos Frecuentes

- `No se encuentra el acta`

### Permisos

- Desde `acta_ver` con permiso de edición de actas.

### Referencias Internas

- Flujo: `notas.acta_pdf.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/acta_pdf.md`

## Descargar el PDF de un acta

### Para Que Sirve

Descargar el PDF firmado de un acta.

### Donde Entrar

- Acta Pdf Download (frontend/notas/controller/acta_pdf_download.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Descargar el PDF de un acta

1. En **actas**, marcar el acta.
2. Pulsar descargar PDF: se abre en una pestaña nueva (el enlace caduca a los pocos minutos).

### Errores O Avisos Frecuentes

- `Enlace de descarga no válido o caducado.`
- `No se encuentra el acta.`
- `No hay PDF asociado a este acta.`

### Permisos

- Token firmado generado en `acta_select`/`acta_ver` (`SignedDownloadToken`).

### Referencias Internas

- Flujo: `notas.acta_pdf_download.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/acta_pdf_download.md`

## Subir el PDF firmado

### Para Que Sirve

Adjuntar a un acta el PDF firmado.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Subir el PDF firmado

1. Imprimir el acta después del último cambio y firmarla.
2. En la ficha, elegir el fichero PDF y subirlo.
3. Si es demasiado grande aparece «El servidor rechazó la subida: archivo o petición demasiado grandes».
4. Desde ese momento el acta queda cerrada y no se puede modificar.

### Errores O Avisos Frecuentes

- `No se encuentra el acta`
- `No se puede subir el archivo %s`
- `No se puede leer el archivo %s`
- `No se puede abrir el archivo %s`
- `Debe imprimir el acta después del último cambio antes de subir el PDF firmado`

### Permisos

- Desde `acta_ver` (`fnjs_upload_pdf`).

### Referencias Internas

- Flujo: `notas.acta_pdf_subir.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/acta_pdf_subir.md`

## Buscar un acta

### Para Que Sirve

Buscar actas del curso y abrir, modificar, imprimir o descargar su PDF.

### Donde Entrar

- Acta Select (frontend/notas/controller/acta_select.php)
- **Legacy:** vest > actas... > actas; stgr > actas > actas
- **Pills2:** ESTUDIOS > Actas y certificados > Actas; vest > actas... > actas; stgr > actas > actas

### Tareas Habituales

#### Buscar un acta

1. Entrar en **actas**.
2. Escribir en el buscador el número de acta con formato nn/aa (por ejemplo 12/21). Se pueden usar comodines: un punto (.) es cualquier carácter y .* cualquier cantidad de caracteres (por ejemplo «dlm. 2/19» devuelve dlmE 2/19, dlmO 2/19…).
3. En una delegación solo se buscan las actas de la propia delegación o región; una región con delegaciones busca en todas.
4. Pulsar el acta de la tabla para abrirla.

#### Imprimir o descargar un acta

1. En la tabla, marcar el acta.
2. Elegir imprimir o descargar el PDF.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Menú actas; edición solo DL con `est`.

### Referencias Internas

- Flujo: `notas.acta_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/acta_select.md`

## Ver o modificar la cabecera del acta

### Para Que Sirve

Consultar o modificar la cabecera de un acta (asignatura, fechas, libro, tribunal, PDF) y, cuando se abre desde el listado de actas, ver las notas ya grabadas y añadir un alumno.

### Donde Entrar

- Acta Ver (frontend/notas/controller/acta_ver.php)
- **Legacy:** sin entrada de menú en el índice (fragmento desde listado ESTUDIOS > Actas)
- **Pills2:** sin entrada de menú en el índice (fragmento desde listado ESTUDIOS > Actas)

### Tareas Habituales

#### Ver o modificar la cabecera del acta

1. Abrir el acta desde el listado o desde la actividad.
2. Se muestra la cabecera con los datos del acta.
3. Para un acta nueva, rellenarla y guardar; para uno existente, corregir lo que haga falta y guardar los cambios.

#### Ver las notas del acta

1. Con un acta ya creada y con asignatura, debajo de la cabecera aparece la tabla de alumnos con su nombre, nota y situación.
2. Si de alguna nota no se puede ver el nombre, sale un aviso.

#### Añadir un alumno al acta

1. Solo está disponible desde el listado de actas, con permiso de escritura y si el acta no tiene el PDF firmado.
2. Elegir a la persona en el desplegable de candidatos (de la dl o publicados para la dl; no salen quienes están en repaso ni quienes ya tienen nota de esa asignatura).
3. Escribir la nota y la nota máxima y guardar.
4. El listado se recarga con el alumno añadido.

### Errores O Avisos Frecuentes

- `Faltan acta o persona`
- `No se encuentra el acta`
- `El acta está firmada y no se puede modificar`
- `El acta no tiene asignatura`
- `Falta el acta`
- `Aviso listado: existe una nota de la que no se tiene acceso al nombre (id_nom = %s)`

### Permisos

- `scope_permiso` (default 3); forzado 0 en `rstgr`.
- Sin control propio en el caso de uso. El frontend solo llama este endpoint en contexto standalone (`$notas` y `$Qnotas` vacíos), acta existente, no modo nueva, y con asignatura válida. Autorización de pantalla: `have_perm_oficina('est')` / `rstgr` solo lectura.
- Sin `perm_*` en el caso de uso. Frontend solo pide el form si `$permiso === 3` y el PDF no pone `readonly` (acta no firmada).
- Sin control en el caso de uso. UI solo muestra el form con `permiso === 3` y sin `readonly` (no firmada). Ámbito `rstgr`/`r` suele ir en solo lectura en `acta_ver`.
- Frontend `acta_select`/`acta_ver`: `have_perm_oficina('est')` en ámbito DL; en `rstgr` solo lectura.
- Igual que `acta_nueva` (`est` en DL).

### Referencias Internas

- Flujo: `notas.acta_ver.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/acta_ver.md`

## Vincular una actividad al acta

### Para Que Sirve

Elegir la actividad de estudios (CA) a la que corresponde el acta.

### Donde Entrar

- Actividad Buscar Form (frontend/notas/controller/actividad_buscar_form.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Vincular una actividad al acta

1. En la ficha del acta, pulsar para seleccionar la actividad.
2. Elegir la **organización** y la **actividad** y pulsar **seleccionar la actividad** y **guardar**.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- `actividad_buscar_form` en `acta_ver`.

### Referencias Internas

- Flujo: `notas.actividades_buscar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/actividades_buscar.md`

## Buscar los alumnos que deben una asignatura

### Para Que Sirve

Ver qué alumnos deben una asignatura concreta.

### Donde Entrar

- Asig Faltan Personas Select (frontend/notas/controller/asig_faltan_personas_select.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Buscar los alumnos que deben una asignatura

1. Entrar en **buscar asig. pendientes**.
2. En **Búsqueda de personas por asignatura** marcar **numerarios** o **agregados**, el ciclo (**bienio**, **cuadrienio**, **año I**, **año II-IV**) y elegir la **Asignatura**.
3. Pulsar **Buscar** (o **Borrar** para limpiar).
4. Se muestra la lista de alumnos que la deben.

### Errores O Avisos Frecuentes

- `Debe marcar un grupo de personas (n o agd)`
- `No se ha encontrado la asignatura con id: %s`

### Permisos

- Desde `asig_faltan_que`.

### Referencias Internas

- Flujo: `notas.asig_faltan_personas_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/asig_faltan_personas_select.md`

## Buscar alumnos por número de asignaturas pendientes

### Para Que Sirve

Ver los alumnos a los que les faltan como mucho N asignaturas.

### Donde Entrar

- Asig Faltan Select (frontend/notas/controller/asig_faltan_select.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Buscar alumnos por número de asignaturas pendientes

1. Entrar en **buscar asig. pendientes**.
2. En **Búsqueda de personas por número de asignaturas** marcar **numerarios** y/o **agregados** y elegir el ciclo con el botón **bienio** o **cuadrienio** (en cuadrienio, además, **año I** y/o **año II-IV**).
3. Escribir el **número de asignaturas que faltan** (no se incluyen las cursadas sin examinar). Marcar **incluir lista de asignaturas** si se quiere ver cuáles son.
4. Pulsar **Buscar** (o **Borrar** para limpiar). El resultado se titula, por ejemplo, «lista de numerarios a los que faltan 2 o menos asignaturas para finalizar el cuadrienio»; si no hay nadie, «no hay ninguna fila».

### Errores O Avisos Frecuentes

- `Debe marcar un grupo de personas (n o agd)`

### Permisos

- Menú buscar asig. pendientes.

### Referencias Internas

- Flujo: `notas.asig_faltan_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/asig_faltan_select.md`

## Consultar el cuadro alumnos-asignaturas

### Para Que Sirve

Ver el cuadro de alumnos y asignaturas pendientes de cada uno.

### Donde Entrar

- Asignaturas Pendientes (frontend/notas/controller/asignaturas_pendientes.php)
- **Legacy:** vest > actas... > tabla alumnos-asignaturas; stgr > actas > tabla alumnos-asignaturas
- **Pills2:** ESTUDIOS > Preparación planes estudio > Tab. Alumn/asig.; vest > actas... > tabla alumnos-asignaturas; stgr > actas > tabla alumnos-asignaturas

### Tareas Habituales

#### Consultar el cuadro alumnos-asignaturas

1. Entrar en **tabla alumnos-asignaturas**.
2. Se muestra una fila por alumno y una columna por asignatura, con lo que le falta.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Menú tabla alumnos-asignaturas.

### Referencias Internas

- Flujo: `notas.asignaturas_pendientes.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/asignaturas_pendientes.md`

## Consultar el resumen de pendientes

### Para Que Sirve

Ver, para cada asignatura, cuántos alumnos la tienen pendiente.

### Donde Entrar

- Asignaturas Pendientes Resumen (frontend/notas/controller/asignaturas_pendientes_resumen.php)
- **Legacy:** vest > actas... > resumen pendientes
- **Pills2:** ESTUDIOS > Preparación planes estudio > nº alum. por asignatura; vest > actas... > resumen pendientes

### Tareas Habituales

#### Consultar el resumen de pendientes

1. Entrar en **resumen pendientes**.
2. Cada fila es una asignatura (con sus créditos y año). Las columnas dan el número de alumnos que la tienen pendiente: las que empiezan por **n** son numerarios y las que empiezan por **a** son agregados. Después de la letra va el curso en que está el alumno: **b** = bienio, **c1** = cuadrienio año I, **c2** = cuadrienio año II. La columna **total** suma b + c1 + c2. Así, nb, nc1, nc2 y ntotal son numerarios; ab, ac1, ac2 y atotal son agregados.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Menú resumen pendientes.

### Referencias Internas

- Flujo: `notas.asignaturas_pendientes_resumen.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/asignaturas_pendientes_resumen.md`

## Elegir una asignatura

### Para Que Sirve

Autocompletar el nombre de la asignatura al rellenar un acta.

### Donde Entrar

- Acta Ver (frontend/notas/controller/acta_ver.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Elegir una asignatura

1. En la ficha del acta, escribir parte del nombre de la asignatura.
2. Elegir de la lista que aparece.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Contexto `acta_ver`.

### Referencias Internas

- Flujo: `notas.asignaturas_search.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/asignaturas_search.md`

## Elegir el acta de una nota

### Para Que Sirve

Ayudar a elegir un acta mientras se rellenan las notas de una persona.

### Donde Entrar

- Form Notas De Una Persona (frontend/notas/controller/form_notas_de_una_persona.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Elegir el acta de una nota

1. En el formulario de nota, empezar a escribir el número del acta en **acta** (formato num/aa).
2. Elegir de la lista que aparece.

### Errores O Avisos Frecuentes

- `No se ha encontrado la asignatura con id: %s`

### Permisos

- Formulario notas / acta_ver.

### Referencias Internas

- Flujo: `notas.buscar_acta.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/buscar_acta.md`

## Preparar la comprobación de notas

### Para Que Sirve

Cargar las constantes que usan las comprobaciones de notas.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice (variantes id_tabla)
- **Pills2:** ESTUDIOS > Datos e informes > Comprobar datos n / Comprobar datos agd

### Tareas Habituales

#### Preparar la comprobación de notas

1. Al abrir **Comprobar datos** las constantes se cargan automáticamente.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Menú comprobar datos n/agd.

### Referencias Internas

- Flujo: `notas.comprobar_notas_constants.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/comprobar_notas_constants.md`

## Comprobar los datos de notas

### Para Que Sirve

Ejecutar las comprobaciones de los datos de notas y ver las incidencias.

### Donde Entrar

- Comprobar Notas (frontend/notas/controller/comprobar_notas.php)
- **Legacy:** sin entrada de menú en el índice (variantes id_tabla)
- **Pills2:** ESTUDIOS > Datos e informes > Comprobar datos n / Comprobar datos agd

### Tareas Habituales

#### Comprobar los datos de notas

1. Entrar en **Comprobar datos n** (o **agd**).
2. La pantalla ejecuta las comprobaciones y muestra el listado de incidencias.
3. Corregir los datos de las personas afectadas y volver a comprobar.

### Errores O Avisos Frecuentes

- `Excepciones SQL/runtime en mensaje`

### Permisos

- Menú ESTUDIOS > Comprobar datos n/agd.

### Referencias Internas

- Flujo: `notas.comprobar_notas_page.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/comprobar_notas_page.md`

## Elegir un examinador

### Para Que Sirve

Autocompletar el nombre del examinador al rellenar un acta.

### Donde Entrar

- Acta Ver (frontend/notas/controller/acta_ver.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Elegir un examinador

1. En la ficha del acta, escribir parte del nombre del examinador.
2. Elegir de la lista que aparece.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Contexto `acta_ver`; sin permiso propio.

### Referencias Internas

- Flujo: `notas.examinadores_search.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/examinadores_search.md`

## Sacar el informe anual de agregados

### Para Que Sirve

Generar el informe anual del Studium Generale de agregados.

### Donde Entrar

- Informe Stgr Agd (frontend/notas/controller/informe_stgr_agd.php)
- **Legacy:** Calendario > Nuevo calendario > Previsión asistentes (sin lista); variantes lista en ESTUDIOS
- **Pills2:** ACTIVIDADES > Estadísticas económicas > Previsión asistentes; ESTUDIOS > Datos e informes > Informe anual agd > Con números / Con listados

### Tareas Habituales

#### Sacar el informe anual de agregados

1. Entrar en **Informe anual agd** y elegir **Con números** o **Con listados**.
2. Se genera el informe del curso.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Menú informe anual agd / previsión asistentes (variante calendario).

### Referencias Internas

- Flujo: `notas.informe_stgr_agd.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/informe_stgr_agd.md`

## Sacar el informe anual de numerarios

### Para Que Sirve

Generar el informe anual del Studium Generale de numerarios.

### Donde Entrar

- Informe Stgr N (frontend/notas/controller/informe_stgr_n.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** ESTUDIOS > Datos e informes > Informe anual n > Con números / Con listados

### Tareas Habituales

#### Sacar el informe anual de numerarios

1. Entrar en **Informe anual n** y elegir **Con números** o **Con listados**.
2. Se genera el informe del curso.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Menú informe anual n.

### Referencias Internas

- Flujo: `notas.informe_stgr_n.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/informe_stgr_n.md`

## Sacar el informe anual de profesores

### Para Que Sirve

Generar el informe anual del Studium Generale de profesores.

### Donde Entrar

- Informe Stgr Profesores (frontend/notas/controller/informe_stgr_profesores.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** ESTUDIOS > Datos e informes > Informe anual profesores > Con números / Con listados

### Tareas Habituales

#### Sacar el informe anual de profesores

1. Entrar en **Informe anual profesores** y elegir **Con números** o **Con listados**.
2. Se genera el informe del curso.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Menú informe anual profesores.

### Referencias Internas

- Flujo: `notas.informe_stgr_profesores.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/informe_stgr_profesores.md`

## Nota Persona

### Para Que Sirve

Formulario completo de nota: carga (`nota_persona_form_data`) y mutaciones.

### Donde Entrar

- Form Notas De Una Persona (frontend/notas/controller/form_notas_de_una_persona.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Abrir el formulario

1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

### Errores O Avisos Frecuentes

- `No se encuentra la nota a editar`
- `No se ha encontrado la asignatura con id: %s`

### Permisos

- Dossier 1011.

### Referencias Internas

- Flujo: `notas.nota_persona.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/nota_persona.md`

## Añadir una nota

### Para Que Sirve

Apuntar a una persona una asignatura aprobada, con su nota y el acta o certificado.

### Donde Entrar

- Form Notas De Una Persona (frontend/notas/controller/form_notas_de_una_persona.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/dossier)

### Tareas Habituales

#### Añadir una nota

1. Abrir el dossier de notas de la persona (se titula «Notas del stgr»; sin notas dice «no hay ninguna fila») y pulsar **añadir nota**.
2. Se abre «asignaturas aprobadas». Elegir si procede de un **acta** o de un **certificado**: delegación (por ejemplo dlb) y número (formato num/aa, por ejemplo 38/26).
3. Elegir la **Asignatura**, escribir la **nota** (y «sobre» cuánto, por defecto 10), la **situación** (por ejemplo «nota numérica») y la **fecha acta**.
4. Marcar **preceptor** si procede, elegir la **época** (sin especificar, ca o semestre invierno), la actividad en **cursada en** (enlace **añadir ca**) y un **detalle** opcional.
5. Pulsar **Guardar** una sola vez (un doble clic no guarda la nota dos veces). Una asignatura que ya tiene nota deja de salir en el desplegable de asignaturas, así que no se puede guardar dos veces. Sale el aviso «Nota guardada en el esquema H-dlbv» (el nombre del esquema es intencionado) y vuelve la tabla con asignatura, nota (por ejemplo «Cum laude (8/10)»), acta, fecha acta, preceptor, época (ca, semestre invierno o sin especificar), detalle y cursada en.
6. Si se guarda dos veces la misma nota, quedan dos filas iguales.

#### Quitar una nota

1. En la tabla de notas marcar la fila y pulsar **borrar asignatura**.
2. Confirmar «¿Está seguro que desea borrar la nota de esta asignatura?».

### Errores O Avisos Frecuentes

- `Selección de nota no válida.`
- `No se ha eliminado la Nota: %s`
- `No se encuentra una asignatura para el nivel: %s`
- `No se ha guardado la nota`

### Permisos

- Frontend dossier 1011 + `$_SESSION['oPerm']`.
- Frontend dossier 1011 + `$_SESSION['oPerm']`; sin `perm_*` en caso de uso.

### Referencias Internas

- Flujo: `notas.persona_nota.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/persona_nota.md`

## Modificar una nota

### Para Que Sirve

Corregir los datos de una nota ya apuntada a una persona.

### Donde Entrar

- Form Notas De Una Persona (frontend/notas/controller/form_notas_de_una_persona.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Modificar una nota

1. En la tabla de notas marcar la fila y pulsar **modificar nota**.
2. Cambiar los datos que hagan falta (nota, acta, fecha, época…).
3. Pulsar **Guardar**.

### Errores O Avisos Frecuentes

- `Selección de nota no válida.`
- `No se encuentra una asignatura para el nivel: %s`
- `No se ha guardado la nota`

### Permisos

- Frontend dossier 1011 + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `notas.persona_nota_editar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/persona_nota_editar.md`

## Elegir una opcional

### Para Que Sirve

Ofrecer las asignaturas opcionales disponibles al editar una nota.

### Donde Entrar

- Form Notas De Una Persona (frontend/notas/controller/form_notas_de_una_persona.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Elegir una opcional

1. En el formulario de nota, elegir una asignatura opcional.
2. La lista muestra solo las opcionales que se pueden anotar.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Formulario nota persona.

### Referencias Internas

- Flujo: `notas.posibles_opcionales.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/posibles_opcionales.md`

## Elegir el preceptor de una nota

### Para Que Sirve

Ofrecer los posibles preceptores al anotar una nota.

### Donde Entrar

- Form Notas De Una Persona (frontend/notas/controller/form_notas_de_una_persona.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Elegir el preceptor de una nota

1. En el formulario de nota, abrir **preceptor**.
2. Elegir de la lista.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Formulario nota persona.

### Referencias Internas

- Flujo: `notas.posibles_preceptores.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/posibles_preceptores.md`

## Tessera

### Para Que Sirve

Flujo tessera: ver, imprimir y copiar entre personas.

### Donde Entrar

- Tessera Copiar Select (frontend/notas/controller/tessera_copiar_select.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/dossier)

### Tareas Habituales

#### Copiar

1. Abrir el listado en el contexto origen/destino correspondiente.
2. Pulsar la accion de copiar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que los datos copiados aparecen en el listado.

### Errores O Avisos Frecuentes

- `No se han recibido las personas de origen y destino`

### Permisos

- Dossier tessera; frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `notas.tessera.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/tessera.md`

## Copiar una tessera a otra persona

### Para Que Sirve

Copiar la tessera de una persona a otra con el mismo primer apellido (por ejemplo de numerario a agregado).

### Donde Entrar

- Tessera Copiar Select (frontend/notas/controller/tessera_copiar_select.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Copiar una tessera a otra persona

1. Abrir la tessera de la persona.
2. En **copiar la tesera de … a**, elegir la persona destino: se listan las personas con el mismo primer apellido entre numerarios y agregados.
3. Pulsar **copiar**.

### Errores O Avisos Frecuentes

- `No existe una persona con id_nom: %s`

### Permisos

- Dossier tessera.

### Referencias Internas

- Flujo: `notas.tessera_copiar_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/tessera_copiar_select.md`

## Imprimir la tessera

### Para Que Sirve

Imprimir la tessera de estudios de una persona.

### Donde Entrar

- Tessera Imprimir (frontend/notas/controller/tessera_imprimir.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Imprimir la tessera

1. Abrir la tessera de la persona.
2. Pulsar imprimir (HTML o PDF).

### Errores O Avisos Frecuentes

- `No se ha encontrado la asignatura con id: %s`

### Permisos

- Dossier tessera.

### Referencias Internas

- Flujo: `notas.tessera_imprimir.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/tessera_imprimir.md`

## Consultar la tessera de una persona

### Para Que Sirve

Ver la tessera de estudios de una persona: asignaturas hechas y créditos, de filosofía y teología.

### Donde Entrar

- Tessera Ver (frontend/notas/controller/tessera_ver.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Consultar la tessera de una persona

1. Abrir la tessera de la persona.
2. Aparecen el número de asignaturas y créditos realizados en el curso y en total, separados por filosofía y teología, y lo que queda pendiente.

### Errores O Avisos Frecuentes

- `No se ha encontrado la asignatura con id: %s`

### Permisos

- Dossier tessera.

### Referencias Internas

- Flujo: `notas.tessera_ver.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/tessera_ver.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/notas/flujos/`.
