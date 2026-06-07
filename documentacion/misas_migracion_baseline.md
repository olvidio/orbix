# Baseline funcional migracion `apps/misas` → `frontend/misas` + `src/misas`

Este baseline documenta el estado actual del modulo `misas` y el plan de migracion por slices, siguiendo los criterios de `refactor.md`.

## Estado de partida

- `apps/misas/controller/` — controladores legacy; los migrados son wrappers `require` hacia `frontend/misas/controller/`.
- `apps/misas/view/` — vistas `*.html.twig` residuales donde aun no hay PHTML equivalente (p. ej. otras pantallas del modulo); status/cuadricula principales ya en `frontend/misas/view/*.phtml`.
- `frontend/misas/` — **existe** (`controller/` + `view/`) con index, iniciales, encargos, cuadricula, plan SACD/CTR, plan de misas, plantilla/importar, `horario_tarea`, `crear_nuevo_periodo`, **cambiar estado** y **`ver_misas_zona`**.
- `src/misas/` — en expansion:
  - `config/routes.php` y `infrastructure/ui/http/controllers/` activos para slices 3–10.
  - `application/*` con use cases de datos/mutaciones (iniciales, encargos, cuadricula, planes, desplegables, plantilla, horarios, status, zona_sacd).
  - `domain/`, `infrastructure/persistence/postgresql`, `config/dependencies.php` como antes.

## Plan de slices (un commit logico por slice)

1. **Slice 0 — Baseline y andamiaje.** Este documento + `src/misas/config/routes.php` + carpetas vacias `frontend/misas/controller|view` y `src/misas/infrastructure/ui/http/controllers`.
2. **Slice 1 — `misas_index`.** Index de navegacion (sin backend: pura UI + `Hash::link`).
3. ~~**Slice 2 — Seleccion de zona.** `seleccionar_zona`.~~ **DESCARTADO.** El controlador `apps/misas/controller/seleccionar_zona.php` es codigo muerto: renderizaba una vista inexistente (`seleccionar_zona_tipo.html.twig`), apuntaba a un destino inexistente (`apps/misas/controller/crear_plantilla.php`) y no tiene consumidores en el repo. Se ha renombrado a `zseleccionar_zona.php` + `zseleccionar_zona.html.twig` para preservarlo por si hiciera falta consulta/rollback. No se migra a `frontend/misas`.
4. **Slice 3 — Iniciales SACD.** `modificar_iniciales_sacd_zona`, `update_iniciales`, `ver_iniciales_zona`.
5. **Slice 4 — Encargos zona.** `modificar_encargos`, `ver_encargos_zona`, `update_encargos_zona`. `modificar_encargos_zona` queda descartado como codigo muerto (duplicado de `zver_plantilla_zona.php` que renderiza `ver_plantilla_zona.html.twig` inexistente, sin consumidores). `desplegable_encargos` se migra en el Slice 5 porque su unico consumidor es `ver_encargos_centros`.
6. **Slice 5 — Encargos centros.** `modificar_encargos_centros`, `ver_encargos_centros`, `update_encargos_centros`, `desplegable_encargos`. `desplegable_ctr` se descarta como codigo muerto (sin consumidores en el repo).
7. **Slice 6a — Cuadricula zona (cleanup + update).** `cuadricula_zona` (dead code, se renombra con `z`), `zver_plantilla_zona` (confirmado dead code, se anade documentacion) y `cuadricula_update` (endpoint de mutacion se migra a `src/misas`).
8. **Slice 6b — Cuadricula zona (pantallas).** `ver_cuadricula_zona` y `modificar_cuadricula_zona` → `CuadriculaZonaGridData` + `ver_cuadricula_zona_data` + `frontend/.../ver_cuadricula_zona.phtml`.
9. ~~**Slice 7 — Plan SACD / CTR.** `buscar_plan_sacd`, `ver_plan_sacd`, `desplegable_sacd`, `buscar_plan_ctr`, `ver_plan_ctr`, `imprimir_plan_ctr`.~~ **Completado:** frontend + endpoints JSON en `src/misas` (ver seccion mas abajo).
10. ~~**Slice 8 — Plan de misas.** `preparar_plan_de_misas`, `modificar_plan_de_misas`, `ver_plan_de_misas`, `crear_nuevo_periodo`.~~ **Completado:** frontend + `PlanDeMisasPantallaData` + `crear_nuevo_periodo` renderiza `ver_cuadricula_zona.phtml` (sin Twig en ese flujo).
11. ~~**Slice 9 — Plantillas y horarios.** `modificar_plantilla`, `importar_plantilla`, `horario_tarea`, `guardar_horario`, `zquitar_horario`, `zanadir_ctr_tarea`.~~ **Completado:** ver seccion Slice 9 mas abajo (`anadir_ctr_tarea.php` canonico; `zanadir_ctr_tarea` queda como alias al mismo endpoint).
12. ~~**Slice 10 — Status y datos zona.** `cambiar_status`, `nuevo_status`, `zona_sacd_datos_get`, `zona_sacd_datos_put`, `ver_misas_zona`.~~ **Completado:** ver seccion Slice 10.
13. ~~**Slice 11 — Limpieza y menus.** Actualizar `documentacion/Documentacion_Obix/menus.csv`, `proves/aux_metamenus.csv` y otros consumidores.~~ **Completado:** ver seccion Slice 11.

