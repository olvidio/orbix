# Baseline migracion `actividadtarifas`

Este documento fija el estado legacy del modulo **antes** de migrarlo a
`frontend/` + `src/` siguiendo `refactor.md`, y el plan concreto de
reorganizacion.

## Resumen del modulo

Pantallas de mantenimiento de tarifas de actividades. Tres flujos:

1. **Tipos de tarifa** (`tarifa.php`): altas y ediciones del catalogo
   de `TipoTarifa` (letra, modo, observ; `sfsv` lo asigna el sistema
   segun el usuario). Lo ven/editan `adl`, `pr`, `calendario`.
2. **Tarifas por casa y año** (`tarifa_ubi.php`): asocia un `TipoTarifa`
   + importe + serie a una casa (`TarifaUbi`) para un año dado. Incluye
   un boton "copiar las del año anterior" (actualmente **roto**: llama
   a `$TarifaUbiRepository->copiar()` que no existe en la interfaz).
3. **Tarifa ↔ tipo actividad** (`tarifa_tipo_actividad.php`): gestiona
   la relacion n-a-n `RelacionTarifaTipoActividad` entre tipos de
   actividad (sfsv + asistentes + actividad + nom_tipo) y `TipoTarifa`.

Ademas, la rama `update_inc` de `tarifa_ajax.php` la usa
`apps/casas/controller/calendario_ubi_resumen_ajax.php` para grabar
masivamente tarifas incrementadas desde el estudio economico de casa.

## Estado inicial

Ficheros legacy en `apps/actividadtarifas/`:

| Fichero | LOC | Rol |
|---------|-----|-----|
| `controller/tarifa.php` | 33 | Entry point, renderiza `tarifa.phtml` |
| `controller/tarifa_ajax.php` | 394 | **Dispatcher** con 10+ ramas `switch($Qque)` |
| `controller/tarifa_tipo_actividad.php` | 43 | Entry point, renderiza `tarifa_tipo_actividad.phtml` |
| `controller/tarifa_tipo_actividad_ajax.php` | 118 | Dispatcher 3 ramas |
| `controller/tarifa_tipo_actividad_form.php` | 142 | Form Twig modificar/nuevo |
| `controller/tarifa_ubi.php` | 64 | Entry point, renderiza `tarifa_ubi.phtml` |
| `view/tarifa.phtml` | 86 | JS + shell HTML |
| `view/tarifa_ubi.phtml` | 123 | Form filtros + JS + shell |
| `view/tarifa_tipo_actividad.phtml` | 116 | JS + shell |
| `view/tarifa_tipo_actividad_form.html.twig` | 86 | Form modificar (Twig) |
| `view/tarifa_tipo_actividad_form_nuevo.html.twig` | 25 | Form nuevo (Twig) |
| `view/actividad_tipo_que.html.twig` | 2 | Wrapper para `ActividadTipo.getHtml()` |
| `view/_actividad_tipo.js.html.twig` | 125 | Helper JS para desplegables dinamicos |

`src/actividadtarifas/` ya tiene `application/` (vacio salvo
`domain/example.php`), `domain/` con entidades `TipoTarifa`,
`RelacionTarifaTipoActividad` y value objects `SerieId`, `TarifaId`,
`TarifaLetraCode`, `TarifaModoId`, `infrastructure/persistence/postgresql/`
con repositorios Pg y `config/dependencies.php`.

`TarifaUbi` y su repo viven en `src/ubis/` (no se mueve).

## Dispatcher `tarifa_ajax.php` — ramas

