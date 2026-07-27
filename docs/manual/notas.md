---
tipo: "manual_usuario"
modulo: "notas"
flujos: 31
estado_revision: "generado"
---

# Manual De Usuario - notas

Este manual es un borrador generado desde `docs/catalogo`. Debe revisarse para ajustar nombres de menu, permisos, validaciones y lenguaje final de usuario.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas de menu y nombres visibles pueden necesitar revision manual.

## Acta

### Para Que Sirve

Ciclo completo de actas: listar en `acta_select`, abrir `acta_ver`, crear (`acta_nueva`), modificar (`acta_modificar`) o eliminar (`acta_eliminar`), con PDF e impresión.

### Donde Entrar

- Pendiente de revisar.
- **Legacy:** sin entrada de menú en el índice (fragmento/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/dossier)

### Tareas Habituales

#### Crear

1. En `acta_select`, pulsar **añadir acta** (`fnjs_nuevo`).
2. Se abre `acta_ver` en modo nuevo; rellenar asignatura, actividad, fechas y tribunal.
3. Guardar (`fnjs_guardar_acta` → `acta_nueva`).

#### Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

### Errores O Avisos Frecuentes

- `No se encuentra el acta`

### Permisos

- `have_perm_oficina('est')` en `acta_select` (DL).
- Frontend `acta_select`/`acta_ver`: `have_perm_oficina('est')` en ámbito DL; en `rstgr` solo lectura.

### Referencias Internas

- Flujo: `notas.acta.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/acta.md`

## Acta Imprimir Presentacion

### Para Que Sirve

Obtener datos de presentación e imprimir acta (HTML/PDF).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

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

## Acta Listado Anual

### Para Que Sirve

Consultar actas por rango de fechas en vista anual.

### Donde Entrar

- Pendiente de revisar.
- **Legacy:** vest > actas... > listado actas
- **Pills2:** ESTUDIOS > Actas y certificados > Listado de actas; vest > actas... > listado actas

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Menú listado actas; `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `notas.acta_listado_anual.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/acta_listado_anual.md`

## Acta Modificar

### Para Que Sirve

Guardar cambios de un acta existente desde `acta_ver`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `No se encuentra el acta`

### Permisos

- Igual que `acta_nueva` (`est` en DL).

### Referencias Internas

- Flujo: `notas.acta_modificar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/acta_modificar.md`

## Acta Pdf

### Para Que Sirve

Gestión del PDF escaneado: subir, descargar y eliminar.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

### Errores O Avisos Frecuentes

- `No se encuentra el acta`

### Permisos

- Desde `acta_ver` con permiso de edición de actas.

### Referencias Internas

- Flujo: `notas.acta_pdf.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/acta_pdf.md`

## Acta Pdf Download

### Para Que Sirve

Descargar PDF con token firmado.

### Donde Entrar

- Acta Pdf Download (frontend/notas/controller/acta_pdf_download.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Enlace de descarga no válido o caducado.`
- `No se encuentra el acta.`
- `No hay PDF asociado a este acta.`

### Permisos

- Token firmado generado en `acta_select`/`acta_ver` (`SignedDownloadToken`).

### Referencias Internas

- Flujo: `notas.acta_pdf_download.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/acta_pdf_download.md`

## Acta Pdf Subir

### Para Que Sirve

Adjuntar PDF a un acta.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `No se encuentra el acta`
- `No se puede subir el archivo %s`
- `No se puede leer el archivo %s`
- `No se puede abrir el archivo %s`

### Permisos

- Desde `acta_ver` (`fnjs_upload_pdf`).

### Referencias Internas

- Flujo: `notas.acta_pdf_subir.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/acta_pdf_subir.md`

## Acta Select

### Para Que Sirve

Buscar y seleccionar actas del curso; navegar a ver, modificar, imprimir o descargar PDF.

### Donde Entrar

- Pendiente de revisar.
- **Legacy:** vest > actas... > actas; stgr > actas > actas
- **Pills2:** ESTUDIOS > Actas y certificados > Actas; vest > actas... > actas; stgr > actas > actas

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Menú actas; edición solo DL con `est`.

### Referencias Internas

- Flujo: `notas.acta_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/acta_select.md`

## Acta Ver

### Para Que Sirve

Consultar o modificar la cabecera del acta (asignatura, fechas, libro, tribunal, PDF) y, cuando se abre desde el listado de actas, ver las notas ya grabadas y añadir un alumno.

