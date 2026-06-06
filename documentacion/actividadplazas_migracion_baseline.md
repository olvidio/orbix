# Actividadplazas — baseline de migracion

Slice unico que mueve el modulo `actividadplazas` desde `apps/` a
`frontend/` + `src/` siguiendo el patron de `refactor.md` (mismo
enfoque que `actividadessacd` y `actividadescentro`).

## Resumen

`actividadplazas` gestiona las plazas entre delegaciones (grupo de
estudios) para cada actividad. Cuatro pantallas principales:

1. **Gestion de plazas** (`gestion_plazas`) — cuadro editable con las
   plazas totales de la actividad y las plazas concedidas / pedidas
   entre dl del grupo. Edicion inline con `TablaEditable` (SlickGrid).
2. **Balance de plazas** (`plazas_balance_que` + `plazas_balance_dl`)
   — filtro de dl y grid comparativo A vs B (concedidas + libres
   por dl).
3. **Incorporar peticiones** (`incorporar_peticion`) — accion que
   convierte la primera peticion de cada numerario/agregado en
   asistencia con plaza "asignada" / "pedida" si la actividad es de
   otra dl.
4. **Peticiones de una persona** (`peticiones_activ`) — listado
   editable (+/- desplegables) de actividades que una persona quiere
   como peticion de plaza. Viene desde `personas_select`.
5. **Resumen de plazas de una actividad** (`resumen_plazas`) —
   tabla con calendario / cedidas / conseguidas / ocupadas por dl +
   mutacion "ceder" de plazas a otra dl. Viene desde la pantalla
   de actividades (`actividades.js`).

El flujo legacy mezcla controllers UI, dispatchers `*_ajax`
(mutaciones y devolucion de desplegables HTML inline) y vistas
Twig/phtml bajo `apps/`.

## Estado inicial (antes del refactor)

```
apps/actividadplazas/
├── controller/
│   ├── gestion_plazas.php          (279 LOC) pantalla principal (filtros + TablaEditable)
│   ├── gestion_plazas_ajax.php     (103 LOC) dispatcher 2 ramas: update (text) + lst_propietarios (HTML)
│   ├── incorporar_peticion.php     (205 LOC) UI + mutacion (incorpora peticiones -> asistencias)
│   ├── peticiones_activ.php        (225 LOC) pantalla con DesplegableArray de actividades
│   ├── peticiones_activ_ajax.php   ( 59 LOC) dispatcher 2 ramas: update (text) + borrar (text)
│   ├── plazas_balance_dl.php       (219 LOC) grid comparativo A vs B
│   ├── plazas_balance_que.php      ( 65 LOC) filtro dl (Desplegable) + URL a plazas_balance_dl
│   ├── resumen_plazas.php          (104 LOC) resumen por dl (tabla HTML inline en phtml)
│   └── resumen_plazas_update.php   ( 76 LOC) dispatcher 1 rama (ceder) con JSON ContestarJson::enviar
└── view/
    ├── gestion_plazas.phtml        ( 21 LOC) form + mostrar_tabla
    ├── peticiones_activ.phtml      ( 67 LOC) form + JS con .one('submit') y action legacy
    ├── plazas_balance_dl.phtml     (  7 LOC) grid comparativo
    ├── plazas_balance_que.phtml    ( 28 LOC) select dl + JS `fnjs_comparativa` (ajax HTML)
    └── resumen_plazas.phtml        (140 LOC) tabla HTML inline + form "ceder"

src/actividadplazas/
├── application/services/
│   └── ResumenPlazasService.php   (583 LOC) servicio de dominio (se mantiene; se expondra via use cases)
├── config/dependencies.php         (repos + service)
├── domain/{contracts,entity,value_objects}/  (sin cambios)
└── infrastructure/persistence/postgresql/    (sin cambios)
    └── ui/http/controllers/        (vacio al inicio)

frontend/actividadplazas/            (no existe)
```

No existe `src/actividadplazas/config/routes.php` ni endpoints
`/src/actividadplazas/*`.

## Consumidores externos identificados

- `apps/asistentes/controller/form_3101.php` y `form_1301.php`
  firman URL `apps/actividadplazas/controller/gestion_plazas_ajax.php`
  para consumir la rama `lst_propietarios` (devolver `<select>`).
  Vistas en `apps/asistentes/view/form_3101.phtml` y `form_1301.phtml`
  hacen `$.ajax` con `que=lst_propietarios` e inyectan el HTML en
  `#lst_propietarios`.
