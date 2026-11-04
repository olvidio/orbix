---
tipo: "manual_usuario"
modulo: "actividadestudios"
flujos: 25
estado_revision: "revisado"
---

# Manual De Usuario - actividadestudios

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Ver el acta de una asignatura

### Para Que Sirve

Abrir el acta de una asignatura impartida en una actividad: se ve la cabecera del acta y, debajo, la lista de matriculados con su nota, nota máxima, preceptor y situación de acta, según los permisos de la delegación propietaria.

### Donde Entrar

- Acta Notas (frontend/actividadestudios/controller/acta_notas.php)
- Sin entrada de menú en el índice (subflujo desde dossier 3005 «asignaturas de una actividad»).
- Acceso habitual: buscar actividad CA (`actividad_select`) → dossier asignaturas → **actas**.
- Menú de la pantalla padre de búsqueda de actividades:
- **Legacy:** vsm > ca > buscar ca; vest > actas... > actas (vía módulo notas).
- **Pills2:** ACTIVIDADES > Buscar actividad > ca n; ESTUDIOS > Actas y certificados > Actas;
- ESTUDIOS > Buscar actividades.

### Tareas Habituales

#### Ver el acta de una asignatura

1. En el dossier de asignaturas de una actividad, seleccionar una asignatura.
2. Pulsar **actas**.
3. Se muestra el acta con los matriculados, el desplegable de situación del acta y, si se tiene permiso, la posibilidad de editar.
4. Los alumnos con nota en un acta firmada quedan en solo lectura; si quedan alumnos sin nota, se puede crear una segunda convocatoria.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- El caso de uso calcula un flag `permiso` (3/1) comparando la DL de la sesión (`OrbixRuntime::miDelef`)

### Referencias Internas

- Flujo: `actividadestudios.acta_notas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/acta_notas.md`

## Grabar las notas definitivas

### Para Que Sirve

Confirmar las notas del acta como definitivas: las notas en borrador pasan a ser notas definitivas de cada persona, con su época, nivel y acta.

### Donde Entrar

- Acta Notas (frontend/actividadestudios/controller/acta_notas.php)
- Sin entrada de menú en el índice (subflujo desde pantalla `acta_notas`, accesible desde
- dossier 3005 o módulo de actas).
- **Legacy:** vest > actas... > actas (pantalla padre habitual).
- **Pills2:** ESTUDIOS > Actas y certificados > Actas.

### Tareas Habituales

#### Grabar las notas definitivas

1. En el acta de notas de una asignatura, revisar la nota y la situación de cada alumno.
2. Pulsar el botón de grabar las notas definitivas.
3. Si todo es correcto, las notas quedan grabadas en la tessera de cada alumno; si no, sale un aviso con el motivo.

### Errores O Avisos Frecuentes

- `no encuentro la actividad`
- `no se puede definir cursada con preceptor`
- `no encuentro el acta`
- `debe introducir los datos del acta. No se ha guardado nada.`
- `falta definir el acta para alguna nota`
- `no encuentro la asignatura de actividad`
- `no encuentro la asignatura`
- `nota no guardada para %s porque la nota (%s) no llega al mínimo: 6`
- `ha cursado una opcional que no tocaba (id_nom=%s)`
- `está intentando poner una nota que ya existe para: %s`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.acta_notas_definitivas_grabar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/acta_notas_definitivas_grabar.md`

## Guardar las notas en borrador

### Para Que Sirve

Editar las notas, la nota máxima, el preceptor o la situación de acta de los alumnos matriculados, y guardarlo como borrador en sus matrículas.

### Donde Entrar

- Acta Notas (frontend/actividadestudios/controller/acta_notas.php)
- Sin entrada de menú en el índice (subflujo desde pantalla `acta_notas`).
- **Legacy:** vest > actas... > actas.
- **Pills2:** ESTUDIOS > Actas y certificados > Actas.

### Tareas Habituales

#### Guardar las notas en borrador

1. En el acta de notas, cambiar la nota, la nota máxima, el preceptor o la situación del acta de un alumno.
2. Al salir de la casilla de la nota (o al guardar) se graba el cambio en su matrícula.
3. Si algún dato no es válido, sale un aviso con el motivo.

### Errores O Avisos Frecuentes

- `Hay una nota mayor que el máximo`
- `hay un error, no se ha guardado`
- `no se puede definir cursada con preceptor`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.acta_notas_matricula.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/acta_notas_matricula.md`