| `que` | Destino |
|-------|---------|
| `get` | Listado `TarifaUbi` por `id_ubi` + `year` (HTML `web\Lista`) |
| `copiar` | (intento) copiar tarifas del año anterior. **Bug**: metodo `copiar()` inexistente en `TarifaUbiRepositoryInterface`; cae por `break;` ausente en `get` |
| `form_tarifa_ubi` | HTML form modificar/nueva `TarifaUbi` |
| `update` | Guardar `TarifaUbi` |
| `borrar` | Eliminar `TarifaUbi` (de la rama "tar_ubi_eliminar" del form) |
| `tar_ubi_eliminar` | Eliminar `TarifaUbi` (de la rama "eliminar" del form) |
| `update_inc` | Update masivo `TarifaUbi.cantidad` desde `calendario_ubi_resumen` |
| `tarifas` | Listado `TipoTarifa` (HTML `web\Lista`) |
| `tar_form` | HTML form modificar/nuevo `TipoTarifa` |
| `tar_update` | Guardar `TipoTarifa` |
| `tar_eliminar` | Eliminar `TipoTarifa` |

Dispatcher `tarifa_tipo_actividad_ajax.php`:

| `que` | Destino |
|-------|---------|
| `get` | Listado `RelacionTarifaTipoActividad` (HTML `web\Lista`) |
| `update` | Guardar `RelacionTarifaTipoActividad` |
| `eliminar` | Eliminar `RelacionTarifaTipoActividad` |

## Consumidores externos

- `apps/casas/controller/calendario_ubi_resumen.php` pasa
  `url_tarifas = 'apps/actividadtarifas/controller/tarifa_ajax.php'` a
  `view/ubi_resumen.html.twig`; el form hace `POST` a esa URL con
  `que=update_inc`. Hay que apuntar a nuevo endpoint JSON.
- `frontend/pasarela/controller/nombre_form.php` (sin callers vivos salvo
  ficheros `.po`) reutiliza los mismos hashes; se le redirigen las URLs
  aunque esta muerto.
- `src/actividades/application/ActividadTipo.php:221` renderiza
  `actividad_tipo_que.html.twig` con
  `ViewNewTwig('apps/actividadtarifas/controller', ...)` cuando
  `para === 'tipoactiv-tarifas'`. Se mueve a
  `ViewNewTwig('actividadtarifas/controller', ...)` tras migrar las
  plantillas.
- Las plantillas `_actividad_tipo.js.html.twig` ya existen duplicadas
  en `frontend/actividades/view/`; usan el endpoint `url` = `/src/actividades/actividad_tipo_get` pasado por `ActividadTipo`.

## Violaciones `refactor.md` a corregir

- **Dispatcher** `*_ajax.php` con `switch($Qque)` (evitar).
- **Backend devuelve HTML**: `web\Lista::lista()`, `web\Desplegable::desplegable()`, `<form>` inline en `tarifa_ajax.php` y `tarifa_tipo_actividad_ajax.php`.
- **Backend devuelve texto plano**: `echo` de mensajes de error en
  mutaciones, sin `ContestarJson::enviar()`.
- **Frontend hardcodea URLs** `apps/actividadtarifas/...`.

## Plan de migracion

### 1. `src/actividadtarifas/application/`

Data builders (sin efectos secundarios, devuelven arrays para
`ContestarJson::enviar`):

- `TarifaUbiListaData`: lista `TarifaUbi` por `id_ubi`+`year` con
  datos para pintar la tabla (cabeceras, filas, permisos, botones
  "añadir"/"copiar").
- `TarifaUbiFormData`: datos del form modificar/nuevo
  `TarifaUbi` (cantidad, opciones `TipoTarifa`, opciones `SerieId`,
  letra actual, etc.).
- `TipoTarifaListaData`: catalogo `TipoTarifa`.
- `TipoTarifaFormData`: datos form modificar/nuevo `TipoTarifa`.
- `RelacionTarifaListaData`: catalogo relacion tarifa ↔ tipo actividad.
- `RelacionTarifaFormData`: datos form asociar `TipoTarifa` a un tipo
  de actividad (modo modificar o nuevo).

Mutaciones:

- `TarifaUbiUpdate`: guardar (`insert` o `update`) `TarifaUbi`.
- `TarifaUbiEliminar`: borrar `TarifaUbi`.
- `TarifaUbiUpdateInc`: update masivo `cantidad` desde el estudio
  economico (consume POST `inc_cantidad[]`).
