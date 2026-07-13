---
tipo: "manual_usuario"
modulo: "actividadessacd"
flujos: 13
estado_revision: "generado"
---

# Manual De Usuario - actividadessacd

Este manual es un borrador generado desde `docs/catalogo`. Debe revisarse para ajustar nombres de menu, permisos, validaciones y lenguaje final de usuario.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas de menu y nombres visibles pueden necesitar revision manual.

## Com Sacd Activ Periodo Page

### Para Que Sirve

- Al cargar la pantalla de comunicación, el sistema determina si el usuario puede editar los textos base (`perm_mod_txt`).
- Los usuarios con rol `p-sacd` no tienen permiso de edición.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Abrir la pantalla de comunicación a los sacd.
2. El sistema resuelve `perm_mod_txt` según el rol del usuario.
3. Si hay permiso, se muestra el enlace para editar textos.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- El propio payload transporta el permiso (`perm_mod_txt`), derivado del rol del usuario

### Referencias Internas

- Flujo: `actividadessacd.com_sacd_activ_periodo_page.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadessacd/flujos/com_sacd_activ_periodo_page.md`

## Comunicacion Activ Sacd

### Para Que Sirve

El usuario selecciona un periodo y pulsa **buscar**: el sistema construye, por cada sacd, la lista de actividades a comunicar (incluidas las de los "sacd de paso" cuando procede) con los textos de la carta y las cabeceras de columnas.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Seleccionar periodo en la barra de filtros (o entrar con un sacd preseleccionado).
2. Pulsar **buscar** (o auto-carga si `AUTO_CARGAR`).
3. El sistema pinta el listado por sacd con actividades, textos y leyenda.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio de acceso, pero el filtrado por actividad usa

### Referencias Internas

- Flujo: `actividadessacd.comunicacion_activ_sacd.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadessacd/flujos/comunicacion_activ_sacd.md`

## Comunicacion Activ Sacd Enviar

### Para Que Sirve

- El usuario pulsa **enviar mail**: el sistema encola los correos de comunicación (uno por sacd con copia al jefe de calendario, y otro para el ctr del sacd si tiene email).
- Requiere un periodo válido y el jefe de calendario configurado.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Tener un listado generado (flujo de búsqueda previo).
2. Pulsar **enviar mail**.
3. El sistema encola los correos y muestra el resultado.

### Errores O Avisos Frecuentes

- `falta determinar un periodo`

### Permisos

- Sin control propio de acceso en el caso de uso, pero el filtrado por actividad usa

### Referencias Internas

- Flujo: `actividadessacd.comunicacion_activ_sacd_enviar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadessacd/flujos/comunicacion_activ_sacd_enviar.md`

## Lista Actividades Sacd

### Para Que Sirve

El usuario elige un periodo y pulsa **buscar**: el sistema muestra la tabla de actividades del tipo (`na` / `sg` / `sr` / `sssc` / `sf` / variantes `sf_*` / `falta_sacd` / `solape`) en ese periodo y, por cada una, los sacd encargados actuales y los flags de permiso que deciden qué acciones se ofrecen (asignar, reordenar, borrar).

### Donde Entrar

