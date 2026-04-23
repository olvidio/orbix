# Baseline de migracion del modulo `cartaspresentacion`

Resume, antes de mover codigo, que pantallas viven en
`apps/cartaspresentacion/` y cual es su forma previa (parametros POST, salida,
dependencias). El destino es el patron canonico `frontend/<modulo>/controller`
+ `src/<modulo>/application` + `src/<modulo>/infrastructure/ui/http/controllers`
+ `src/<modulo>/config/routes.php`, en la misma linea que `casas` o `notas`.

Al empezar, `src/cartaspresentacion/` ya tiene dominio (`entity`,
`value_objects`, `contracts`) e infraestructura PostgreSQL para
`CartaPresentacion` (y las variantes `Dl`, `Ex`). **No hay** `application/`,
`infrastructure/ui/http/controllers/` ni `config/routes.php`. Hay que crearlos.

## Inventario de pantallas

### 1. `cartas_presentacion_buscar` (formulario de filtros)

- **Entrada**: `apps/cartaspresentacion/controller/cartas_presentacion_buscar.php`.
- **Render**: `cartas_presentacion_buscar.html.twig` con `oDesplRegion`,
  `oDesplPais`, `oDesplDelegacion` (todos `web\Desplegable`).
- **Accion del formulario**: POST a
  `apps/cartaspresentacion/controller/cartas_presentacion_lista.php?que=get`
  (ver pantalla #2). La respuesta HTML se inyecta en `#resultados`.
- **Dependencias**: `RegionDropdown::activasOrdenNombre()`,
  `DelegacionDropdown::byRegiones(['H'])`,
  `DireccionCentroRepositoryInterface::getArrayPaises()`.

### 2. `cartas_presentacion_lista` (listado textual agrupado)

- **Entrada**: `apps/cartaspresentacion/controller/cartas_presentacion_lista.php`.
  Switch `$Qque` con ramas:
  - `lista_dl`: cartas solo de la delegacion actual (`ConfigGlobal::mi_delef()`).
  - `lista_todo`: cartas de todas las delegaciones (repositorio global).
  - `get`: filtrado por `poblacion`, `pais`, `region`, `dl` (modo #1 → #2).
- **Salida**: HTML con `<h3>`/`<table>` agrupado por tipo de labor (`agd`,
  `numerarios`, `s`, `sss+`), delegacion (si aplica) y poblacion; sin `web\Lista`.
- **Dependencias**: `CartaPresentacionRepositoryInterface`,
  `CartaPresentacionDlRepositoryInterface`, `CentroRepositoryInterface`,
  `DireccionCentroRepositoryInterface`, `RelacionCentroDireccionRepositoryInterface`,
  `UbiTelecoService`, `CuadrosLabor`.
- **Riesgos**: hace muchas consultas por iteracion (N+1). Se mantiene la
  logica tal cual en la primera migracion y se devuelve ya el HTML renderizado
  desde el use case (porque el formateo esta muy acoplado con el agrupamiento
  multinivel). Si mas adelante hace falta, se puede mover a vista `.phtml`.

### 3. `cartas_presentacion_que` (pantalla principal de modificacion)

- **Entrada**: `apps/cartaspresentacion/controller/cartas_presentacion_que.php`.
  Render de `cartas_presentacion_que.html.twig` con `oSelCiudades`
  (desplegable `get_dl` / `get_r`) + zona `#lst_lugar` para poblaciones + zona
  `#ficha2` para listado de centros + `#div_modificar` para form modal.
- **Dispatcher**: `apps/cartaspresentacion/controller/cartas_presentacion_ajax.php`
  con `switch($Qque_mod)`:
  - `poblaciones` con sub-filtro `get_H` / `get_r` / `get_dl`: devuelve
    `<select id="poblacion_sel">` con las poblaciones disponibles.
  - `form_pres`: HTML con `<form id='frm_pres'>` + campos `pres_nom`,
    `pres_telf`, `pres_mail`, `zona`, `observ`. Valida permisos (solo dl o cr).
  - `update`: guarda la `CartaPresentacion` y llama a `sanear()` para eliminar
    cartas cuyas direcciones ya no pertenecen al centro.
  - `eliminar`: elimina la `CartaPresentacion`.
  - `actualizar`: (bloque comentado / desactivado) rellenar `pres_nom` desde
    tablas de cargos. No se migra.
  - `get_dl`: imprime `web\Lista` de centros de la delegacion + estado de
    carta (si/no) con botones `director` (modificar), `ver_ubi`,
    `eliminar_cp`. Admite `poblacion_sel` para filtrar por poblacion.
  - `get_r`: lo mismo para centros `cr`/`dl` de otras regiones.
- **Dependencias**: `CartaPresentacionRepositoryInterface`,
  `CartaPresentacionDlRepositoryInterface`,
  `CartaPresentacionExRepositoryInterface`, `CentroRepositoryInterface`,
  `CentroDlRepositoryInterface`, `CentroExRepositoryInterface`,
  `DireccionCentroRepositoryInterface`,
  `DireccionCentroDlRepositoryInterface`,
  `RelacionCentroDireccionRepositoryInterface`,
  `RelacionCentroDlDireccionRepositoryInterface`,
  `RelacionCentroExDireccionRepositoryInterface`.

## Plan de migracion

1. Baseline (este documento).
2. **Pantalla 1 (buscar)**: use case `CartasPresentacionBuscarOpcionesData`
   con las 3 listas de opciones + controlador HTTP JSON. Frontend
   `cartas_presentacion_buscar.php` + vista `.phtml` que construye los
   `web\Desplegable` localmente y postea a `cartas_presentacion_lista.php`.
3. **Pantalla 2 (lista)**: use case `CartasPresentacionListaData` que
   agrupa cartas por tipo labor / dl / poblacion y devuelve HTML ya
   formateado dentro del array (`html_lista`, `errores`). Controlador HTTP
   JSON. Frontend `cartas_presentacion_lista.php` que imprime `html_lista`.
4. **Pantalla 3 (que + ajax)**: partir el dispatcher `cartas_presentacion_ajax.php`
   en endpoints dedicados:
   - `CartasPresentacionPoblacionesData` (array value => label de poblaciones).
   - `CartasPresentacionUbisListaData` (listado de centros con estado de carta
     + columnas para `web\Lista`).
   - `CartaPresentacionFormData` (datos del form de modificacion, valida
     permisos).
   - `CartaPresentacionUpdate` (mutacion, incluye sanear).
   - `CartaPresentacionEliminar` (mutacion).
   Frontend `cartas_presentacion.php` (pantalla principal) +
   `cartas_presentacion_ubis_lista.php` (render listado) +
   `cartas_presentacion_form.php` (render form) + vistas `.phtml`.
   El desplegable de poblaciones sigue el contrato estandar
   (`fnjs_construir_desplegable`).
5. Convertir los 4 controladores `apps/cartaspresentacion/controller/*.php` en
   wrappers minimos que delegan a sus equivalentes `frontend/`, marcar como
   deprecados.
6. Actualizar menus (`documentacion/Documentacion_Obix/menus.csv`,
   `proves/aux_metamenus.csv`, `log/menus/comun.sql`) para apuntar a los
   controladores `frontend/cartaspresentacion/controller/*`.
7. `php -l` en todos los ficheros nuevos y tocados.

## Notas y riesgos

- Las ramas `lista_todo` / `lista_dl` / `get` de la pantalla #2 comparten
  funciones libres (`mega_array`, `datos_a_celdas`, `lista_cartas`,
  `format_telf`, `obj_pau_from_centro`). Se encapsulan todas en el use case
  `CartasPresentacionListaData` como metodos privados; el formateo de
  telefonos se mueve a `CartasPresentacionFormatter::formatTelf()` para
  poder reutilizar si hace falta.
- La pantalla #3 mezcla `CartaPresentacion`, `CartaPresentacionDl` y
  `CartaPresentacionEx`. La eleccion del repositorio al guardar una carta
  nueva depende de si el centro es de la dl del usuario (`dl` =
  `mi_delef()`) o un `cr` extranjero. Se conserva esa logica en
  `CartaPresentacionUpdate`.
- El `sanear()` que se ejecuta despues de `update` recorre todas las cartas
  de la dl y elimina las que tengan direcciones desconectadas. Se mantiene
  tal cual (es una salvaguarda).
- En la pantalla #3 el desplegable de poblaciones (HTML `<select>`) pasa a
  ser un payload array en el backend + constructor JS estandar
  (`fnjs_construir_desplegable`) en el frontend, siguiendo el patron ya
  aplicado en `actividades`.
- `Hash` cambia al mover cada endpoint: se genera un `Hash` nuevo por URL.
- `cartas_presentacion_ajax.php` se convierte en un wrapper de compatibilidad
  opcional solo si hay menu en produccion que lo use; en el repo actual no
  se enlaza desde `menus.csv` ni desde otros `apps/*`, asi que se borra el
  contenido y se deja un `require` al frontend equivalente con nota de
  deprecacion.