## URLs canonicas (destino)

Tras cada slice, la URL canonica para enlaces y menus nuevos sera:

| Pantalla | URL canonica nueva |
|----------|--------------------|
| Index del modulo | `frontend/misas/controller/misas_index.php` |
| Iniciales SACD | `frontend/misas/controller/modificar_iniciales_sacd_zona.php` |
| Encargos zona | `frontend/misas/controller/modificar_encargos.php` |
| Encargos centros | `frontend/misas/controller/modificar_encargos_centros.php` |
| Cuadricula zona | `frontend/misas/controller/ver_cuadricula_zona.php` / `modificar_cuadricula_zona.php` |
| Plan SACD | `frontend/misas/controller/buscar_plan_sacd.php` |
| Plan CTR | `frontend/misas/controller/buscar_plan_ctr.php` |
| Plan de misas | `frontend/misas/controller/preparar_plan_de_misas.php` / `modificar_plan_de_misas.php` / `ver_plan_de_misas.php` |
| Nuevo periodo (cuadricula) | `frontend/misas/controller/crear_nuevo_periodo.php` |
| Plantilla | `frontend/misas/controller/modificar_plantilla.php` |
| Importar plantilla (AJAX) | `frontend/misas/controller/importar_plantilla.php` |
| Modal horario encargo | `frontend/misas/controller/horario_tarea.php` |
| Cambiar status | `frontend/misas/controller/cambiar_status.php` |
| Ver misas zona | `frontend/misas/controller/ver_misas_zona.php` |

## Compatibilidad legacy

Los ficheros bajo `apps/misas/controller/` con el mismo nombre que el frontend equivalente pasaran a ser wrappers (`require __DIR__ . '/../../../frontend/misas/controller/<nombre>.php';`) para no romper:

- Menus o datos antiguos que aun guarden `apps/misas/controller/...` (los wrappers siguen resolviendo).
- Enlaces ya almacenados: nuevos usan `frontend/misas/controller/...` (p. ej. `public/ayuda/index.php`, CSVs de documentacion).
- Referencias cruzadas entre vistas del propio modulo y otros modulos (por ejemplo, `frontend/zonassacd/view/zona_sacd.phtml`).

## API backend (JSON, no HTML)

Los use cases se ubicaran en `src/misas/application/*` devolviendo arrays/strings serializables. Los controladores HTTP bajo `src/misas/infrastructure/ui/http/controllers/*.php` solo orquestan: leen input, llaman al use case y responden con `ContestarJson::enviar($error, $data)`. Las rutas se registran en `src/misas/config/routes.php` con prefijo `/src/misas/<nombre>`.

## Endpoints por accion (eliminar dispatchers `Qmod`/`salida`)

El modulo `misas` usa varios dispatchers que habra que partir por accion en cada slice:

- `desplegable_ctr.php`, `desplegable_sacd.php`, `desplegable_encargos.php` — posibles endpoints `_data` que devuelvan `{opciones, id, selected, blanco, val_blanco, action}` segun el contrato de desplegables de `refactor.md`. El `<select>` se monta en la vista.
- `zona_sacd_datos_get.php` / `zona_sacd_datos_put.php` — mantener split get/put y migrar a endpoints JSON.
- `cuadricula_update.php`, `update_iniciales.php`, `update_encargos_centros.php`, `update_encargos_zona.php` — mutaciones, deben responder `{success, mensaje}` aunque no haya payload.

## Slice 1 — `misas_index` (este PR/commit)

- **Entrada actual:** `apps/misas/controller/misas_index.php` (Twig) — index estatico con 10 enlaces `Hash::link(...)` a las pantallas del modulo, sin acceso a BD.
- **Destino:** `frontend/misas/controller/misas_index.php` + `frontend/misas/view/misas_index.phtml`.
- **Backend en `src`:** no aplica (pantalla pura UI, sin `PostRequest`).
- **Compatibilidad legacy:** `apps/misas/controller/misas_index.php` pasa a ser wrapper que hace `require` al frontend. Asi `public/ayuda/index.php`, menus en BD y `documentacion/Documentacion_Obix/menus.csv` siguen funcionando sin cambios.
- **Enlaces salientes del index:** en este slice se mantienen apuntando a `apps/misas/controller/<subpantalla>.php` porque esos ficheros aun contienen la logica original. En cada slice posterior, cuando `<subpantalla>` quede migrada y `apps/misas/controller/<subpantalla>.php` se convierta en wrapper, los enlaces seguiran funcionando. En el Slice 11 se reemplazaran por rutas `frontend/misas/controller/...`.

