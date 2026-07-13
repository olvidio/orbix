---
tipo: "manual_usuario"
modulo: "actividades"
flujos: 28
estado_revision: "generado"
---

# Manual De Usuario - actividades

Este manual es un borrador generado desde `docs/catalogo`. Debe revisarse para ajustar nombres de menu, permisos, validaciones y lenguaje final de usuario.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas de menu y nombres visibles pueden necesitar revision manual.

## Crear y eliminar actividad

### Para Que Sirve

- - **Crear:** rellenar la ficha en modo *nuevo* y guardar (`actividad_nuevo`).
- - **Eliminar:** seleccionar actividad(es) en un listado y confirmar borrado (`actividad_eliminar`).

### Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (frontend/actividades/controller/actividad_ver.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

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

## Cambiar tipo de actividad

### Para Que Sirve

Seleccionar un nuevo tipo en la cascada, confirmar aviso de vuelta a *proyecto* y guardar.

### Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (frontend/actividades/controller/actividad_ver.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `debe seleccionar un tipo de actividad`
- `actividad no encontrada`
- `hay un error, no se ha guardado + detalle`

### Permisos

- **No valida permisos en servidor**; el control esta en la UI (la accion

### Referencias Internas

- Flujo: `actividades.actividad_cambiar_tipo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_cambiar_tipo.md`

## Duplicar actividad

### Para Que Sirve

Seleccionar actividad origen y duplicarla (nueva ficha en proyecto).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

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

## Guardar edición de actividad

### Para Que Sirve

Modificar campos de la actividad (fechas, lugar, plazas, observaciones, etc.) y guardar sin cambiar el tipo.

### Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (frontend/actividades/controller/actividad_ver.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

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

## Consultar fase completada

### Para Que Sirve

Validar estado de una fase sin recargar toda la ficha.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- No valida permisos.

### Referencias Internas

- Flujo: `actividades.actividad_fase_completada.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_fase_completada.md`

## Prefill fases completadas

### Para Que Sirve

Ver checkboxes de fases coherentes con el estado real del proceso al editar/crear.

### Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (frontend/actividades/controller/actividad_ver.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- No valida permisos: cualquier sesion puede consultar las fases de cualquier

### Referencias Internas

- Flujo: `actividades.actividad_fases_completadas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_fases_completadas.md`

## Importar actividad de otra dl

### Para Que Sirve

Buscar actividades externas (`modo=importar`), seleccionar una o varias e importarlas.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `hay un error, no se ha importado + detalle (por id fallido)`

### Permisos

- El caso de uso no valida permisos; el control de acceso esta en la UI

### Referencias Internas

- Flujo: `actividades.actividad_importar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_importar.md`

## Nivel STGR por defecto

### Para Que Sirve

Al concretar tipo de actividad, el desplegable STGR se pre-rellena con el nivel habitual.

### Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (frontend/actividades/controller/actividad_ver.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `actividades.actividad_nivel_stgr_default.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_nivel_stgr_default.md`

## Ejecutar generación nuevo curso

### Para Que Sirve

Confirmar años en `actividad_nuevo_curso` y lanzar la generación (puede tardar varios minutos).

### Donde Entrar

- Generar actividades del nuevo curso (frontend/actividades/controller/actividad_nuevo_curso.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el caso de uso; opera siempre sobre la delegación actual. La

### Referencias Internas

- Flujo: `actividades.actividad_nuevo_curso_ejecutar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_nuevo_curso_ejecutar.md`

## Permiso crear actividad

### Para Que Sirve

Al crear ficha nueva, el sistema bloquea o permite el formulario según permisos de proceso.

### Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (frontend/actividades/controller/actividad_ver.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `Sesión sin permisos de actividades`

### Permisos

- Requiere sesion con `$_SESSION['oPermActividades']` (`PermisosActividades`);

### Referencias Internas

- Flujo: `actividades.actividad_permiso_crear.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_permiso_crear.md`

## Publicar actividades

### Para Que Sirve

Buscar actividades en modo publicar, seleccionar y ejecutar publicación masiva.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado + detalle (por id fallido)`

### Permisos

- El caso de uso no valida permisos; el control de acceso esta en la UI

### Referencias Internas

- Flujo: `actividades.actividad_publicar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_publicar.md`

## Selector tipo en buscar actividad

### Para Que Sirve

Al cargar `actividad_que` o el bloque tipo del planning, ver desplegables coherentes con permisos y parámetros (`sasistentes`, `sactividad`, `ssfsv`).

### Donde Entrar

- Buscar actividad (filtros) (frontend/actividades/controller/actividad_que.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- No exige permiso para llamar; los permisos de oficina de la sesion

### Referencias Internas

- Flujo: `actividades.actividad_que.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_que.md`

## Filtros extra buscar actividad

### Para Que Sirve

Tras abrir buscar actividad, ver filtros adicionales según rol (ocultos para usuarios `ctr`).

### Donde Entrar

- Buscar actividad (filtros) (frontend/actividades/controller/actividad_que.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- El bloque solo se devuelve si el rol del usuario **no** es un rol PAU de

### Referencias Internas

- Flujo: `actividades.actividad_que_filtros.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_que_filtros.md`

## Listar resultados buscar actividad

### Para Que Sirve

Ver listado tras buscar, con enlaces a ficha, importar, publicar o seleccionar según modo.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control que bloquee el endpoint. Lee `$_SESSION['oPerm']`/rol PAU para decidir columnas y si se

### Referencias Internas

- Flujo: `actividades.actividad_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_select.md`

## Desplegables lugar (popup)

### Para Que Sirve

Al elegir modo historial o región, cargar casas/ubis candidatas antes de confirmar.

### Donde Entrar

- Seleccionar lugar (popup) (frontend/actividades/controller/actividad_select_ubi.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `opción no definida: tipo=…`
- `falta saber quien organiza (modo freq sin dl_org)`

### Permisos

- Sin control de permisos propio. La autorización se resuelve en el frontend

### Referencias Internas

- Flujo: `actividades.actividad_select_ubi_desplegable.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_select_ubi_desplegable.md`

## Etiquetas de estado actividad

### Para Que Sirve

Ver nombres de estado correctos según sf/sv y permisos al abrir ficha o planning.

### Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (frontend/actividades/controller/actividad_ver.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `actividades.actividad_status_labels.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_status_labels.md`

## Cascada tipo actividad (AJAX)

### Para Que Sirve

Al cambiar un nivel de la cascada, actualizar los desplegables dependientes sin recargar toda la página.

### Donde Entrar

- Seleccionar lugar (popup) (frontend/actividades/controller/actividad_select_ubi.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `opción no definida: salida=<valor>`

### Permisos

- No exige permiso para llamar; en `salida=asistentes` los permisos de oficina

### Referencias Internas

- Flujo: `actividades.actividad_tipo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_tipo.md`

## Cargar datos de ficha actividad

### Para Que Sirve

Al abrir ver/editar/nuevo/planning, el sistema carga en servidor los datos necesarios para pintar la ficha sin acceder a `src/` desde el navegador.

### Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (frontend/actividades/controller/actividad_ver.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- No valida permisos: devuelve los datos de cualquier `id_activ`. El control de

### Referencias Internas

- Flujo: `actividades.actividad_ver.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/actividad_ver.md`

## Listados calendario nuevo

### Para Que Sirve

Desde menú *Nuevo calendario > listados*, elegir informe y periodo; ver tabla de actividades.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `opción no definida en switch… ( que inválido; aparece dentro del HTML)`

### Permisos

- Sin control que bloquee el endpoint. En modo `o_actual` filtra los grupos de oficina con

### Referencias Internas

- Flujo: `actividades.calendario_listas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/calendario_listas.md`

## Tabla listado actividades

### Para Que Sirve

Ver tabla de actividades tras enviar filtros desde `lista_activ_que` o `actividad_que`.

### Donde Entrar

- Filtros listados SR/SG (frontend/actividades/controller/lista_activ_que.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- El controller lee `$_SESSION['oPerm']` para calcular flags de oficina (`vcsd`, `des`, `sg`, `admin`)

### Referencias Internas

- Flujo: `actividades.lista_activ.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/lista_activ.md`

## Listado actividades SG

### Para Que Sirve

Consultar actividades SG de la r/dl o del centro, filtrar y abrir fichas desde la tabla.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- No hay control de permisos que bloquee el endpoint. La visibilidad de cada actividad depende de

### Referencias Internas

- Flujo: `actividades.lista_actividades_sg.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/lista_actividades_sg.md`

## Listado por centros

### Para Que Sirve

Tras elegir centro y periodo en *de cada ctr*, ver el listado AJAX en la misma pantalla.

### Donde Entrar

- Seleccionar centro y periodo (listados por ctr) (frontend/actividades/controller/actividades_centro_que.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el caso de uso. La autorización se resuelve en el frontend

### Referencias Internas

- Flujo: `actividades.lista_centros_activ.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/lista_centros_activ.md`

## Resultado listado CSV SR

### Para Que Sirve

Visualizar listado o descargar CSV para San Rafael.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado la preferencia (en pref_error, no bloquea listado)`

### Permisos

- Sin control de permisos que bloquee el endpoint. Lee `$_SESSION['oPerm']` para ocultar el nombre de

### Referencias Internas

- Flujo: `actividades.lista_sr_csv.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/lista_sr_csv.md`

## Bootstrap listado CSV SR

### Para Que Sirve

Ver el formulario pre-rellenado con la última preferencia guardada del usuario.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; solo lee la preferencia del usuario actual. La autorización se

### Referencias Internas

- Flujo: `actividades.lista_sr_csv_que.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/lista_sr_csv_que.md`

## tipos de actividad

### Para Que Sirve

Listar tipos, crear uno nuevo, renombrar o eliminar desde la pantalla de administración.

### Donde Entrar

- Gestión de tipos de actividad (frontend/actividades/controller/tipo_activ.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

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

## Formulario alta tipo actividad

### Para Que Sirve

Pulsar *nuevo* en gestión de tipos y ver el formulario vacío con desplegables.

### Donde Entrar

- Gestión de tipos de actividad (frontend/actividades/controller/tipo_activ.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- El formulario se construye con `perm_jefe(true)`. El control de acceso real se resuelve en el

### Referencias Internas

- Flujo: `actividades.tipo_activ_form.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/tipo_activ_form.md`

## Formulario editar tipo actividad

### Para Que Sirve

Elegir tipo en la lista y abrir formulario con nombre actual para modificar.

### Donde Entrar

- Gestión de tipos de actividad (frontend/actividades/controller/tipo_activ.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el caso de uso. La autorización se resuelve en el frontend

### Referencias Internas

- Flujo: `actividades.tipo_activ_form_modificar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/tipo_activ_form_modificar.md`

## Metadatos cascada tipo actividad

### Para Que Sirve

Al cambiar un nivel en el formulario de tipos, actualizar los siguientes desplegables.

### Donde Entrar

- Gestión de tipos de actividad (frontend/actividades/controller/tipo_activ.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; se trata de metadatos de catálogo. La autorización de contexto la

### Referencias Internas

- Flujo: `actividades.tipo_activ_metadata.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividades/flujos/tipo_activ_metadata.md`

## Revision Pendiente

- Sustituir nombres tecnicos por nombres visibles en la aplicacion.
- Completar rutas de menu.
- Confirmar permisos necesarios.
- Anadir capturas o ejemplos si se quiere publicar para usuarios finales.