## Añadir una asignatura a la actividad

### Para Que Sirve

Añadir una asignatura impartida en la actividad (con su profesor, fechas y tipo) o eliminar una que ya está, desde el dossier de asignaturas.

### Donde Entrar

- Form Asignaturas De Una Actividad (frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php)
- Sin entrada de menú en el índice (subflujo desde dossier 3005, accesible al buscar una
- actividad CA y abrir asignaturas).
- **Legacy:** vsm > ca > buscar ca.
- **Pills2:** ACTIVIDADES > Buscar actividad > ca n; ESTUDIOS > Buscar actividades.

### Tareas Habituales

#### Añadir una asignatura a la actividad

1. En el dossier de asignaturas de la actividad, pulsar **nuevo**.
2. Elegir la asignatura, el profesor, las fechas y el tipo, y pulsar **guardar**.
3. Se vuelve al dossier de asignaturas con la nueva asignatura en la lista.

#### Eliminar una asignatura de la actividad

1. En la lista de asignaturas del dossier, seleccionar la fila.
2. Pulsar **borrar** y confirmar.
3. La asignatura desaparece de la lista.

### Errores O Avisos Frecuentes

- `faltan claves de la asignatura de actividad`
- `hay un error, no se ha borrado`
- `hay un error, no se ha creado`
- `no encuentro la asignatura`
- `sólo se puede eliminar una asignatura desde el dossier de la actividad`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.actividad_asignatura.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/actividad_asignatura.md`

## Modificar una asignatura de la actividad

### Para Que Sirve

Cambiar el profesor, las fechas, el tipo u otros datos de una asignatura que ya se imparte en la actividad.

### Donde Entrar

- Form Asignaturas De Una Actividad (frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php)
- Sin entrada de menú en el índice (subflujo desde dossier 3005).
- **Legacy:** vsm > ca > buscar ca.
- **Pills2:** ACTIVIDADES > Buscar actividad > ca n; ESTUDIOS > Buscar actividades.

### Tareas Habituales

#### Modificar una asignatura de la actividad

1. En el dossier de asignaturas, seleccionar la asignatura y pulsar **modificar**.
2. Ajustar el profesor, las fechas, el aviso al profesor o el tipo.
3. Pulsar **guardar**.

### Errores O Avisos Frecuentes

- `faltan claves de la asignatura de actividad`
- `hay un error, no se ha guardado`
- `no encuentro la asignatura`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.actividad_asignatura_editar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/actividad_asignatura_editar.md`

## Guardar las observaciones de un asistente

### Para Que Sirve

Guardar el texto de observaciones generales de un asistente en una actividad.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- Sin entrada de menú en el índice. Subflujo previsto desde dossier 3103 (matrículas de una
- actividad), accesible vía búsqueda de actividad CA.
- **Legacy:** vsm > ca > buscar ca.
- **Pills2:** ACTIVIDADES > Buscar actividad > ca n.

### Tareas Habituales

#### Guardar las observaciones de un asistente

1. Esta acción no se puede lanzar desde las pantallas actuales.
2. Las observaciones de un asistente se editan desde su ficha de asistencia (ver el apartado de asistentes).

### Errores O Avisos Frecuentes

- `falta id_activ o id_nom`
- `hay un error, no se ha guardado`
- `no encuentro al asistente`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.asistente_observ.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/asistente_observ.md`

## Guardar las observaciones del plan de estudios

### Para Que Sirve

Guardar las observaciones sobre el plan de estudios de un asistente en su actividad.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- Sin entrada de menú en el índice (subflujo desde dossier 1303 «matrículas de una persona»).
- **Legacy:** vest > buscar persona > n r/dl; stgr > personas > n r/dl.
- **Pills2:** PERSONAS > Numerarios > Buscar n de la r/dl; PERSONAS > Agregados > Buscar agd de la r/dl.

### Tareas Habituales

#### Guardar las observaciones del plan de estudios

1. En el dossier de matrículas de una persona, escribir el texto en las observaciones del plan de estudios.
2. Pulsar el botón para grabar las observaciones.
3. Si se guarda bien, la lista se refresca con el texto nuevo.

### Errores O Avisos Frecuentes

- `falta id_activ o id_nom`
- `hay un error, no se ha guardado`
- `no encuentro al asistente`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.asistente_observ_est.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/asistente_observ_est.md`

