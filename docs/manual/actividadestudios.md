---
tipo: "manual_usuario"
modulo: "actividadestudios"
flujos: 25
estado_revision: "generado"
---

# Manual De Usuario - actividadestudios

Este manual es un borrador generado desde `docs/catalogo`. Debe revisarse para ajustar nombres de menu, permisos, validaciones y lenguaje final de usuario.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas de menu y nombres visibles pueden necesitar revision manual.

## Acta Notas

### Para Que Sirve

El usuario abre el acta de una asignatura impartida en una actividad: el sistema muestra el formulario del acta (cabecera vía `acta_ver`) y debajo la lista de matriculados con nota, nota máxima, preceptor y situación de acta, según permisos de la DL propietaria.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. En el dossier de asignaturas de una actividad (3005), seleccionar una asignatura.
2. Pulsar **actas** (`fnjs_actas`).
3. El sistema carga `acta_notas` y consulta `acta_notas_data` con las claves de actividad
4. Se muestra el acta con matriculados, desplegable de situaciones y permiso de edición.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- El caso de uso calcula un flag `permiso` (3/1) comparando la DL de la sesión (`OrbixRuntime::miDelef`)

### Referencias Internas

- Flujo: `actividadestudios.acta_notas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/acta_notas.md`

## Acta Notas Definitivas Grabar

### Para Que Sirve

- El usuario confirma las notas del acta como definitivas: el sistema convierte las matrículas/notas borrador en registros `PersonaNota` definitivos, asignando época, nivel y acta correspondiente.
- Sustituye la rama `que=3` del legacy `apps/actividadestudios/controller/acta_notas_update.php`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. En el acta de notas de una asignatura, revisar notas y situaciones de cada alumno.
2. Pulsar la acción de grabar definitivas (`fnjs_guardar_tessera`).
3. El sistema serializa `#f_1303` con `que=3` y llama al endpoint.
4. Si la respuesta es correcta, las notas quedan grabadas en tessera; si no, se muestra alerta.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `actividadestudios.acta_notas_definitivas_grabar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/acta_notas_definitivas_grabar.md`

## Acta Notas Matricula

### Para Que Sirve

- El usuario edita notas, nota máxima, preceptor o situación de acta de los alumnos matriculados y guarda el borrador en las matrículas.
- Sustituye la rama `que=1` del legacy `apps/actividadestudios/controller/acta_notas_update.php`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Guardar

1. En el acta de notas, modificar nota, nota máxima, preceptor o desplegable de acta de un alumno.
2. Al salir del campo nota (`fnjs_nota`) o al guardar explícitamente, se invoca `fnjs_guardar_nota`.
3. El sistema serializa `#f_1303` y llama al endpoint.
4. Si hay error de validación, se muestra alerta con el mensaje devuelto.

### Errores O Avisos Frecuentes

- `Hay una nota mayor que el máximo`
- `hay un error, no se ha guardado`
- `no se puede definir cursada con preceptor`

### Referencias Internas

- Flujo: `actividadestudios.acta_notas_matricula.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/acta_notas_matricula.md`

## Actividad Asignatura

### Para Que Sirve

- El usuario crea una nueva asignatura impartida en la actividad (profesor, fechas, tipo) o elimina una existente desde el dossier de asignaturas.
- Sustituye los cases `nuevo` y `eliminar` del antiguo `update_3005.php`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Crear

1. En el dossier 3005 de una actividad, pulsar **nuevo** para abrir el formulario de alta.
2. Elegir asignatura, profesor, fechas y tipo; pulsar **guardar**.
3. El sistema crea la `ActividadAsignatura` y abre el dossier 3005 de la actividad.

#### Eliminar

1. En el listado de asignaturas del dossier 3005, seleccionar una fila.
2. Pulsar **borrar** y confirmar.
3. El sistema elimina la asignatura impartida y refresca el listado.

### Errores O Avisos Frecuentes

- `faltan claves de la asignatura de actividad`
- `hay un error, no se ha borrado`
- `hay un error, no se ha creado`
- `no encuentro la asignatura`
- `sólo se puede eliminar una asignatura desde el dossier de la actividad`

### Referencias Internas

- Flujo: `actividadestudios.actividad_asignatura.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/actividad_asignatura.md`

## Actividad Asignatura Editar

### Para Que Sirve

- El usuario modifica profesor, fechas, tipo u otros datos de una asignatura ya impartida en la actividad y guarda los cambios.
- Sustituye el case `editar` del antiguo `update_3005.php`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. En el dossier 3005, seleccionar una asignatura impartida y pulsar **modificar**.
2. Ajustar profesor, fechas, aviso a profesor o tipo en el formulario.
3. Pulsar **guardar**; el sistema persiste los cambios en la `ActividadAsignatura`.