## Slice 4 — Encargos zona (este PR/commit)

- **Entradas actuales:**
  - `apps/misas/controller/modificar_encargos.php` (entry point, Twig): selectores `id_zona` y `orden` + carga `ver_encargos_zona.php` por AJAX.
  - `apps/misas/controller/ver_encargos_zona.php`: SlickGrid con los encargos `id_tipo_enc >= 8100` de la zona + modal de edicion/alta/borrado que postea a `update_encargos_zona.php`.
  - `apps/misas/controller/update_encargos_zona.php`: dispatcher con `Qque = modificar|nuevo|borrar`.
- **Dead code detectado:** `apps/misas/controller/modificar_encargos_zona.php` es una copia casi literal de `apps/misas/controller/zver_plantilla_zona.php` y renderiza `ver_plantilla_zona.html.twig` inexistente. No tiene consumidores. Se renombra a `zmodificar_encargos_zona.php`.
- **Destino:**
  - `frontend/misas/controller/modificar_encargos.php` + `view/modificar_encargos.phtml` (wrapper del entry point).
  - `frontend/misas/controller/ver_encargos_zona.php` + `view/ver_encargos_zona.phtml` (fragmento AJAX con SlickGrid y modal).
  - `src/misas/application/VerEncargosZonaData.php` — lectura (columnas + filas + desplegables del modal).
  - `src/misas/application/GuardarEncargoZona.php` — upsert de un `Encargo` de grupo `ZONAS_MISAS` (ramas `modificar` y `nuevo` del dispatcher legacy).
  - `src/misas/application/EliminarEncargoZona.php` — borrado.
  - `src/misas/infrastructure/ui/http/controllers/ver_encargos_zona_data.php`, `guardar_encargo_zona.php`, `eliminar_encargo_zona.php`.
- **Rutas nuevas:** `/src/misas/ver_encargos_zona_data`, `/src/misas/guardar_encargo_zona`, `/src/misas/eliminar_encargo_zona`.
- **Compatibilidad legacy:**
  - `apps/misas/controller/modificar_encargos.php` y `ver_encargos_zona.php` → wrappers que hacen `require` al frontend.
  - `apps/misas/controller/update_encargos_zona.php` → wrapper que despacha segun `Qque` a `/src/misas/guardar_encargo_zona` o `/src/misas/eliminar_encargo_zona` (aun se necesita mientras la vista legacy `ver_encargos_zona.html.twig` siga activa en otras zonas del repo). El wrapper se puede eliminar cuando grep en el repo no encuentre referencias.
  - Vistas Twig obsoletas (`modificar_encargos.html.twig`, `ver_encargos_zona.html.twig`) se renombran con prefijo `z` (coherente con `zseleccionar_zona`, `zver_iniciales_zona`, etc.).

## Slice 5 — Encargos centros (este PR/commit)

- **Entradas actuales:**
  - `apps/misas/controller/modificar_encargos_centros.php`: entry point con selector de zona (filtrado por rol `p-sacd`) + AJAX a `ver_encargos_centros`.
  - `apps/misas/controller/ver_encargos_centros.php`: SlickGrid de `EncargoCtr` (encargos visibles por cada centro de la zona) + modal con 3 desplegables (zona del encargo, encargo cargado dinamicamente, centro) y botones grabar/eliminar/cancelar.
  - `apps/misas/controller/update_encargos_centros.php`: dispatcher `Qque = nuevo|modificar|borrar` sobre `EncargoCtr`.
  - `apps/misas/controller/desplegable_encargos.php`: AJAX que devuelve HTML `<select>` de los encargos 8100+ de una zona. Se usa en el modal de `ver_encargos_centros.html.twig` cuando el usuario cambia la zona del encargo.
