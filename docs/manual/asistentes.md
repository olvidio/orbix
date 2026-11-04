---
tipo: "manual_usuario"
modulo: "asistentes"
flujos: 14
estado_revision: "revisado"
---

# Manual De Usuario - asistentes

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Consultar quién tiene pendiente el ca o el crt

### Para Que Sirve

Saber quién tiene pendiente hacer el ca o el crt.

### Donde Entrar

- Activ Pendientes Select (frontend/asistentes/controller/activ_pendientes_select.php)
- **Legacy:** vsm/vest/dagd/dre > pendientes según `sactividad` y `tipo_personas`
- **Pills2:** ACTIVIDADES > Listados > Listado de personas sin ca/crt

### Tareas Habituales

#### Consultar quién tiene pendiente el ca o el crt

1. Entrar en la opción **pendientes** del colectivo que interese (numerarios, agregados o sacd).
2. Se muestra «Personas» con la lista de quienes no han hecho todavía la actividad en el curso, o «no hay ninguna fila» si no queda nadie.
3. Pulsar el nombre de una persona abre su ficha.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio; listado de menú: frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `asistentes.activ_pendientes_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/activ_pendientes_select.md`

## los asistentes de una actividad

### Para Que Sirve

Ver y gestionar los asistentes de una actividad: quién está, estado de su plaza, añadir, cambiar de actividad y quitar.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- sin entrada de menú en el índice (acceso desde dossier actividad/persona, `actividad_que` o navegación embebida).

### Tareas Habituales

#### Gestionar los asistentes de una actividad

1. Marcar la actividad y pulsar **asistentes**. Se abre «Relación de asistentes» con un enlace **( dossiers )** y una tabla (nombre, dl, propio, estudios, observaciones, teléfono, mail, ctr, propietario de la plaza…).
2. El color de cada fila indica su estado: **pedida**, **en espera**, **asignada** o **confirmada** (la leyenda está debajo).
3. Marcar una o varias filas (Ctrl+clic o Mayús+clic para varias) y pulsar **modificar asistencia**, **cambiar actividad**, **borrar asistencia**, **transferir a históricos**, **añadir cargo**, **modificar cargo**, **quitar cargo**, **plan estudios** o **E43**.
4. Bajo la tabla se indican las plazas (por ejemplo «plazas actividad: 31» y «dlb: 1 (de las 1 cedidas por dlmE) => ocupadas=1/(1) libres=0»).
5. Para añadir a alguien de la dl, pulsar **+ n** o **+ agd** (según el colectivo) en «Añadir asistente de la dl».

#### Mover un asistente a otra actividad

1. Marcar al asistente y pulsar **cambiar actividad**.
2. Se abre «Mover a:» con las actividades del mismo tipo, cada una con sus «plazas libres/concedidas».
3. Elegir la actividad de destino y confirmar.

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

## Mover un asistente a otra actividad

### Para Que Sirve

Pasar un asistente de una actividad a otra del mismo tipo.

### Donde Entrar

- Asistente Mover (frontend/asistentes/controller/asistente_mover.php)
- sin entrada de menú en el índice (acceso desde dossier actividad/persona, `actividad_que` o navegación embebida).

### Tareas Habituales

#### Mover un asistente a otra actividad

1. Seleccionar al asistente en la lista y elegir mover.
2. En **mover a** elegir la actividad de destino (del mismo tipo) y, si se quiere, escribir **observaciones**.
3. Pulsar **guardar** (o **cancelar**).

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Comprueba `perm_modificar()`; si falla, devuelve `aviso_txt` sin opciones.

### Referencias Internas

- Flujo: `asistentes.asistente_mover.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/asistente_mover.md`

## Asignar plaza a varios asistentes

### Para Que Sirve

Asignar una plaza común a varios asistentes marcados a la vez.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- sin entrada de menú en el índice (acceso desde dossier actividad/persona, `actividad_que` o navegación embebida).

### Tareas Habituales

#### Asignar plaza a varios asistentes

1. En la lista de asistentes, marcar los que corresponda.
2. Elegir asignar plaza y la plaza que se quiere dar.
3. Confirmar.

### Errores O Avisos Frecuentes

- `falta id_activ`
- `falta lista de seleccion`

### Permisos

- Por asistente: `perm_modificar()` en el caso de uso.
- Lote desde listado de actividad: autorización en frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `asistentes.asistente_plaza_asignar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/asistente_plaza_asignar.md`

## Anotar la asistencia de una persona a una actividad

### Para Que Sirve

Añadir o modificar la asistencia de una persona a una actividad desde su ficha.

### Donde Entrar

