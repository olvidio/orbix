---
tipo: "manual_usuario"
modulo: "encargossacd"
flujos: 28
estado_revision: "generado"
---

# Manual De Usuario - encargossacd

Este manual es un borrador generado desde `docs/catalogo`. Debe revisarse para ajustar nombres de menu, permisos, validaciones y lenguaje final de usuario.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas de menu y nombres visibles pueden necesitar revision manual.

## Comprobaciones Ctr

### Para Que Sirve

- Gestiona EncargoComprobacionesCtr.
- Elimina encargos ligados a centros inactivos y sacd huérfanos (misma lógica que el antiguo frontend/encargossacd/controller/comprobaciones.php).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.comprobaciones_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/comprobaciones_ctr.md`

## Ctr Ficha

### Para Que Sirve

- Gestiona CtrFicha.
- Datos de la pantalla ctr_ficha: - calcula el filtro_ctr efectivo a partir del centro (cuando no viene del POST) - devuelve las opciones_seccion para el desplegable de grupo de ctrs.
- Reemplaza la lectura directa de repos y el acceso a EncargoAplicacionService que el frontend hacia en ctr_ficha.php.
- Mutacion de la ficha de atencion sacerdotal de un centro.
- Puerto de frontend/encargossacd/controller/ctr_ficha_update.php.
- Devuelve siempre ['error' => string] (vacio = exito).
- El controlador HTTP convierte ese resultado en JSON {success, mensaje} (el proxy legacy en frontend/ preserva el contrato "alert(rta_txt)" reemitiendo mensaje).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

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

## Ctr Get Ficha

### Para Que Sirve

- Gestiona CtrGetFicha.
- Lectura de la ficha de atencion sacerdotal de un centro.
- Puerto del antiguo frontend/encargossacd/controller/ctr_get_ficha.php.
- Devuelve arrays planos/estructurados para que el controlador frontend arme frontend\shared\web\Desplegable y la HTML sin instanciar nada de src\.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Permiso oficina `des`
- Permiso oficina `vcsd`

### Referencias Internas

- Flujo: `encargossacd.ctr_get_ficha.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/ctr_get_ficha.md`

## Ctr Get Select

### Para Que Sirve

- Gestiona EncargoCtrSelect.
- Payload JSON para el desplegable de centros segun filtro (y zona opcional).
- Devuelve el contrato estandar definido en refactor.md (id, name, opciones, selected, blanco, val_blanco, action) para que el frontend monte el <select> con fnjs_construir_desplegable (o el modelo frontend/encargossacd/model/DesplCentros).
- Importante: esta clase vive en capa application y por tanto **no** puede instanciar frontend\shared\web\Desplegable (ver refactor.md).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.ctr_get_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/ctr_get_select.md`

## Encargo Horario Select

### Para Que Sirve

- Gestiona EncargoHorarioSelect.
- Datos para la lista de horarios de un encargo (encargo_horario_select).
- Se devuelven ya precalculados el texto descriptivo del horario y las fechas formateadas para que el frontend solo arme frontend\shared\web\Lista.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.encargo_horario_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/encargo_horario_select.md`

## Encargo Lst Tipo Enc

### Para Que Sirve

- Gestiona EncargoLstTipoEnc.
- Payload de desplegable de tipos de encargo filtrados por prefijo de grupo (id_tipo_enc ~ ^grupo).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.encargo_lst_tipo_enc.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/encargo_lst_tipo_enc.md`

## Encargo Select

### Para Que Sirve

- Gestiona EncargoSelect.
- Datos para la lista de encargos (encargo_select).
- El frontend construye la frontend\shared\web\Lista y los enlaces; aqui devolvemos unicamente los datos planos de cada fila.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.encargo_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/encargo_select.md`

## Encargo Ver

### Para Que Sirve

- Gestiona EncargoVer.
- Alta de encargo desde el formulario de encargo_ver (antes encargo_ajax.php que=nuevo).
- Borrado desde lista encargo_select (antes encargo_ajax.php que=eliminar).
- Datos para la pantalla encargo_ver (nuevo / editar encargo).
- El frontend arma los frontend\shared\web\Desplegable a partir de los arrays devueltos.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Crear

1. Revisar manualmente los pasos de esta accion.

#### Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

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

## Encargo Ver Editar

### Para Que Sirve

- Gestiona EncargoVerEditar.
- Actualización de encargo desde encargo_ver (antes encargo_ajax.php que=editar).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Debe llenar el campo descripción`
- `No se encuentra el encargo %d`
- `hay un error, no se ha guardado`