- **Dead code detectado:** `apps/misas/controller/desplegable_ctr.php` no tiene consumidores en `apps/`, `frontend/`, `src/`, `templates/` ni `*.js`. Se renombra a `zdesplegable_ctr.php`.
- **Destino:**
  - `frontend/misas/controller/modificar_encargos_centros.php` + `view/modificar_encargos_centros.phtml` (entry point).
  - `frontend/misas/controller/ver_encargos_centros.php` + `view/ver_encargos_centros.phtml` (fragmento AJAX con SlickGrid + modal).
  - `src/misas/application/ModificarEncargosCentrosData.php` — desplegable de zonas filtrado por rol.
  - `src/misas/application/VerEncargosCentrosData.php` — columnas + filas del grid + desplegables estaticos del modal (zonas, centros de la zona).
  - `src/misas/application/GuardarEncargoCentro.php` — upsert de `EncargoCtr` (ramas `nuevo` y `modificar`).
  - `src/misas/application/EliminarEncargoCentro.php` — borrado de `EncargoCtr`.
  - `src/misas/application/DesplegableEncargosData.php` — payload JSON para el desplegable dinamico de encargos segun zona (contrato `{id, opciones, selected, blanco, val_blanco, action}` de `refactor.md`).
  - `src/misas/infrastructure/ui/http/controllers/`: `modificar_encargos_centros_data.php`, `ver_encargos_centros_data.php`, `guardar_encargo_centro.php`, `eliminar_encargo_centro.php`, `desplegable_encargos.php`.
- **Rutas nuevas:** `/src/misas/modificar_encargos_centros_data`, `/src/misas/ver_encargos_centros_data`, `/src/misas/guardar_encargo_centro`, `/src/misas/eliminar_encargo_centro`, `/src/misas/desplegable_encargos`.
- **Compatibilidad legacy:**
  - `apps/misas/controller/modificar_encargos_centros.php` y `ver_encargos_centros.php` → wrappers `require` al frontend.
  - `apps/misas/controller/update_encargos_centros.php` → wrapper dispatcher que lee `Qque` y delega a `/src/misas/guardar_encargo_centro` o `/src/misas/eliminar_encargo_centro`.
  - `apps/misas/controller/desplegable_encargos.php` → wrapper `require` al endpoint backend. Se mantiene compatibilidad de forma: el nuevo endpoint devuelve JSON con payload estandar de desplegable; el unico consumidor vivo del contrato antiguo (HTML `<select>` bajo `data.desplegable`) era `ver_encargos_centros.html.twig`, que al migrar pasa a usar `fnjs_construir_desplegable`.
  - Vistas Twig obsoletas (`modificar_encargos_centros.html.twig`, `ver_encargos_centros.html.twig`) se renombran con prefijo `z`.

## Slice 6a — Cuadricula zona cleanup + update (este PR/commit)

- **Dead code detectado y renombrado:**
  - `apps/misas/controller/cuadricula_zona.php` (163 lineas): usaba fechas hardcoded `2023-12-01 → 2023-12-21`, renderizaba `ver_cuadricula_zona.html.twig` con placeholders `"xx $d"`. No tiene consumidores (grep sobre `apps/`, `frontend/`, `src/`, `templates/`, `*.js`, `menus.csv` → 0 hits). Se renombra a `zcuadricula_zona.php`.
  - `apps/misas/controller/zver_plantilla_zona.php` ya estaba renombrado en un slice anterior. En este slice se confirma definitivamente como dead code: la unica referencia `apps/misas/controller/ver_plantilla_zona.php` que contiene internamente es un `href` a una URL que ya no existe; no tiene consumidores PHP ni JS. Se anade la cabecera `z`-deadcode con la razon.
- **Backend nuevo:**
  - `src/misas/application/CuadriculaUpdate.php` — use case que agrupa la logica de 528 lineas de `apps/misas/controller/cuadricula_update.php`: upsert/delete de un `EncargoDia` para un dia concreto + recalculo del `meta` (color, texto, conteos de misas, disponibilidad del sacd anterior y del nuevo). Devuelve un array con `{error, meta}`.
  - `src/misas/infrastructure/ui/http/controllers/cuadricula_update.php` — orquestacion HTTP: `filter_input` → use case → `ContestarJson::enviar($error, ['meta' => ...])`.
- **Ruta nueva:** `/src/misas/cuadricula_update`.
- **Compatibilidad legacy:** `apps/misas/controller/cuadricula_update.php` delega en `CuadriculaUpdate` y emite el JSON historico `{success, meta}` en la raiz (sin `data` anidado) para no romper pantallas que aun no esten migradas.

## Slice 6b — Cuadricula zona (pantallas + payload grid) (este PR/commit)

