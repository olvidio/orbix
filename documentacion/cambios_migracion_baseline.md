# Baseline de migracion del modulo `cambios`

Resume, antes de mover codigo, que pantallas viven en `apps/cambios/` y cual
es su forma previa (parametros POST, salida, dependencias). El destino es
el patron canonico `frontend/<modulo>/controller` + `src/<modulo>/application`
+ `src/<modulo>/infrastructure/ui/http/controllers` + `src/<modulo>/config/routes.php`,
siguiendo `refactor.md` en la misma linea que `cartaspresentacion` y `notas`.

Al empezar, `src/cambios/` ya tiene dominio completo (`entity`, `value_objects`,
`contracts`) e infraestructura PostgreSQL para `Cambio`, `CambioAnotado`,
`CambioDl`, `CambioUsuario`, `CambioUsuarioObjetoPref`, `CambioUsuarioPropiedadPref`,
mas `config/dependencies.php`. **No hay** `application/`,
`infrastructure/ui/http/controllers/`, ni `config/routes.php`. Hay que crearlos.

## Estado final (abril 2026)

Las tres pantallas de usuario estan migradas. Los dos scripts CLI/cron
(`avisos_generar_tabla`, `avisos_generar_mails`) tambien se han migrado en
una pasada posterior: driver bajo `src/cambios/infrastructure/cli/` y use
case en `src/cambios/application/`; los ficheros antiguos en `apps/cambios/
controller/` quedan como wrappers deprecados con `require` al driver.

### Rutas JSON resultantes en `src/cambios/`

- `/src/cambios/usuario_form_avisos_data` — datos del listado por usuario.
- `/src/cambios/avisos_generar_lista_data` — datos del listado de avisos.
- `/src/cambios/cambio_usuario_eliminar` — borrado por seleccion.
- `/src/cambios/cambio_usuario_eliminar_hasta_fecha` — borrado por fecha.
- `/src/cambios/usuario_avisos_pref_form_data` — formulario principal.
- `/src/cambios/cambio_usuario_propiedad_pref_item_data` — condicion (lookup).
- `/src/cambios/cambio_usuario_objeto_pref_propiedades_data` — propiedades.
- `/src/cambios/cambio_usuario_objeto_pref_fases_data` — fases (dependiente de `procesos`).
- `/src/cambios/cambio_usuario_propiedad_pref_preview` — preview sin persistir.
- `/src/cambios/cambio_usuario_objeto_pref_guardar` — mutacion `guardar_objeto`.
- `/src/cambios/cambio_usuario_objeto_pref_eliminar` — mutacion `eliminar`.
- `/src/cambios/cambio_usuario_propiedad_pref_guardar_todas` — mutacion `guardar_propiedades`.

### Controladores frontend resultantes en `frontend/cambios/`

- `usuario_form_avisos.php` (+ vista `.phtml`) — listado por usuario.
- `avisos_generar.php` (+ vista `.phtml`) — pantalla de avisos.
- `usuario_avisos_pref.php` (+ vista `.phtml`) — formulario principal de
  configuracion de aviso.
- `usuario_avisos_pref_propiedades.php` (+ `.phtml`) — fragmento AJAX con
  las propiedades del objeto.
- `usuario_avisos_pref_condicion.php` (+ `.phtml`) — fragmento AJAX con el
  modal de condicion.
- `usuario_avisos_pref_fases.php` — fragmento AJAX con el desplegable de
  fases.

### Wrappers deprecados en `apps/cambios/controller/`

- `usuario_form_avisos.php` — delega al endpoint JSON.
- `avisos_generar.php` — delega al nuevo frontend.
- `avisos_generar_ajax.php` — dispatcher que redirige cada rama al endpoint
  correspondiente.
- `usuario_avisos_pref.php` — delega al nuevo frontend.
- `usuario_avisos_pref_ajax.php` — dispatcher que redirige cada rama al
  endpoint JSON o frontend correspondiente.