## Confirmar el plan de estudios

### Para Que Sirve

Marcar como confirmado el plan de estudios de un asistente.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- Sin entrada de menú en el índice (subflujo desde dossier 1303).
- **Legacy:** vest > buscar persona > n r/dl; stgr > personas > n r/dl.
- **Pills2:** PERSONAS > Numerarios > Buscar n de la r/dl.

### Tareas Habituales

#### Confirmar el plan de estudios

1. En el dossier de matrículas de una persona, marcar la casilla de plan de estudios confirmado.
2. Pulsar el botón de confirmar el plan.
3. La lista se refresca y el plan queda como confirmado.

### Errores O Avisos Frecuentes

- `falta id_activ o id_nom`
- `hay un error, no se ha guardado`
- `no encuentro al asistente`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.asistente_plan_est_ok.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/asistente_plan_est_ok.md`

## Ver los posibles asistentes al ca

### Para Que Sirve

Obtener, para el centro y el periodo elegidos, el cuadro de posibles asistentes al ca: por cada alumno, los créditos que puede cursar, las asignaturas pendientes y enlaces al detalle.

### Donde Entrar

- Ca Posibles (frontend/actividadestudios/controller/ca_posibles.php)
- Se accede desde la pantalla `ca_posibles_que` o desde búsqueda de personas:
- **Legacy:** vest > posibles ca > posibles ca; vest > buscar persona > n r/dl (botón posibles CA).
- **Pills2:** vest > posibles ca > posibles ca; PERSONAS > Numerarios > Buscar n de la r/dl.

### Tareas Habituales

#### Ver los posibles asistentes al ca

1. Abrir el formulario de búsqueda de posibles ca.
2. Elegir el centro (n o agd), el periodo y las opciones de filtro.
3. Pulsar **buscar**; hace falta haber elegido un centro.
4. Aparece el cuadro con los posibles asistentes, uno por alumno, con sus créditos y asignaturas pendientes.

### Errores O Avisos Frecuentes

- `debe seleccionar un centro o grupo de centros`
- `Parámetro na no válido`
- `sólo debebería haber uno`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.ca_posibles.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/ca_posibles.md`

## Ver el cuadro de posibles ca

### Para Que Sirve

Preparar el cuadro de posibles ca: qué personas podrían ir a cada ca según las asignaturas que les faltan.

### Donde Entrar

- Ca Posibles Que (frontend/actividadestudios/controller/ca_posibles_que.php)
- **Legacy:** vest > posibles ca > posibles ca.
- **Pills2:** vest > posibles ca > posibles ca.

### Tareas Habituales

#### Ver el cuadro de posibles ca

1. Entrar en «Ver cuadro de posibles ca». Nota de la pantalla: para que salgan los ca en el cuadro deben tener introducidas las asignaturas y el campo de nivel de stgr.
2. Marcar qué ca se quieren: **ca de estudios**, **ca de repaso** o **todos los ca**.
3. Elegir los centros de los numerarios y de los agregados (o «todos los ctr»), el periodo (verano, curso, todo el año u otro) y el año.
4. Escoger el grupo de estudios y, si hace falta, un texto de referencia y el texto de cabecera: **horizontal (excel)** o **vertical (imprimir)**.
5. Pulsar **ver cuadro**.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.ca_posibles_que.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/ca_posibles_que.md`

## Docencia Actualizar

### Para Que Sirve

- El usuario elige un periodo de actividades terminadas y ejecuta la actualización: el sistema recorre las asignaturas con profesor asignado y graba/actualiza registros en `d_docencia_stgr` (`ProfesorDocenciaStgr`).
- Sustituye la rama «continuar» del legacy `apps/actividadestudios/controller/actualizar_docencia.php`.

### Donde Entrar

- Actualizar Docencia (frontend/actividadestudios/controller/actualizar_docencia.php)
- **Legacy:** vest > mantenimiento > actualizar docencia.
- **Pills2:** ESTUDIOS > Datos e informes > Actualizar docencia; vest > mantenimiento >
- actualizar docencia.

### Tareas Habituales

#### Ejecutar

1. Abrir **actualizar docencia** desde el menú.
2. Elegir año y periodo (o fechas personalizadas) y pulsar **buscar**.
3. El sistema calcula la docencia de actividades terminadas en el rango y la persiste.
4. Se muestra el mensaje de resultado en la misma pantalla.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.docencia_actualizar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/docencia_actualizar.md`

