---
tipo: "manual_usuario"
modulo: "inventario"
flujos: 44
estado_revision: "revisado"
---

# Manual De Usuario - inventario

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Editar los textos tipo

### Para Que Sirve

Fijar los textos tipo que llevan por defecto los equipajes impresos: cabecera, cabecera para el material que envía la dl y pie de firma.

### Donde Entrar

- Textos cabecera/pie equipajes (frontend/inventario/controller/cabecera_pie_txt.php)
- **Legacy:** scdl > Inventario > equipajes > tipos de texto
- **Pills2:** —

### Tareas Habituales

#### Editar los textos tipo

1. Abrir **tipos de texto**.
2. Modificar la **cabecera tipo para equipajes** (instrucciones para comprobar el inventario de la casa al llegar y al marchar).
3. Modificar la **cabecera para equipajes tipo B) (que envía la dl)** (instrucciones para el material que lleva la delegación y se devuelve al final).
4. Modificar el **pie tipo para equipajes** (lista de lo que se deja en la casa) y el texto **para la firma en equipajes** (observaciones, fecha y firma).
5. Pulsar **guardar**; aparece «textos guardados correctamente».

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.cabecera_pie_txt.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/cabecera_pie_txt.md`

## Guardar la asignación a centros

### Para Que Sirve

Guardar la asignación de un documento a los centros elegidos.

### Donde Entrar

- Formulario asignar a centros (frontend/inventario/controller/doc_asignar_ctr.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Guardar la asignación a centros

1. En **Asignar a centros (sin especificar lugar)**, marcar los centros y pulsar para continuar. En la pantalla siguiente completar, para cada centro, el número de registro (propone «último + 1») o de ejemplares y las fechas **recibido** y **asignado**.
2. Pulsar **guardar**.
3. Si hay algún problema aparece un aviso **respuesta**; si va bien, la pantalla vuelve atrás sola.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.doc_asignar_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/doc_asignar_ctr.md`

## Guardar la asignación a la dlb/casa

### Para Que Sirve

Guardar la asignación de un documento a los lugares de la dlb/casa elegidos.

### Donde Entrar