### Donde Entrar

- Pendiente de revisar.
- **Legacy:** sin entrada de menú en el índice (fragmento desde listado ESTUDIOS > Actas)
- **Pills2:** sin entrada de menú en el índice (fragmento desde listado ESTUDIOS > Actas)

### Tareas Habituales

#### Ver / editar cabecera

1. Abrir acta desde el listado o desde la actividad.
2. Cargar cabecera (`acta_ver_form_data`).
3. Guardar alta (`acta_nueva`) o cambios (`acta_modificar`).

#### Listado de notas (solo standalone)

1. Con acta existente (no modo nueva) y asignatura válida, la UI pide `acta_ver_notas_listado_data`.
2. Se muestra tabla id/nombre/nota/situación; avisos si hay notas sin acceso al nombre.

#### Añadir alumno (solo standalone + permiso escritura + sin PDF)

1. Pedir `acta_ver_add_persona_form_data` (desplegable de candidatos).
2. Elegir persona, nota y máximo; enviar `acta_ver_add_persona`.
3. Recargar listado / mensaje de esquema de escritura.

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

## Actividades Buscar

### Para Que Sirve

Seleccionar actividad CA vinculada al acta.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- `actividad_buscar_form` en `acta_ver`.

### Referencias Internas

- Flujo: `notas.actividades_buscar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/actividades_buscar.md`

## Asig Faltan Personas Select

### Para Que Sirve

Listar alumnos que deben una asignatura.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Debe marcar un grupo de personas (n o agd)`
- `No se ha encontrado la asignatura con id: %s`

### Permisos

- Desde `asig_faltan_que`.

### Referencias Internas

- Flujo: `notas.asig_faltan_personas_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/asig_faltan_personas_select.md`

## Asig Faltan Select

### Para Que Sirve

Listar alumnos con hasta N asignaturas pendientes.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Debe marcar un grupo de personas (n o agd)`

### Permisos

- Menú buscar asig. pendientes.

### Referencias Internas

- Flujo: `notas.asig_faltan_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/asig_faltan_select.md`

## Asignaturas Pendientes

### Para Que Sirve

Consultar matriz de asignaturas pendientes por alumno.

### Donde Entrar

- Pendiente de revisar.
- **Legacy:** vest > actas... > tabla alumnos-asignaturas; stgr > actas > tabla alumnos-asignaturas
- **Pills2:** ESTUDIOS > Preparación planes estudio > Tab. Alumn/asig.; vest > actas... > tabla alumnos-asignaturas; stgr > actas > tabla alumnos-asignaturas

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Menú tabla alumnos-asignaturas.

### Referencias Internas

- Flujo: `notas.asignaturas_pendientes.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/asignaturas_pendientes.md`

## Asignaturas Pendientes Resumen

### Para Que Sirve

Ver resumen de pendientes por asignatura.

### Donde Entrar

- Pendiente de revisar.
- **Legacy:** vest > actas... > resumen pendientes
- **Pills2:** ESTUDIOS > Preparación planes estudio > nº alum. por asignatura; vest > actas... > resumen pendientes

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Menú resumen pendientes.

### Referencias Internas

- Flujo: `notas.asignaturas_pendientes_resumen.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/asignaturas_pendientes_resumen.md`

## Asignaturas Search

### Para Que Sirve

Autocompletado de asignaturas en formulario de acta.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Contexto `acta_ver`.

### Referencias Internas

- Flujo: `notas.asignaturas_search.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/asignaturas_search.md`

## Buscar Acta

### Para Que Sirve

Autocompletar/búsqueda de acta al rellenar notas.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `No se ha encontrado la asignatura con id: %s`

### Permisos

- Formulario notas / acta_ver.

### Referencias Internas

- Flujo: `notas.buscar_acta.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/buscar_acta.md`

## Comprobar Notas Constants

### Para Que Sirve

Cargar constantes VO antes de ejecutar comprobaciones.

### Donde Entrar

- Pendiente de revisar.
- **Legacy:** sin entrada de menú en el índice (variantes id_tabla)
- **Pills2:** ESTUDIOS > Datos e informes > Comprobar datos n / Comprobar datos agd

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Menú comprobar datos n/agd.

### Referencias Internas