## Ver el certificado E43 de un alumno

### Para Que Sirve

Consultar el certificado E43 de un alumno en una actividad: datos personales, delegaciones de origen y destino, actividad, y las asignaturas matriculadas con sus notas.

### Donde Entrar

- E43 (frontend/actividadestudios/controller/e43.php)
- Sin entrada de menú en el índice (subflujo desde dossier de asistentes de una actividad).
- **Legacy:** vsm > ca > buscar ca (pantalla padre de búsqueda de actividad).
- **Pills2:** ACTIVIDADES > Buscar actividad > ca n; ESTUDIOS > Buscar actividades.

### Tareas Habituales

#### Ver el certificado E43 de un alumno

1. En el dossier de asistentes de una actividad, seleccionar al alumno.
2. Pulsar **E43**.
3. Se muestra el certificado en pantalla, con un botón para imprimirlo en PDF.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.e43.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/e43.md`

## Imprimir el certificado E43

### Para Que Sirve

Imprimir el certificado E43 de un alumno en PDF, con los mismos datos que se ven en pantalla.

### Donde Entrar

- E43 Imprimir Mpdf (frontend/actividadestudios/controller/e43_imprimir_mpdf.php)
- Sin entrada de menú en el índice (subflujo desde pantalla `e43`).
- **Legacy:** vsm > ca > buscar ca (misma cadena que E43).
- **Pills2:** ACTIVIDADES > Buscar actividad > ca n.

### Tareas Habituales

#### Imprimir el certificado E43

1. En la pantalla del certificado E43, pulsar **imprimir**.
2. Se abre una ventana con el certificado listo para imprimir o guardar como PDF.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.e43_imprimir_mpdf.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/e43_imprimir_mpdf.md`

## las asignaturas de una actividad

### Para Que Sirve

Ver y modificar las asignaturas que se imparten en una actividad.

### Donde Entrar

- Form Asignaturas De Una Actividad (frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php)
- Sin entrada de menú en el índice (subflujo desde dossier 3005).
- **Legacy:** vsm > ca > buscar ca.
- **Pills2:** ACTIVIDADES > Buscar actividad > ca n; ESTUDIOS > Buscar actividades.

### Tareas Habituales

#### Gestionar las asignaturas de una actividad

1. Marcar la actividad y pulsar **asignaturas**.
2. Se abre «Relación de asignaturas» con el enlace **( dossiers )**; si no hay ninguna, indica «no hay ninguna fila».
3. Pulsar **añadir asignatura** para añadir una.

### Errores O Avisos Frecuentes

- `no encuentro la asignatura de actividad`
- `No se ha encontrado la asignatura con id: <id>`
- `debería haber un nombre de asignatura`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.form_asignaturas_de_una_actividad.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/form_asignaturas_de_una_actividad.md`

## Matricular o modificar la matrícula de una persona

### Para Que Sirve

Abrir el formulario para matricular a una persona en una asignatura de una actividad, o para cambiar esa matrícula: nivel, asignatura, preceptor y datos de la actividad.

### Donde Entrar

- Form Matriculas De Una Persona (frontend/actividadestudios/controller/form_matriculas_de_una_persona.php)
- Sin entrada de menú en el índice (subflujo desde dossier 1303 o 3103).
- **Legacy:** vest > buscar persona > n r/dl (dossier 1303); vsm > ca > buscar ca (dossier 3103).
- **Pills2:** PERSONAS > Numerarios > Buscar n de la r/dl; ACTIVIDADES > Buscar actividad > ca n.

### Tareas Habituales

#### Matricular o modificar la matrícula de una persona

1. En el dossier de matrículas, pulsar **nuevo** o seleccionar una matrícula y pulsar **modificar**.
2. Se abre el formulario con los desplegables de nivel, asignatura y preceptor; al cambiar uno se actualizan los demás.
3. Rellenar los datos y guardar.

### Errores O Avisos Frecuentes

- `No se ha encontrado actividad con id: <id>`
- `no encuentro la matricula`
- `No se ha encontrado la asignatura con id: <id>`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.form_matriculas_de_una_persona.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/form_matriculas_de_una_persona.md`