- Form Actividades De Una Persona (frontend/asistentes/controller/form_actividades_de_una_persona.php)
- sin entrada de menú en el índice (acceso desde dossier actividad/persona, `actividad_que` o navegación embebida).

### Tareas Habituales

#### Anotar la asistencia de una persona a una actividad

1. Abrir el dossier de actividades de la persona.
2. Elegir la **actividad** (obligatoria: «debe indicar una actividad»), la **plaza**, quién es **propiedad de** la plaza, **estudios confirmados** y las **observaciones**.
3. Pulsar **guardar**.

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

## Modificar los datos de un asistente en una actividad

### Para Que Sirve

Editar los datos de un asistente en una actividad (plaza, observaciones, falta, estudios confirmados).

### Donde Entrar

- Form Asistentes A Una Actividad (frontend/asistentes/controller/form_asistentes_a_una_actividad.php)
- sin entrada de menú en el índice (acceso desde dossier actividad/persona, `actividad_que` o navegación embebida).

### Tareas Habituales

#### Modificar los datos de un asistente en una actividad

1. En «Relación de asistentes», marcar a la persona y pulsar **modificar asistencia** (o **+ n** / **+ agd** para añadir a alguien).
2. Se abre «Asistente a una actividad» con el nombre y los campos **propio**, **falta**, **estudios confirmados**, **Observaciones actividad**, **Observaciones estudios**, **plaza** (pedida, en espera, asignada o confirmada) y **propiedad de** (la dl dueña de la plaza, con «n de m» plazas).
3. Pulsar **Guardar datos del asistente**, o **Guardar y añadir otro asistente** para seguir con otra persona.

### Errores O Avisos Frecuentes

- `No se encontró el asistente para esta actividad.`
- `los datos de asistencia los modifica el propietario de la plaza: %s`

### Permisos

- Bloqueo por propietario de plaza en edición (no `perm_modificar` de entidad).
- Acceso al dossier: frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `asistentes.form_asistentes_a_una_actividad.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/form_asistentes_a_una_actividad.md`

## Ver las actividades por centro

### Para Que Sirve

Ver, centro por centro, las actividades previstas de sus personas.

### Donde Entrar

- Lista Activ Ctr (frontend/asistentes/controller/lista_activ_ctr.php)
- **Legacy:** Destino del submit de que_ctr_lista (`lista=list_activ`)
- **Pills2:** ACTIVIDADES > Listados > Listado de asistentes ca/crt por ctr

### Tareas Habituales

#### Ver las actividades por centro

1. Elegir los centros y el periodo (ver «Elegir centro y periodo») y pulsar **Buscar**.
2. Para cada centro (por ejemplo «Alimara») aparece una tabla «nombre / actividades previstas» con cada persona y su actividad (por ejemplo «ca n repaso Hostal Rialb (7/8/2026-29/8/2026)-dlb»), «NO HACE CA [pedida]» o «pendiente de solicitar» si aún no tiene.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio en el caso de uso; `ssfsv` se ajusta con `$_SESSION['oPerm']` solo para filtrar datos.

### Referencias Internas

- Flujo: `asistentes.lista_activ_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/lista_activ_ctr.md`

## Ver las plazas de varias actividades

### Para Que Sirve

Ver el listado conjunto de plazas y asistentes de varias actividades.

### Donde Entrar

- Lista Asis Conjunto Activ (frontend/asistentes/controller/lista_asis_conjunto_activ.php)
- sin entrada de menú en el índice (acceso desde dossier actividad/persona, `actividad_que` o navegación embebida).

### Tareas Habituales

#### Ver las plazas de varias actividades

1. Seleccionar las actividades.
2. Elegir el listado conjunto: se muestran las plazas y asistentes de todas ellas.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio; desde `actividad_que` (`que=list_cjto`): frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `asistentes.lista_asis_conjunto_activ.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/lista_asis_conjunto_activ.md`

## Ver la relación de asistentes de una actividad

### Para Que Sirve

Ver la relación de asistentes de una actividad (botones **lista cl** y **lista**).

### Donde Entrar

- Lista Asistentes (frontend/asistentes/controller/lista_asistentes.php)
- sin entrada de menú en el índice (acceso desde dossier actividad/persona, `actividad_que` o navegación embebida).

### Tareas Habituales

#### Ver la relación de asistentes de una actividad

1. Marcar la actividad en la lista y pulsar **lista** (nombres) o **lista cl** (con más datos).
2. Aparece el título de la actividad y «Relación de asistentes» numerada.
3. **lista**: nombre y dl (o centro) de cada asistente. **lista cl**: además estudios, profesión, edad, eap y observaciones de la actividad.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio; acceso desde ficha actividad: frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `asistentes.lista_asistentes.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/lista_asistentes.md`

## Ver los estudios matriculados por centro