- **Entradas actuales:** `apps/misas/controller/ver_cuadricula_zona.php` (AJAX desde preparar/ver plan, cambiar status, etc.) y `apps/misas/controller/modificar_cuadricula_zona.php` (AJAX desde modificar plan / plantilla). Ambas comparten la vista `ver_cuadricula_zona.html.twig` (~550 lineas SlickGrid + modal SACD).
- **Destino:**
  - `src/misas/application/CuadriculaZonaGridData.php` — fachada PSR-4.
  - `src/misas/application/cuadricula_zona_grid_data_build.php` + `src/misas/application/_cuadricula_zona_grid_fragment.php` — cuerpo procedural extraido de `modificar_cuadricula_zona.php` (espacio global, sin `namespace`, para no romper resolucion de `Class::class` del original).
  - `src/misas/infrastructure/ui/http/controllers/ver_cuadricula_zona_data.php` — `ContestarJson` con `columns_cuadricula`, `data_cuadricula`, parametros de contexto y `preference_warning`.
  - Ruta: `/src/misas/ver_cuadricula_zona_data`.
  - `frontend/misas/controller/ver_cuadricula_zona.php` y `modificar_cuadricula_zona.php` — `PostRequest` al endpoint + hashes: `cuadricula_update` y `desplegable_sacd` como URLs absolutas bajo `/src/misas/...` (`ConfigGlobal::getWeb()` donde aplica).
  - `frontend/misas/view/ver_cuadricula_zona.phtml` — migracion desde Twig; el AJAX de celda usa `/src/misas/cuadricula_update` y parsea `data.meta`.
- **Twig residual:** `apps/misas/view/ver_cuadricula_zona.html.twig` **se mantiene** solo para consumidores legacy que aun no migraron. **`crear_nuevo_periodo`**, **`nuevo_status`** (respuesta vacia + recarga AJAX) y **`ver_misas_zona`** usan ya `ver_cuadricula_zona.phtml`. Cuando no queden llamadas al twig, renombrarlo con prefijo `z`.
- **Compatibilidad legacy:** `apps/misas/controller/ver_cuadricula_zona.php` y `modificar_cuadricula_zona.php` → wrappers `require` al frontend.

## Slice 7 — Plan SACD / CTR (completado)

- **Entradas legacy:** `buscar_plan_sacd.php`, `ver_plan_sacd.php`, `buscar_plan_ctr.php`, `ver_plan_ctr.php`, `imprimir_plan_ctr.php`, `desplegable_sacd.php`.
- **Destino:**
  - Pantallas de busqueda: `frontend/misas/controller/buscar_plan_sacd.php` + `view/buscar_plan_sacd.phtml`; `buscar_plan_ctr.php` + `buscar_plan_ctr.phtml` (y `buscar_plan_un_ctr.phtml` si aplica al flujo CTR).
  - `ver_plan_sacd.php` y `ver_plan_ctr.php` — `PostRequest` al `*_data` correspondiente y respuesta HTML (`echo` del payload, sin vista PHTML propia).
  - `imprimir_plan_ctr.php` — mismo patron que `ver_plan_ctr` con documento minimo + `window.print()`.
  - Endpoints JSON: `/src/misas/buscar_plan_sacd_data`, `/src/misas/ver_plan_sacd_data`, `/src/misas/buscar_plan_ctr_data`, `/src/misas/ver_plan_ctr_data`, `/src/misas/desplegable_sacd`.
- **Compatibilidad legacy:** wrappers en `apps/misas/controller/` con `require` al frontend (mismo nombre de fichero).

## Slice 8 — Plan de misas + nuevo periodo (completado)

- **Entradas legacy:** `preparar_plan_de_misas`, `modificar_plan_de_misas`, `ver_plan_de_misas`, `crear_nuevo_periodo`.
- **Destino:**
  - `frontend/misas/controller/preparar_plan_de_misas.php`, `modificar_plan_de_misas.php`, `ver_plan_de_misas.php` — `PostRequest` a `/src/misas/plan_de_misas_pantalla_data` con `pantalla = preparar|modificar|ver`.
  - `src/misas/application/PlanDeMisasPantallaData.php` + `plan_de_misas_pantalla_data.php` — JSON (`zonas`, `orden`, permisos, tipos de plantilla en preparar, `periodo_td_html`, etc.).
  - `frontend/misas/controller/crear_nuevo_periodo.php` — ahora solo `PostRequest::getDataFromUrl('/src/misas/crear_nuevo_periodo_data', $post)` + construccion de hashes (`cuadricula_update`, `desplegable_sacd`, self) y render de `ver_cuadricula_zona.phtml`. Toda la logica de negocio (periodos, `EncargoDia`, plantilla, lectura/mutacion de repositorios) vive en backend.
  - `src/misas/application/CrearNuevoPeriodoData.php` (wrapper fino) + `crear_nuevo_periodo_data_build.php` (funcion global con `use`) + `_crear_nuevo_periodo_data_fragment.php` (fragmento procedural con los mismos `use`); controlador HTTP `crear_nuevo_periodo_data.php` responde `ContestarJson`.
- **Rutas nuevas:** `/src/misas/plan_de_misas_pantalla_data` y `/src/misas/crear_nuevo_periodo_data`.
- **Compatibilidad legacy:** wrappers en `apps/misas/controller/` para los cuatro entry points.

## Slice 9 — Plantillas y horarios (completado)