## Ver la lista de clases de una actividad

### Para Que Sirve

Ver la lista de clases de una actividad: director de estudios y asistentes por asignatura.

### Donde Entrar

- Lista Clases Ca (frontend/actividadestudios/controller/lista_clases_ca.php)
- Se accede desde `actividad_select` (sin entrada directa al informe):
- **Legacy:** vsm > ca > buscar ca; vest > sem inv. > buscar.
- **Pills2:** ACTIVIDADES > Buscar actividad > ca n; ESTUDIOS > Buscar actividades;
- ESTUDIOS > Semestres de invierno > Buscar.

### Tareas Habituales

#### Ver la lista de clases de una actividad

1. Marcar una actividad de tipo ca y pulsar **listas de clase**.
2. Se muestra el título de la actividad, el **Director de estudios** (para nombrarlo, ir al dossier de cargos de la actividad) y una tabla por asignatura.
3. Si la actividad no tiene asignaturas, solo aparece la cabecera.

### Errores O Avisos Frecuentes

- `No se ha encontrado la asignatura con id: <id>`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.lista_clases_ca.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/lista_clases_ca.md`

## Matricula

### Para Que Sirve

El usuario crea una matrícula (persona + asignatura + nivel en una actividad) o elimina una o varias matrículas seleccionadas desde listados o formularios de dossier.

### Donde Entrar

- Form Matriculas De Una Persona (frontend/actividadestudios/controller/form_matriculas_de_una_persona.php)
- Matriculas Lista (frontend/actividadestudios/controller/matriculas_lista.php)
- Matriculas Pendientes (frontend/actividadestudios/controller/matriculas_pendientes.php)
- Sin entrada de menú directa; las altas se hacen desde dossiers y las bajas también desde
- listados de menú:
- **Legacy:** vest > actas... > Matrículas / Matr. Pendientes; vest > buscar persona > n r/dl.
- **Pills2:** ESTUDIOS > Actas y certificados > Matrículas realizadas / Exam. pendientes de acta;
- PERSONAS > Numerarios > Buscar n de la r/dl.

### Tareas Habituales

#### Crear

1. En dossier 1303 o 3103, pulsar **nuevo** para abrir el formulario de matrícula.
2. Elegir nivel, asignatura y opciones de preceptor.
3. Pulsar **guardar**; el sistema crea la matrícula y actualiza dossiers 1303/3103.

#### Eliminar

1. En un listado de matrículas, seleccionar una o varias filas.
2. Pulsar **borrar matrícula** y confirmar.
3. El sistema elimina las matrículas y refresca el listado.

### Errores O Avisos Frecuentes

- `falta id_activ o id_nom`
- `hay un error, no se ha borrado`
- `hay un error, no se ha guardado`
- `no encuentro asignatura para ese nivel`
- `no encuentro la matricula`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.matricula.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/matricula.md`

## Matricular a todos automáticamente

### Para Que Sirve

Matricular automáticamente en las asignaturas del ca o la cv a todas las personas que tienen actividad de estudios asignada.

### Donde Entrar

- Matricular (frontend/actividadestudios/controller/matricular.php)
- **Legacy:** vest > buscar persona > matricular a todos.
- **Pills2:** vest > buscar persona > matricular a todos; ESTUDIOS > Preparación planes estudio >
- Matricular a todos.

### Tareas Habituales

#### Matricular a todos automáticamente