### Referencias Internas

- Flujo: `encargossacd.encargo_ver_editar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/encargo_ver_editar.md`

## Horario Sacd Update

### Para Que Sirve

- Gestiona EncargoSacdHorario.
- Alta/edición/baja de horario de encargo sacd (encargo_sacd_horario).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `acción no válida`
- `registro no encontrado`
- `Debe llenar todos los campos que tengan un (*)`
- `hay un error, no se ha guardado`

### Referencias Internas

- Flujo: `encargossacd.horario_sacd_update.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/horario_sacd_update.md`

## Horario Sacd Ver

### Para Que Sirve

- Gestiona EncargoSacdHorarioVer.
- Datos del formulario horario sacd (ficha tareas).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.horario_sacd_ver.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/horario_sacd_ver.md`

## Horario Update

### Para Que Sirve

- Gestiona EncargoHorario.
- Alta/edición/baja de horario de encargo (tabla encargo_horario).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `acción no válida`
- `Debe llenar todos los campos que tengan un (*)`
- `registro no encontrado`
- `hay un error, no se ha guardado`

### Referencias Internas

- Flujo: `encargossacd.horario_update.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/horario_update.md`

## Horario Ver

### Para Que Sirve

- Gestiona EncargoHorarioVer.
- Datos del formulario de horario de encargo (no sacd).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.horario_ver.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/horario_ver.md`

## Listas A

### Para Que Sirve

- Gestiona ListasA.
- Genera el listado de atencion SACD "a" (cr 9/05, Anexo2, 9.4 a).
- Sustituye la logica que habia en frontend/encargossacd/controller/listas_a.php.
- Devuelve el HTML completo junto con los textos de cabecera, listos para inyectarlos en la vista listas.phtml.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.listas_a.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/listas_a.md`

## Listas B

### Para Que Sirve

- Gestiona ListasB.
- Genera el listado de atencion SACD "b" (cr 9/05, Anexo2, 9.4 b).
- Sustituye la logica de frontend/encargossacd/controller/listas_b.php.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.listas_b.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/listas_b.md`

## Listas C

### Para Que Sirve

- Gestiona ListasC.
- Genera el listado de atencion SACD "c" (cr 9/05, Anexo2, 9.4 c).
- Sustituye la logica de frontend/encargossacd/controller/listas_c.php.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Permiso oficina `vcsd`
- Permiso oficina `des`

### Referencias Internas

- Flujo: `encargossacd.listas_c.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/listas_c.md`

## Listas Cl

### Para Que Sirve

- Gestiona ListasCl.
- Listado de cl para cr, restringido a los centros de la sss+.
- Sustituye la logica de frontend/encargossacd/controller/listas_cl.php (era una plantilla con SQL crudo).
- Devuelve el HTML completo listo para volcarlo al cliente; el frontend se limita a pasar sf y a echo del resultado.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.listas_cl.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/listas_cl.md`

## Listas Com Ctr

### Para Que Sirve

- Gestiona ListasComCtr.
- Datos para la comunicacion a los centros.
- Sustituye la logica de frontend/encargossacd/controller/listas_com_ctr.php.
- El modelo de salida replica el consumido por la vista listas_com_ctr.phtml: - array_atn_sacd[nombre_ubi] con titular, suplente, colaboradores y el texto de comunicacion traducido al idioma del idioma actual.
- - origen_txt cabecera de emisor y lugar_fecha pie.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.listas_com_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/listas_com_ctr.md`

## Listas Com Sacd

### Para Que Sirve

- Gestiona ListasComSacd.
- Datos para la comunicacion a los SACD.
- Sustituye la logica de frontend/encargossacd/controller/listas_com_sacd.php.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.listas_com_sacd.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/listas_com_sacd.md`

## Listas Com Txt

### Para Que Sirve

- Gestiona ListasComTxt, ListasComTxtGet.
- Datos para la pantalla de textos de comunicacion (frontend/encargossacd/controller/listas_com_txt.php).
- Devuelve las opciones de idiomas configurados y el texto inicial correspondiente a la clave/idioma por defecto (com_sacd / es).
- Lectura del texto de comunicacion para un par (clave, idioma).
- Extraido de EncargoTextoListasComAjax (rama que=get_texto) para eliminar el dispatcher multiproposito (criterio refactor.md).
- Mutacion del texto de comunicacion para un par (clave, idioma).
- Si el texto llega vacio, se elimina la fila.
- Extraido de EncargoTextoListasComAjax (rama que=update) para eliminar el dispatcher multiproposito (criterio refactor.md).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

#### Obtener

1. Revisar manualmente los pasos de esta accion.

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.listas_com_txt.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/listas_com_txt.md`