- Activ Sacd (frontend/actividadessacd/controller/activ_sacd.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Elegir periodo (año + trimestre o rango libre) en la barra de filtros.
2. Pulsar **buscar**.
3. El sistema construye la tabla con actividades, sacd encargados y leyenda de colores.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Por fila usa `$_SESSION['oPermActividades']` (si `procesos` está instalado):

### Referencias Internas

- Flujo: `actividadessacd.lista_actividades_sacd.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadessacd/flujos/lista_actividades_sacd.md`

## Locales Desplegable

### Para Que Sirve

Al abrir el fragmento de edición de textos, el sistema carga la lista de idiomas/locales disponibles para poblar el desplegable `idioma` del formulario de comunicación.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Abrir el fragmento de edición de textos desde la pantalla de comunicación.
2. El sistema rellena el desplegable de idiomas con `a_locales`.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- El caso de uso no aplica control de permisos propio. La autorización se resuelve en el frontend

### Referencias Internas

- Flujo: `actividadessacd.locales_desplegable.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadessacd/flujos/locales_desplegable.md`

## Sacd

### Para Que Sirve

- El usuario quita un sacd ya asignado a una actividad.
- El sistema elimina el cargo (`ActividadCargo`) y, si existe, la fila de asistencia asociada (`Asistencia`).

### Donde Entrar

- Activ Sacd (frontend/actividadessacd/controller/activ_sacd.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Eliminar

1. En una actividad, pulsar un sacd ya asignado.
2. Elegir **borrar** en el menú contextual.
3. El sistema elimina cargo y asistencia (si aplica) y refresca la celda de sacd de la actividad.

### Errores O Avisos Frecuentes

- `no se sabe cual borrar`
- `hay un error, no se ha eliminado el cargo`
- `hay un error, no se ha eliminado la asistencia`

### Permisos

- Sin control propio en el caso de uso. Autorización en el frontend (`activ_sacd.php`): permiso de

### Referencias Internas

- Flujo: `actividadessacd.sacd.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadessacd/flujos/sacd.md`

## Sacd Asignar

### Para Que Sirve

- El usuario asigna un sacd candidato (elegido en el desplegable de disponibles) a una actividad.
- El sacd queda en el primer hueco libre de cargos tipo `sacd`.
- Si la actividad es de sv (`id_tipo_activ` empieza por `1`), se crea además la fila de asistencia.

### Donde Entrar

- Activ Sacd (frontend/actividadessacd/controller/activ_sacd.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. En una actividad con permiso, pulsar **nuevo** para ver los sacd candidatos.
2. Pulsar el sacd deseado (titular del centro o global según checkboxes de selección).
3. El sistema lo guarda como encargado y refresca la celda de sacd de la actividad.

### Errores O Avisos Frecuentes

- `No puede haber tantos cargos de sacd en una actividad`
- `faltan parametros id_activ / id_nom`
- `hay un error, no se ha guardado el cargo`
- `hay un error, no se ha guardado la asistencia`

### Permisos

- El caso de uso no aplica control de permisos propio. La autorización se resuelve en el frontend

### Referencias Internas

- Flujo: `actividadessacd.sacd_asignar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadessacd/flujos/sacd_asignar.md`

## Sacd Asignar Auto

### Para Que Sirve

- El usuario confirma la asignación automática: el sistema asigna el sacd titular del centro encargado a las actividades sr/sg actuales posteriores al inicio de curso des que aún no tienen sacd.
- Devuelve cuántas se han asignado y cuántas quedan sin asignar; las asignadas quedan con observaciones `auto`.

### Donde Entrar

- Asignar Sacd Auto (frontend/actividadessacd/controller/asignar_sacd_auto.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Leer el texto que describe el criterio de asignación automática.
2. Pulsar **continuar**.
3. El sistema procesa y muestra el resultado (`asignadas`, `sin_asignar`) sin recargar la página.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio en el caso de uso. La pantalla `asignar_sacd_auto.php` firma la URL con `HashFront`;

### Referencias Internas

- Flujo: `actividadessacd.sacd_asignar_auto.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadessacd/flujos/sacd_asignar_auto.md`

## Sacd Reordenar

### Para Que Sirve

El usuario sube o baja la prioridad de un sacd ya asignado intercambiando su posición con el anterior o el siguiente en el listado de cargos `sacd` de la actividad.

### Donde Entrar

- Activ Sacd (frontend/actividadessacd/controller/activ_sacd.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. En una actividad, pulsar un sacd ya asignado.
2. Elegir **más prioridad** o **menos prioridad**.
3. El sistema intercambia el orden y refresca la celda de sacd de la actividad.

### Errores O Avisos Frecuentes

- `direccion de orden incorrecta (mas / menos)`
- `faltan parametros id_activ / id_nom`
- `hay un error, no se ha guardado`

### Permisos

- Sin control propio en el caso de uso. Autorización en el frontend (`activ_sacd.php`): permiso de

### Referencias Internas

- Flujo: `actividadessacd.sacd_reordenar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadessacd/flujos/sacd_reordenar.md`

## Sacds Disponibles

### Para Que Sirve

Antes de asignar un sacd, el usuario abre el popup de candidatos: el sistema devuelve los sacd del centro encargado (titulares) y los sacd globales según el bitmask de selección (`sel`) activo en la barra de filtros.

### Donde Entrar

- Activ Sacd (frontend/actividadessacd/controller/activ_sacd.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. En una actividad con permiso, pulsar **nuevo**.
2. El sistema muestra el popup con sacd titulares del centro y globales filtrados.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- El caso de uso no aplica control de permisos propio (solo comprueba `is_app_installed('encargossacd')`).

### Referencias Internas

- Flujo: `actividadessacd.sacds_disponibles.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadessacd/flujos/sacds_disponibles.md`

## Sacds Encargados

### Para Que Sirve

Tras asignar, reordenar o borrar un sacd, el sistema refresca la celda de sacd de la actividad consultando los encargados actuales y los flags de permiso que deciden si se muestran como enlaces interactivos.

### Donde Entrar

- Activ Sacd (frontend/actividadessacd/controller/activ_sacd.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Tras una asignación, reordenación o borrado, el sistema actualiza la celda `<id_activ>_sacds`.
2. Se repintan los sacd encargados con sus enlaces de menú contextual.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- El caso de uso resuelve el permiso sacd con `$_SESSION['oPermActividades']` (si `procesos` está

### Referencias Internas

- Flujo: `actividadessacd.sacds_encargados.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadessacd/flujos/sacds_encargados.md`

## Solapes Sacd

### Para Que Sirve

Con el tipo de menú `solape`, el usuario elige un periodo y pulsa **buscar**: el sistema muestra los sacd que tienen actividades incompatibles (solapes horarios) y, para cada uno, las actividades afectadas.

### Donde Entrar

- Activ Sacd (frontend/actividadessacd/controller/activ_sacd.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Entrar desde el menú con tipo `solape`.
2. Elegir periodo y pulsar **buscar**.
3. El sistema construye la tabla de sacd con sus actividades incompatibles y la leyenda de colores.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- El caso de uso no aplica control de permisos propio (acota por la delegación del usuario,

### Referencias Internas

- Flujo: `actividadessacd.solapes_sacd.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadessacd/flujos/solapes_sacd.md`

## Texto Comunicacion

### Para Que Sirve

- El usuario edita los textos de la carta de comunicación: elige clave (comunicación general o títulos de columna) e idioma, carga el texto guardado, lo modifica y guarda.
- Guardar con el textarea vacío elimina el texto de ese `{clave, idioma}`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Elegir **clave** e **idioma** en los desplegables.
2. El sistema recarga el textarea con el texto guardado.

#### Guardar

1. Editar el texto en el textarea.
2. Pulsar **guardar** (o **cancelar** para volver sin guardar).
3. El sistema hace upsert o elimina si el texto queda vacío.

### Errores O Avisos Frecuentes

- `faltan parametros clave / idioma`
- `hay un error, no se ha eliminado el texto`
- `hay un error, no se ha guardado el texto`

### Permisos

- El caso de uso no aplica control de permisos propio. La autorización se resuelve en el frontend
- Sin control propio en el caso de uso. La pantalla `com_sacd_txt.php` restringe la edición según

### Referencias Internas

- Flujo: `actividadessacd.texto_comunicacion.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadessacd/flujos/texto_comunicacion.md`

## Revision Pendiente

- Sustituir nombres tecnicos por nombres visibles en la aplicacion.
- Completar rutas de menu.
- Confirmar permisos necesarios.
- Anadir capturas o ejemplos si se quiere publicar para usuarios finales.