- **Pantalla plantilla:** `frontend/misas/controller/modificar_plantilla.php` + `modificar_plantilla.phtml` — `PostRequest` a `/src/misas/modificar_plantilla_data` (reutiliza `PlanDeMisasPantallaData` con `pantalla = modificar_plantilla`: zonas, orden, tipos + preferencia `ultima_plantilla`). AJAX de cuadricula: `modificar_cuadricula_zona.php`; importacion masiva: `importar_plantilla.php` (ahora solo `PostRequest` al endpoint `/src/misas/importar_plantilla_data`; la logica de negocio vive en backend).
- **Importar:** `frontend/misas/controller/importar_plantilla.php` (thin) + `src/misas/application/ImportarPlantillaData.php` (wrapper) + `importar_plantilla_data_build.php` (funcion global con `use`) + `_importar_plantilla_data_fragment.php` (fragmento procedural con los mismos `use`) + controlador HTTP `importar_plantilla_data.php`. Wrapper legacy `apps/misas/controller/importar_plantilla.php`.
- **Horario (modal):** `frontend/misas/controller/horario_tarea.php` + `horario_tarea.phtml`; los botones siguen llamando a `fnjs_guardar_horario` / `fnjs_quitar_horario` definidos en la vista contenedora cuando existan.
- **JSON (mutaciones, contrato historico `success`/`mensaje` plano):**
  - `/src/misas/guardar_horario` — `GuardarHorarioTarea` (`EncargoHorario`).
  - `/src/misas/quitar_horario` — `QuitarHorarioPlantilla` (anula `t_start`/`t_end` en `Plantilla`; corrige el bug de variables inexistentes en `zquitar_horario.php`).
  - `/src/misas/anadir_ctr_tarea` — `AnadirCtrTarea` (`que=anadir|quitar`; en `quitar` exige `id_item` por POST).
- **Wrappers legacy:** `apps/misas/controller/guardar_horario.php`, `quitar_horario.php`, `anadir_ctr_tarea.php`, `horario_tarea.php`, `modificar_plantilla.php`, `importar_plantilla.php`; `zquitar_horario.php` y `zanadir_ctr_tarea.php` redirigen al mismo controlador HTTP que `quitar_horario` / `anadir_ctr_tarea`.
- **Ajuste:** `lista_ctr.html.twig` envia `que=anadir` en el POST (antes faltaba y el `switch` caia en defecto).

## Slice 10 — Status y datos zona (completado)

- **Cambiar estado:** `frontend/misas/controller/cambiar_status.php` + `cambiar_status.phtml` — `PostRequest` a `/src/misas/cambiar_status_data` (`CambiarStatusPantallaData`: zonas, estados, orden, `periodo_td_html` con tres periodos). AJAX cuadricula: `ver_cuadricula_zona.php`; aplicar estado: `nuevo_status.php`. Corregidos parametros AJAX (sin espacios) y nombre del hash de cuadricula (`h_zona_status`).
- **Nuevo estado masivo:** `NuevoStatusPeriodo` + `frontend/misas/controller/nuevo_status.php` — actualiza `status` de `EncargoDia` en rango; respuesta HTML vacia o texto de error (el padre hace `alert` si hay cuerpo y luego recarga la cuadricula).
- **Ver misas zona:** `frontend/misas/controller/ver_misas_zona.php` — salida `ver_cuadricula_zona.phtml`; corregidos fechas (`d/m/Y` → ISO), bucles por dias (lista materializada de `DatePeriod`), y ramas `Qseleccion` usaban `$PersonaSacd` en vez de `$oPersonaSacd`; metadatos de celda incluyen `dia` y `tipo` para el grid. Revision posterior: el frontend ya solo llama a `/src/misas/ver_misas_zona_data` (`VerMisasZonaData` + `ver_misas_zona_data_build.php` + `_ver_misas_zona_data_fragment.php`) y construye los `Hash` locales; los repositorios (`PersonaSacd`, `Zona`, `EncargoDia`) viven en backend y el `exit(_("..."))` se convierte en `RuntimeException`.
- **Zona / SACD (modal en `frontend/zonassacd`):** rutas `/src/misas/zona_sacd_datos_get` y `/src/misas/zona_sacd_datos_put` (`ZonaSacdDatosGet` / `ZonaSacdDatosPut`; `dw*` como boolean via `FILTER_VALIDATE_BOOLEAN`). Wrappers `apps/misas/controller/zona_sacd_datos_*.php` sin cambiar las URLs que ya usa `zona_sacd.phtml`.
- **Compatibilidad legacy:** wrappers `apps` para `cambiar_status`, `nuevo_status`, `ver_misas_zona`.

## Slice 12 — Refactor post-migracion (simplificaciones internas)

Cambios centrados en **coherencia con `refactor.md` y reduccion de duplicacion** tras cerrar slices 1–11 (sin alterar comportamiento visible para el usuario):