- `avisos_generar_tabla.php` — delega al driver CLI
  `src/cambios/infrastructure/cli/avisos_generar_tabla.php`.
- `avisos_generar_mails.php` — delega al driver CLI
  `src/cambios/infrastructure/cli/avisos_generar_mails.php`.

### Scripts CLI / cron en `src/cambios/infrastructure/cli/`

- `avisos_generar_tabla.php` — parsea `$argv`, arranca el contenedor y
  llama a `src\cambios\application\AvisosGenerarTabla::execute()`. Imprime
  la tabla HTML de errores (legacy) y sale con codigo `1` si el bucle no
  progresa. Invocado por cron, `Cambio::generarTabla()` (via `exec`) y el
  menu web.
- `avisos_generar_mails.php` — parsea `$argv`, arranca el contenedor y
  llama a `src\cambios\application\AvisosEnviarMails::execute()`. Imprime
  un resumen con contadores (`enviados`, `sin_email`, `total_avisos`) util
  para los logs de cron.

## Pantallas en apps/cambios/controller

### 1. `usuario_form_avisos.php` (listado de avisos de un usuario)

- **Origen**: `apps/cambios/controller/usuario_form_avisos.php` (125 LOC).
- Recibe `id_usuario`, `quien` (debe ser `'usuario'`).
- Devuelve JSON `{ a_valores, nombre_usuario }` con las filas para la `web\Lista`.
- Ya existe `frontend/cambios/controller/usuario_form_avisos.php` + vista
  `.phtml` que postean al controlador apps con `PostRequest::getDataFromUrl`.
- **Plan**: mover el backend a `src/cambios/application/UsuarioFormAvisosData.php`
  + endpoint `/src/cambios/usuario_form_avisos_data`. El controlador
  `apps/...` queda como wrapper deprecado que hace `require` al mismo archivo
  HTTP via el ruteador (o redelegar al `frontend/...`).

### 2. `avisos_generar.php` (listado de avisos del usuario conectado)

- **Origen**: `apps/cambios/controller/avisos_generar.php` (189 LOC).
- Segun `$_POST` (`id_usuario`, `aviso_tipo`, `refresh`, `Gstack`), renderiza
  el listado (con `web\Lista`) o el formulario de filtro (cuando es admin).
  En el legacy esto eran dos Twig separados (`avisos_generar_lista.html.twig`
  y `avisos_generar_condicion.html.twig`), ya eliminados.
- **Salida**: HTML.
- **Plan**: migrar a `frontend/cambios/controller/avisos_generar.php` + vista
  `.phtml` unica que pinta ambas secciones (filtro arriba + lista abajo si
  hay `Qid_usuario`). Backend: `src/cambios/application/AvisosGenerarListaData.php`
  que devuelve `a_valores`, `aOpcionesUsuarios`, `aOpcionesAvisoTipo`,
  `zona_horaria`. Endpoint `/src/cambios/avisos_generar_lista_data`.

### 3. `avisos_generar_ajax.php` (dispatcher de 2 ramas)

- **Origen**: `apps/cambios/controller/avisos_generar_ajax.php` (48 LOC).
- Dispatcher `$Qque`:
  - `eliminar_fecha`: llama a `CambioUsuarioRepository::eliminarHastaFecha($f_fin)`.
  - `eliminar`: recibe `sel[]` con `id_item_cambio#id_usuario#sfsv#aviso_tipo`,
    itera y elimina cada `CambioUsuario`.
- **Salida**: vacio (en exito) o texto del error.
- **Plan**: partir en 2 endpoints JSON:
  - `/src/cambios/cambio_usuario_eliminar` (`application/CambioUsuarioEliminar`).
  - `/src/cambios/cambio_usuario_eliminar_hasta_fecha`
    (`application/CambioUsuarioEliminarHastaFecha`).
  Frontend: la vista llama directamente a cada endpoint con
  `fnjs_construir_desplegable`-style JSON y refresca en `.done()`.

### 4. `avisos_generar_tabla.php` (proceso batch / cron, 367 LOC)

