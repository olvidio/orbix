---
tipo: "manual_usuario"
modulo: "actividades"
flujos: 28
estado_revision: "revisado"
---

# Manual De Usuario - actividades

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Crear una actividad

### Para Que Sirve

Dar de alta una actividad nueva o borrar una existente.

### Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (frontend/actividades/controller/actividad_ver.php)
- **Legacy:** dre/Calendario > actividades > nueva activ (crear).
- **Pills2:** ACTIVIDADES > Buscar actividad > Nueva actividad; ATENCIÓN SACD >
- Actividades > Nueva actividad. Borrado desde listados de búsqueda (sin menú propio).

### Tareas Habituales

#### Crear una actividad

1. Pulsar **nueva actividad…** debajo de la lista (o la opción de nueva actividad del menú). Se abre «Nueva actividad» con el estado «proyecto».
2. Elegir el tipo: **sf/sv**, **asistentes**, **actividad** y **tipo de actividad** (es obligatorio concretar los dos últimos; si no, avisa «debe concretar la actividad en el tipo de actividad» o «debe concretar el tipo de actividad»).
3. Rellenar el nombre (o pulsar **generar** para que lo proponga), las fechas y los demás datos: **Estado**, **Nombre actividad** (con el enlace **generar**), **Fecha inicio**, **Hora inicio**, **Fecha fin**, **Hora fin**, **dl/r que organiza**, **Plazas totales**, **Lugar**, **Tarifa**, **Precio**, **Observaciones**, **Repetición anual por**, **Nivel de stgr**, **publicado** (sí/no) e **Idioma**.
4. Pulsar **Crear ficha**.
5. La actividad queda creada; el formulario se vacía y no muestra mensaje. Para verla, buscarla con **Buscar actividad** (estado «proyecto» o «cualquiera»).

#### Borrar una actividad

1. En la lista, marcar **una** actividad y pulsar **borrar**.
2. Confirmar «¿Está seguro que desea borrar esta actividad?».
3. La actividad desaparece al volver a buscar.

### Errores O Avisos Frecuentes

- `actividad no encontrada`
- `sesión de permisos no disponible`
- `No tiene permiso para borrar esta actividad`
- `hay un error, no se ha eliminado`
- `hay un error, no se ha guardado`
- `debe seleccionar un tipo de actividad`
- `No puede crear una actividad que organiza una dl/r que ya usa aquinate`
- `No tiene permiso para crear una actividad de este tipo`
- `debe llenar todos los campos que tengan un (*)`
- `tipo de actividad incorrecto`
- `hay un error, no se ha importado`

### Permisos

- Si la app `procesos` esta instalada: por **cada** actividad exige
- Sin `procesos`: no hay validacion de permisos en servidor.
- Si la app `procesos` esta instalada: exige `$_SESSION['oPermActividades']`
- Sin `procesos`: no hay validacion de permisos en servidor (control en UI).

### Referencias Internas

- Flujo: `actividades.actividad.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad.md`

## Cambiar el tipo de una actividad

### Para Que Sirve

Cambiar el tipo de una actividad ya creada (sf/sv, asistentes, actividad o tipo).

### Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (frontend/actividades/controller/actividad_ver.php)
- sin entrada de menú en el índice (acción desde ficha de actividad ya abierta).

### Tareas Habituales

#### Cambiar el tipo de una actividad

1. Marcar la actividad en la lista y pulsar **cambiar tipo**.
2. Se abre un formulario con los datos de la actividad ya rellenados (aunque el título dice «Nueva actividad»).
3. Elegir el nuevo tipo en **sf/sv**, **asistentes**, **actividad** y **tipo de actividad**.
4. Pulsar **Guardar ficha**.

### Errores O Avisos Frecuentes

- `debe seleccionar un tipo de actividad`
- `actividad no encontrada`
- `hay un error, no se ha guardado + detalle`

### Permisos