- `frontend/personas/view/personas_select.phtml` (boton
  "peticiones activ") hace `form.action = apps/actividadplazas/controller/peticiones_activ.php`.
- `frontend/actividades/view/actividades.js` (caso `plazas`) hace
  `form.action = apps/actividadplazas/controller/resumen_plazas.php`.
- Menus: `log/menus/comun.sql`, `proves/aux_metamenus.csv`,
  `documentacion/Documentacion_Obix/menus.csv` apuntan a 3
  entradas (gestion_plazas, incorporar_peticion, plazas_balance_que).
- Docs: `documentacion/Documentacion_Obix/2.` (numerarios),
  `3.` (agregados), `10.` (supernumerarios), `12.` (estudios),
  `actividadplazas/mapa_*.md`, `personas/mapa_personas_select.md`.

## Ramas de los dispatchers

| Dispatcher | Rama | Tipo | Salida legacy | Destino refactor |
|------------|------|------|---------------|------------------|
| `gestion_plazas_ajax` | `update` | mutacion | texto plano | `/src/actividadplazas/gestion_plazas_update` (JSON `ContestarJson::enviar`; `TablaEditable` consume JSON) |
| `gestion_plazas_ajax` | `lst_propietarios` | lectura (Desplegable HTML) | HTML del `<select>` | `/src/actividadplazas/posibles_propietarios_data` (JSON payload desplegable) |
| `peticiones_activ_ajax` | `update` | mutacion | texto plano (`$oPosicion->go_atras(1)`) | `/src/actividadplazas/peticiones_guardar` (JSON) |
| `peticiones_activ_ajax` | `borrar` | mutacion | texto plano (vacio) | `/src/actividadplazas/peticiones_eliminar` (JSON) |
| `resumen_plazas_update` | `ceder` | mutacion | JSON `ContestarJson::enviar` | `/src/actividadplazas/plazas_ceder` (JSON, ya cumple) |

## Violaciones de `refactor.md` detectadas

1. **Dispatchers `*_ajax.php`** con `switch($Qque)` que mezclan
   mutaciones y lecturas.
2. **`echo` de HTML** desde el backend (`lst_propietarios` monta un
   `<select>` con `web\Desplegable`).
3. **Mutaciones sin `ContestarJson`**: `update` de `gestion_plazas_ajax`
   y `peticiones_activ_ajax` responden con texto plano no JSON.
4. **Controladores en `apps/`** con UI + negocio mezclados
   (`incorporar_peticion.php` hace `echo` de texto y `<script>` en el
   mismo fichero).
5. **Vistas en `apps/actividadplazas/view/`** con scripts JS que hacen
   `form.action = "apps/actividadplazas/..."` hardcodeado.
6. **`die()` / `exit()`** en caso de error (p. ej.
   `plazas_balance_dl.php` si no hay dl).
7. **Funcion global + `global`** en `plazas_balance_dl.php`
   (`function PlazasAB_por_actividad`).
8. **URLs `apps/actividadplazas/...`** firmadas desde
   `apps/asistentes/*`, `frontend/personas/*`, `frontend/actividades/*`.

## Plan de migracion (un slice)

### 1. `src/actividadplazas/application/` — use cases

- **`GestionPlazasData`** — data builder de la pantalla principal:
  calcula grupo de estudios, lista actividades del periodo, monta
  array `{a_cabeceras, a_valores, a_grupo, extendida, id_tipo_activ}`.
- **`GestionPlazasUpdate`** — mutacion texto plano (igual contrato
  que `centros_update`) para la edicion inline de celdas en la
  `TablaEditable`. Reemplaza la rama `update` de `gestion_plazas_ajax`
  y tambien la de `resumen_plazas_update`/`plazas_balance_dl` (usa la
  misma URL para todas las tablas editables).
- **`PosiblesPropietariosData`** — devuelve array
  `{id, opciones, selected, blanco, val_blanco}` (contrato payload de
  desplegable, ver `refactor.md`). Reemplaza la rama `lst_propietarios`.
- **`PeticionesActivData`** — data builder de la pantalla
  `peticiones_activ`: resuelve tipo de actividad (ca, cv, crt),
  periodo, lista de actividades candidatas y peticiones actuales;
  limpia peticiones antiguas que ya no estan en la lista. Devuelve
  `{ap_nom, sid_activ, opciones, txt_guardar}`.