- Formulario asignar DLB (frontend/inventario/controller/doc_asignar_dlb.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Guardar la asignación a la dlb/casa

1. Completar el número de registro o de ejemplares y las fechas **recibido** y **asignado**.
2. Pulsar **guardar**.
3. Si hay algún problema aparece un aviso **respuesta**; si va bien, la pantalla vuelve atrás sola.

### Errores O Avisos Frecuentes

- `No se encuentra el documento`
- `hay un error, no se ha guardado`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.doc_asignar_dlb.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/doc_asignar_dlb.md`

## Modificar varios documentos a la vez

### Para Que Sirve

Cambiar de golpe fechas, numeración o estado de varios documentos a la vez.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** scdl > Inventario > documentos > asignar documento
- **Pills2:** —

### Tareas Habituales

#### Modificar varios documentos a la vez

1. Abrir **Inventario > documentos > por sigla**.
2. Elegir el **tipo de documento** y pulsar **Buscar**.
3. En el listado, marcar los documentos que se quieren cambiar y pulsar **Modificar por bloques**. Si falta el tipo o no hay documentos marcados avisa «Debe seleccionar un tipo de documento» o «Debe seleccionar algún documento».
4. En «campos a modificar en los documentos seleccionados», **marcar la casilla «modificar» de cada campo que se quiere cambiar** y escribir su valor: **fecha recibido**, **fecha asignado**, **eliminado** (sí/no), **fecha eliminado**, **número inicial**, **número final**. Los campos sin marcar no se tocan; un campo marcado y vacío borra el valor.
5. Pulsar **guardar** (o **cancel** para salir sin cambios). Si no se marca ningún campo avisa «Debe marcar al menos un campo a modificar». Al guardar bien aparece «guardado» y el listado se actualiza.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`
- `No ha seleccionado ningún documento`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.documentos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/documentos.md`

## Ciclo completo de un equipaje

### Para Que Sirve

Ciclo de vida de los equipajes: crear, componer las valijas, imprimir y eliminar.

### Donde Entrar

- Gestionar equipajes (frontend/inventario/controller/equipajes_ver.php)
- **Legacy:** scdl > Inventario > equipajes > hacer equipajes
- **Pills2:** —

### Tareas Habituales

#### Ciclo completo de un equipaje

1. Crear con **nuevo equipaje**.
2. Componer las valijas en **hacer equipajes**.
3. Imprimir con **imprimir equipajes**.
4. Al terminar, eliminar el equipaje desde **hacer equipajes** (se eliminan también sus valijas).

### Errores O Avisos Frecuentes

- `falta id_equipaje`
- `hay un error, no se ha eliminado`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes.md`

## Añadir un documento a una valija

### Para Que Sirve

Meter un documento en una valija del equipaje.

### Donde Entrar

- Gestionar equipajes (frontend/inventario/controller/equipajes_ver.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Añadir un documento a una valija

1. Abrir **hacer equipajes** y elegir el equipaje.
2. En la valija, pulsar **añadir documento**.
3. Elegir el **tipo de documento**; se listan los ejemplares libres. Marcar el ejemplar y pulsar **añadir** (o **cancel**).
4. El documento aparece en la valija; si falla se muestra el aviso **respuesta**.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes_add_doc.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes_add_doc.md`

## Quitar un documento de una valija

### Para Que Sirve

Sacar un documento de una valija del equipaje.

### Donde Entrar

- Gestionar equipajes (frontend/inventario/controller/equipajes_ver.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Quitar un documento de una valija

1. Abrir **hacer equipajes** y elegir el equipaje.
2. En la valija, pulsar **quitar documento**.
3. Marcar los documentos a quitar y pulsar el botón de quitar (**Cancel** para salir sin cambios).
4. Los documentos dejan de estar en la valija; si falla se muestra el aviso **respuesta**.

### Errores O Avisos Frecuentes

- `hay un error, no se ha eliminado`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes_del_doc.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes_del_doc.md`

## Consultar los documentos de la casa y sus valijas

### Para Que Sirve

Ver los documentos que pertenecen a la casa (se quedan siempre allí) y las valijas del equipaje.

### Donde Entrar

- Docs por casa (equipaje) (frontend/inventario/controller/equipajes_doc_casa.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Consultar la documentación de la casa y sus valijas

1. Abrir **hacer equipajes**; elegir el filtro **desde hoy**, **todo el curso** o **todos**.
2. Elegir el equipaje en **escoger un equipaje** (aparece como «casa (fechas)»).
3. En **documentos en** (nombre de la casa) se listan **Nombre**, **Identificador** y **Lugar** de la documentación que está en la casa.
4. En **maletas para** (nombre de la casa) aparecen las valijas del equipaje; **nueva valija** añade otra.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes_doc_casa.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes_doc_casa.md`

## Imprimir un equipaje

### Para Que Sirve

Preparar e imprimir el equipaje de una casa: el inventario de lo que hay en la casa más el material que lleva la delegación, con espacio para observaciones y firma.

### Donde Entrar

- Imprimir equipaje (frontend/inventario/controller/equipajes_imprimir.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Imprimir un equipaje

1. Abrir **imprimir equipajes** y elegir el filtro **desde hoy**, **todo el curso** o **todos**.
2. Elegir el equipaje en **escoger un equipaje**.
3. Se muestra la hoja «(casa): Inventario de publicaciones internas» con las actividades del equipaje y la cabecera.
4. Revisar el apartado **A) Documentación que pertenece a la casa y se queda siempre allí** (con los ejemplares y numeración de cada documento).
5. Si el equipaje tiene valijas, revisar el apartado **B) Documentación que envía la Delegación**, con cada valija y sus documentos.
6. Comprobar el pie y el bloque final de **Observacions** y **data i firma**.
7. Imprimir desde el navegador. Los lápices de **modificar texto** no salen en el papel.

#### Ver las valijas de un equipaje

1. Abrir **hacer equipajes** y elegir el equipaje.
2. En **maletas para** (nombre de la casa) se listan las valijas con sus documentos.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes_egm.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes_egm.md`

## Eliminar una valija

### Para Que Sirve

Borrar una valija entera del equipaje.

### Donde Entrar

- Gestionar equipajes (frontend/inventario/controller/equipajes_ver.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Eliminar una valija

1. Abrir **hacer equipajes** y elegir el equipaje.
2. En la valija, pulsar **eliminar valija**.
3. Confirmar la pregunta «¿Está seguro de borrar este grupo?».
4. La valija desaparece del equipaje y los documentos que llevaba dejan de estar en ella (siguen existiendo).

### Errores O Avisos Frecuentes

- `hay un error, no se ha eliminado`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes_eliminar_grupo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes_eliminar_grupo.md`

## Consultar las actividades de un equipaje

### Para Que Sirve

Mostrar, en el equipaje impreso, las actividades que lo forman.

### Donde Entrar

- Imprimir equipaje (frontend/inventario/controller/equipajes_imprimir.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Ver las actividades del equipaje impreso

1. Abrir **imprimir equipajes** y elegir el equipaje.
2. Bajo el título «Inventario de publicaciones internas» aparecen las actividades del equipaje con sus fechas.
3. No hay nada que rellenar: se genera solo.

### Errores O Avisos Frecuentes

- `OJO! No se encuentra la actividad con id: %s`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes_lista_activ_equipaje.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes_lista_activ_equipaje.md`

## Elegir las actividades del equipaje

### Para Que Sirve

Ver las actividades de una casa en el periodo elegido, para marcar las que irán en el mismo equipaje.

### Donde Entrar

- Actividades por periodo (frontend/inventario/controller/equipajes_lista_activ_periodo.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Marcar las actividades del equipaje

1. En **nuevo equipaje**, elegir periodo y casa.
2. Leer la lista: **empieza**, **termina**, **actividad** y **observaciones**.
3. Marcar las actividades que viajan juntas.
4. Pulsar **seleccionar** para ponerle nombre al equipaje.

### Errores O Avisos Frecuentes

- `debe seleccionar un lugar`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes_lista_activ_periodo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes_lista_activ_periodo.md`

## Nombrar el equipaje

### Para Que Sirve

Ponerle nombre al equipaje formado con las actividades marcadas.

### Donde Entrar

- Fragmento selección actividades (frontend/inventario/controller/equipajes_form_nuevo.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Nombrar el equipaje

1. Marcar las actividades y pulsar **seleccionar**.
2. En la ventana aparece el **nombre del equipaje** ya propuesto como «casa (fechas)»; se puede cambiar.
3. Pulsar **guardar** (o **cancel** para cerrar sin crear nada).

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes_lista_activ_sel.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes_lista_activ_sel.md`

## Consultar los movimientos de un equipaje

### Para Que Sirve

Ver qué documentos se han añadido o quitado de las valijas de un equipaje.

### Donde Entrar

- Movimientos maletas — resultado (frontend/inventario/controller/equipajes_movimientos.php)
- **Legacy:** scdl > Inventario > equipajes > movimientos maletas
- **Pills2:** —

### Tareas Habituales

#### Consultar los movimientos de un equipaje

1. Abrir **movimientos maletas**.
2. Marcar el equipaje y pulsar **seleccionar**.
3. Para cada valija se listan los documentos **añadir** y **quitar**.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes_movimientos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes_movimientos.md`

## Crear un equipaje nuevo

### Para Que Sirve

Crear un equipaje: la documentación que se lleva a una casa para las actividades de unas fechas.

### Donde Entrar

- Nuevo equipaje (frontend/inventario/controller/equipajes_nuevo.php)
- **Legacy:** scdl > Inventario > equipajes > nuevo equipaje
- **Pills2:** —

### Tareas Habituales

#### Crear un equipaje nuevo

1. Abrir **nuevo equipaje**.
2. En **periodo de selección de actividades** elegir el periodo (**todo el año**, un trimestre, **curso ca**, **curso crt** u **otro**), el año, o las fechas **entre … y …**.
3. Pulsar **ver las casas donde se realizan las actividades**.
4. Elegir la casa en el desplegable que aparece; se muestran las actividades de esa casa en el periodo.
5. Marcar las actividades que van en el mismo equipaje y pulsar **seleccionar**.
6. El **nombre del equipaje** viene propuesto como «casa (fechas)»; se puede cambiar. Pulsar **guardar**.
7. La pantalla pasa a **hacer equipajes** con el equipaje ya creado.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes_nuevo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes_nuevo.md`

## Cambiar los textos del listado del equipaje

### Para Que Sirve

Guardar el texto de cabecera, cabecera B), pie o el texto para las maletas de un equipaje.

### Donde Entrar

- Gestionar equipajes (frontend/inventario/controller/equipajes_ver.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Cambiar los textos del listado del equipaje

1. En **imprimir equipajes**, pulsar el lápiz (**modificar texto**) (o **introducir texto** si aún está vacío).
2. Escribir el texto de **cabecera**, **cabecera B)**, **pie** o **texto a añadir en las maletas**.
3. Pulsar **guardar** (o **cancel**).

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes_texto_listado.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes_texto_listado.md`

## Crear una valija con documentos

### Para Que Sirve

Crear una valija o cambiar los documentos que lleva.

### Donde Entrar

- Gestionar equipajes (frontend/inventario/controller/equipajes_ver.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Crear una valija

1. Abrir **hacer equipajes** y elegir el equipaje.
2. En **maletas para** (casa), pulsar **nueva valija**: aparece una fila «valija1» (el número va subiendo) con una lista de maletas.
3. Elegir la maleta (por ejemplo «Maleta» o «Maleta Rialb 1 (color groc)»): se muestran los documentos que lleva.
4. La valija queda creada en el equipaje; aparecerá en la hoja impresa como «valija1: Maleta».

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes_update_grupo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes_update_grupo.md`

## Qué hace al imprimir

### Para Que Sirve

Dar formato de impresión (márgenes, cabecera y pie de página) a las hojas de inventario.

### Donde Entrar

- Imprimir inventario centros (frontend/inventario/controller/doc_imprimir_ctr.php)
- Imprimir inventario DLB (frontend/inventario/controller/doc_imprimir_dlb.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Qué hace al imprimir

1. Generar un inventario con **Imprimir ctr**, **Imprimir** o **Imprimir dl**.
2. El formato se aplica solo; no hay nada que configurar.
3. Si la hoja sale mal maquetada, avisar a mantenimiento.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.inventario_css_inline.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/inventario_css_inline.md`

## Imprimir el inventario de centros

### Para Que Sirve

Obtener el inventario impreso de los documentos de uno o varios centros, listo para firmar.

### Donde Entrar

- Imprimir inventario centros (frontend/inventario/controller/doc_imprimir_ctr.php)
- **Legacy:** scdl > Inventario > inventarios > de centros o dlb
- **Pills2:** —

### Tareas Habituales

#### Imprimir el inventario de centros

1. Marcar los centros en **inventario de centros**.
2. Pulsar **Imprimir ctr** (o **Imprimir dl** para añadir la columna **observaciones dl**).
3. Se muestra una hoja por centro titulada **Inventario de documentos de** (nombre del centro), con **documento** y **observaciones**.
4. Imprimir desde el navegador. Cada hoja termina con la nota «(*) Se guarda bajo llave», el total de páginas y el espacio **Conforme (firma)**.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.inventario_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/inventario_ctr.md`

## Imprimir el inventario de la dlb/casa

### Para Que Sirve

Obtener el inventario impreso de los documentos de uno o varios lugares de la dlb/casa, listo para firmar.

### Donde Entrar

- Imprimir inventario DLB (frontend/inventario/controller/doc_imprimir_dlb.php)
- **Legacy:** scdl > Inventario > inventarios > de centros o dlb
- **Pills2:** —

### Tareas Habituales

#### Imprimir el inventario de la dlb/casa

1. Marcar los lugares en **inventario de dlb/casa**.
2. Pulsar **Imprimir** (o **Imprimir dl** para añadir la columna **observaciones dl**).
3. Se muestra una hoja por lugar titulada **Inventario de documentos de** (centro y lugar), con **documento**, **id** y **observaciones**.
4. El botón **seleccionar** y la primera columna (sin cabecera) de la hoja sirven solo para imprimir y marcar a mano en papel.
5. Imprimir desde el navegador. Cada hoja termina con «(*) Se guarda bajo llave», el total de páginas y el espacio **Conforme (firma)**.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.inventario_dlb.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/inventario_dlb.md`

## Elegir inventario de centros o de dlb/casa

### Para Que Sirve

Decidir si se va a trabajar con el inventario de **centros** o con el de la **dlb/casa** (la dlb y las casas pueden tener varios lugares, y se inventaría lo que hay en cada lugar), y pasar a la pantalla correspondiente (asignar, modificar o imprimir documentos).

### Donde Entrar

- Elegir inventario centros o DLB (frontend/inventario/controller/inventario_que.php)
- **Legacy:** scdl > Inventario > inventarios > de centros o dlb
- **Pills2:** sin entrada en el índice de menús (falta en el layout nuevo; pendiente de añadir).

### Tareas Habituales

#### Trabajar con el inventario de centros

1. Abrir **de centros o dlb** desde el menú.
2. Pulsar **inventario de centros**.
3. En la pantalla que se abre, elegir el tipo de documento y los centros, y usar los enlaces para asignar, modificar o imprimir.

#### Trabajar con el inventario de la dlb/casa

1. Abrir **de centros o dlb** desde el menú.
2. Pulsar **inventario de dlb/casa**.
3. En la pantalla que se abre, elegir el tipo de documento y usar los enlaces para asignar, modificar o imprimir.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `inventario.inventario_que.elegir.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/inventario_que.md`

## Consultar las casas del periodo

### Para Que Sirve

Ver en qué casas se realizan actividades en el periodo elegido, para escoger la casa del equipaje.

### Donde Entrar

- Casas posibles (frontend/inventario/controller/equipajes_casas_posibles.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Elegir la casa del equipaje

1. En **nuevo equipaje**, elegir el periodo.
2. Pulsar **ver las casas donde se realizan las actividades**.
3. Elegir la casa en el desplegable: se carga la lista de sus actividades.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_casas_posibles_periodo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_casas_posibles_periodo.md`

## Agrupar un listado por colección

### Para Que Sirve

Cargar las colecciones de documentos para agrupar o filtrar un listado.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Agrupar un listado por colección

1. En un listado de documentos con agrupación, abrir el desplegable de colección.
2. Las colecciones disponibles se cargan solas.
3. Elegir la colección para ver solo sus documentos.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_colecciones.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_colecciones.md`

## Elegir el centro de origen y de destino

### Para Que Sirve

Cargar la lista de centros disponibles como origen y destino en el traslado de documentos.

### Donde Entrar

- Traslado de documentos — filtro (frontend/inventario/controller/traslado_doc_que.php)
- **Legacy:** scdl > Inventario > inventarios > traslado de doc
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Elegir el centro de origen y de destino

1. Abrir **traslado de doc**.
2. Elegir el centro de origen en su desplegable.
3. Elegir el centro de destino en el segundo desplegable.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_de_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_de_ctr.md`

## Elegir centros para asignar un documento

### Para Que Sirve

Elegir los centros a los que se asignará un documento (o de los que se va a imprimir el inventario).

### Donde Entrar

- Inventario por centros (frontend/inventario/controller/doc_de_ctr.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Elegir centros para asignar un documento

1. En **asignar documento**, elegir el tipo de documento y pulsar **Asignar a centros (sin especificar lugar)**.
2. Marcar los centros en la lista (**marcar todos**, **desmarcar todos** y **marcar/desmarcar todos** ayudan con listas largas).
3. Pulsar **Asignar** para pasar a indicar número y fechas.

#### Elegir centros para imprimir su inventario

1. Abrir **de centros o dlb** y pulsar **inventario de centros**.
2. Marcar los centros que interesan.
3. Pulsar **Imprimir ctr** (inventario para el centro) o **Imprimir dl** (inventario con las observaciones de la delegación).

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_de_ctr_con_docs.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_de_ctr_con_docs.md`

## Consultar los centros que tienen el documento

### Para Que Sirve

Ver qué centros tienen ya asignado un tipo de documento.

### Donde Entrar

- Documentos ya asignados (frontend/inventario/controller/doc_asignado.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Consultar los centros que tienen el documento

1. En **asignar documento**, elegir el tipo de documento.
2. Pulsar **lista de asignados**.
3. Leer el listado **Centros que tienen este documento**.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_docs_asignados_por_tipo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_docs_asignados_por_tipo.md`

## Rellenar la asignación a centros

### Para Que Sirve

Indicar, para cada centro elegido, cuántos ejemplares (o qué número de registro) recibe de un documento.

### Donde Entrar

- Formulario asignar a centros (frontend/inventario/controller/doc_asignar_ctr.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Rellenar la asignación a centros

1. Revisar el nombre del documento que aparece como título.
2. Escribir en cada centro el **número de registro** (documentos numerados) o el **número de ejemplares**.
3. Indicar las fechas **recibido** y **asignado** (formato dd/mm/aa) si procede.
4. Pulsar **guardar**.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_docs_asignar_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_docs_asignar_ctr.md`

## Rellenar la asignación a la dlb/casa

### Para Que Sirve

Indicar, para cada lugar de la dlb/casa elegido, cuántos ejemplares (o qué número de registro) recibe de un documento.

### Donde Entrar

- Formulario asignar DLB (frontend/inventario/controller/doc_asignar_dlb.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Rellenar la asignación a la dlb/casa

1. Revisar el nombre del documento que aparece como título.
2. Escribir en cada **delegación** el **número de registro** (documentos numerados) o el **número de ejemplares**.
3. Indicar las fechas **recibido** y **asignado** si procede.
4. Pulsar **guardar**.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_docs_asignar_dlb.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_docs_asignar_dlb.md`

## Consultar los documentos con observaciones

### Para Que Sirve

Consultar los documentos que tienen alguna observación anotada.

### Donde Entrar

- Documentos con observaciones (frontend/inventario/controller/docs_con_observaciones.php)
- **Legacy:** scdl > Inventario > inventarios > lista docs con observ.
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Consultar los documentos con observaciones

1. Abrir **lista docs con observ.**.
2. Leer el listado: **centro - lugar**, **documento**, **número**, **observaciones** y **observaciones ctr**.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_docs_con_observaciones.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_docs_con_observaciones.md`

## Buscar los documentos que se van a trasladar

### Para Que Sirve

Ver los documentos de un centro (y lugar) para marcar cuáles se trasladan.

### Donde Entrar

- Traslado — lista y guardar (frontend/inventario/controller/traslado_doc_lista.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Buscar los documentos que se van a trasladar

1. En **traslado de doc**, elegir centro y lugar de origen.
2. Pulsar **buscar**.
3. Aparece **lista de documentos del centro** con **documento** y **observaciones**.
4. Marcar los documentos a trasladar (**marcar todos** / **desmarcar todos** ayudan).

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_docs_de_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_docs_de_ctr.md`

## Elegir lugares para asignar un documento

### Para Que Sirve

Elegir los centros-lugar de la dlb/casa a los que se asignará un documento (o de los que se va a imprimir el inventario).

### Donde Entrar

- Inventario DLB/casa (frontend/inventario/controller/doc_de_dlb.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Elegir lugares para asignar un documento

1. En **asignar documento**, elegir el tipo de documento y pulsar **Asignar a dlb/casa (especificar lugar)**.
2. Marcar los **centro - lugar** de la lista.
3. Pulsar **Asignar** para pasar a indicar número y fechas.

#### Elegir lugares para imprimir su inventario

1. Abrir **de centros o dlb** y pulsar **inventario de dlb/casa**.
2. Marcar los lugares que interesan.
3. Pulsar **Imprimir** (o **Imprimir dl** para incluir las observaciones de la delegación).

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_docs_de_dlb.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_docs_de_dlb.md`

## Ver los documentos de una valija

### Para Que Sirve

Ver los documentos que lleva una valija, para quitar alguno.

### Donde Entrar

- Quitar doc de maleta (frontend/inventario/controller/equipajes_form_del.php)
- Lista docs EGM/lugar (frontend/inventario/controller/equipajes_lista_docs.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Ver los documentos de una valija

1. En **hacer equipajes**, pulsar **quitar documento** en una valija.
2. Aparece la lista de documentos de esa valija (sigla e identificador).
3. Marcar los que se quitan.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_docs_de_egm.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_docs_de_egm.md`

## Ver los documentos de la casa

### Para Que Sirve

Ver los documentos que tiene la casa (el lugar) para repartirlos en valijas.

### Donde Entrar

- Lista docs EGM/lugar (frontend/inventario/controller/equipajes_lista_docs.php)
- Ver docs de lugar (frontend/inventario/controller/equipajes_ver_docs.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Ver los documentos de la casa

1. En **hacer equipajes**, elegir el equipaje y abrir una valija.
2. Se muestra la lista de documentos de la casa con **sigla**, **identificador** y **num reg**.
3. Marcar los que van en la valija y pulsar **crear valija**.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_docs_de_lugar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_docs_de_lugar.md`

## Consultar los documentos pendientes

### Para Que Sirve

Consultar los documentos que están pendientes de localizar (en búsqueda).

### Donde Entrar

- Documentos pendientes (frontend/inventario/controller/docs_en_busqueda.php)
- **Legacy:** scdl > Inventario > inventarios > lista docs pendientes
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Consultar los documentos pendientes

1. Abrir **lista docs pendientes**.
2. Leer el listado: **centro - lugar**, **documento** y **núm. reg.**.
3. Usar la lista para saber qué hay que localizar en cada centro.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_docs_en_busqueda.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_docs_en_busqueda.md`

## Elegir un documento libre

### Para Que Sirve

Ver los documentos todavía sin meter en ninguna valija del equipaje, para añadirlos.

### Donde Entrar

- Docs libres (frontend/inventario/controller/equipajes_docs_libres.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Elegir un documento libre

1. En **hacer equipajes**, abrir una valija y pulsar **añadir documento**.
2. Elegir el **tipo de documento** entre los libres.
3. Pulsar **añadir** (o **cancel**).

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_docs_libres.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_docs_libres.md`

## Consultar los centros que no tienen el documento

### Para Que Sirve

Ver qué centros todavía no tienen asignado un tipo de documento.

### Donde Entrar

- Centros sin documento (frontend/inventario/controller/doc_no_asignado.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Consultar los centros que no tienen el documento

1. En **asignar documento**, elegir el tipo de documento.
2. Pulsar **lista de NO asignados**.
3. Leer el listado **Centros que NO tienen este documento**; sirve de punto de partida para asignarlo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_docs_no_asignados_por_tipo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_docs_no_asignados_por_tipo.md`

## Consultar los documentos perdidos

### Para Que Sirve

Consultar los documentos dados por perdidos.

### Donde Entrar

- Documentos perdidos (frontend/inventario/controller/docs_perdidos.php)
- **Legacy:** scdl > Inventario > inventarios > lista docs perdidos
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Consultar los documentos perdidos

1. Abrir **lista docs perdidos**.
2. Leer el listado: **centro - lugar**, **documento**, **número** y **fecha perdido**.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_docs_perdidos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_docs_perdidos.md`

## Cambiar los equipajes que se ven

### Para Que Sirve

Cargar la lista de equipajes entre los que escoger, según el filtro de fechas.

### Donde Entrar

- Desplegable equipajes (frontend/inventario/controller/equipajes_desplegable.php)
- Movimientos maletas — filtro (frontend/inventario/controller/equipajes_movimientos_que.php)
- Gestionar equipajes (frontend/inventario/controller/equipajes_ver.php)
- **Legacy:** scdl > Inventario > equipajes > movimientos maletas
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Cambiar los equipajes que se ven

1. Abrir **hacer equipajes** (o imprimir / eliminar).
2. Elegir el filtro: **desde hoy**, **todo el curso** o **todos**.
3. El desplegable **escoger un equipaje** se actualiza con los equipajes de ese periodo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_equipajes_desde_fecha.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_equipajes_desde_fecha.md`

## Crear una valija nueva

### Para Que Sirve

Calcular el nombre de la siguiente valija (maleta) que se puede crear en un equipaje.

### Donde Entrar

- Elegir maleta/grupo (frontend/inventario/controller/equipajes_posibles_maletas.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Crear una valija nueva

1. En **hacer equipajes**, elegir el equipaje.
2. Pulsar **nueva valija**.
3. El sistema propone el siguiente número («valija» + número) para la nueva maleta.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_equipajes_posibles_maletas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_equipajes_posibles_maletas.md`

## Elegir el lugar dentro del centro

### Para Que Sirve

Cargar los lugares (estancias) de un centro cuando se elige como origen o destino de un traslado.

### Donde Entrar

- Traslado de documentos — filtro (frontend/inventario/controller/traslado_doc_que.php)
- **Legacy:** scdl > Inventario > inventarios > traslado de doc
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Elegir el lugar dentro del centro

1. En **traslado de doc**, elegir un centro (origen o destino).
2. Aparece el desplegable **seleccione un lugar** con los lugares de ese centro.
3. Si falla la carga se muestra «Error al cargar lugares de origen/destino»; volver a elegir el centro.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_lugares_de_ubi.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_lugares_de_ubi.md`

## Elegir el tipo de documento

### Para Que Sirve

Elegir el tipo de documento con el que se va a trabajar antes de asignarlo o de consultar a quién se ha asignado.

### Donde Entrar

- Asignar documentos — selector (frontend/inventario/controller/docs_asignar_que.php)
- **Legacy:** scdl > Inventario > documentos > asignar documento
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Elegir el tipo de documento

1. Abrir **asignar documento** desde el menú.
2. Elegir el documento en el desplegable **seleccione un tipo de documento**.
3. Pulsar **lista de asignados** para ver los centros que ya lo tienen, **lista de NO asignados** para ver los que no, o uno de los dos botones de asignación (**Asignar a centros (sin especificar lugar)** / **Asignar a dlb/casa (especificar lugar)**).

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_tipo_doc.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_tipo_doc.md`

## Consultar y mantener las tablas del inventario

### Para Que Sirve

Buscar, consultar y corregir los datos de base del inventario: las colecciones, los tipos de documento, qué documentos tiene cada centro, los documentos por sigla, los centros con inventario y los lugares dentro de cada centro.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** scdl > Inventario > colecciones
- **Legacy:** scdl > Inventario > documentos > tipos de documentos / por centros / por sigla
- **Legacy:** scdl > Inventario > centros
- **Legacy:** scdl > Inventario > lugares
- **Pills2:** sin entrada en el índice de menús.

### Tareas Habituales

#### Buscar en una tabla del inventario

1. Abrir la entrada de menú que interesa (por ejemplo **colecciones**).
2. Rellenar el formulario de búsqueda: en **colecciones** se busca en «nombre colección», en **tipos de documentos** en «detalle», en **por sigla** se elige el **tipo de documento**, en **por centros** se elige el **centro** (con la opción **realizar una búsqueda exacta** no/sí). En **centros** y **lugares** basta con pulsar el botón de buscar.
3. Pulsar **buscar** (**Buscar** en «por centros» y «por sigla»).
4. Se muestra el listado de la tabla; desde él se abre cada fila para consultarla o modificarla.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `inventario.tablas_inventario.mantener.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/tablas_inventario.md`

## Abrir el texto para modificarlo

### Para Que Sirve

Cargar el texto que se añade a las maletas (cabecera, cabecera B, pie) para poder modificarlo.

### Donde Entrar

- Editar texto listado (frontend/inventario/controller/equipajes_form_texto_listado.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

### Tareas Habituales

#### Abrir el texto para modificarlo

1. En **imprimir equipajes**, pulsar el lápiz junto a la cabecera, la cabecera B) o el pie.
2. Se abre el cuadro con el texto actual.
3. Cambiarlo y guardar.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.texto_de_egm.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/texto_de_egm.md`

## Trasladar documentos a otro centro

### Para Que Sirve

Pasar documentos de un centro (y lugar) a otro.

### Donde Entrar

- Traslado de documentos — filtro (frontend/inventario/controller/traslado_doc_que.php)
- **Legacy:** scdl > Inventario > inventarios > traslado de doc
- **Pills2:** —

### Tareas Habituales

#### Trasladar documentos a otro centro

1. Abrir **traslado de doc**.
2. Elegir el centro de origen, luego el lugar, y pulsar **Buscar**. La lista puede ser muy larga (más de mil documentos en un almacén).
3. Marcar en **lista de documentos del centro** los documentos que se mueven.
4. Elegir el centro y el lugar de destino.
5. Pulsar **Asignar**: los documentos pasan al lugar de destino y la lista de origen se actualiza.
6. Si hay un problema aparece un aviso **respuesta**.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.traslado_doc.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/traslado_doc.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/inventario/flujos/`.