- **Origen**: `apps/cambios/controller/avisos_generar_tabla.php`.
- Se invoca por `cron` (via `exec("nohup php ... $args")`), desde menu (para
  admin) y desde clases `Cambio` / `CambioDl` al insertar un cambio.
- Recorre `Cambio` nuevos, aplica preferencias y permisos, y apunta avisos en
  `CambioUsuario`.
- **Migrado**: la logica vive en
  `src/cambios/application/AvisosGenerarTabla::execute($username, $esquema)`.
  El driver CLI esta en `src/cambios/infrastructure/cli/avisos_generar_tabla.php`
  y se encarga de argv + bootstrap. El wrapper en `apps/cambios/controller/`
  queda como compatibilidad para crontabs legacy.
- La clase auxiliar `Avisos` se ha movido a
  `src/cambios/application/legacy/Avisos.php` (namespace
  `src\cambios\application\legacy`). Solo la consume `AvisosGenerarTabla`;
  el resto del modulo no debe importar `application/legacy/` (regla
  `refactor.md`). Se conserva el comportamiento legacy 1:1 (`echo`, PID en
  `log/avisos.<esquema>.pid`, `exit` en `crear_pid` cuando detecta otro
  proceso). El `apps/cambios/model/Avisos.php` se ha eliminado.

### 5. `avisos_generar_mails.php` (proceso batch / cron, 221 LOC)

- **Origen**: `apps/cambios/controller/avisos_generar_mails.php`.
- Itera `CambioUsuario` con `aviso_tipo = TIPO_MAIL` y envia emails.
- **Migrado**: `src/cambios/application/AvisosEnviarMails::execute()`
  devuelve `{enviados, usuarios_sin_email, total_avisos}`. Driver CLI en
  `src/cambios/infrastructure/cli/avisos_generar_mails.php`, wrapper
  deprecado en `apps/cambios/controller/`.
- Deuda asumida: sigue usando `mail()` directo y `web\Lista` para montar
  el body del correo. Candidatos a `MailerInterface` y a una plantilla
  `.phtml` en una pasada posterior.

### 6. `usuario_avisos_pref.php` (pantalla de configuracion de preferencia)

- **Origen**: `apps/cambios/controller/usuario_avisos_pref.php` (270 LOC).
- Recibe `id_usuario` / `id_item_usuario_objeto` / `quien` / `salida`
  (`'nuevo'` o `'modificar'`).