- **`PeticionesGuardar`** — mutacion JSON: borra todas las
  peticiones del `{id_nom, tipo}` y crea las nuevas en orden.
- **`PeticionesEliminar`** — mutacion JSON: borra todas las
  peticiones de `{id_nom, tipo}`.
- **`PeticionesIncorporar`** — accion de negocio (no va a endpoint
  directo, se invoca desde el controlador HTTP). Construye la lista
  de actividades posibles y crea asistencias con plaza pedida /
  asignada para cada petición de orden=1 que no tenga ya asistencia.
  Devuelve `{incorporadas: int, mensaje_final: string}`.
- **`PlazasBalanceData`** — data builder del grid comparativo A vs
  B. Elimina la funcion suelta + `global` del legacy.
  Devuelve `{dlA, dlB, concedidasA2B, concedidasB2A, a_cabeceras, a_valores}`.
- **`ResumenPlazasData`** — data builder del resumen por actividad.
  Reempaqueta los datos que hoy construye `resumen_plazas.php` en un
  array neutro (`{publicado, otra_dl, a_plazas, totales, dl_opciones}`).
  Usa internamente `ResumenPlazasService`.
- **`PlazasCeder`** — mutacion JSON (ya existia como
  `resumen_plazas_update`, se renombra a use case). Actualiza el
  array `cedidas` de `ActividadPlazasDl`.

### 2. Contratos HTTP en `src/actividadplazas/infrastructure/ui/http/controllers/`

Todos devuelven JSON con `ContestarJson::enviar(...)`. Se ha
actualizado `web\TablaEditable::getUpdateFunction()` para consumir
la respuesta con `dataType: 'json'` y asi todos los endpoints siguen
el mismo contrato (`{success, mensaje, data}`).

- `/src/actividadplazas/gestion_plazas_data` → JSON
  `{success, mensaje, data:{a_cabeceras, a_valores, a_grupo, ...}}`.
- `/src/actividadplazas/gestion_plazas_update` → JSON
  `{success, mensaje, data:'ok'}`.
- `/src/actividadplazas/posibles_propietarios_data` → JSON con el
  payload estandar de desplegable.
- `/src/actividadplazas/peticiones_activ_data` → JSON data builder.
- `/src/actividadplazas/peticiones_guardar` → JSON `{success, mensaje, data:'ok'}`.
- `/src/actividadplazas/peticiones_eliminar` → JSON `{success, mensaje, data:'ok'}`.
- `/src/actividadplazas/peticiones_incorporar` → JSON
  `{success, mensaje, data:{incorporadas, mensaje_final}}`.
- `/src/actividadplazas/plazas_balance_data` → JSON data builder.
- `/src/actividadplazas/resumen_plazas_data` → JSON data builder.
- `/src/actividadplazas/plazas_ceder` → JSON (ya existia).

### 3. `src/actividadplazas/config/routes.php` — registrar todas.

### 4. `frontend/actividadplazas/controller/`

- `gestion_plazas.php` — filtros (`PeriodoQue`), consulta
  `gestion_plazas_data` via `PostRequest`, monta `web\TablaEditable`
  con los datos devueltos, firma la URL `gestion_plazas_update` y la
  pasa al `TablaEditable`. Renderiza via `ViewNewPhtml`.
- `peticiones_activ.php` — consulta `peticiones_activ_data`,
  construye `web\DesplegableArray` con las opciones, firma URLs para
  guardar y borrar.
- `incorporar_peticion.php` — muestra pagina de confirmacion con
  boton "continuar" que invoca endpoint via AJAX y pinta el mensaje
  (patron del slice 2 de sacd).
- `plazas_balance_que.php` — filtro dl (desplegable) + div
  `#comparativa` que se llena via AJAX usando
  `plazas_balance_data`.
- `resumen_plazas.php` — consulta `resumen_plazas_data`, monta
  desplegable dl, firma URL `plazas_ceder`.

### 5. `frontend/actividadplazas/view/` — `.phtml`:

- `gestion_plazas.phtml` — form + `mostrar_tabla()` (igual que el
  legacy). JS `fnjs_buscar` apunta al propio controller frontend.
- `peticiones_activ.phtml` — form + DesplegableArray. JS
  `fnjs_guardar` y `fnjs_borrar` usan `$.ajax` + `dataType: 'json'`.