1. Al pulsar la opción de menú sale primero un aviso: «Se van a matricular automáticamente todas las personas. Si el plan de estudios de una persona no está confirmado, se borrarán sus matrículas anteriores. ¿Quiere continuar?». Con **volver** no se hace nada; con **continuar** se ejecuta la matriculación para todas las personas, sin más confirmación.
2. Para cada persona con ca o cv asignado, el sistema recalcula sus asignaturas (respetando las ya aprobadas y los topes de opcionales) y crea las matrículas; si el plan no está confirmado, borra antes las matrículas previas.
3. La pantalla muestra un mensaje por cada persona con la que no se hizo nada, por ejemplo «no se ha hecho nada con Sánchez Andreu, Enrique: no tiene asignado ca».

### Errores O Avisos Frecuentes

- `No se ha encontrado a la persona con id: %s`
- `está de repaso`
- `no se ha hecho nada con %s no tiene asignado ca`
- `no se ha hecho nada com %s. ya tiene el plan de estudios confirmado`
- `hay un error, no se ha eliminado`
- `error al guardar la matrícula`
- `no se ha hecho nada con %s, tiene asignado más de un ca`
- `no se ha hecho nada`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.matricula_automatica.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/matricula_automatica.md`

## Matricula Editar

### Para Que Sirve

- El usuario modifica nivel, asignatura, preceptor u otros datos de una matrícula ya creada y guarda los cambios.
- Sustituye el case `editar` de `update_3103.php`.

### Donde Entrar

- Form Matriculas De Una Persona (frontend/actividadestudios/controller/form_matriculas_de_una_persona.php)
- Sin entrada de menú en el índice (subflujo desde dossier 1303 o 3103).
- **Legacy:** vest > buscar persona > n r/dl; vsm > ca > buscar ca.
- **Pills2:** PERSONAS > Numerarios > Buscar n de la r/dl; ACTIVIDADES > Buscar actividad > ca n.

### Tareas Habituales

#### Ejecutar

1. En dossier 1303 o 3103, seleccionar una matrícula y pulsar **modificar**.
2. Ajustar nivel, asignatura o preceptor en el formulario.
3. Pulsar **guardar**; el sistema persiste los cambios en la matrícula.

### Errores O Avisos Frecuentes

- `faltan claves de la matricula`
- `hay un error, no se ha guardado`
- `no encuentro la matricula`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.matricula_editar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/matricula_editar.md`

## Listar las matrículas de un periodo

### Para Que Sirve

Ver las matrículas de las actividades de un periodo.

### Donde Entrar

- Matriculas Lista (frontend/actividadestudios/controller/matriculas_lista.php)
- **Legacy:** vest > actas... > Matrículas.
- **Pills2:** ESTUDIOS > Actas y certificados > Matrículas realizadas; ESTUDIOS > Preparación
- planes estudio > Matrículas realizadas; vest > actas... > Matrículas.

### Tareas Habituales

#### Listar las matrículas de un periodo

1. Entrar en **Matrículas**: «PERIODO DE SELECCIÓN DE ACTIVIDADES».
2. Elegir todo el año, un trimestre, el curso ca u **otro** (con fechas) y el año.
3. Pulsar **buscar** para ver la lista de matrículas.

### Errores O Avisos Frecuentes

- `Se requieren inicioIso y finIso`
- `No se ha encontrado la asignatura con id: <id>`

### Permisos

- El caso de uso no aplica control de permisos propio: la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.matriculas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/matriculas.md`

## Consultar alumnos de otras regiones

### Para Que Sirve

- Buscar por apellido a alumnos de otras regiones para ver sus asignaturas matriculadas y emitir certificados E43.
- Solo está disponible para las regiones del stgr.

### Donde Entrar

- Matriculas Lista Otras R (frontend/actividadestudios/controller/matriculas_lista_otras_r.php)
- **Legacy:** — (sin entrada en el índice Legacy).
- **Pills2:** ESTUDIOS > Actas y certificados > Envío información a otras r.

### Tareas Habituales

#### Consultar alumnos de otras regiones

1. Abrir **Envío información a otras r**.
2. Si se quiere, escribir un apellido y pulsar **buscar**.
3. Aparecen los alumnos con sus avisos y asignaturas.
4. Seleccionar un alumno y pulsar **imprimir certificado** para emitir su E43.

### Errores O Avisos Frecuentes

- `No se pudo resolver el repositorio de notas de otras regiones`
- `No se pudo determinar el esquema región STGR de la sesión.`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.matriculas_lista_otras_r.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/matriculas_lista_otras_r.md`