### Para Que Sirve

Ver, centro por centro, los estudios en los que están matriculadas sus personas.

### Donde Entrar

- Lista Est Ctr (frontend/asistentes/controller/lista_est_ctr.php)
- **Legacy:** vest/dagd > estudios x ctr
- **Pills2:** ACTIVIDADES > Listados > Mejores ca para n/agd

### Tareas Habituales

#### Ver los estudios matriculados por centro

1. Elegir los centros y el curso y pulsar **Buscar**.
2. Debería aparecer, por centro, la lista de personas con sus estudios matriculados (pantalla no comprobada en la instancia).

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio; listado de menú: frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `asistentes.lista_est_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/lista_est_ctr.md`

## Preparar el informe de última asistencia

### Para Que Sirve

Elegir centro y curso para el informe de última asistencia (quién no ha ido al crt o a la cv).

### Donde Entrar

- Lista Ultim Que Ctr (frontend/asistentes/controller/lista_ultim_que_ctr.php)
- **Legacy:** vsg > crt/cv > informes de seguimiento
- **Pills2:** vsg > crt/cv > s que no han ido / corresponde ir

### Tareas Habituales

#### Preparar el informe de última asistencia

1. Entrar en la opción que interese (este curso o el curso pasado).
2. Elegir el centro.
3. Se muestra el informe.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio; entrada menú vsg: frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `asistentes.lista_ultim_que_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/lista_ultim_que_ctr.md`

## Consultar quién no ha asistido recientemente

### Para Que Sirve

Informe de las personas s que no han asistido recientemente al crt o a la cv.

### Donde Entrar

- Lista Ultima Activ (frontend/asistentes/controller/lista_ultima_activ.php)
- **Legacy:** Destino del submit de lista_ultim_que_ctr
- **Pills2:** vsg > crt/cv > informes de seguimiento

### Tareas Habituales

#### Consultar quién no ha asistido recientemente

1. Entrar en la opción **s que no han ido al crt** (o a la **cv**) del curso actual o del anterior.
2. Se muestra la lista con la fecha de su última asistencia.

### Errores O Avisos Frecuentes

- `No sé en que tipo de actividad hay que mirar las asistencias`

### Permisos

- Sin control propio; informes de menú vsg: frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `asistentes.lista_ultima_activ.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/lista_ultima_activ.md`

## Elegir centro y periodo

### Para Que Sirve

Elegir de qué centros se quiere ver las actividades de sus personas y en qué periodo.

### Donde Entrar

- Que Ctr Lista (frontend/asistentes/controller/que_ctr_lista.php)
- **Legacy:** Varias entradas según `lista`/`sactividad` (vsm/vest/dagd > crt/ca/cv > list por ctr; dre > personas > pendientes)
- **Pills2:** ACTIVIDADES > Listados > Listado de asistentes ca/crt por ctr, Mejores ca para n/agd, Listado de personas sin ca/crt

### Tareas Habituales

#### Elegir centro y periodo

1. Abrir «Actividades de personas por centros de la delegación» (pregunta «¿qué centro interesa?»).
2. Elegir **Todos los numerarios**, **Todos ctr numerarios jóvenes**, **Todos ctr numerarios mayores**, **Todos los agregados**, **Todos los de sss+** u **Otro...** (y entonces el centro concreto).
3. En «PERIODO DE INICIO O FINALIZACIÓN DE LAS ACTIVIDADES» elegir curso ca, curso crt, todo el año u **otro** (con fechas) y el año.
4. Pulsar **Buscar** (**Borrar** limpia el formulario). Si el resultado es grande, tarda unos segundos (hasta unos 10 con todos los numerarios y todo el año, lo que se considera aceptable) y muestra «Cargando...».

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio; listados de menú ACTIVIDADES: frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `asistentes.que_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/que_ctr.md`

## Ver las otras peticiones de los asistentes

### Para Que Sirve

Ver las peticiones de plaza de las personas que quieren asistir a la actividad.

### Donde Entrar

- Tabla Peticiones (frontend/asistentes/controller/tabla_peticiones.php)
- sin entrada de menú en el índice (acceso desde dossier actividad/persona, `actividad_que` o navegación embebida).

### Tareas Habituales

#### Ver las otras peticiones de los asistentes

1. Marcar la actividad y pulsar **otras peticiones**.
2. Se muestra el título de la actividad y una tabla «nombre / peticiones (libres/concedidas)» con cada persona que ha pedido plaza y sus otras peticiones.
3. Si no hay peticiones, la tabla sale vacía.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio; requiere `actividadplazas`; acceso desde actividad: frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `asistentes.tabla_peticiones.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/asistentes/flujos/tabla_peticiones.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/asistentes/flujos/`.