- **URLs rotas corregidas.** `frontend/encargossacd/controller/horario_sacd_ver.php` + `frontend/zonassacd/view/zona_sacd.phtml` + `frontend/misas/controller/horario_tarea.php` apuntaban a `apps/misas/controller/...` (directorio eliminado). Ahora apuntan a endpoints canonicos `/src/misas/...`.
- **`horario_tarea` (lectura):** nueva `src/misas/application/HorarioTareaData` + endpoint `/src/misas/horario_tarea_data`. El frontend ya no instancia repositorios backend ni accede al contenedor DI.
- **`nuevo_status` movido a backend.** Se elimina `frontend/misas/controller/nuevo_status.php` (actuaba de endpoint HTML) y se crea `src/misas/infrastructure/ui/http/controllers/nuevo_status.php` (JSON). El frontend de `cambiar_status` adapta la llamada AJAX a JSON (`ContestarJson`).
- **Helper `IdNomJefeResolver`** (`src/misas/application/support`). Centraliza el calculo de `id_nom_jefe` + chequeo de permisos que estaba duplicado (13 lineas) en 5 clases (`CambiarStatusPantallaData`, `PlanDeMisasPantallaData`, `BuscarPlanCtrData`, `ModificarEncargosData`, `ModificarEncargosCentrosData`). Se elimina el `exit()` en capa application; ahora devuelve error estructurado (`RuntimeException` o array `error`) y el controlador HTTP responde con `ContestarJson`.
- **Fragmentos `_*_fragment.php` absorbidos.** Se fusionan en su `*_data_build.php` hermano los cuatro fragmentos procedurales (`ver_misas_zona`, `crear_nuevo_periodo`, `cuadricula_zona_grid`, `importar_plantilla`), eliminando los `use` duplicados y el salto adicional `require`.
- **Helper frontend `PeriodoTdHelper`** (`frontend/misas/support`). Sustituye ~20 lineas duplicadas de configuracion de `web\\PeriodoQue` en 6 controllers (`buscar_plan_ctr`, `buscar_plan_sacd`, `cambiar_status`, `preparar_plan_de_misas`, `modificar_plan_de_misas`, `ver_plan_de_misas`).
- **Helper frontend `CuadriculaZonaRenderer`** (`frontend/misas/support`). Encapsula la construccion de `Hash` (`cuadricula_update`, `desplegable_sacd`, self) y la composicion de `$a_campos` para `ver_cuadricula_zona.phtml`. Aplicado a los 4 controllers que compartian ~60 lineas: `ver_cuadricula_zona`, `modificar_cuadricula_zona`, `ver_misas_zona`, `crear_nuevo_periodo`.
- **`ver_cuadricula_zona_data.php` homogeneizado.** Cambio `if (error) enviar; unset; enviar` a `unset + enviar` unico, alineandose con el patron usado por el resto de `*_data.php`.
- **`VerPlanCtrData` / `VerPlanSacdData` ya no devuelven HTML.** Ambos use case se convierten en productores de datos (`columns`, `rows`, `legend`). El HTML pasa a `frontend/misas/view/ver_plan_ctr.phtml`, `ver_plan_sacd.phtml` e `imprimir_plan_ctr.phtml`. Con ello los tres controllers frontend dejan de hacer `echo $data['html']` y usan `ViewNewPhtml`, respetando la separacion application ↔ view de `refactor.md`. Se extrae ademas `PeriodoDateRange` (`src/misas/application/support`) para eliminar la duplicacion del calculo de rango entre ambos use cases.

## Slice 11 — Limpieza y menus (completado)

- **`documentacion/Documentacion_Obix/menus.csv`** y **`proves/aux_metamenus.csv`:** entrada del modulo misas → `frontend/misas/controller/misas_index.php`.
- **`public/ayuda/index.php`:** enlace `goMisas` → `frontend/misas/controller/misas_index.php`.
- **`frontend/misas/controller/misas_index.php`:** enlaces internos de encargos (zona/centro) e iniciales SACD pasan a rutas `frontend/...` (alineado con la tabla de URLs canonicas).
- **`documentacion/Documentacion_Obix/8. dre (personas, actividades y encargos).md`:** ruta del menu misas actualizada.

## Checklist de no-regresion minimo por slice

- Misma estructura de salida (ids de tabla, cabeceras, cardinalidad).
- Mismo comportamiento en ambito `rstgr` y no `rstgr`.
- Menus `fnjs_update_div(...)` siguen pintando el mismo contenido.
- `php -l` sin errores en ficheros tocados.
- Caso con datos y caso vacio probados cuando aplica.

## Cierre DI (junio 2026)

Migracion al patron de modulos cerrados (`certificados`, `dossiers`, `planning`):
constructor DI en application, `DependencyResolver::get()` en controllers HTTP,
`GlobalPdo::get()` en repos `Pg*`, 0 `$GLOBALS['container']` en todo `src/misas/`.

