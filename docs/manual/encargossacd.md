---
tipo: "manual_usuario"
modulo: "encargossacd"
flujos: 32
estado_revision: "revisado"
---

# Manual De Usuario - encargossacd

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Eliminar encargos de centros inexistentes

### Para Que Sirve

Limpiar los encargos ligados a centros que ya no existen.

### Donde Entrar

- Comprobaciones (frontend/encargossacd/controller/comprobaciones.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Eliminar encargos de centros inexistentes

1. Entrar en **listados**.
2. En **comprobaciones** pulsar **Eliminar encargos de centros inexistentes**.
3. Se borran los encargos de centros inactivos y los que no tienen sacerdote.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.comprobaciones_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/comprobaciones_ctr.md`

## Consultar y modificar la ficha de un centro

### Para Que Sirve

Ver y modificar la atención sacerdotal de un centro: sacerdote titular, suplente, colaboradores, dedicación y exigencias.

### Donde Entrar

- Ctr Ficha (frontend/encargossacd/controller/ctr_ficha.php)
- **Legacy:** dre > Encargos > ficha ctr
- **Pills2:** ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Ficha ctr

### Tareas Habituales

#### Consultar y modificar la ficha de un centro

1. Entrar en **ficha ctr**.
2. Elegir el **grupo de ctrs** (sv, sf, sss+, igl, cgi/oc…) y luego el **centro**.
3. Se abre la ficha con el **sacd titular**, el **Suplente**, los colaboradores, la dedicación (mañanas, tarde 1ª y 2ª hora), las exigencias del centro y las observaciones.
4. Cambiar lo que proceda y pulsar **guardar**. Si titular y suplente son el mismo sacerdote avisa «El sacd titular y suplente deben ser distintos»; no se guarda hasta corregirlo.

### Errores O Avisos Frecuentes

- `Debe nombrar un sacerdote tirular`
- `grupo de encargo no valido`
- `hay un error, no se ha guardado`
- `Debe nombrar un sacerdote titular`
- `El sacd titular y suplente deben ser distintos`
- `hay un error, no se ha eliminado`

### Referencias Internas

- Flujo: `encargossacd.ctr_ficha.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/ctr_ficha.md`

## Abrir la ficha de un centro

### Para Que Sirve

Cargar la ficha de atención sacerdotal del centro elegido.

### Donde Entrar

- Ctr Get Ficha (frontend/encargossacd/controller/ctr_get_ficha.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Abrir la ficha de un centro

1. En **ficha ctr**, elegir el grupo y el centro.
2. La ficha aparece como «Atención ctr (nombre del centro)» con **sacd titular**, casilla **cl**, **dedicación** (mañanas, tarde 1ª hora, tarde 2ª hora), **Suplente**, colaboradores, **observaciones** y **exigencias del ctr**. En **Selección de sacd** las casillas **prelatura** y **sss+** limitan qué sacerdotes salen en las listas.
3. Para añadir un colaborador, pulsar **añadir sacd colaborador**; para fijar horas, **crear horario**.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Permiso oficina `des`
- Permiso oficina `vcsd`

### Referencias Internas

- Flujo: `encargossacd.ctr_get_ficha.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/ctr_get_ficha.md`

## Elegir un centro

### Para Que Sirve

Rellenar el desplegable de centros según el grupo elegido.

### Donde Entrar

- Ctr Ficha (frontend/encargossacd/controller/ctr_ficha.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Elegir un centro

1. En **ficha ctr**, elegir el **grupo de ctrs**.
2. El desplegable **centro** se rellena con los centros de ese grupo; elegir uno.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.ctr_get_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/ctr_get_select.md`

## Consultar los horarios de un encargo

### Para Que Sirve

Ver los horarios de un encargo y añadir otro.

### Donde Entrar

- Encargo Horario Select (frontend/encargossacd/controller/encargo_horario_select.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Consultar los horarios de un encargo

1. Abrir el encargo.
2. Aparece la lista **horario de** el encargo.
3. Pulsar **añadir horario** para crear uno nuevo, o un horario para modificarlo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.encargo_horario_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/encargo_horario_select.md`

## Elegir el tipo de encargo

### Para Que Sirve

Ofrecer los tipos de encargo que corresponden al grupo elegido.

### Donde Entrar

- Encargo Ver (frontend/encargossacd/controller/encargo_ver.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Elegir el tipo de encargo

1. En el formulario de encargo, elegir el **grupo**.
2. El desplegable **tipo encargo** muestra solo los tipos de ese grupo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.encargo_lst_tipo_enc.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/encargo_lst_tipo_enc.md`

## Buscar un encargo

### Para Que Sirve

Buscar los encargos existentes y acceder a su ficha, o crear uno nuevo.

### Donde Entrar

- Encargo Select (frontend/encargossacd/controller/encargo_select.php)
- **Legacy:** dre > Encargos > ver encargo
- **Pills2:** ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Ver encargos

### Tareas Habituales

#### Buscar un encargo

1. Entrar en **ver encargo**.
2. Rellenar los criterios de **buscar un encargo** y pulsar **buscar**.
3. Pulsar el encargo en la tabla para abrirlo (o usar **Buscar en todas las columnas**).

#### Crear un encargo

1. Pulsar **añadir encargo**.
2. Rellenar el formulario (ver «Modificar un encargo») y guardar.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.encargo_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/encargo_select.md`

## Modificar un encargo

### Para Que Sirve

Crear, ver, modificar o eliminar un encargo (atención de un centro, zona, etc.).

### Donde Entrar

- Encargo Ver (frontend/encargossacd/controller/encargo_ver.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Modificar un encargo

1. Abrir el encargo desde **ver encargo**.
2. Cambiar el **grupo**, el **tipo encargo**, el **centro** o la **zona**, el **lugar**, la **descripción** y el **idioma**.
3. Pulsar **modificar encargo**.

#### Crear un encargo nuevo

1. En **ver encargo** pulsar **añadir encargo**.
2. Elegir el **tipo de encargo** («escoger el tipo de encargo»), el centro o zona y la descripción.
3. Pulsar **nuevo encargo** (o **generar**).

#### Eliminar un encargo

1. En la tabla de encargos, marcar el encargo y eliminarlo; confirmar.

### Errores O Avisos Frecuentes

- `No se encuentra el encargo %d`
- `hay un error, no se ha eliminado`
- `Debe seleccionar un tipo de encargo`
- `Debe llenar el campo descripción`
- `grupo de encargo no valido`
- `hay un error, no se ha guardado`

### Referencias Internas

- Flujo: `encargossacd.encargo_ver.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/encargo_ver.md`

## Guardar los cambios de un encargo

### Para Que Sirve

Guardar los cambios hechos en un encargo.

### Donde Entrar

- Encargo Ver (frontend/encargossacd/controller/encargo_ver.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Guardar los cambios de un encargo

1. Modificar los campos del encargo.
2. Pulsar **modificar encargo**.

### Errores O Avisos Frecuentes

- `Debe llenar el campo descripción`
- `No se encuentra el encargo %d`
- `hay un error, no se ha guardado`

### Referencias Internas

- Flujo: `encargossacd.encargo_ver_editar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/encargo_ver_editar.md`

## Guardar un horario de sacerdote

### Para Que Sirve

Guardar, modificar o quitar el horario de un sacerdote en un encargo.

### Donde Entrar

- Horario Sacd Update (frontend/encargossacd/controller/horario_sacd_update.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Guardar un horario de sacerdote

1. Rellenar el horario y pulsar **guardar horario**.
2. Para borrarlo, **eliminar horario** y confirmar «¿Está seguro que desea borrar este horario?».

### Errores O Avisos Frecuentes

- `acción no válida`
- `registro no encontrado`
- `Debe llenar todos los campos que tengan un (*)`
- `hay un error, no se ha guardado`

### Referencias Internas

- Flujo: `encargossacd.horario_sacd_update.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/horario_sacd_update.md`

## Crear o modificar el horario de un sacerdote

### Para Que Sirve

Ver el formulario de horario de un sacerdote en un encargo, con excepciones antes o después de una fecha.

### Donde Entrar

- Horario Sacd Ver (frontend/encargossacd/controller/horario_sacd_ver.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Crear o modificar el horario de un sacerdote

1. En la ficha, pulsar **crear horario**.
2. Indicar **dia**, **hora inicio**, **hora fin** y el periodo (**activo desde** / **hasta**).
3. Se pueden **añadir horario** y **generar excepciones** (antes del / después del).
4. Pulsar **guardar horario**; **eliminar horario** pide confirmación.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.horario_sacd_ver.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/horario_sacd_ver.md`

## Guardar un horario de encargo

### Para Que Sirve

Guardar, modificar o quitar un horario de un encargo.

### Donde Entrar

- Horario Update (frontend/encargossacd/controller/horario_update.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Guardar un horario de encargo

1. Rellenar día, horas y periodo.
2. Pulsar guardar. Para quitarlo, usar eliminar y confirmar.

### Errores O Avisos Frecuentes

- `acción no válida`
- `Debe llenar todos los campos que tengan un (*)`
- `registro no encontrado`
- `hay un error, no se ha guardado`

### Referencias Internas

- Flujo: `encargossacd.horario_update.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/horario_update.md`

## Ver o crear un horario de encargo

### Para Que Sirve

Ver el formulario de horario de un encargo (días, horas, nº de sacerdotes, periodo de vigencia).

### Donde Entrar

- Horario Ver (frontend/encargossacd/controller/horario_ver.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ver o crear un horario de encargo

1. En la lista de horarios, pulsar **añadir horario** o un horario existente.
2. Indicar **dia**, **hora inicio**, **hora fin**, **nº de sacd** y el periodo (**activo desde** / **hasta**).

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.horario_ver.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/horario_ver.md`

## Listar la atención de los centros

### Para Que Sirve

Sacar el listado de atención de centros de sv y sf (cr 1/14, 10,a).

### Donde Entrar

- Listas A (frontend/encargossacd/controller/listas_a.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Listar la atención de los centros

1. Entrar en **listados**.
2. En «listados de encargos para cr» elegir **atención ctr sv** o **atención ctr sf**.
3. Se muestra el listado, listo para imprimir: cabecera con el **Curso** (por ejemplo 1/9/2026 - 31/8/2027), «dlb» y la referencia «ref. cr 1/14, 10, a)»; luego «1. ctr de n» con cada centro y sus sacerdotes. Si un sacerdote asignado ya no existe, el listado lo dice («No encuentro a nadie con id_nom: …»); hay que corregir ese encargo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.listas_a.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/listas_a.md`

## Listar la atención de iglesias, oc y lp

### Para Que Sirve

Sacar el listado de atención de iglesias, obras corporativas y lugares de paso (cr 1/14, 10,b).

### Donde Entrar

- Listas B (frontend/encargossacd/controller/listas_b.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Listar la atención de iglesias, oc y lp

1. Entrar en **listados**.
2. Elegir **atención iglesias, oc y lp** (o **oc y lp sf**).
3. Se muestra el listado.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.listas_b.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/listas_b.md`

## Listar los encargos por sacerdote

### Para Que Sirve

Sacar el listado de encargos por sacerdote (cr 1/14, 10,d).

### Donde Entrar

- Listas C (frontend/encargossacd/controller/listas_c.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Listar los encargos por sacerdote

1. Entrar en **listados**.
2. Elegir **por sacd**.
3. Se muestra cada sacerdote (agrupados por ciudad) con sus encargos y la dedicación entre paréntesis, por ejemplo «cl de: Raset (1 tarde 2ª hora)», «atención de: Ribalera (2 tarde 1ª hora)», estudio o descanso.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Permiso oficina `vcsd`
- Permiso oficina `des`

### Referencias Internas

- Flujo: `encargossacd.listas_c.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/listas_c.md`

## Preparar la comunicación a los centros

### Para Que Sirve

Preparar el escrito que se envía a cada centro con su sacerdote titular, suplente y colaboradores.

### Donde Entrar

- Listas Com Ctr (frontend/encargossacd/controller/listas_com_ctr.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Preparar la comunicación a los centros

1. Entrar en **listados**.
2. Elegir **comunicación a los centros sv** (o **sf**).
3. Aparece un escrito por centro con **titular**, **suplente** y **colaboradores**, en el idioma del centro, con cabecera y lugar/fecha (por ejemplo «Barcelona, 3.11.2026»). El curso que aparece en el texto («para el curso …») se calcula solo, según la fecha de hoy (el curso empieza en septiembre); el resto del texto se cambia en **modificar los textos de las comunicaciones**.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.listas_com_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/listas_com_ctr.md`

## Preparar la comunicación a los sacerdotes

### Para Que Sirve

Preparar el escrito que se envía a cada sacerdote con sus encargos.

### Donde Entrar

- Listas Com Sacd (frontend/encargossacd/controller/listas_com_sacd.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Preparar la comunicación a los sacerdotes

1. Entrar en **listados**.
2. Elegir **comunicación a sacd Prelatura** o **comunicación a sacd sss+**.
3. Aparece un escrito por sacerdote (en el idioma configurado, por ejemplo catalán) con la relación de sus labores y la dedicación prevista (marcando si es **coordinador**). Es una salida muy larga: un escrito por cada sacerdote.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.listas_com_sacd.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/listas_com_sacd.md`

## Modificar los textos de las comunicaciones

### Para Que Sirve

Modificar los textos fijos que llevan las comunicaciones, por idioma.

### Donde Entrar

- Listas Com Txt (frontend/encargossacd/controller/listas_com_txt.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Modificar los textos de las comunicaciones

1. Entrar en **listados** y elegir **modificar los textos de las comunicaciones**.
2. Elegir el tipo de texto y el idioma.
3. Editar el texto y pulsar **guardar**. Si se deja vacío, se elimina.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.listas_com_txt.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/listas_com_txt.md`

## Listar la tabla por sacerdote

### Para Que Sirve

Sacar la tabla por sacerdote (cr 9/20, 10), para sv o sf.

### Donde Entrar

- Listas D (frontend/encargossacd/controller/listas_d.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Listar la tabla por sacerdote

1. Entrar en **listados**.
2. Elegir **tabla por sacd** o **tabla por sacd sf**.
3. Se muestra, por sacerdote y ciudad, la lista de centros con el nº de horas (por ejemplo Raset 3, Moneders 5), incluidos estudio y descanso.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Permiso oficina `vcsd`
- Permiso oficina `des`

### Referencias Internas

- Flujo: `encargossacd.listas_d.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/listas_d.md`

## Listar las exigencias de los centros

### Para Que Sirve

Sacar el listado de las exigencias de atención sacerdotal de cada centro o iglesia.

### Donde Entrar

- Listas Exigencia Ctr (frontend/encargossacd/controller/listas_exigencia_ctr.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Listar las exigencias de los centros

1. Entrar en **listados**.
2. En «listados comunicaciones» elegir **exigencias ctr sv**, **exigencias ctr sf** o **exigencias igl, oc…**.
3. Se muestra, por centro o iglesia, quién es el encargado, rector o sacerdotes y las horas exigidas por franja, por ejemplo «(4 mañana, 2 tarde 1ª hora)».

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.listas_exigencia_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/listas_exigencia_ctr.md`

## Ver la lista de propuestas

### Para Que Sirve

- Filtrar por centro, ver los encargos propuestos y cambiar el sacd titular, suplente o colaborador, o su dedicación (mañanas, tardes, vespertino), antes de aprobar las propuestas.
- (Redactado desde el código; no contrastado con la pantalla.).

### Donde Entrar

- Propuestas Lista (frontend/encargossacd/controller/propuestas_lista.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ver la lista de propuestas

1. Abrir **modificar propuestas** desde el menú de propuestas.
2. Elegir la sección y, si se quiere, filtrar por centro; la lista se actualiza sola.
3. Si algo falla, sale un aviso con el mensaje.

#### Asignar o cambiar el sacd de un encargo

1. Pulsar el nombre del sacd de un encargo: se abre un desplegable.
2. Elegir el sacd. Si se deja un colaborador en blanco, se borra.
3. La fila se actualiza y el desplegable se cierra.

#### Ver el detalle o cambiar la dedicación

1. Pulsar la información o la dedicación de un encargo: se abre una ventana.
2. Para cambiar el horario, modificarlo y guardar; la lista se refresca.

### Errores O Avisos Frecuentes

- `Debe crear la tabla de propuestas`
- `No se puede guardar. Vuelva a cargar la vista`
- `Registro no encontrado`
- `Alert cliente: «Primero debe introducir un sacd» (id_sacd == 1)`

### Referencias Internas

- Flujo: `encargossacd.propuestas_lista.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/propuestas_lista.md`

## Ver las propuestas por encargos

### Para Que Sirve

- Ver cómo van las propuestas, por encargos, después (o durante) la edición.
- (Redactado desde el código; no contrastado con la pantalla.).

### Donde Entrar

- Propuestas Lista Enc (frontend/encargossacd/controller/propuestas_lista_enc.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ver las propuestas por encargos

1. Abrir **listado propuestas por encargos**.
2. Si se quiere, filtrar por centro.
3. Se muestra el listado. Si todavía no se ha creado la tabla de propuestas, avisa «Debe crear la tabla de propuestas».

### Errores O Avisos Frecuentes

- `Debe crear la tabla de propuestas`

### Referencias Internas

- Flujo: `encargossacd.propuestas_lista_enc.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/propuestas_lista_enc.md`

## Ver las propuestas por sacd

### Para Que Sirve

- Revisar, e imprimir si hace falta, los encargos propuestos para cada sacd.
- (Redactado desde el código; no contrastado con la pantalla.).

### Donde Entrar

- Propuestas Lista Sacd (frontend/encargossacd/controller/propuestas_lista_sacd.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ver las propuestas por sacd

1. Abrir **listado propuestas por sacd**.
2. Se muestra, por cada sacd, la lista de encargos propuestos, lista para imprimir.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.propuestas_lista_sacd.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/propuestas_lista_sacd.md`

## Crear la tabla de propuestas

### Para Que Sirve

- Desde la pantalla de propuestas: volver a crear la tabla de trabajo, aprobar los cambios y pasarlos a los datos reales, o abrir la edición y los listados de propuestas.
- (Redactado desde el código; no contrastado con la pantalla.).

### Donde Entrar

- Propuestas Menu (frontend/encargossacd/controller/propuestas_menu.php)
- **Legacy:** dre > Encargos > propuestas
- **Pills2:** ATENCIÓN SACD > Encargos sacd (ctr, etc.) > propuestas

### Tareas Habituales

#### Crear la tabla de propuestas

1. Pulsar la opción para crear la tabla de propuestas.
2. Confirmar el aviso: elimina la tabla anterior y la crea de nuevo.
3. Si no se ha podido crear, sale un aviso.

#### Aprobar las propuestas

1. Pulsar **aprobar**.
2. Leer y confirmar el aviso: tarda unos 30 segundos y no se puede deshacer.
3. Al terminar aparece «Hecho!».

#### Abrir la edición o los listados

1. Elegir **modificar propuestas**, **listado propuestas por sacd** o **listado propuestas por encargos**; se abre en la misma ventana.

### Errores O Avisos Frecuentes

- `No se puede crear la tabla (crear_tabla)`
- `Fallos de aprobar: sin mensaje _() explícito en el caso de uso (pendiente)`

### Referencias Internas

- Flujo: `encargossacd.propuestas_menu.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/propuestas_menu.md`

## Guardar las ausencias de un sacerdote

### Para Que Sirve

Guardar o modificar las ausencias y otras tareas de un sacerdote.

### Donde Entrar

- Sacd Ausencias (frontend/encargossacd/controller/sacd_ausencias.php)
- **Legacy:** dre > ausencias > sacd
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Guardar las ausencias de un sacerdote

1. Elegir el tipo y el sacd.
2. Rellenar fechas de inicio y fin y el tipo de ausencia; para borrar una, dejar las dos fechas en blanco.
3. Pulsar **guardar**.

### Errores O Avisos Frecuentes

- `no se ha encontrado el encargo del sacd`

### Referencias Internas

- Flujo: `encargossacd.sacd_ausencias.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/sacd_ausencias.md`

## Anotar una ausencia

### Para Que Sirve

Ver y anotar las ausencias o actividades personales de un sacerdote.

### Donde Entrar

- Sacd Ausencias Get (frontend/encargossacd/controller/sacd_ausencias_get.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Anotar una ausencia

1. Entrar en **ausencias > sacd**.
2. Elegir el **tipo** y el **sacd**.
3. En **ausencias/tareas** (columnas **fechas** y **horario**), elegir en **añadir actividades personales** el tipo (Clases del stgr, Ausencia, cv de un dia, 1ª Confesiones, boda, confirmación, 1ª comunión, bautizo, Tarea, rtm): aparece una fila con **inicio** y **fin** (al poner el inicio, el fin se rellena con la misma fecha). Con **ver anteriores** se ve el historial.
4. Pulsar **Guardar**; la pantalla vuelve atrás sola. Para borrar una, dejar en blanco la fecha de inicio y la de fin.
5. **añadir actividades personales** y **crear horario** permiten añadir otras tareas.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.sacd_ausencias_get.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/sacd_ausencias_get.md`

## Anotar ausencias de los sacerdotes de la zona

### Para Que Sirve

Que el jefe de zona anote las ausencias de los sacerdotes de su zona.

### Donde Entrar

- Sacd Ausencias Jefe Zona (frontend/encargossacd/controller/sacd_ausencias_jefe_zona.php)
- **Legacy:** exterior > sacd > Misas > Ausencias
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Anotar ausencias de los sacerdotes de la zona

1. Entrar en **Ausencias**.
2. Elegir el **sacd** de la lista (los de la zona del jefe; el oficial de la dl ve todos).
3. Rellenar sus ausencias como en «Anotar una ausencia».

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.sacd_ausencias_jefe_zona.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/sacd_ausencias_jefe_zona.md`

## Consultar y modificar la ficha de un sacerdote

### Para Que Sirve

Ver y modificar los encargos de un sacerdote y su dedicación en cada uno.

### Donde Entrar

- Sacd Ficha (frontend/encargossacd/controller/sacd_ficha.php)
- **Legacy:** dre > Encargos > ficha sacd
- **Pills2:** ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Ficha sacd

### Tareas Habituales

#### Consultar y modificar la ficha de un sacerdote

1. Entrar en **ficha sacd**; la pantalla se titula «fase real».
2. Elegir el **tipo** (n, agd, sss+, cp) y luego el sacerdote en **Sacd:**.
3. Aparecen sus encargos agrupados en **titular**, **titular (no cl)**, **suplente** y **colaborador** (por ejemplo «Atención de un ctr (Raset)», «descanso», «estudio», «oficial dre»). Cada uno tiene tres casillas numéricas: **mañanas**, **tarde 1ª hora** y **tarde 2ª hora**. El número es la cantidad de días (a la semana) que el sacerdote cubre esa franja.
4. Para añadir otro encargo, elegir en **añadir encargo** (estudio, descanso, retiro, otros).
5. Escribir o cambiar las **observaciones** si hace falta y pulsar **Guardar**.

### Errores O Avisos Frecuentes

- `id_nom no valido`
- `Error con las tareas`
- `hay un error, no se ha eliminado`
- `hay un error, no se ha guardado`

### Permisos

- Permiso oficina `des`
- Permiso oficina `vcsd`

### Referencias Internas

- Flujo: `encargossacd.sacd_ficha.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/sacd_ficha.md`

## Elegir un sacerdote

### Para Que Sirve

Rellenar el desplegable de sacerdotes según el tipo elegido.

### Donde Entrar

- Sacd Ficha Ajax (frontend/encargossacd/controller/sacd_ficha_ajax.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Elegir un sacerdote

1. En **ficha sacd** o **ausencias**, elegir el **tipo**.
2. El desplegable **sacd** se rellena con los sacerdotes de ese tipo; elegir uno.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.sacd_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/sacd_select.md`

## Elegir la zona de un encargo

### Para Que Sirve

Ofrecer las zonas de misas al crear o editar un encargo.

### Donde Entrar

- Encargo Ver (frontend/encargossacd/controller/encargo_ver.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Elegir la zona de un encargo

1. En el formulario de encargo, abrir **zona**.
2. Elegir una de las zonas de misas.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.zonas_get_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/zonas_get_select.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/encargossacd/flujos/`.