### Errores O Avisos Frecuentes

- `faltan claves de la asignatura de actividad`
- `hay un error, no se ha guardado`
- `no encuentro la asignatura`

### Referencias Internas

- Flujo: `actividadestudios.actividad_asignatura_editar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/actividad_asignatura_editar.md`

## Asistente Observ

### Para Que Sirve

- El usuario guarda el texto de observaciones generales (`observ`) de un asistente en una actividad.
- Sustituye al case `observ` de `update_3103.php`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Desde el contexto de un asistente en una actividad, editar el campo `observ`.
2. Enviar `id_activ`, `id_nom` (o `id_pau`) y `observ` al endpoint.
3. El sistema localiza al asistente y persiste el texto.

### Errores O Avisos Frecuentes

- `falta id_activ o id_nom`
- `hay un error, no se ha guardado`
- `no encuentro al asistente`

### Referencias Internas

- Flujo: `actividadestudios.asistente_observ.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/asistente_observ.md`

## Asistente Observ Est

### Para Que Sirve

- El usuario guarda las observaciones de plan de estudios (`observ_est`) de un asistente en su actividad vigente.
- Sustituye al case `observ_est` de `update_3103.php`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. En el dossier de matrículas de una persona (1303), editar el campo de observaciones de
2. Pulsar grabar observaciones (`fnjs_grabar_observ`).
3. El sistema envía el formulario serializado al endpoint y refresca el fragmento si tiene éxito.

### Errores O Avisos Frecuentes

- `falta id_activ o id_nom`
- `hay un error, no se ha guardado`
- `no encuentro al asistente`

### Referencias Internas

- Flujo: `actividadestudios.asistente_observ_est.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/asistente_observ_est.md`

## Asistente Plan Est Ok

### Para Que Sirve

- El usuario marca el plan de estudios de un asistente como confirmado (`est_ok`).
- Sustituye al case `plan` de `update_3103.php`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. En el dossier de matrículas de una persona (1303), marcar o confirmar el plan de estudios.
2. Pulsar la acción de confirmar plan (`fnjs_grabar_est`).
3. El sistema actualiza el flag `est_ok` del asistente y refresca el fragmento.

### Errores O Avisos Frecuentes

- `falta id_activ o id_nom`
- `hay un error, no se ha guardado`
- `no encuentro al asistente`

### Referencias Internas

- Flujo: `actividadestudios.asistente_plan_est_ok.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/asistente_plan_est_ok.md`

## Ca Posibles

### Para Que Sirve

- Tras elegir centro, periodo y filtros en `ca_posibles_que`, el usuario obtiene el cuadro de posibles CA por alumno: créditos cursables, asignaturas pendientes y enlaces de detalle.
- Misma lógica que `frontend/actividadestudios/controller/ca_posibles.php`; en modo lista, `pagina_link_spec` lo firma el front.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. En `ca_posibles_que`, elegir centro N o AGD, periodo y opciones de filtro.
2. Pulsar **buscar**; el formulario envía a `ca_posibles.php`.
3. El controlador valida que haya centro seleccionado y consulta `ca_posibles_data`.
4. Se muestra el listado o cuadro de posibles CA por alumno.

### Errores O Avisos Frecuentes

- `debe seleccionar un centro o grupo de centros`
- `Parámetro na no válido`
- `sólo debebería haber uno`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.ca_posibles.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/ca_posibles.md`

## Ca Posibles Que

### Para Que Sirve

- El usuario configura los filtros del informe de posibles CA: centro (N o AGD), periodo, grupo de estudios y opciones de inclusión (estudios, repaso, todos).
- Al cargar la pantalla obtiene los desplegables y textos iniciales.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Abrir la entrada de menú **posibles ca**.
2. El sistema carga desplegables de centros N/AGD y texto de grupo vía `ca_posibles_que_data`.
3. El usuario ajusta periodo, centro y flags; al buscar pasa al flujo `ca_posibles`.

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

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Abrir **actualizar docencia** desde el menú.
2. Elegir año y periodo (o fechas personalizadas) y pulsar **buscar**.
3. El sistema calcula la docencia de actividades terminadas en el rango y la persiste.
4. Se muestra el mensaje de resultado en la misma pantalla.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `actividadestudios.docencia_actualizar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/docencia_actualizar.md`

## E43

### Para Que Sirve

El usuario consulta los datos del certificado E43 de un alumno en una actividad: datos personales, delegaciones origen/destino, actividad y asignaturas matriculadas con notas.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. En el dossier de asistentes de una actividad, seleccionar un alumno.
2. Pulsar la acción de certificado E43.
3. El sistema carga `e43` y consulta `e43_data`.
4. Se muestra el certificado en pantalla con botón de impresión PDF.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.e43.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/e43.md`