- Renderiza la pantalla de preferencias (en el legacy, mediante
  `usuario_avisos_pref.html.twig`, ya eliminado; ahora en
  `frontend/cambios/view/usuario_avisos_pref.phtml`) con:
  - desplegable de objetos (`AvisoObjetoCatalog::getArrayObjetosPosibles()`),
  - desplegable de fases (`ActividadFaseRepository::getArrayActividadFases()`
    o `StatusId` si no hay modulo `procesos`),
  - desplegable de tipos aviso (`AvisoTipoId::getArrayAvisoTipo()`),
  - `DesplegableArray` de casas,
  - `ActividadTipo` (form complejo de tipo de actividad),
  - 3 `Hash` (actualizar, propiedades, modificar) apuntando al dispatcher
    ajax (pantalla #7).
- **Plan**: migrar a `frontend/cambios/controller/usuario_avisos_pref.php` +
  vista `.phtml`. Backend `src/cambios/application/UsuarioAvisosPrefFormData.php`
  devuelve todos los arrays de opciones (objetos, fases, tiposaviso, casas,
  preseleccion). Los desplegables se construyen en la vista con
  `web\Desplegable`. Endpoint `/src/cambios/usuario_avisos_pref_form_data`.

### 7. `usuario_avisos_pref_ajax.php` (dispatcher de 7 ramas)

- **Origen**: `apps/cambios/controller/usuario_avisos_pref_ajax.php` (522 LOC).
- Dispatcher `$Qsalida`:
  - `guardar_cond`: guarda un `CambioUsuarioPropiedadPref` parcial (sin
    `id_item_usuario_objeto` aun). Devuelve mini HTML con `<input hidden>`
    + texto legible de la condicion. **HTML inline -> frontend**.
  - `condicion`: construye un `<form>` modal con radios `old/new` +
    operador (`=,<,>,regexp`) + valor (o `DesplegableArray` si propiedad es
    `id_ubi`). **HTML inline -> frontend**.
  - `propiedades`: construye la tabla de propiedades del objeto con
    `DatosCampos`, con checkboxes + text mod. **HTML inline -> frontend**.
  - `av_fases`: devuelve `oDesplFases->desplegable()` (HTML `<select>`).
    **-> endpoint JSON con payload dropdown estandar**.
  - `eliminar`: elimina un `CambioUsuarioObjetoPref`. **-> JSON mutation**.
  - `guardar_objeto`: guarda el `CambioUsuarioObjetoPref` (inicial/update).
    Devuelve `id_item_usuario_objeto` como texto plano.
    **-> JSON mutation (`{success, mensaje, data: {id_item_usuario_objeto}}`)**.
  - `guardar_propiedades`: guarda/elimina los
    `CambioUsuarioPropiedadPref` asociados al objeto. **-> JSON mutation**.
- **Plan**: dividir en:
  - `src/cambios/application/CambioUsuarioObjetoGuardar` + endpoint JSON.
  - `src/cambios/application/CambioUsuarioObjetoEliminar` + endpoint JSON.
  - `src/cambios/application/CambioUsuarioPropiedadesGuardar` + endpoint JSON.
  - `src/cambios/application/CambioUsuarioCondicionGuardarData` +
    endpoint JSON (devuelve estructura preview para pintar el hidden + texto).
  - `src/cambios/application/AvFasesDropdownData` + endpoint JSON con
    contrato estandar de desplegables (payload `opciones/selected/...`).
  - `frontend/cambios/controller/usuario_aviso_propiedades_form.php`
    (HTML del bloque de propiedades del objeto).
  - `frontend/cambios/controller/usuario_aviso_condicion_form.php`
    (HTML del modal de condicion por propiedad).
  Los dos ultimos consumen datos basicos via `PostRequest` (propiedad,
  valores, opciones casas si aplica) y generan el HTML en la vista.

## Orden y dependencias de los slices

1. **Slice 1** — `usuario_form_avisos` (solo falta mover backend).
2. **Slice 2** — `avisos_generar` + `avisos_generar_ajax` (pantalla usuario).
3. **Slice 3** — `usuario_avisos_pref` + `usuario_avisos_pref_ajax` (la mas
   grande, depende de tener el dispatcher desmontado).
4. **Menus y wrappers** — al final: actualizar `menus.csv`,
   `aux_metamenus.csv`, `log/menus/comun.sql`, `index.php` (`pag_ini`
   `avisos`) y reducir los `apps/cambios/controller/*.php` a wrappers con
   nota de deprecacion.
5. **Slice CLI** — `avisos_generar_tabla` y `avisos_generar_mails` bajo
   `src/cambios/infrastructure/cli/` + `application/*`. Los wrappers en
   `apps/cambios/controller/` siguen existiendo para no romper crontabs
   instalados.

## Riesgos y notas

- La pantalla #6 usa `web\TiposActividades` + `ActividadTipo` (viven en
  `src/actividades/application`). Esto no se toca: sigue igual.
- `GestorAvisoCambios` se ha dividido y movido: los metodos estaticos
  (`getArrayObjetosPosibles`, `getFullPathObj`) viven ahora en
  `src/cambios/domain/AvisoObjetoCatalog.php` (domain, porque los
  necesita la entidad `Cambio` ademas de los data builders); la logica
  de escritura (`addCanvi`) se ha extraido a
  `src/cambios/application/RegistrarCambio.php`, que es el caso de uso
  invocado desde `RegistrarCambioListener`. El fichero legacy
  `apps/cambios/model/GestorAvisoCambios.php` se ha eliminado.