- **No valida permisos en servidor**; el control esta en la UI (la accion

### Referencias Internas

- Flujo: `actividades.actividad_cambiar_tipo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_cambiar_tipo.md`

## Duplicar una actividad

### Para Que Sirve

Crear una copia de una actividad con las mismas fechas, plazas y demás datos.

### Donde Entrar

- Resultados buscar actividad (frontend/actividades/controller/actividad_select.php)
- sin entrada de menú en el índice (acción desde listado de búsqueda).

### Tareas Habituales

#### Duplicar una actividad

1. Marcar la actividad en la lista y pulsar **duplicar**.
2. Confirmar el aviso «Seguro que desea duplicar esta actividad».
3. La copia se llama «dup » + el nombre original y conserva fechas y plazas; la lista se vuelve a cargar con la búsqueda por defecto, así que hay que buscar de nuevo para ver la copia.

### Errores O Avisos Frecuentes

- `no se ha seleccionado ninguna actividad`
- `actividad no encontrada`
- `no se puede duplicar actividades que no sean de la propia dl`
- `hay un error, no se ha guardado + detalle`

### Permisos

- No exige permiso para duplicar actividades de la propia dl (control en la UI:
- El permiso oficina `des` (`$_SESSION['oPerm']`) solo amplia el origen permitido

### Referencias Internas

- Flujo: `actividades.actividad_duplicar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_duplicar.md`

## Modificar una actividad

### Para Que Sirve

Modificar los datos de una actividad existente (fechas, lugar, plazas, observaciones, etc.) sin cambiar su tipo.

### Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (frontend/actividades/controller/actividad_ver.php)
- sin entrada de menú en el índice (se llega desde ficha abierta vía buscar activ o planning).

### Tareas Habituales

#### Modificar una actividad

1. Marcar la actividad en la lista y pulsar **datos**.
2. Cambiar los campos que hagan falta (fechas, horas, plazas, observaciones, publicado…).
3. Pulsar **Guardar cambios**.
4. Si se guarda bien, se vuelve a la lista de la búsqueda; no hay mensaje de confirmación.

### Errores O Avisos Frecuentes

- `sesión de permisos no disponible`
- `debe seleccionar un tipo de actividad`
- `actividad no encontrada`
- `hay un error, no se ha guardado + detalle`

### Permisos

- Exige `$_SESSION['oPermActividades']` (`PermisosActividades`); sin ella responde error.
- **No valida el permiso `modificar` en servidor**: el control esta en la UI

### Referencias Internas

- Flujo: `actividades.actividad_editar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_editar.md`

## Ver si una fase está completada

### Para Que Sirve

Comprobar si una fase del proceso de la actividad está completada, sin recargar la ficha.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- sin entrada de menú en el índice.

### Tareas Habituales

#### Ver si una fase está completada

1. Abrir la ficha de la actividad.
2. Las casillas de fases reflejan si cada fase está completada; se actualizan solas al cambiar algo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- No valida permisos.

### Referencias Internas

- Flujo: `actividades.actividad_fase_completada.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_fase_completada.md`

## Ver las fases completadas de una actividad

### Para Que Sirve

Mostrar en la ficha las fases completadas coherentes con el estado real del proceso.

### Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (frontend/actividades/controller/actividad_ver.php)
- sin entrada propia (requiere módulo procesos; acceso vía ficha actividad).

### Tareas Habituales

#### Ver las fases completadas de una actividad

1. Abrir la ficha de una actividad existente o crear una nueva.
2. Las casillas de fase aparecen marcadas según el estado real del proceso.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- No valida permisos: cualquier sesion puede consultar las fases de cualquier

### Referencias Internas

- Flujo: `actividades.actividad_fases_completadas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_fases_completadas.md`

## Importar actividades de otra dl

### Para Que Sirve

Buscar actividades de otra delegación o región, elegir una o varias e importarlas a la propia.

### Donde Entrar

- Buscar actividad (filtros) (frontend/actividades/controller/actividad_que.php)
- Resultados buscar actividad (frontend/actividades/controller/actividad_select.php)
- **Legacy:** dre/Calendario > actividades > importar activ; variantes dagd/vsm/vest (crt, cv, ca…).
- **Pills2:** ACTIVIDADES > Buscar actividad > Importar crt/cv/ca n/agd de otras r/dl; ESTUDIOS >
- Semestres de invierno > Importar.

### Tareas Habituales

#### Importar actividades de otra dl

1. Abrir la opción de importar del menú.
2. Rellenar el formulario de búsqueda y pulsar buscar para localizar la actividad de origen.
3. En la lista, marcar las actividades que se quieren importar y pulsar **importar**.
4. Revisar los mensajes: sale uno por cada actividad importada o con error.

### Errores O Avisos Frecuentes

- `hay un error, no se ha importado + detalle (por id fallido)`

### Permisos

- El caso de uso no valida permisos; el control de acceso esta en la UI

### Referencias Internas

- Flujo: `actividades.actividad_importar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_importar.md`

## Elegir el tipo y el nivel STGR

### Para Que Sirve

Al elegir el tipo de actividad, rellenar solo el nivel STGR habitual.

### Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (frontend/actividades/controller/actividad_ver.php)
- sin entrada propia (AJAX en ficha nueva/editar).

### Tareas Habituales

#### Elegir el tipo y el nivel STGR

1. En la ficha, elegir el tipo de actividad.
2. El desplegable de nivel STGR se pre-rellena con el nivel habitual para ese tipo; se puede cambiar.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `actividades.actividad_nivel_stgr_default.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_nivel_stgr_default.md`

## Generar las actividades de un nuevo curso

### Para Que Sirve

- Confirmar los años de origen y destino y lanzar la generación de las actividades del nuevo curso.
- Puede tardar varios minutos.

### Donde Entrar

- Generar actividades del nuevo curso (frontend/actividades/controller/actividad_nuevo_curso.php)
- **Legacy:** dre > Nuevo calendario > nuevo curso.
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Generar nuevo curso.

### Tareas Habituales

#### Generar las actividades de un nuevo curso

1. Leer los avisos sobre lo que se borra y lo que se crea.
2. Elegir el **año de destino** y el **año de origen**.
3. Enviar el formulario y esperar a que termine.
4. Revisar el resumen o, si se ha pedido, el listado de actividades creadas.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el caso de uso; opera siempre sobre la delegación actual. La

### Referencias Internas

- Flujo: `actividades.actividad_nuevo_curso_ejecutar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_nuevo_curso_ejecutar.md`

## Crear una actividad con o sin permiso

### Para Que Sirve

Al crear una actividad nueva, permitir o bloquear el formulario según los permisos de proceso del usuario.

### Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (frontend/actividades/controller/actividad_ver.php)
- **Legacy / Pills2:** entradas *nueva activ* (ver `actividad_ver`).

### Tareas Habituales

#### Crear una actividad con o sin permiso

1. Abrir una actividad nueva.
2. Si el usuario tiene permiso de crear para ese tipo, el formulario está activo.
3. Si no, el formulario se muestra bloqueado y hay que pedir el permiso.

### Errores O Avisos Frecuentes

- `Sesión sin permisos de actividades`

### Permisos

- Requiere sesion con `$_SESSION['oPermActividades']` (`PermisosActividades`);

### Referencias Internas

- Flujo: `actividades.actividad_permiso_crear.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_permiso_crear.md`

## Publicar actividades

### Para Que Sirve

Buscar actividades que aún no están publicadas y publicarlas de golpe.

### Donde Entrar

- Buscar actividad (filtros) (frontend/actividades/controller/actividad_que.php)
- Resultados buscar actividad (frontend/actividades/controller/actividad_select.php)
- **Legacy:** dre/Calendario > actividades > publicar activ.
- **Pills2:** dre/Calendario > actividades > publicar activ; ATENCIÓN SACD > Actividades >
- publicar activ.

### Tareas Habituales

#### Publicar actividades

1. Abrir **publicar activ** del menú.
2. Buscar las actividades de la propia dl que aún no están publicadas.
3. En la lista, marcar las filas y pulsar **publicar**.
4. Comprobar en el listado que ya aparecen como publicadas.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado + detalle (por id fallido)`

### Permisos

- El caso de uso no valida permisos; el control de acceso esta en la UI

### Referencias Internas

- Flujo: `actividades.actividad_publicar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_publicar.md`

## Elegir el tipo de actividad al buscar

### Para Que Sirve

Elegir el tipo de actividad (sf/sv, asistentes, actividad y tipo) en la pantalla de búsqueda; los cuatro desplegables van encadenados.

### Donde Entrar

- Buscar actividad (filtros) (frontend/actividades/controller/actividad_que.php)
- Herencia de `actividad_que` (buscar/importar/publicar activ y entradas por colectivo).

### Tareas Habituales

#### Elegir el tipo de actividad al buscar

1. Abrir **Buscar actividad**. Según la entrada de menú, algunos desplegables ya vienen rellenados.
2. En **sf/sv** elegir sv, sf o reservada.
3. En **asistentes** elegir n, agd, s, sg, sssc o sr.
4. En **actividad** elegir crt, ca, cv, cve o cv-crt.
5. En **tipo de actividad** elegir el tipo concreto (la lista depende de lo elegido antes; «(sin especificar)» no filtra por tipo).
6. Marcar el **estado** (proyecto, actual, terminada, borrable o cualquiera) y, si hace falta, las fases **marcadas** / **sin marcar**; no se puede elegir la misma fase en las dos.
7. Escribir parte del nombre en **el nombre contiene** si se conoce.
8. En **periodo** elegir todo el año, un trimestre, un curso o un año concreto; con **otro** hay que indicar fecha de inicio y de fin («debe introducir la fecha inicio/fin del periodo»).
9. Pulsar **buscar**; se abre la lista de actividades que cumplen todo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- No exige permiso para llamar; los permisos de oficina de la sesion

### Referencias Internas

- Flujo: `actividades.actividad_que.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_que.md`

## Usar los filtros adicionales

### Para Que Sirve

Ver los filtros adicionales de la búsqueda de actividades: lugar, organiza y publicada.

### Donde Entrar

- Buscar actividad (filtros) (frontend/actividades/controller/actividad_que.php)
- Herencia de `actividad_que` (buscar activ e importar/publicar).

### Tareas Habituales

#### Usar los filtros adicionales

1. Abrir **Buscar actividad**; los filtros extra se cargan solos debajo del formulario (no aparecen para usuarios de centro).
2. Elegir el filtro de **lugar** (según país o dl, o lugar concreto) y, si interesa, **organiza**.
3. En **publicada** elegir **si**, **no** o **todas**.
4. Pulsar **buscar**.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- El bloque solo se devuelve si el rol del usuario **no** es un rol PAU de

### Referencias Internas

- Flujo: `actividades.actividad_que_filtros.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_que_filtros.md`

## Ver y trabajar con la lista de actividades encontradas

### Para Que Sirve

Ver las actividades que cumplen la búsqueda y trabajar con las que se marquen: abrir su ficha, duplicarlas, borrarlas, cambiar su tipo o ir a sus asistentes, cargos, plazas o proceso.

### Donde Entrar

- Resultados buscar actividad (frontend/actividades/controller/actividad_select.php)
- Destino de `actividad_que` — ver rutas de buscar actividad en pantalla `actividad_select`.

### Tareas Habituales

#### Ver y trabajar con la lista de actividades encontradas

1. Tras **buscar** aparece el título «N actividades encontradas (M sin permiso) entre fecha y fecha» y una tabla con inicio, fin, actividad, horas, sf/sv, tarifa, Sacd, dl org, centro, observaciones e idioma.
2. Si hay muchas filas, antes de la tabla aparece un aviso y hay que confirmar que se quiere continuar.
3. Marcar la casilla de la actividad (o de varias, según el botón) y pulsar una de las acciones: **datos**, **duplicar**, **borrar**, **cambiar tipo**, **cargos**, **lista cl**, **asistentes**, **otras peticiones**, **lista**, **plazas**, **proceso**, **asignaturas**, **plan estudios**, **listas de clase** o **posibles asignaturas**.
4. Pulsar el nombre de una actividad abre directamente su ficha.
5. Debajo de la tabla, **nueva actividad del mismo tipo** y **nueva actividad sv n** (el tipo que se buscó) crean una actividad nueva.
6. **realizar otra búsqueda** (arriba) vuelve al formulario de búsqueda.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control que bloquee el endpoint. Lee `$_SESSION['oPerm']`/rol PAU para decidir columnas y si se

### Referencias Internas

- Flujo: `actividades.actividad_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_select.md`

## Elegir el lugar de una actividad

### Para Que Sirve

En la ventana para elegir lugar, al escoger buscar por historial o por región, cargar las casas o ubicaciones candidatas antes de confirmar.

### Donde Entrar

- Seleccionar lugar (popup) (frontend/actividades/controller/actividad_select_ubi.php)
- sin entrada de menú en el índice (popup desde ficha/planning).

### Tareas Habituales

#### Elegir el lugar de una actividad

1. En la ventana de lugar, elegir el tipo de búsqueda (por ejemplo historial o región) y quién organiza.
2. Se carga el desplegable de casas o ubicaciones posibles.
3. Al elegir una, se rellenan los datos de lugar en la pantalla desde la que se abrió la ventana.

### Errores O Avisos Frecuentes

- `opción no definida: tipo=…`
- `falta saber quien organiza (modo freq sin dl_org)`

### Permisos

- Sin control de permisos propio. La autorización se resuelve en el frontend

### Referencias Internas

- Flujo: `actividades.actividad_select_ubi_desplegable.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_select_ubi_desplegable.md`

## Ver el estado de una actividad

### Para Que Sirve

Mostrar los nombres de estado correctos (proyecto, publicada, terminada…) según la sección y los permisos.

### Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (frontend/actividades/controller/actividad_ver.php)
- sin entrada propia (paso interno de ficha/planning).

### Tareas Habituales

#### Ver el estado de una actividad

1. Abrir la ficha de la actividad o el planning.
2. El desplegable de estado muestra los nombres que corresponden a su sección (sf/sv) y a los permisos del usuario.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `actividades.actividad_status_labels.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_status_labels.md`

## Elegir el tipo de actividad paso a paso

### Para Que Sirve

Al cambiar uno de los desplegables que definen el tipo de actividad, actualizar los siguientes sin recargar la página.

### Donde Entrar

- Seleccionar lugar (popup) (frontend/actividades/controller/actividad_select_ubi.php)
- sin entrada propia (AJAX dentro de buscar actividad, ficha o popup lugar).

### Tareas Habituales

#### Elegir el tipo de actividad paso a paso

1. Elegir el primer valor (por ejemplo los asistentes: n, agd, s…).
2. Los desplegables siguientes (actividad, tipo) se rellenan solos con las opciones válidas.
3. Seguir eligiendo hasta completar el tipo.

### Errores O Avisos Frecuentes

- `opción no definida: salida=<valor>`

### Permisos

- No exige permiso para llamar; en `salida=asistentes` los permisos de oficina

### Referencias Internas

- Flujo: `actividades.actividad_tipo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_tipo.md`

## Abrir la ficha de una actividad

### Para Que Sirve

Abrir la ficha de una actividad (o el formulario de una nueva) con todos sus datos.

### Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (frontend/actividades/controller/actividad_ver.php)
- Misma que `actividad_ver` / planning (sin menú propio para el paso AJAX).

### Tareas Habituales

#### Abrir la ficha de una actividad

1. Buscar la actividad y marcar su casilla en la lista.
2. Pulsar **datos** (o pulsar el nombre de la actividad).
3. Se abre «DATOS ACTIVIDAD» con el título de la actividad, el enlace **(dossiers)** y el tipo (sf/sv, asistentes, actividad y tipo de actividad, solo lectura).
4. Los campos son: **Estado**, **Nombre actividad** (con el enlace **generar**), **Fecha inicio**, **Hora inicio**, **Fecha fin**, **Hora fin**, **dl/r que organiza**, **Plazas totales**, **Lugar**, **Tarifa**, **Precio**, **Observaciones**, **Repetición anual por**, **Nivel de stgr**, **publicado** (sí/no) e **Idioma**.
5. Según los permisos, la ficha se puede modificar o es solo de consulta.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- No valida permisos: devuelve los datos de cualquier `id_activ`. El control de

### Referencias Internas

- Flujo: `actividades.actividad_ver.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_ver.md`

## Sacar un listado del calendario

### Para Que Sirve

Sacar listados del calendario de actividades por oficina o por casas, en un periodo.

### Donde Entrar

- Listados calendario (casas/oficinas) (frontend/actividades/controller/calendario_listas.php)
- **Legacy:** adl/dre/Calendario > Nuevo calendario > listados (por oficinas, casas comunes, etc.).
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Activ. nuevo calendario por oficina /
- casas comunes / oficina propia / casas sv (según `que`).

### Tareas Habituales

#### Sacar un listado del calendario

1. Entrar en **Nuevo calendario > listados** y elegir el tipo de listado: por oficinas, de la oficina propia, casas comunes, todas las casas (sf o sv).
2. Elegir el periodo.
3. Se muestra la tabla de actividades; pulsar una para abrir su ficha.

### Errores O Avisos Frecuentes

- `opción no definida en switch… ( que inválido; aparece dentro del HTML)`

### Permisos

- Sin control que bloquee el endpoint. En modo `o_actual` filtra los grupos de oficina con

### Referencias Internas

- Flujo: `actividades.calendario_listas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/calendario_listas.md`

## Ver el listado de actividades

### Para Que Sirve

Ver la tabla de actividades después de enviar los filtros del formulario de búsqueda.

### Donde Entrar

- Filtros listados SR/SG (frontend/actividades/controller/lista_activ_que.php)
- **Legacy:** dre/Calendario > actividades > listas sg (vía `lista_activ_que`).
- **Pills2:** ACTIVIDADES > Listados > Listas asistentes sg.

### Tareas Habituales

#### Ver el listado de actividades

1. Rellenar el formulario con el tipo de actividad, el periodo y las secciones; si no se ponen fechas, se toma el curso actual.
2. Pulsar buscar.
3. Aparece la tabla de actividades que cumplen los filtros.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- El controller lee `$_SESSION['oPerm']` para calcular flags de oficina (`vcsd`, `des`, `sg`, `admin`)

### Referencias Internas

- Flujo: `actividades.lista_activ.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/lista_activ.md`

## Consultar las actividades de sg

### Para Que Sirve

Consultar las actividades de San Gabriel (crt o cv) de la r/dl o de un centro, filtrar y abrir las fichas.

### Donde Entrar

- Listado actividades San Gabriel (crt/cv) (frontend/actividades/controller/lista_actividades_sg.php)
- **Legacy:** vsg > crt/cv > de la r/dl.
- **Pills2:** sin entrada dedicada (vsg).

### Tareas Habituales

#### Consultar las actividades de sg

1. Entrar en **vsg > crt** (o **cv**) **> de la r/dl**.
2. Elegir el periodo y otros filtros y buscar.
3. Pulsar una actividad de la tabla para abrir su ficha.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- No hay control de permisos que bloquee el endpoint. La visibilidad de cada actividad depende de

### Referencias Internas

- Flujo: `actividades.lista_actividades_sg.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/lista_actividades_sg.md`

## Ver las actividades de cada centro

### Para Que Sirve

Ver, tras elegir centro y periodo, las actividades de cada centro de sg.

### Donde Entrar

- Seleccionar centro y periodo (listados por ctr) (frontend/actividades/controller/actividades_centro_que.php)
- **Legacy:** vsg > crt/cv > de cada ctr.
- **Pills2:** sin entrada dedicada (vsg).

### Tareas Habituales

#### Ver las actividades de cada centro

1. Entrar en **vsg > crt** (o **cv**) **> de cada ctr**.
2. Elegir el centro y el periodo y buscar.
3. El listado aparece en la misma pantalla; pulsar una actividad para abrirla.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el caso de uso. La autorización se resuelve en el frontend

### Referencias Internas

- Flujo: `actividades.lista_centros_activ.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/lista_centros_activ.md`

## Ver o descargar el listado de sr

### Para Que Sirve

Ver el listado de actividades de San Rafael o descargarlo como CSV.

### Donde Entrar

- Listado SR (tabla/CSV) (frontend/actividades/controller/lista_sr_csv.php)
- **Legacy:** vsr > listas actividades > listado csv (resultado).
- **Pills2:** sin entrada dedicada (vsr).

### Tareas Habituales

#### Ver o descargar el listado de sr

1. Desde el formulario de **listado csv**, elegir ver en pantalla o descargar CSV.
2. Si se descarga, abrir el fichero con una hoja de cálculo.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado la preferencia (en pref_error, no bloquea listado)`

### Permisos

- Sin control de permisos que bloquee el endpoint. Lee `$_SESSION['oPerm']` para ocultar el nombre de

### Referencias Internas

- Flujo: `actividades.lista_sr_csv.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/lista_sr_csv.md`

## Preparar el listado csv de sr

### Para Que Sirve

Elegir los criterios del listado de actividades de San Rafael, con la última preferencia guardada.

### Donde Entrar

- Filtros listado CSV San Rafael (frontend/actividades/controller/lista_sr_csv_que.php)
- **Legacy:** vsr > listas actividades > listado csv.
- **Pills2:** sin entrada dedicada (vsr).

### Tareas Habituales

#### Preparar el listado csv de sr

1. Entrar en **listado csv**.
2. El formulario aparece con los últimos valores usados.
3. Ajustar el periodo y los criterios y pulsar para ver el listado o descargar el CSV.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; solo lee la preferencia del usuario actual. La autorización se

### Referencias Internas

- Flujo: `actividades.lista_sr_csv_que.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/lista_sr_csv_que.md`

## Ver los tipos de actividad

### Para Que Sirve

Ver los tipos de actividad, crear uno nuevo, cambiar el nombre de uno existente o eliminarlo.

### Donde Entrar

- Gestión de tipos de actividad (frontend/actividades/controller/tipo_activ.php)
- **Legacy:** sistema > Configuración > gestión Tipos actividades.
- **Pills2:** ADMIN LOCAL > Gestión tipos de actividad.

### Tareas Habituales

#### Ver los tipos de actividad

1. Abrir la gestión de tipos de actividad: aparece la tabla con todos los tipos.

#### Crear o modificar un tipo de actividad

1. Pulsar **nuevo**, o seleccionar un tipo y pulsar **modificar**.
2. Rellenar el formulario y guardar.

#### Eliminar un tipo de actividad

1. Seleccionar el tipo y pulsar **eliminar**.
2. Confirmar el aviso.

### Errores O Avisos Frecuentes

- `tipo de actividad no encontrado`
- `Id incorrecto (alta)`
- `hay un error, no se ha guardado / hay un error, no se ha eliminado`
- `Aviso: IMPORTANTE: Debe añadir un proceso… (con procesos instalado)`

### Permisos

- Sin control de permisos propio. La autorización se resuelve en el frontend (`tipo_activ.php`, firma
- El caso de uso no aplica control de permisos propio. La autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividades.tipo_activ.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/tipo_activ.md`

## Crear un tipo de actividad

### Para Que Sirve

Crear un tipo de actividad nuevo.

### Donde Entrar

- Gestión de tipos de actividad (frontend/actividades/controller/tipo_activ.php)
- **Legacy:** sistema > Configuración > gestión Tipos actividades.
- **Pills2:** ADMIN LOCAL > Gestión tipos de actividad.

### Tareas Habituales

#### Crear un tipo de actividad

1. Entrar en **gestión Tipos actividades** y pulsar **nuevo**.
2. Se abre el formulario vacío con los desplegables por niveles.
3. Rellenar el nombre y los niveles y guardar.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- El formulario se construye con `perm_jefe(true)`. El control de acceso real se resuelve en el

### Referencias Internas

- Flujo: `actividades.tipo_activ_form.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/tipo_activ_form.md`

## Modificar un tipo de actividad

### Para Que Sirve

Modificar el nombre de un tipo de actividad existente.

### Donde Entrar

- Gestión de tipos de actividad (frontend/actividades/controller/tipo_activ.php)
- **Legacy:** sistema > Configuración > gestión Tipos actividades.
- **Pills2:** ADMIN LOCAL > Gestión tipos de actividad.

### Tareas Habituales

#### Modificar un tipo de actividad

1. En **gestión Tipos actividades**, pulsar modificar en el tipo.
2. Se abre el formulario con el nombre actual.
3. Cambiarlo y guardar.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el caso de uso. La autorización se resuelve en el frontend

### Referencias Internas

- Flujo: `actividades.tipo_activ_form_modificar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/tipo_activ_form_modificar.md`

## Elegir los niveles de un tipo

### Para Que Sirve

Al cambiar un nivel en el formulario de tipos, actualizar los desplegables siguientes.

### Donde Entrar

- Gestión de tipos de actividad (frontend/actividades/controller/tipo_activ.php)
- **Legacy:** sistema > Configuración > gestión Tipos actividades.
- **Pills2:** ADMIN LOCAL > Gestión tipos de actividad.

### Tareas Habituales

#### Elegir los niveles de un tipo

1. En el formulario de tipos, elegir el primer nivel.
2. Los desplegables siguientes se actualizan con las opciones que corresponden.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; se trata de metadatos de catálogo. La autorización de contexto la

### Referencias Internas

- Flujo: `actividades.tipo_activ_metadata.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/tipo_activ_metadata.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/actividades/flujos/`.