## E43 Imprimir Mpdf

### Para Que Sirve

El usuario imprime el certificado E43 en formato PDF: el sistema obtiene los mismos datos que la pantalla E43 y los renderiza en la plantilla imprimible (`e43_imprimir_mpdf.php` / `e43_2_mpdf.php`).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. En la pantalla E43, pulsar **imprimir** (abre ventana con `e43_2_mpdf.php`).
2. El controlador consulta `e43_imprimir_mpdf_data`.
3. Se renderiza el certificado con estilos `e43_mpdf.css` listo para imprimir/exportar.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `actividadestudios.e43_imprimir_mpdf.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/e43_imprimir_mpdf.md`

## Form Asignaturas De Una Actividad

### Para Que Sirve

El usuario abre el formulario para crear o editar una asignatura impartida en una actividad CA: el sistema devuelve desplegables de asignaturas y profesores, fechas, flags de aviso y permisos según el modo (nuevo/editar).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. En el dossier 3005, pulsar **nuevo** o **modificar** sobre una asignatura.
2. El sistema carga el formulario consultando `form_asignaturas_de_una_actividad_data`.
3. Se muestran desplegables, fechas y botón guardar con hash de seguridad.

### Errores O Avisos Frecuentes

- `no encuentro la asignatura de actividad`
- `No se ha encontrado la asignatura con id: <id>`
- `debería haber un nombre de asignatura`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.form_asignaturas_de_una_actividad.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/form_asignaturas_de_una_actividad.md`

## Form Matriculas De Una Persona

### Para Que Sirve

El usuario abre el formulario para matricular o editar la matrícula de una persona en una asignatura de una actividad: el sistema devuelve desplegables de nivel, asignatura, preceptor y datos de la actividad según el modo.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. En el dossier de matrículas (1303 o 3103), pulsar **nuevo** o **modificar**.
2. El sistema carga el formulario con `id_nom`, `id_activ`, `id_nivel`, `id_asignatura`.
3. Se muestran desplegables de nivel y preceptor, con enlaces AJAX a opcionales/preceptores.

### Errores O Avisos Frecuentes

- `No se ha encontrado actividad con id: <id>`
- `no encuentro la matricula`
- `No se ha encontrado la asignatura con id: <id>`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.form_matriculas_de_una_persona.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/form_matriculas_de_una_persona.md`

## Lista Clases Ca

### Para Que Sirve

El usuario consulta, para una actividad CA seleccionada, el listado de clases: por cada asignatura impartida muestra profesor, tipo y alumnos matriculados.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. En el listado de actividades (`actividad_select`), seleccionar una actividad CA.
2. Pulsar la acción **lista clase**.
3. El sistema carga `lista_clases_ca` y consulta `lista_clases_ca_data`.
4. Se muestra el informe con director de estudios y tabla por asignatura.

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

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

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

### Referencias Internas

- Flujo: `actividadestudios.matricula.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/matricula.md`

## Matricula Automatica

### Para Que Sirve

- El usuario ejecuta la matriculación automática de una o todas las personas activas: el sistema determina la actividad de estudios vigente (`ca-n`, `cv-agd`), recalcula asignaturas matriculables respetando aprobadas y topes de opcionales, y crea las matrículas.
- Sustituye `apps/actividadestudios/controller/matricular.php`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Abrir **matricular a todos** desde el menú (o desde búsqueda de persona con selección).
2. El sistema recibe `id_pau`/`sel` (persona concreta) o procesa todas las personas activas.
3. Para cada persona, borra matrículas previas si el plan no está confirmado y recalcula.
4. Se muestra el mensaje resumen en `matricular.phtml`.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `actividadestudios.matricula_automatica.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/matricula_automatica.md`

## Matricula Editar

### Para Que Sirve

- El usuario modifica nivel, asignatura, preceptor u otros datos de una matrícula ya creada y guarda los cambios.
- Sustituye el case `editar` de `update_3103.php`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. En dossier 1303 o 3103, seleccionar una matrícula y pulsar **modificar**.
2. Ajustar nivel, asignatura o preceptor en el formulario.
3. Pulsar **guardar**; el sistema persiste los cambios en la matrícula.

### Errores O Avisos Frecuentes

- `faltan claves de la matricula`
- `hay un error, no se ha guardado`
- `no encuentro la matricula`