### Resultado del cierre DI

| Criterio | Estado |
|----------|--------|
| `$GLOBALS['container']` en `src/misas/` | **0** (antes **~80** en **~33** ficheros) |
| Controllers HTTP con `DependencyResolver::get()` | **33/33** |
| `application/` con constructor DI | **31** casos de uso + **4** wrappers `*_data_build` |
| Casos de uso en `config/dependencies.php` | **37** entradas `autowire()` (4 repos + 2 helpers + 31 use cases) |
| Pg repos con `GlobalPdo` | **4** repos (`oDBC`/`oDBC_Select`; Plantilla usa `oDBE`/`oDBE_Select`) |
| Tests `tests/unit/misas/` | **92 OK** |
| Tests `tests/integration/misas/` | **124 OK** |

### `src/misas/config/dependencies.php`

Registra 4 repositorios del modulo (`PgEncargoDia`, `PgEncargoCtr`, `PgInicialesSacd`,
`PgPlantilla`), `IdNomJefeResolver`, `InicialesSacdService` y los 31 casos de uso HTTP.
Repos cross-modulo (`Encargo*`, `ZonaSacd`, `PersonaSacd`, `Actividad*`, etc.) se resuelven
por autowire desde los `dependencies.php` de sus modulos.

### Application layer (constructor DI)

- Casos de uso de datos/mutacion con metodos de instancia `execute()` / `getData()` / `build()`.
- `IdNomJefeResolver`: instancia con `UsuarioRepository` + `RoleRepository` inyectados.
- `EncargosZona` (domain): constructor con `EncargoHorarioRepositoryInterface` +
  `EncargoRepositoryInterface`.
- Wrappers build (`CuadriculaZonaGridData`, `CrearNuevoPeriodoData`, `ImportarPlantillaData`,
  `VerMisasZonaData`): constructor DI + getters publicos; funciones `*_data_build.php` reciben
  el wrapper como 2.º parametro (`$self`).

### Repositorios `Pg*`

| Clase | PDO |
|-------|-----|
| `PgEncargoDiaRepository`, `PgEncargoCtrRepository`, `PgInicialesSacdRepository` | `GlobalPdo::get('oDBC')` / `GlobalPdo::get('oDBC_Select')` |
| `PgPlantillaRepository` | `GlobalPdo::get('oDBE')` / `GlobalPdo::get('oDBE_Select')` |

Guards `PDOStatement|false`, PHPDoc `list<Entity>` en colecciones, normalizacion de filas en
`datosById()`. `PgPlantillaRepository`: namespaces `src\shared\infrastructure\persistence\`.

### HTTP controllers

Los 33 controllers en `infrastructure/ui/http/controllers/` usan
`DependencyResolver::get()` (sin `::execute()` / `::getData()` / `::build()` estaticos).
Entrada POST via `input_int` / `input_string` / `input_string_list`.

### PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (inicio cierre DI) | `composer phpstan:file -- src/misas/` | **354** |
| 2026-06-06 (cierre DI) | `composer phpstan:file -- src/misas/` | **295** |
| 2026-06-06 (cierre PHPStan) | `composer phpstan:file -- src/misas/` | **0** |

Areas abordadas:

- **DI:** 0 `$GLOBALS['container']`; controllers con `DependencyResolver`; application con
  constructor DI; `dependencies.php` completo.
- **Repos `Pg*`:** `GlobalPdo`, guards PDO, PHPDoc retornos, `$stmt === false` antes de execute.
- **Wrappers build:** `MisasBuildInput`, `EncargoDiaTimeHelper`, getters corregidos; `$self`
  propagado a funciones internas; null guards en `DateTimeLocal`/`NullDateTimeLocal`.
- **Domain:** `EncargosZona`, entities (`EncargoCtr`, `EncargoDia`), VOs (`EncargoDiaStatus`).
- **Application:** `MisasBuildInput` en execute/getData; contratos `list<>` / `array<string,mixed>`;
  `Ubi::NewUbi()` con null check; `DBEsquema`/`DBEsquemaSelect` retornos `void`.
- **Controllers:** `MisasBuildInput` para POST; sin `@var` rotos en asignaciones de use case.

### Checklist de cierre

- [x] `$GLOBALS['container']` migrado a DI por constructor en `application/`
- [x] Controllers HTTP sin `$GLOBALS` directo (`DependencyResolver`)
- [x] `dependencies.php` con todos los use cases
- [x] Tests application pasan (`tests/unit/misas/`: 92 tests)
- [x] Tests integracion repos pasan (`tests/integration/misas/`: 124 tests)
- [x] PHPStan `src/misas/` en 0 (phpstan-nobaseline.neon)
