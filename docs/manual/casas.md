---
tipo: "manual_usuario"
modulo: "casas"
flujos: 9
estado_revision: "generado"
---

# Manual De Usuario - casas

Este manual es un borrador generado desde `docs/catalogo`. Debe revisarse para ajustar nombres de menu, permisos, validaciones y lenguaje final de usuario.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas de menu y nombres visibles pueden necesitar revision manual.

## Calendario Ubi Resumen

### Para Que Sirve

- Gestiona CalendarioUbiResumen.
- Datos del estudio económico de una casa (calendario_ubi_resumen).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Casa no encontrada`

### Permisos

- Sin control propio; acceso vía menú de calendario / estadísticas económicas.

### Referencias Internas

- Flujo: `casas.calendario_ubi_resumen.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/casas/flujos/calendario_ubi_resumen.md`

## Casa Actividades

### Para Que Sirve

- Gestiona CasaActividades.
- Listado de actividades por casa y periodo (casa_actividades_lista).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Por actividad: `PermisosActividades` facetas `datos` (`ocupado`/`ver`), `ctr` (`ver` centros),

### Referencias Internas

- Flujo: `casas.casa_actividades.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/casas/flujos/casa_actividades.md`

## Casa Ec Gastos

### Para Que Sirve

- Gestiona CasaEcGastos.
- Data builder: formulario anual con gastos y aportaciones (sv/sf) por mes de una casa.
- Sucesor de la rama que=getGastos de apps/casas/controller/casa_ec_ajax.php.
- Use case: guardar los gastos y aportaciones (sv/sf) mensuales de una casa para un año completo.
- Borra los existentes y los reinserta con fecha 5 de cada mes.
- Sucesor de la rama que=guardarGasto de apps/casas/controller/casa_ec_ajax.php.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Guardar

1. Revisar manualmente los pasos de esta accion.

#### Abrir el formulario

1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

### Errores O Avisos Frecuentes

- `Debe seleccionar una casa.`
- `Faltan id_ubi o year.`
- `Hay un error, no se ha guardado.`

### Permisos

- Sin control propio; autorización en frontend + `$_SESSION['oPerm']`.
- Sin control propio; autorización en frontend.

### Referencias Internas

- Flujo: `casas.casa_ec_gastos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/casas/flujos/casa_ec_gastos.md`

## Casa Ingreso

### Para Que Sirve

- Gestiona CasaIngreso.
- Crear/actualizar el Ingreso de una actividad.
- Datos para el formulario de ingreso de una actividad (casa_ingreso_form).
- Eliminar el Ingreso de una actividad.

### Donde Entrar

- Casa (frontend/casas/controller/casa.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

#### Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

#### Abrir el formulario

1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

### Errores O Avisos Frecuentes

- `Falta id_activ`
- `Actividad no encontrada`
- `Hay un error, no se ha guardado la actividad.`
- `Hay un error, no se ha guardado.`
- `no sé cuál he de borar`
- `Ingreso no encontrado`
- `Hay un error, no se ha eliminado`

### Permisos

- Sin control propio; autorización en frontend + permisos de actividad (`economic`).
- `puede_modificar_tarifa`: `PermisosActividades` faceta `id_tarifa`, acción `modificar` (requiere
- Sin control propio; el formulario solo muestra tarifa editable si `casa_ingreso_form_data` devolvió

### Referencias Internas

- Flujo: `casas.casa_ingreso.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/casas/flujos/casa_ingreso.md`

## Casa Ingresos

### Para Que Sirve

- Gestiona CasaIngresos.
- Listado económico de actividades por casa (casa_ingresos_lista).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

### Errores O Avisos Frecuentes

- `Debe seleccionar una casa.`

### Permisos

- Por actividad: `PermisosActividades` faceta `economic` (`ver` para incluir fila, `modificar` para

### Referencias Internas

- Flujo: `casas.casa_ingresos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/casas/flujos/casa_ingresos.md`

## Casas Resumen

### Para Que Sirve

- Gestiona CasasResumen.
- Use case: resumen económico de casas (dias ocupados, asistentes previstos/reales, ingresos, gastos, aportaciones, superávit).
- Sucesor de apps/casas/controller/casas_resumen_ajax.php.
- Dos modos: - que='' → un único periodo (año/trimestre/rango) por casa.
- - que!='' → estadística por año (5 años) por casa.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio en el caso de uso; el frontend filtra acceso por oficina.

### Referencias Internas

- Flujo: `casas.casas_resumen.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/casas/flujos/casas_resumen.md`

## Grupo

### Para Que Sirve

- Gestiona GrupoCasa.
- Crea o actualiza un GrupoCasa.
- Datos del formulario GrupoCasa (nuevo/editar).
- Elimina un GrupoCasa.
- Listado de GrupoCasa (relaciones padre ↔ hijo).

### Donde Entrar

- Grupo (frontend/casas/controller/grupo.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

#### Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

#### Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

#### Abrir el formulario

1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

### Errores O Avisos Frecuentes

- `debe indicar las dos casas`
- `No puede ser la misma casa`
- `no se encuentra el grupo`
- `debe indicar el grupo a eliminar`
- `Hay un error, no se ha guardado.`
- `Hay un error, no se ha eliminado.`

### Permisos

- Sin control propio; autorización en frontend + `$_SESSION['oPerm']`.
- Sin control propio; la autorización se resuelve en frontend + `$_SESSION['oPerm']`.
- `puede_anadir` depende de permiso oficina `adl` vía `XPermisos`. El listado en sí no filtra por permiso.
- Sin control propio en el caso de uso; autorización en frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `casas.grupo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/casas/flujos/grupo.md`

## Ingreso Plazas Previstas

### Para Que Sirve

- Gestiona IngresoPlazasPrevistas.
- Actualiza num_asistentes_previstos de un Ingreso desde la TablaEditable de prevision_asistentes.

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

- `Hay un error, no se ha guardado`
- `no se encuentra el ingreso`

### Permisos

- Sin control propio; la tabla solo se muestra si `prevision_asistentes_data` devolvió `permitido: true`.

### Referencias Internas

- Flujo: `casas.ingreso_plazas_previstas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/casas/flujos/ingreso_plazas_previstas.md`

## Prevision Asistentes

### Para Que Sirve

- Gestiona PrevisionAsistentes.
- Datos de la pantalla prevision_asistentes.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- `permitido: false` si oficina desconocida y `oConfig.gestionCalendario !== 'central'`.
- Filtro de tipos de actividad según oficina (p. ej. `des` → `^(16|1141|1125|1341)`).

### Referencias Internas

- Flujo: `casas.prevision_asistentes.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/casas/flujos/prevision_asistentes.md`

## Revision Pendiente

- Sustituir nombres tecnicos por nombres visibles en la aplicacion.
- Completar rutas de menu.
- Confirmar permisos necesarios.
- Anadir capturas o ejemplos si se quiere publicar para usuarios finales.
