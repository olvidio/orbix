---
tipo: "manual_usuario"
modulo: "casas"
flujos: 10
estado_revision: "revisado_parcial"
---

# Manual De Usuario - casas

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Consultar la previsión económica de una casa

### Para Que Sirve

Ver el estudio económico de una casa: ocupación, ingresos y gastos.

### Donde Entrar

- Calendario Ubi Resumen (frontend/casas/controller/calendario_ubi_resumen.php)
- **Legacy:** adl > Nuevo Calendario > Previsión económica
- **Pills2:** ACTIVIDADES > Estadísticas económicas > Previsión económica

### Tareas Habituales

#### Consultar la previsión económica de una casa

1. Entrar en **Previsión económica**.
2. Elegir la casa y el periodo.
3. Se muestra el estudio económico.

### Errores O Avisos Frecuentes

- `Casa no encontrada`

### Permisos

- Sin control propio; acceso vía menú de calendario / estadísticas económicas.

### Referencias Internas

- Flujo: `casas.calendario_ubi_resumen.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/casas/flujos/calendario_ubi_resumen.md`

## Consultar las actividades de una casa

### Para Que Sirve

Ver las actividades de una casa en un periodo.

### Donde Entrar

- Casa Actividades Lista (frontend/casas/controller/casa_actividades_lista.php)
- **Legacy:** exterior > casas > lista actividades
- **Pills2:** exterior > casas > lista actividades

### Tareas Habituales

#### Consultar las actividades de una casa

1. Abrir el estudio económico de la casa y elegir el periodo.
2. Aparece la lista de actividades; pulsar una para abrir su ficha.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Por actividad: `PermisosActividades` facetas `datos` (`ocupado`/`ver`), `ctr` (`ver` centros),

### Referencias Internas

- Flujo: `casas.casa_actividades.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/casas/flujos/casa_actividades.md`

## Consultar estadística económica por años

### Para Que Sirve

Ver, para las casas elegidas, cómo han evolucionado la ocupación, los ingresos, los gastos y el superávit en los últimos años, y desplegar el detalle por actividad.

### Donde Entrar

- Casa Ec (frontend/casas/controller/casa_ec.php)
- **Legacy:** adl > Gestión casas > estadística por años
- **Legacy (variante):** adl > Nuevo Calendario > Estadística por años
- **Pills2:** CASAS Y CTR > Gestión casas > estadística por años
- **Pills2 (variante):** ACTIVIDADES > Estadísticas económicas > Estadística por años

### Tareas Habituales

#### Consultar la estadística de una casa

1. Abrir **estadística por años** desde el menú.
2. Elegir la casa en el desplegable del recuadro **RESUMEN ECONÓMICO**.
3. Pulsar **buscar**.
4. Leer la tabla que aparece debajo: una fila por año, con días ocupados, nº días x nº asistentes (previsto y real), ingresos previstos y reales acumulados, gastos reales, aportación real dl y superávit. Las columnas **%** indican el porcentaje respecto al total.
5. Solo se cuentan las actividades que no están borradas: los estados son proyecto, actual, terminada y borrable (1 a 4), y se excluye «borrable».
6. Si una casa pertenece a un grupo de casas, el superávit se calcula para el conjunto del grupo, no para cada casa por separado.
7. Pulsar **mostrar detalles** en la fila de un año para ver las actividades de esa casa (ocupación sv/sf e ingresos sv/sf). **ocultar detalles** lo pliega.

#### Consultar varias casas

1. Abrir **estadística por años** y elegir una casa.
2. Al elegir una casa aparece debajo otro desplegable para elegir otra; repite hasta tener todas las casas que interesen.
3. Pulsar **buscar**: la tabla muestra cada casa con sus años y los totales por año.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `casas.casa_ec.consultar.flujo`
- Fichero catalogo: `docs/catalogo/casas/flujos/casa_ec.md`

## Casa Ec Gastos

### Para Que Sirve

- Gestiona CasaEcGastos.
- Data builder: formulario anual con gastos y aportaciones (sv/sf) por mes de una casa.
- Sucesor de la rama que=getGastos de apps/casas/controller/casa_ec_ajax.php.
- Use case: guardar los gastos y aportaciones (sv/sf) mensuales de una casa para un año completo.
- Borra los existentes y los reinserta con fecha 5 de cada mes.
- Sucesor de la rama que=guardarGasto de apps/casas/controller/casa_ec_ajax.php.

### Donde Entrar

- Casa Ec Gastos Lista (frontend/casas/controller/casa_ec_gastos_lista.php)
- **Legacy:** exterior > casas > gastos casa
- **Pills2:** CASAS Y CTR > Gestión casas > gastos casas

### Tareas Habituales

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

## Anotar el ingreso de una actividad

### Para Que Sirve

Anotar o corregir los ingresos de una actividad en una casa.

### Donde Entrar