## Listas D

### Para Que Sirve

- Gestiona ListasD.
- Genera el listado "d" de atencion SACD (cr 9/20, 10).
- Sustituye la logica de frontend/encargossacd/controller/listas_d.php.
- La vista original devolvia dos tablas HTML sueltas (cabecera + listado); aqui se componen ambas en Html para que el frontend solo tenga que volcarlas al cliente.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Permiso oficina `vcsd`
- Permiso oficina `des`

### Referencias Internas

- Flujo: `encargossacd.listas_d.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/listas_d.md`

## Listas Exigencia Ctr

### Para Que Sirve

- Gestiona ListasExigenciaCtr.
- Listado de exigencias SACD por centro/iglesia.
- Sustituye la logica de frontend/encargossacd/controller/listas_exigencia_ctr.php.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.listas_exigencia_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/listas_exigencia_ctr.md`

## Sacd Ausencias

### Para Que Sirve

- Gestiona SacdAusencias.
- Guarda/modifica las ausencias de un SACD (frontend/encargossacd/controller/sacd_ausencias_update.php).
- Devuelve ['error' => bool, 'mensajes' => string] donde mensajes acumula los errores de guardado/eliminacion para mostrar al usuario.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

### Errores O Avisos Frecuentes

- `no se ha encontrado el encargo del sacd`

### Referencias Internas

- Flujo: `encargossacd.sacd_ausencias.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/sacd_ausencias.md`

## Sacd Ausencias Get

### Para Que Sirve

- Gestiona SacdAusenciasGet.
- Datos para la ficha de ausencias de un SACD (frontend/encargossacd/controller/sacd_ausencias_get.php).
- Devuelve la lista de tipos de ausencia disponibles (encargos con prefijo 7/4) y las filas asociadas al SACD.
- Con historial=1 incluye todas las ausencias; sin historial solo muestra las que aun tienen vigencia.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.sacd_ausencias_get.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/sacd_ausencias_get.md`

## Sacd Ausencias Jefe Zona

### Para Que Sirve

- Gestiona SacdAusenciasJefeZona.
- Datos para el listado de SACDs susceptibles de gestionar ausencias desde la ficha de jefe de zona (frontend/encargossacd/controller/sacd_ausencias_jefe_zona.php).
- Recopila los SACDs de la(s) zona(s) del jefe y, cuando corresponde (Oficial_dl o jefe de calendario), la totalidad de SACDs activos.
- El array se devuelve ordenado por iniciales para alimentar el desplegable.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.sacd_ausencias_jefe_zona.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/sacd_ausencias_jefe_zona.md`

## Sacd Ficha

### Para Que Sirve

- Gestiona SacdFicha.
- Datos para la ficha de encargos de un SACD (sacd_ficha_ajax?que=ficha).
- Porta la lectura del antiguo controlador frontend y devuelve un payload estructurado con los encargos y sus dedicaciones (horario del centro y del SACD ya calculadas como texto cuando mod_horario=3).
- Mutacion de la ficha de encargos de un SACD (sacd_ficha_ajax?que=update).
- Porta la logica del antiguo controlador frontend, haciendo la misma actualizacion de dedicaciones por modulo y de observaciones.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

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

## Sacd Select

### Para Que Sirve

- Gestiona SacdSelect.
- Opciones para el desplegable de SACDs filtrados por tabla (sacd_ficha_ajax?que=get_select).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.sacd_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/sacd_select.md`

## Zonas Get Select

### Para Que Sirve

- Gestiona EncargoZonasSelect.
- Payload JSON para el desplegable de zonas (grupo «zonas misas»).
- Devuelve el contrato estandar definido en refactor.md, sin instanciar frontend\shared\web\Desplegable (responsabilidad exclusiva del frontend).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `encargossacd.zonas_get_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/encargossacd/flujos/zonas_get_select.md`

## Revision Pendiente

- Sustituir nombres tecnicos por nombres visibles en la aplicacion.
- Completar rutas de menu.
- Confirmar permisos necesarios.
- Anadir capturas o ejemplos si se quiere publicar para usuarios finales.