### Referencias Internas

- Flujo: `actividadestudios.matricula_editar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/matricula_editar.md`

## Matriculas

### Para Que Sirve

El usuario elige un periodo y pulsa **buscar**: el sistema muestra la tabla de matrículas de actividades cuyo `f_ini` cae en ese intervalo, con alumno, centro, actividad, asignatura, preceptor y nota.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Consultar el listado

1. Abrir **Matrículas** desde el menú.
2. Elegir año y periodo (por defecto `curso_ca`) y pulsar **buscar**.
3. El sistema consulta `matriculas_lista_data` y muestra la tabla paginada.
4. Opcionalmente, seleccionar filas para ver dossier CA o borrar matrículas.

### Errores O Avisos Frecuentes

- `Se requieren inicioIso y finIso`
- `No se ha encontrado la asignatura con id: <id>`

### Permisos

- El caso de uso no aplica control de permisos propio: la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.matriculas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/matriculas.md`

## Matriculas Lista Otras R

### Para Que Sirve

- El usuario busca alumnos de otras regiones por apellido para consultar sus asignaturas matriculadas y emitir certificados E43.
- Solo visible en ámbito región STGR (`rstgr` o `r`).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Abrir **Envío información a otras r** (solo regiones STGR).
2. Opcionalmente filtrar por apellido y pulsar **buscar**.
3. El sistema consulta `matriculas_lista_otras_r_data` y muestra alumnos con alertas y
4. Seleccionar un alumno para **imprimir certificado** (`fnjs_imp_certificado`).

### Errores O Avisos Frecuentes

- `No se pudo resolver el repositorio de notas de otras regiones`
- `No se pudo determinar el esquema región STGR de la sesión.`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.matriculas_lista_otras_r.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/matriculas_lista_otras_r.md`

## Matriculas Pendientes

### Para Que Sirve

- El usuario consulta las matrículas que aún no tienen nota definitiva en acta: una fila por matrícula con actividad, asignatura, alumno y permiso.
- Puede abrir el dossier de la actividad o borrar matrículas seleccionadas.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Abrir **Matr. Pendientes** / **Exam. pendientes de acta** desde el menú.
2. El sistema carga automáticamente `matriculas_pendientes_data`.
3. Se muestra la tabla con avisos de región STGR si aplica.
4. Opcional: ver dossier CA de una fila o borrar matrículas.

### Errores O Avisos Frecuentes

- `No se ha encontrado la asignatura con id: <id>`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.matriculas_pendientes.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/matriculas_pendientes.md`

## Plan Estudios Ca

### Para Que Sirve

El usuario consulta el plan de estudios de una actividad CA: director de estudios, preceptores, profesores por asignatura y alumnos con sus asignaturas matriculadas y observaciones de plan (`observ_est`).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. En `actividad_select`, seleccionar una actividad CA.
2. Pulsar la acción **plan estudios**.
3. El sistema consulta `plan_estudios_ca_data` y muestra el informe completo.

### Errores O Avisos Frecuentes

- `No se ha encontrado la asignatura con id: <id>`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.plan_estudios_ca.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/plan_estudios_ca.md`

## Posibles Asignaturas Ca

### Para Que Sirve

El usuario consulta, para una actividad CA, qué asignaturas podrían matricular los alumnos según su historial de notas y pendientes, agrupado por asignatura y por alumno.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. En `actividad_select`, seleccionar una actividad CA.
2. Pulsar la acción **posibles asignaturas**.
3. El sistema consulta `posibles_asignaturas_ca_data`.
4. Se muestra el informe Twig con asignaturas y alumnos posibles.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.posibles_asignaturas_ca.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/posibles_asignaturas_ca.md`

## Profesores Desplegable

### Para Que Sirve

Al cambiar la asignatura o añadir un profesor en el formulario de asignatura impartida, el usuario obtiene la lista actualizada de profesores candidatos para esa asignatura en la actividad.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. En el formulario de asignatura impartida, cambiar la asignatura del desplegable.
2. Se dispara `fnjs_mas_profes('asignatura')` o reconstrucción del desplegable.
3. El sistema consulta `profesores_desplegable_data` con `id_activ`, `id_asignatura` y `salida`.
4. Se actualiza el desplegable de profesores en pantalla.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadestudios.profesores_desplegable.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadestudios/flujos/profesores_desplegable.md`

## Revision Pendiente

- Sustituir nombres tecnicos por nombres visibles en la aplicacion.
- Completar rutas de menu.
- Confirmar permisos necesarios.
- Anadir capturas o ejemplos si se quiere publicar para usuarios finales.