- `TarifaUbiCopiar`: **no-op documentado** (la accion legacy llamaba a
  un metodo inexistente en el repositorio, el boton estaba roto; el
  frontend sigue existiendo para mantener paridad, pero el endpoint
  devuelve error tipado).
- `TipoTarifaUpdate`: guardar `TipoTarifa`.
- `TipoTarifaEliminar`: borrar `TipoTarifa`.
- `RelacionTarifaUpdate`: guardar `RelacionTarifaTipoActividad`.
- `RelacionTarifaEliminar`: borrar `RelacionTarifaTipoActividad`.

Services:

- `services/TipoTarifaDropdown`: opciones `id_tarifa => letra` por
  `sfsv`. Ya existe la lectura en
  `TipoTarifaRepository::getArrayTipoTarifas($sfsv)`; el helper se usa
  en `TarifaUbiFormData` y `RelacionTarifaFormData`.

### 2. HTTP controllers `src/actividadtarifas/infrastructure/ui/http/controllers/`

Uno por accion. Todos responden JSON via `ContestarJson::enviar(...)`.

### 3. Rutas `src/actividadtarifas/config/routes.php`

Un `/src/actividadtarifas/<accion>` por controlador HTTP.

### 4. Frontend `frontend/actividadtarifas/controller/` + `view/`

Entry points (`tarifa.php`, `tarifa_ubi.php`, `tarifa_tipo_actividad.php`)
+ controladores AJAX que devuelven HTML (`web\Lista`, forms) para
inyectar en el DOM (equivalente a `plazas_balance_dl.php`):

- `tarifa.php` / `tarifa.phtml` (shell + JS).
- `tarifa_lista.php` (AJAX HTML, lista `TipoTarifa`).
- `tarifa_form.php` / `tarifa_form.phtml` (AJAX HTML, form
  modificar/nuevo `TipoTarifa`).
- `tarifa_ubi.php` / `tarifa_ubi.phtml`.
- `tarifa_ubi_lista.php`.
- `tarifa_ubi_form.php` / `tarifa_ubi_form.phtml`.
- `tarifa_tipo_actividad.php` / `tarifa_tipo_actividad.phtml`.
- `tarifa_tipo_actividad_lista.php`.
- `tarifa_tipo_actividad_form.php` (+ plantillas Twig heredadas
  `tarifa_tipo_actividad_form.html.twig` y `_nuevo.html.twig`, ya
  que internamente consumen `ActividadTipo::getHtml()`).

Las mutaciones se llaman directamente al endpoint
`/src/actividadtarifas/<accion>` con `$.ajax({dataType: 'json'})` y se
procesa `success` / `mensaje` del estandar `ContestarJson`.

### 5. Consumidores externos

- `src/actividades/application/ActividadTipo.php`: cambiar Twig path
  `apps/actividadtarifas/controller` → `actividadtarifas/controller`
  (que `ViewNewTwig` resuelve bajo `frontend/`).
- `apps/casas/controller/calendario_ubi_resumen.php` +
  `apps/casas/view/ubi_resumen.html.twig`: apuntar `url_tarifas` al
  nuevo endpoint `/src/actividadtarifas/tarifa_ubi_update_inc` y
  ajustar AJAX a `dataType: 'json'` / `ContestarJson`.
- `frontend/pasarela/controller/nombre_form.php`: refrescar URLs de los
  hashes al nuevo endpoint, aunque no se use.
- `log/menus/comun.sql`, `proves/aux_metamenus.csv`,
  `documentacion/Documentacion_Obix/menus.csv`: sustituir
  `apps/actividadtarifas/...` por `frontend/actividadtarifas/...`.
- Markdown de la documentacion (`7. Adl.md`, `8. dre …`, `9. Exterior.md`,
  `20. Calendario.md`): actualizar los enlaces.