- `incorporar_peticion.phtml` — pagina simple con mensaje + boton
  "continuar" (ajax POST al endpoint `peticiones_incorporar`).
- `plazas_balance_que.phtml` — desplegable dl + JS que pinta tabla
  a partir del JSON.
- `plazas_balance_dl.phtml` — (opcional) vista reutilizada por JSON
  solo si conviene renderizar server-side; sino, el JS de
  `plazas_balance_que.phtml` la construye cliente.
- `resumen_plazas.phtml` — tabla resumen + form "ceder" (igual
  estructura que el legacy, pero sin HTML generado en server side,
  usando `a_plazas` del endpoint).

### 6. Actualizacion de consumidores externos

- `apps/asistentes/controller/form_3101.php` y `form_1301.php`:
  cambiar `$url_ajax` a `.../src/actividadplazas/posibles_propietarios_data`,
  ajustar el JS de las vistas a construir el `<select>` con el
  helper `fnjs_construir_desplegable` (payload JSON).
- `frontend/personas/view/personas_select.phtml`: cambiar
  `action = "apps/actividadplazas/controller/peticiones_activ.php"`
  a `frontend/actividadplazas/controller/peticiones_activ.php`.
- `frontend/actividades/view/actividades.js`: cambiar
  `action = "apps/actividadplazas/controller/resumen_plazas.php"`
  a `frontend/actividadplazas/controller/resumen_plazas.php`.

### 7. Eliminar legacy

Borrar `apps/actividadplazas/` entero (como se hizo en sacd/ctr tras
migrar).

### 8. Actualizar menus y docs

- `log/menus/comun.sql`, `proves/aux_metamenus.csv`,
  `documentacion/Documentacion_Obix/menus.csv`: rutas → `frontend/actividadplazas/controller/...`.
- `documentacion/Documentacion_Obix/2. Gestión de Numerarios (n).md`,
  `3. Gestión de Agregados (a).md`,
  `10. Supernumerarios (s y sg).md`,
  `12. Estudios y STGR.md`,
  `actividadplazas/mapa_*.md`,
  `personas/mapa_personas_select.md`.

### 9. Validacion

- `php -l` en todos los ficheros nuevos/tocados.
- `rg "apps/actividadplazas"` → 0 (salvo `.po` de traducciones).
- Probar manualmente una pantalla por flujo: gestion, balance,
  peticiones, incorporar, resumen.

## Desviaciones respecto al legacy documentadas

- El dispatcher `gestion_plazas_ajax` se divide en dos endpoints:
  `gestion_plazas_update` (JSON con contrato estandar, ver nota
  sobre `TablaEditable` abajo) y `posibles_propietarios_data`
  (JSON payload de desplegable).
- `lst_propietarios` dejaba el `<select>` HTML en el backend; ahora
  devuelve array `opciones` + metadatos y el `<select>` se monta en
  cliente con `fnjs_construir_desplegable`.
- `peticiones_activ_ajax` rama `update` hacia `echo $oPosicion->go_atras(1)`
  en texto plano; ahora responde JSON `{success, mensaje}` y la
  vista maneja la redireccion con el helper JS.
- `incorporar_peticion.php` devolvia HTML + `<script>` inline; se
  parte en vista (`.phtml`) + endpoint JSON de mutacion.
- `plazas_balance_dl.php` tenia una funcion libre + `global`; se
  convierte en metodos privados del use case `PlazasBalanceData`.
- Se elimina `exit()`/`die()` cuando falta parametro; los endpoints
  devuelven mensaje en el `mensaje` del JSON.
- Se mantiene `ResumenPlazasService` en `application/services/` ya
  que actua como helper compartido entre varios use cases. Se
  ajusta `setArrayDl()` para no pisar `a_dele`/`a_id_dele` (bug
  local: las variables se declaraban en scope de metodo y se
  asignaban a las propiedades al final; esta parte se mantiene para
  no ampliar el alcance).

## Cambios transversales aplicados en este slice

Se eliminaron las dos desviaciones documentadas inicialmente:

- `web\TablaEditable::getUpdateFunction()` pasa a consumir la
  respuesta con `dataType: 'json'` y contrato `{success, mensaje, data}`.
  Esto permite que `gestion_plazas_update` responda JSON igual que el
  resto de endpoints de `src/`. Como efecto colateral se adapto
  `apps/casas/controller/prevision_asistentes_ajax.php` (unico otro
  consumidor de `TablaEditable`) para que tambien responda JSON con
  `ContestarJson::enviar(...)`.