- Casa (frontend/casas/controller/casa.php)
- **Legacy:** exterior > casas > Gestión económica
- **Pills2:** CASAS Y CTR > Gestión casas > Gestión económica

### Tareas Habituales

#### Anotar el ingreso de una actividad

1. Abrir el estudio económico de la casa y elegir la actividad.
2. Rellenar los importes del formulario de ingreso y guardar.
3. Para quitar el ingreso, usar eliminar y confirmar.

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

## Consultar los ingresos de una casa

### Para Que Sirve

Ver los ingresos de las actividades de una casa en un periodo.

### Donde Entrar

- Casa Ingresos Lista (frontend/casas/controller/casa_ingresos_lista.php)
- **Legacy:** exterior > casas > Gestión económica
- **Pills2:** CASAS Y CTR > Gestión casas > Gestión económica

### Tareas Habituales

#### Consultar los ingresos de una casa

1. Abrir el estudio económico de la casa y elegir el periodo.
2. Se muestra la tabla de actividades con sus ingresos.

### Errores O Avisos Frecuentes

- `Debe seleccionar una casa.`

### Permisos

- Por actividad: `PermisosActividades` faceta `economic` (`ver` para incluir fila, `modificar` para

### Referencias Internas

- Flujo: `casas.casa_ingresos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/casas/flujos/casa_ingresos.md`

## Consultar la estadística económica de las casas

### Para Que Sirve

Ver el resumen económico de las casas: días ocupados, asistentes previstos y reales, ingresos, gastos, aportaciones y superávit.

### Donde Entrar

- Casas Resumen (frontend/casas/controller/casas_resumen.php)
- **Legacy:** adl > Gestión casas > estadística  por casas
- **Pills2:** CASAS Y CTR > Gestión casas > estadística  por casas

### Tareas Habituales

#### Consultar la estadística económica de las casas

1. Entrar en **estadística por casas**.
2. Elegir las casas y el periodo (un año, trimestre o varios periodos).
3. Se muestra la tabla de resumen por casa.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio en el caso de uso; el frontend filtra acceso por oficina.

### Referencias Internas

- Flujo: `casas.casas_resumen.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/casas/flujos/casas_resumen.md`

## los grupos de casas

### Para Que Sirve

Agrupar casas: indicar qué casa es la madre y cuáles dependen de ella.

### Donde Entrar

- Grupo (frontend/casas/controller/grupo.php)
- **Legacy:** adl > Gestión casas > grupos
- **Pills2:** CASAS Y CTR > Gestión casas > grupos

### Tareas Habituales

#### Gestionar los grupos de casas

1. Entrar en **grupos**: aparece la lista de **grupos de casas**.
2. Para crear uno, pulsar nuevo; elegir la **Casa Madre** y la **Casa dependiente** (deben ser dos casas distintas) y pulsar **guardar**.
3. Para cambiarlo, abrir la relación, modificar y **guardar**; para quitarlo, eliminarla.
4. Aviso: en la estadística económica las casas de un mismo grupo se recalculan conjuntamente.

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

## Corregir los asistentes previstos

### Para Que Sirve

Cambiar el número de asistentes previstos de una actividad en la previsión.

### Donde Entrar

- Prevision Asistentes (frontend/casas/controller/prevision_asistentes.php)
- **Legacy:** adl > Nuevo Calendario > previsión asistentes
- **Pills2:** Calendario > Nuevo calendario > previsión asistentes

### Tareas Habituales

#### Corregir los asistentes previstos

1. Abrir **previsión asistentes**.
2. Hacer doble clic en la celda del número de asistentes previstos, escribir el valor y confirmar.
3. Se guarda al salir de la celda.

### Errores O Avisos Frecuentes

- `Hay un error, no se ha guardado`
- `no se encuentra el ingreso`

### Permisos

- Sin control propio; la tabla solo se muestra si `prevision_asistentes_data` devolvió `permitido: true`.

### Referencias Internas

- Flujo: `casas.ingreso_plazas_previstas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/casas/flujos/ingreso_plazas_previstas.md`

## Consultar la previsión de asistentes

### Para Que Sirve

Ver la previsión de asistentes de las casas.

### Donde Entrar

- Prevision Asistentes (frontend/casas/controller/prevision_asistentes.php)
- **Legacy:** adl > Nuevo Calendario > previsión asistentes
- **Pills2:** Calendario > Nuevo calendario > previsión asistentes

### Tareas Habituales

#### Consultar la previsión de asistentes

1. Entrar en **previsión asistentes**.
2. Elegir el periodo y las casas.
3. Se muestra la tabla con los asistentes previstos.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- `permitido: false` si oficina desconocida y `oConfig.gestionCalendario !== 'central'`.
- Filtro de tipos de actividad según oficina (p. ej. `des` → `^(16|1141|1125|1341)`).

### Referencias Internas

- Flujo: `casas.prevision_asistentes.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/casas/flujos/prevision_asistentes.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/casas/flujos/`.