- El dispatcher #7 tiene mucho acoplamiento entre
  `CambioUsuarioObjetoPref` y `CambioUsuarioPropiedadPref` (guarda objeto,
  obtiene id, luego propiedades). Para no cambiar el protocolo JS
  (`fnjs_grabar_todo` hace 2 `$.ajax` encadenados), se mantiene el mismo
  orden en el frontend.
- Los dos scripts CLI (`avisos_generar_tabla`, `avisos_generar_mails`) se
  han migrado a `src/cambios/infrastructure/cli/*` + `application/*`.
  Conservan el comportamiento 1:1 (incluido el uso de la clase legacy
  `cambios\model\Avisos`). Los wrappers en `apps/cambios/controller/`
  siguen para los crontabs instalados; cuando se actualicen las
  crontabs en produccion, los wrappers podran eliminarse.

## Cierre DI (junio 2026)

### `$GLOBALS['container']` en `src/cambios/`

| Fase | Ficheros con `$GLOBALS['container']` |
|------|--------------------------------------:|
| Pre-cierre (application + domain + legacy) | **16** |
| Post-cierre | **0** |

La entidad `Cambio` ya no llama al contenedor: `getAvisoTxt()` se extrajo a
`src/cambios/application/CambioAvisoTxtBuilder.php` (inyectado en
`AvisosGenerarListaData` y `AvisosEnviarMails`).

### Application / legacy

| Clase | Repos / servicios inyectados |
|-------|------------------------------|
| `UsuarioFormAvisosData` | `Usuario`, `CambioUsuario*Pref`, `ActividadFase` |
| `AvisosGenerarListaData` | `Usuario`, `Preferencia`, `CambioUsuario`, `Cambio*`, `CambioAvisoTxtBuilder` |
| `UsuarioAvisosPrefFormData` | `Usuario`, `Grupo`, `TipoDeActividad`, `CambioUsuarioObjetoPref`, `ActividadFase`, `CasaDl` |
| `CambioUsuarioPropiedadPrefItemData` | `CambioUsuarioPropiedadPref`, `CasaDl` |
| `CambioUsuarioObjetoPrefPropiedadesData` | `CambioUsuarioPropiedadPref` |
| `CambioUsuarioObjetoPrefFasesData` | `TipoDeActividad`, `ActividadFase` |
| `CambioUsuarioEliminar` / `EliminarHastaFecha` | `CambioUsuario` |
| `CambioUsuarioObjetoPrefGuardar` / `Eliminar` | `CambioUsuarioObjetoPref` |
| `CambioUsuarioPropiedadPrefGuardarTodas` / `Preview` / `ItemData` | `CambioUsuarioPropiedadPref` (Preview sin repo) |
| `RegistrarCambio` | `ActividadAll`, `CambioDl`, `Cambio`, `ActividadProcesoTarea` |
| `AvisosGenerarTabla` | `Avisos` (legacy), `Cambio`, `ActividadAll`, `Importada`, `TipoDeActividad`, `PersonaSacd`, `TareaProceso`, `CambioUsuario*Pref` |
| `AvisosEnviarMails` | `CambioUsuario`, `Usuario`, `Preferencia`, `Cambio*`, `CambioAvisoTxtBuilder` |
| `legacy/Avisos` | `CambioUsuario`, `CambioAnotado`, `Usuario`, `ActividadAll`, `Zona`, `ZonaSacd`, `ActividadCargo` |

Sesión: `instanceof ConfigSnapshot` / `XPermisos` en
`UsuarioAvisosPrefFormData` y `CambioUsuarioPropiedadPrefItemData`.

### Repos `Pg*` (6)