## Ver y borrar matrículas pendientes

### Para Que Sirve

Ver las matrículas de actividades pendientes de procesar y borrar las que no valgan.

### Donde Entrar

- Matriculas Pendientes (frontend/actividadestudios/controller/matriculas_pendientes.php)
- **Legacy:** vest > actas... > Matr. Pendientes.
- **Pills2:** ESTUDIOS > Actas y certificados > Exam. pendientes de acta; ESTUDIOS >
- Preparación planes estudio > Exam. pendientes acta; vest > actas... > Matr. Pendientes.

### Tareas Habituales

#### Ver y borrar matrículas pendientes

1. Entrar en **Matr. Pendientes**: aparece una lista de matrículas con casilla para marcar.
2. Marcar las matrículas y pulsar **ver asignaturas ca** (ver sus asignaturas) o **borrar matrícula**.
3. Si hay datos huérfanos, salen mensajes como «No encuentro a nadie con id_nom: …» o «No encuentro ninguna actividad con id: … (alumno id_nom: …)».

### Errores O Avisos Frecuentes

- `No se ha encontrado la asignatura con id: <id>`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.matriculas_pendientes.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/matriculas_pendientes.md`

## Ver el plan de estudios de una actividad

### Para Que Sirve

Ver el plan de estudios de una actividad (director de estudios, profesores, preceptores y asistentes).

### Donde Entrar

- Plan Estudios Ca (frontend/actividadestudios/controller/plan_estudios_ca.php)
- Se accede desde `actividad_select`:
- **Legacy:** vsm > ca > buscar ca; vest > sem inv. > buscar.
- **Pills2:** ACTIVIDADES > Buscar actividad > ca n; ESTUDIOS > Buscar actividades;
- ESTUDIOS > Semestres de invierno > Buscar.

### Tareas Habituales

#### Ver el plan de estudios de una actividad

1. Marcar una actividad de tipo ca y pulsar **plan estudios**.
2. Se muestra el título de la actividad con **Director de estudios** (para nombrarlo hay que ir al dossier de cargos de la actividad), **Profesores**, **Preceptores** y **Asistentes**.
3. Debajo, cada asistente con su plan; «???» indica que no hay dato.

### Errores O Avisos Frecuentes

- `No se ha encontrado la asignatura con id: <id>`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.plan_estudios_ca.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/plan_estudios_ca.md`

## Ver las posibles asignaturas de una actividad

### Para Que Sirve

Ver qué asignaturas podrían cursar los asistentes de la actividad.

### Donde Entrar

- Posibles Asignaturas Ca (frontend/actividadestudios/controller/posibles_asignaturas_ca.php)
- Se accede desde `actividad_select`:
- **Legacy:** vsm > ca > buscar ca.
- **Pills2:** ACTIVIDADES > Buscar actividad > ca n; ESTUDIOS > Buscar actividades.

### Tareas Habituales

#### Ver las posibles asignaturas de una actividad

1. Marcar una actividad de tipo ca y pulsar **posibles asignaturas**.
2. Se muestra una tabla «asignatura / posibles alumnos / nombres» y, debajo, los «alumnos a los que faltan 4 o menos asignaturas para terminar el cuadrienio».

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.posibles_asignaturas_ca.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/posibles_asignaturas_ca.md`

## Elegir el profesor de una asignatura

### Para Que Sirve

Al cambiar la asignatura, o al añadir otro profesor en el formulario de una asignatura impartida, obtener la lista actualizada de profesores posibles para esa asignatura.

### Donde Entrar

- Form Asignaturas De Una Actividad (frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php)
- Sin entrada de menú en el índice (subflujo AJAX desde formulario dossier 3005).
- **Legacy:** vsm > ca > buscar ca.
- **Pills2:** ACTIVIDADES > Buscar actividad > ca n.

### Tareas Habituales

#### Elegir el profesor de una asignatura

1. En el formulario de la asignatura de la actividad, elegir o cambiar la asignatura.
2. El desplegable de profesores se actualiza con los que pueden impartirla.
3. Para añadir otro profesor, pulsar **más** y elegirlo en el nuevo desplegable.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.profesores_desplegable.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/profesores_desplegable.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/actividadestudios/flujos/`.
