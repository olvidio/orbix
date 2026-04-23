# Baseline de migracion del modulo `casas`

Resume, antes de mover codigo, que pantallas viven en `apps/casas/` y cual es
su forma previa (parametros GET/POST, salida, dependencias). El destino es el
patron canonico `frontend/<modulo>/controller` + `src/<modulo>/application` +
`src/<modulo>/infrastructure/ui/http/controllers` + `src/<modulo>/config/routes.php`,
igual que en `actividadplazas`, `actividadtarifas`, `notas`, etc.

Al empezar, `src/casas/` ya tiene dominio (`entity`, `value_objects`,
`contracts`) e infraestructura (`infrastructure/persistence/postgresql`) para
`Ingreso`, `UbiGasto`, `GrupoCasa`. **No hay** `application/`, ni
`infrastructure/ui/http/controllers/`, ni `config/routes.php`. Hay que crearlos.

## Inventario de pantallas

### 1. `casa_que` + `casa_ajax` (actividades por casa + ingresos)

- **Entrada**: `apps/casas/controller/casa_que.php`. Render de form con
  `web\CasasQue`, `web\PeriodoQue` + seleccion multiple de casas.
- **Dispatcher**: `apps/casas/controller/casa_ajax.php` (~626 LOC) con rama
  `switch($Qque)`:
  - `nuevo`: emite HTML `<form id=frm_periodo>` para crear periodo.
  - `form_ingreso`: emite HTML `<form id=frm_ingreso>` con desplegable
    `TipoTarifa` y campos `precio`, `ingresos`, `num_asistentes`, `observ`.
  - `get`: imprime `web\Lista` con tabla de actividades + totales sf/sv/tot.
  - `guardar`: actualiza actividad (`tarifa`, `precio`) + `Ingreso`.
  - `eliminar`: elimina `Ingreso` de una actividad.
  - `lista_activ`: imprime `web\Lista` con actividades por casa (variante).
- **Permisos**: `PauType::PAU_CDC` restringe a la casa del usuario;
  `oPermActividades` decide si se puede modificar.
- **Dependencias**: `ActividadRepositoryInterface`, `ActividadAllRepositoryInterface`,
  `IngresoRepositoryInterface`, `TipoTarifaRepositoryInterface`,
  `TarifaUbiRepositoryInterface`, `CasaDlRepositoryInterface`,
  `CentroDlRepositoryInterface`, `CentroEncargadoRepositoryInterface`,
  `ActividadProcesoTareaRepositoryInterface`, `ActividadCargoRepositoryInterface`,
  `PermisosActividadesTrue`, `TiposActividades`, `Periodo`.

### 2. `casa_ec_que` + `casa_ec_ajax` (gastos/aportaciones por casa)

- **Entrada**: `apps/casas/controller/casa_ec_que.php`. Similar filtro al #1 pero
  renderiza `casa_ec_que.html.twig` con `url_resumen` apuntando a
  `casas_resumen_ajax.php` y `url_ajax` a `casa_ec_ajax.php` (para la edicion de
  gastos mensuales).
- **Dispatcher `casa_ec_ajax.php`** (~600 LOC, ~400 de ellas codigo comentado
  muerto apuntando a `CasaResumenEc*` clases que ya no existen):
  - `guardarGasto`: guarda 12 meses x 3 tipos (`gasto`, `aportacion_sv`,
    `aportacion_sf`) en `UbiGasto` para una casa/año.
  - `getGastos`: pinta HTML con tabla editable mes x tipo.
  - (codigo muerto): `form`, `get`, `guardar`, `eliminar` para `CasaResumenEc*`
    (presupuestos sv/sf/total). No lo migramos.
- **Dependencias**: `UbiGastoRepositoryInterface`, `CasaDlRepositoryInterface`.

### 3. `casas_resumen` + `casas_resumen_ajax` (resumen economico global)

- **Entrada**: `apps/casas/controller/casas_resumen.php`. Filtro con
  `web\CasasQue` + `web\PeriodoQue` para elegir casas y periodo.
- **Dispatcher `casas_resumen_ajax.php`** (~1108 LOC): imprime HTML directo
  (sin `Lista`), dos variantes segun `$Qque`:
  - **Periodo puntual** (`Qque` vacio): tabla con dias ocupados, asistentes
    previstos/reales, ingresos previstos/reales, gastos, aportaciones,
    superavit, desglosado por casa + totales globales.
  - **Anual** (`Qque = get`): la misma tabla pero repetida por ultimo
    5 años (`$a_anys`).
- **Funciones libres** en el fichero: `inicio_periodo`, `fin_periodo`,
  `reparto`, `dias_ocupacion`. Hay que extraer a un servicio compartido.
- **Dependencias**: `ActividadRepositoryInterface`,
  `GrupoCasaRepositoryInterface`, `IngresoRepositoryInterface`,
  `UbiGastoRepositoryInterface`, `CasaDlRepositoryInterface`,
  `CasaPeriodoRepositoryInterface`, `CentroEllasRepositoryInterface`,
  `Periodo`.

### 4. `calendario_ubi_resumen` + `calendario_ubi_resumen_ajax`

- **Entrada**: `apps/casas/controller/calendario_ubi_resumen.php`. Pantalla
  con desplegable casa + incrementos `inc_t`, `G` + dos botones
  `resumen sf` / `resumen sv` que hacen POST a `..._ajax.php`.