| Repositorio | PDO |
|-------------|-----|
| `PgCambioRepository` | `GlobalPdo::get('oDBPC')` / `oDBPC_Select`; métodos batch usan `oDBC` |
| `PgCambioDlRepository` | `GlobalPdo::get('oDBC')` / `oDBC_Select` |
| `PgCambioUsuarioRepository` | `GlobalPdo::get('oDBC')` / `oDBC_Select` |
| `PgCambioAnotadoRepository` | `GlobalPdo::get('oDBC')` / `oDBC_Select` |
| `PgCambioUsuarioObjetoPrefRepository` | `GlobalPdo::get('oDBE')` / `oDBE_Select` |
| `PgCambioUsuarioPropiedadPrefRepository` | `GlobalPdo::get('oDBE')` / `oDBE_Select` |

### HTTP controllers (12)

Todos en `infrastructure/ui/http/controllers/` usan `DependencyResolver::get()`
(sin `::execute()` estáticos). Entrada POST via `input_int` / `input_string` /
`input_string_list`.

### CLI (2)

- `avisos_generar_tabla.php` → `DependencyResolver::get(AvisosGenerarTabla::class)`
- `avisos_generar_mails.php` → `DependencyResolver::get(AvisosEnviarMails::class)`

### `src/cambios/config/dependencies.php`

Registra 6 repositorios + 17 casos de uso (`Avisos` legacy incluido).
Repos cross-módulo (`Actividad*`, `Usuario`, `CasaDl`, `PersonaSacd`,
`Zona*`, `ActividadCargo`, etc.) se resuelven por autowire desde los
`dependencies.php` de sus módulos.

### Listener compartido

`RegistrarCambioListener` (`src/shared/application/listeners/`) recibe
`RegistrarCambio` por constructor. Registrado en
`src/shared/config/dependencies.php` con `autowire()`; el `EventBusInterface`
factory inyecta el listener (ya no hace `new RegistrarCambioListener()`).

## Deuda post-refactor

### Completado

- [x] 0 `$GLOBALS['container']` en todo `src/cambios/`
- [x] 12 controllers HTTP via `DependencyResolver`
- [x] Casos de uso con constructor DI (incl. `legacy/Avisos`)
- [x] `dependencies.php` con todos los use cases
- [x] 6 Pg repos con `GlobalPdo`
- [x] Frontend sin `use src\...` en controladores
- [x] PHPStan: `src/cambios/` sin errores en `phpstan-nobaseline.neon` (0)
- [x] Tests unitarios (`tests/unit/cambios/`: 97 tests)

### PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (pre-cierre DI) | `composer phpstan:file -- src/cambios/` | **410** |
| 2026-06-06 (cierre DI) | `composer phpstan:file -- src/cambios/` | **0** |

Áreas abordadas:

- **Application:** constructor DI en 17 clases; `CambioAvisoTxtBuilder` extraído
  de `Cambio::getAvisoTxt()`; `instanceof ConfigSnapshot` / `XPermisos` para sesión.
- **Repos `Pg*`:** `GlobalPdo`, guards `PDOStatement|false`, `array_values` en
  colecciones, `datosById(): array|false`.
- **Interfaces / entity / VOs:** PHPDoc retorno; setters nullable en entidades.
- **db/DBEsquema:** tipos de retorno `: void`, `infoTable(string): array`.
- **HTTP controllers / CLI:** `DependencyResolver::get()` + `input_*`.

### Pendiente

- [ ] `legacy/Avisos`: conserva `echo`/`exit` en `crear_pid` (deuda documentada).
- [ ] `AvisosEnviarMails`: sigue usando `mail()` y `sleep(60)` (deuda conocida).
- [ ] N+1 y acoplamiento legacy en `AvisosGenerarTabla` (fuera de alcance DI).

## Checklist de cierre

Ver [`REFACTOR_INDICE.md`](REFACTOR_INDICE.md#checklist-cerrar-un-módulo).

- [x] `$GLOBALS['container']` migrado a DI por constructor en `application/`
- [x] Controllers HTTP sin `$GLOBALS` directo (`DependencyResolver`)
- [x] `dependencies.php` con todos los use cases
- [x] Tests existentes pasan (`tests/unit/cambios/`: 97 tests)
- [x] PHPStan `src/cambios/` en 0 (phpstan-nobaseline.neon)