- Flujo: `notas.comprobar_notas_constants.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/comprobar_notas_constants.md`

## Comprobar Notas Page

### Para Que Sirve

Ejecutar comprobaciones y mostrar HTML de incidencias.

### Donde Entrar

- Pendiente de revisar.
- **Legacy:** sin entrada de menú en el índice (variantes id_tabla)
- **Pills2:** ESTUDIOS > Datos e informes > Comprobar datos n / Comprobar datos agd

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Excepciones SQL/runtime en mensaje`

### Permisos

- Menú ESTUDIOS > Comprobar datos n/agd.

### Referencias Internas

- Flujo: `notas.comprobar_notas_page.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/comprobar_notas_page.md`

## Examinadores Search

### Para Que Sirve

Autocompletado de examinadores en formulario de acta.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Contexto `acta_ver`; sin permiso propio.

### Referencias Internas

- Flujo: `notas.examinadores_search.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/examinadores_search.md`

## Informe Stgr Agd

### Para Que Sirve

Generar informe anual de agregados (números o listados).

### Donde Entrar

- Pendiente de revisar.
- **Legacy:** Calendario > Nuevo calendario > Previsión asistentes (sin lista); variantes lista en ESTUDIOS
- **Pills2:** ACTIVIDADES > Estadísticas económicas > Previsión asistentes; ESTUDIOS > Datos e informes > Informe anual agd > Con números / Con listados

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Menú informe anual agd / previsión asistentes (variante calendario).

### Referencias Internas

- Flujo: `notas.informe_stgr_agd.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/informe_stgr_agd.md`

## Informe Stgr N

### Para Que Sirve

Generar informe anual de numerarios.

### Donde Entrar

- Pendiente de revisar.
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** ESTUDIOS > Datos e informes > Informe anual n > Con números / Con listados

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Menú informe anual n.

### Referencias Internas

- Flujo: `notas.informe_stgr_n.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/informe_stgr_n.md`

## Informe Stgr Profesores

### Para Que Sirve

Generar informe anual de profesores.

### Donde Entrar

- Pendiente de revisar.
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** ESTUDIOS > Datos e informes > Informe anual profesores > Con números / Con listados

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

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

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

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

## Persona Nota

### Para Que Sirve

Alta de nota en dossier 1011 (`persona_nota_nueva`).

### Donde Entrar

- Pendiente de revisar.
- **Legacy:** sin entrada de menú en el índice (fragmento/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/dossier)

### Tareas Habituales

#### Crear

1. Revisar manualmente los pasos de esta accion.

#### Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

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

## Persona Nota Editar

### Para Que Sirve

Edición de nota existente.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Selección de nota no válida.`
- `No se encuentra una asignatura para el nivel: %s`
- `No se ha guardado la nota`

### Permisos

- Frontend dossier 1011 + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `notas.persona_nota_editar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/persona_nota_editar.md`

## Posibles Opcionales

### Para Que Sirve

Consultar opcionales disponibles al editar nota.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Formulario nota persona.

### Referencias Internas

- Flujo: `notas.posibles_opcionales.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/posibles_opcionales.md`

## Posibles Preceptores

### Para Que Sirve

Elegir preceptor en formulario de nota.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

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

- Pendiente de revisar.
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

## Tessera Copiar Select

### Para Que Sirve

Elegir destino y ejecutar copia de tessera.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `No existe una persona con id_nom: %s`

### Permisos

- Dossier tessera.

### Referencias Internas

- Flujo: `notas.tessera_copiar_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/tessera_copiar_select.md`

## Tessera Imprimir

### Para Que Sirve

Imprimir tessera (HTML/PDF).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `No se ha encontrado la asignatura con id: %s`

### Permisos

- Dossier tessera.

### Referencias Internas

- Flujo: `notas.tessera_imprimir.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/tessera_imprimir.md`

## Tessera Ver

### Para Que Sirve

Visualizar tessera de estudios.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `No se ha encontrado la asignatura con id: %s`

### Permisos

- Dossier tessera.

### Referencias Internas

- Flujo: `notas.tessera_ver.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/notas/flujos/tessera_ver.md`

## Revision Pendiente

- Sustituir nombres tecnicos por nombres visibles en la aplicacion.
- Completar rutas de menu.
- Confirmar permisos necesarios.
- Anadir capturas o ejemplos si se quiere publicar para usuarios finales.