- **Dispatcher `..._ajax.php`** (~420 LOC): imprime HTML con formulario de
  tarifas para el año siguiente + tabla de actividades del año previsto +
  tabla "comparacion con minimos de la casa". El formulario de tarifas postea
  a `/src/actividadtarifas/tarifa_ubi_update_inc` (ya JSON, migrado durante
  `actividadtarifas`).
- **Dependencias**: `ActividadRepositoryInterface`,
  `TipoTarifaRepositoryInterface`, `TarifaUbiRepositoryInterface`,
  `CasaDlRepositoryInterface`, `CasaPeriodoRepositoryInterface`,
  `IngresoRepositoryInterface`, `UbiGastoRepositoryInterface`,
  `TiposActividades`.

### 5. `prevision_asistentes` + `prevision_asistentes_ajax`

- **Entrada**: `apps/casas/controller/prevision_asistentes.php`. Tabla editable
  (`web\TablaEditable`) de actividades con columna "asistentes previstos"
  modificable via doble click. `update` se manda por AJAX con `data` (JSON de
  la fila) + `colName`.
- **Dispatcher `..._ajax.php`** (~42 LOC): rama `update` que guarda
  `Ingreso::setNum_asistentes_previstos` para un `id_activ`. Ya usa
  `ContestarJson::enviar`.
- **Dependencias**: `ActividadDlRepositoryInterface`,
  `IngresoRepositoryInterface`, `CasaDlRepositoryInterface`, `Periodo`.

### 6. `grupo_lista` + `grupo_form` + `grupo_ajax` (CRUD GrupoCasa)

- **Listado**: `grupo_lista.php` imprime `web\Lista` con las relaciones
  padre-hijo entre casas. Boton `nuevo` abre `grupo_form.php`.
- **Formulario**: `grupo_form.php` con 2 desplegables de `CasaDl` (padre, hijo).
- **Dispatcher `grupo_ajax.php`**: `update` (guarda la relacion), `eliminar`
  (borra por `id_item`).
- **Dependencias**: `GrupoCasaRepositoryInterface`, `CasaDlRepositoryInterface`.

## Plan de migracion

1. Baseline (este documento).
2. Pantalla **6 (grupo)**: use cases `GrupoCasaListaData`, `GrupoCasaFormData`,
   `GrupoCasaUpdate`, `GrupoCasaEliminar` + controllers HTTP JSON + frontend
   shell `grupo.phtml` con `fnjs_ver`/`fnjs_modificar`/`fnjs_guardar`/
   `fnjs_eliminar`.
3. Pantalla **5 (prevision_asistentes)**: use case `PrevisionAsistentesData`
   (datos de la tabla) + `PrevisionAsistenteUpdate` (ya era JSON) + frontend.
4. Pantalla **4 (calendario_ubi_resumen)**: use case `EstudioUbiResumen`
   (datos crudos), controlador HTTP JSON, frontend `ubi_resumen.php` +
   `ubi_resumen_body.php` que imprime HTML desde el array.
5. Pantalla **1 (casa_que + casa_ajax)**: partir en
   `actividades_casa_lista_data`, `actividades_casa_form_ingreso_data`,
   `actividades_casa_ingreso_update`, `actividades_casa_ingreso_eliminar`,
   `actividades_casa_listado_activ_data` + servicios auxiliares (resolver de
   casas, permisos, construccion de filas).
6. Pantalla **2 (casa_ec)**: use cases `CasaEcGastosData` (datos del form
   editable) + `CasaEcGastosUpdate` (guardado 12 meses). Tirar el codigo
   comentado muerto.
7. Pantalla **3 (casas_resumen)**: caso complicado. Por su tamaño y la
   complejidad de los calculos, usaremos **application/services** con un
   `CasasResumenCalculator` que produce arrays serializables; los controladores
   HTTP devuelven JSON y el frontend imprime la tabla en un `.phtml`. No vamos
   a la carpeta `legacy/` porque la logica, aunque compleja, no es *tan*
   grande como `apps/notas/model/Resumen.php` y cabe partirla en funciones
   puras.
8. Actualizar consumidores externos: los 4 `.md` de documentacion, los 3
   ficheros de menus (`comun.sql`, `aux_metamenus.csv`,
   `Documentacion_Obix/menus.csv`), y los 2 comentarios en
   `src/actividadtarifas/.../tarifa_ubi_update_inc*`.
9. Borrar `apps/casas/`.
10. `php -l` en todos los ficheros nuevos/tocados.

## Notas y riesgos

- La pantalla **#1** tiene dos ramas `get` vs `lista_activ` que son listas
  similares pero con columnas distintas. Se mantienen separadas.
- La pantalla **#3** imprime HTML inline muy denso; al mover a JSON conviene
  devolver arrays ordenados por fila y que el `.phtml` recorra sin logica.
- La pantalla **#4** ya tiene el form de tarifas integrado con el endpoint
  `/src/actividadtarifas/tarifa_ubi_update_inc` (migrado en el slice de
  `actividadtarifas`). Solo hay que migrar el resto de la pagina.
- La pantalla **#5** ya devolvia JSON; solo hay que mover a la nueva
  estructura.
- Casos especiales por grupos de casas (`GrupoCasa` padre-hijo, p.ej.
  Castelldaura): presentes en #3; hay que conservar la logica de traspasar
  gastos al padre.
- Los `Hash` de URLs cambian cuando la pantalla se mueve; todos los formularios
  que posteen a los endpoints `/src/casas/*` necesitan **nuevo** `Hash` con
  los campos exactos.