- `ResumenPlazasService::getPosiblesPropietariosOpciones()` es el
  metodo de datos (devuelve `array<string,string>`) usado por el
  use case `PosiblesPropietariosData`. `getPosiblesPropietarios()`
  pasa a ser un wrapper delgado que envuelve esas opciones en un
  `web\Desplegable` unicamente para los callers legacy en
  `apps/asistentes/controller/form_{1301,3101}.php` (que siguen
  renderizando el `<select>` inicial en servidor mientras no se
  migran al frontend).

## Cierre DI (2026-06-06)

Los 11 controllers en `infrastructure/ui/http/controllers/` usan
`DependencyResolver::get()` (sin `::execute()` estático).
Entrada POST via `input_string` / `input_int` / `input_string_list`.

### Resultado del cierre DI

| Criterio | Estado |
|----------|--------|
| `$GLOBALS['container']` en `src/actividadplazas/` | **0** |
| Controllers HTTP con `DependencyResolver::get()` | **11/11** |
| `application/` con constructor DI | **12** clases (11 use cases + `PlazasDlEdicion`) |
| Pg repos con `GlobalPdo::get()` | **3/3** |
| Casos de uso en `config/dependencies.php` | **16** entradas `autowire()` (3 repos + service + 12 use cases) |
| Tests `tests/unit/actividadplazas/` | **59 OK** |

### `src/actividadplazas/config/dependencies.php`

Registra repositorios del módulo, `ResumenPlazasService` (con deps
externas de actividades/ubis/asistentes) y todos los use cases:
`GestionPlazasData`, `GestionPlazasUpdate`, `PeticionesActivData`,
`PeticionesGuardar`, `PeticionesEliminar`, `PeticionesIncorporar`,
`PlazasBalanceData`, `PlazasBalanceQueData`, `PlazasCeder`,
`PlazasDlEdicion`, `PosiblesPropietariosData`, `ResumenPlazasData`.

`PlazasCalendarioMensaje` permanece como helper estático (mensaje de
ayuda); no requiere registro DI.

### PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (inicio) | `composer phpstan:file -- src/actividadplazas/` | **167** |
| 2026-06-06 (cierre) | `composer phpstan:file -- src/actividadplazas/` | **0** |

Áreas abordadas en el cierre (167 → 0):

- Application: `input_*` helpers, `ConfigSnapshot` para `oConfig`,
  tipos de retorno en payloads JSON, `PlazasDlEdicion` sin guards
  `array|false` obsoletos.
- `ResumenPlazasService`: propiedades tipadas, corrección bug
  `setArrayDl()`, null-checks en `getResumen()` / `getPlazasPropias()`.
- Domain: entidades/VOs/contratos con PHPDoc `array<string,mixed>`,
  setters VO sin null en propiedades no-nullables.
- Repos `PgActividadPlazasRepository`, `PgPlazaPeticionRepository`:
  guards PDO, tipos de retorno, `GlobalPdo`.
- HTTP controllers: `DependencyResolver::get()` + helpers `input_*`.

### Deuda post-refactor

#### Completado

- [x] 0 `$GLOBALS['container']` en todo `src/actividadplazas/`
- [x] Todos los controllers HTTP via `DependencyResolver`
- [x] Casos de uso con constructor DI
- [x] `dependencies.php` con todos los use cases
- [x] Tests `tests/unit/actividadplazas/`: 59 tests
- [x] PHPStan `src/actividadplazas/` en 0 (phpstan-nobaseline.neon)

#### Pendiente

- [ ] 2 controladores frontend con `use src\` (ver
  [`frontend_pendiente_refactor_src.md`](frontend_pendiente_refactor_src.md))

### Checklist de cierre

Ver [`REFACTOR_INDICE.md`](REFACTOR_INDICE.md#checklist-cerrar-un-módulo).

- [x] `$GLOBALS['container']` migrado a DI por constructor en `application/`
- [x] Controllers HTTP sin `$GLOBALS` directo (`DependencyResolver`)
- [x] `dependencies.php` con todos los use cases
- [x] Tests existentes pasan (`tests/unit/actividadplazas/`: 59 tests)
- [x] PHPStan `src/actividadplazas/` en 0 (phpstan-nobaseline.neon)