### 6. Limpieza

- Eliminar `apps/actividadtarifas/` completo.
- `php -l` en todos los ficheros nuevos/tocados.

## Cierre DI (2026-06-06)

Los 14 controllers en `infrastructure/ui/http/controllers/` usan
`DependencyResolver::get()` (sin `::execute()` estático).
Entrada POST via `input_string` / `input_int`; mutaciones con `HashB`
via `input_string` sobre el contexto abierto.

### Resultado del cierre DI

| Criterio | Estado |
|----------|--------|
| `$GLOBALS['container']` en `src/actividadtarifas/` | **0** |
| Controllers HTTP con `DependencyResolver::get()` | **14/14** |
| `application/` con constructor DI | **15** clases (14 use cases + `TipoTarifaDropdown`) |
| Pg repos con `GlobalPdo::get()` | **2/2** |
| Casos de uso en `config/dependencies.php` | **17** entradas `autowire()` (2 repos + service + 14 use cases) |
| Tests `tests/unit/actividadtarifas/` | **59 OK** |

### `src/actividadtarifas/config/dependencies.php`

Registra repositorios del módulo, `TipoTarifaDropdown` y todos los use
cases: `RelacionTarifaEliminar`, `RelacionTarifaFormData`,
`RelacionTarifaListaData`, `RelacionTarifaUpdate`, `TarifaUbiCopiar`,
`TarifaUbiEliminar`, `TarifaUbiFormData`, `TarifaUbiListaData`,
`TarifaUbiUpdate`, `TarifaUbiUpdateInc`, `TipoTarifaEliminar`,
`TipoTarifaFormData`, `TipoTarifaListaData`, `TipoTarifaUpdate`.

`TarifaUbi` y su repositorio permanecen en `src/ubis/` (inyectados por
autowire en los use cases que los necesitan).

### PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (inicio) | `composer phpstan:file -- src/actividadtarifas/` | **123** |
| 2026-06-06 (cierre) | `composer phpstan:file -- src/actividadtarifas/` | **0** |

Áreas abordadas en el cierre (123 → 0):

- Application: constructor DI, `input_*`, `instanceof XPermisos` para
  permisos de oficina, tipos de retorno en payloads JSON.
- Repos `PgTipoTarifaRepository`, `PgRelacionTarifaTipoActividadRepository`:
  guards PDO, tipos de retorno, `GlobalPdo`.
- Domain: entidades/VOs/contratos con PHPDoc `array<string,mixed>`,
  setters VO sin null en propiedades no-nullables.
- HTTP controllers: `DependencyResolver::get()` + helpers `input_*`.

### Deuda post-refactor

#### Completado

- [x] 0 `$GLOBALS['container']` en todo `src/actividadtarifas/`
- [x] Todos los controllers HTTP via `DependencyResolver`
- [x] Casos de uso con constructor DI
- [x] `dependencies.php` con todos los use cases
- [x] Tests `tests/unit/actividadtarifas/`: 59 tests
- [x] PHPStan `src/actividadtarifas/` en 0 (phpstan-nobaseline.neon)
- [x] Frontend `frontend/actividadtarifas/`: 0 `use src\...`

#### Pendiente

- [ ] Reimplementar `TarifaUbiCopiar` (acción legacy rota; endpoint devuelve
  mensaje tipado de deuda).

### Checklist de cierre

Ver [`REFACTOR_INDICE.md`](REFACTOR_INDICE.md#checklist-cerrar-un-módulo).

- [x] `$GLOBALS['container']` migrado a DI por constructor en `application/`
- [x] Controllers HTTP sin `$GLOBALS` directo (`DependencyResolver`)
- [x] `dependencies.php` con todos los use cases
- [x] Tests existentes pasan (`tests/unit/actividadtarifas/`: 59 tests)
- [x] PHPStan `src/actividadtarifas/` en 0 (phpstan-nobaseline.neon)
