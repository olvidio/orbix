# Baseline y migración `apps/encargossacd` → `frontend/encargossacd` + `src/encargossacd`

Documento de referencia según `refactor.md` (misma línea que `profesores` / `misas`).

## Estado aplicado (slice 1 — capa frontend)

- **Controladores canónicos:** `frontend/encargossacd/controller/*.php` con `FrontBootstrap::boot()`.
- **Compatibilidad:** `apps/encargossacd/controller/<nombre>.php` delega con `require` al equivalente en `frontend/` (URL legacy; enlaces nuevos deben usar `frontend/...`).
- **Vistas:** `frontend/encargossacd/view/*.phtml`; render con `ViewNewPhtml('frontend\\encargossacd\\controller')`. La carpeta `apps/encargossacd/view` queda vacía / retirada como duplicado.
- **API JSON `/src/encargossacd/*`:** `src/encargossacd/config/routes.php` registra los endpoints extraídos slice a slice (ver secciones siguientes).

## Inventario de controladores (31)

| Fichero | Rol resumido | Salida principal |
|--------|----------------|------------------|
| `listas_index.php` | Índice listados + enlace comprobaciones | ViewNewPhtml |
| `listas_a.php` … `listas_d.php`, `listas_cl.php` | Listados PDF/HTML según CR | HTML / ViewNewPhtml / crudo |
| `listas_exigencia_ctr.php` | Exigencias CTR/IGL | ViewNewPhtml |
| `listas_com_sacd.php`, `listas_com_ctr.php`, `listas_com_txt.php` | Comunicaciones | ViewNewPhtml + AJAX |
| `listas_com_txt_ajax.php` | AJAX textos comunicaciones | texto/HTML |
| `comprobaciones.php` | Limpieza encargos ctr inactivos (`que=ctr`) | texto plano (alert) |
| `encargo_select.php`, `encargo_ver.php`, `encargo_ajax.php`, `encargo_horario_select.php` | CRUD/lista encargos | ViewNewPhtml + echo AJAX |
| `zonas_get_select.php`, `ctr_get_select.php` | Selectores AJAX | JSON (`/src/...`) + wrappers legacy |
| `ctr_ficha.php`, `ctr_get_ficha.php`, `ctr_ficha_update.php` | Ficha centro | ViewNewPhtml |
| `sacd_ficha.php`, `sacd_ficha_ajax.php` | Ficha SACD + mutaciones | ViewNewPhtml + HTML embebido |
| `horario_ver.php`, `horario_update.php` | Horario encargo genérico | Twig |
| `horario_sacd_ver.php`, `horario_sacd_update.php` | Horario SACD | Twig / lógica legacy |
| `sacd_ausencias.php`, `sacd_ausencias_get.php`, `sacd_ausencias_update.php`, `sacd_ausencias_jefe_zona.php` | Ausencias SACD | Twig |

## Slice `encargo_ajax` → `/src/encargossacd/*` (completado)

- **Desplegable tipos (`lst_tipo_enc`):** `EncargoLstTipoEncData` + `GET|POST /src/encargossacd/encargo_lst_tipo_enc_data` + helper `fnjs_construir_desplegable` en `encargo_ver.phtml`.
- **Mutaciones:** `EncargoVerNuevo`, `EncargoVerEditar`, `EncargoVerEliminar` + `encargo_ver_nuevo|editar|eliminar`.
- **Legacy:** `frontend/encargossacd/controller/encargo_ajax.php` reenvía por `que=` a los controladores anteriores.

## Slice `zonas_get_select` + `ctr_get_select` → JSON (completado)

- **Zonas:** `EncargoZonasSelectData` + `GET|POST /src/encargossacd/zonas_get_select_data` (incluye `label_prefix` para el texto antes del `<select>`).
- **Centros:** `EncargoCtrSelectData` + `GET|POST /src/encargossacd/ctr_get_select_data`.
- **Consumidores:** `encargo_ver.phtml` y `ctr_ficha.phtml` usan `dataType: 'json'` y `fnjs_construir_desplegable` con `data.id` / `data.name`; `encargo_ver.php` y `ctr_ficha.php` construyen URLs absolutas + `Hash` alineados con POST.
- **Legacy:** `frontend/.../zonas_get_select.php` y `ctr_get_select.php` reenvían a los controladores `src` (respuesta JSON).

## Slice `ctr_ficha` + `ctr_get_ficha` + `ctr_ficha_update` (completado)

- **Backend lectura:** `CtrFichaData::execute($id_ubi, $filtro_ctr)` calcula el `filtro_ctr` efectivo a partir del centro y devuelve `opciones_seccion` (antes era `EncargoAplicacionService::getArraySeccion()` llamado desde frontend). `CtrGetFichaData::execute($id_ubi, $seleccion_sacd)` devuelve un array estructurado con `mod`, `tipo_centro`, `num_enc`, checkboxes, `opciones_sacd` (+ `opciones_sacd_sssc` si hay colaboradores fuera de la seleccion base) y `encargos[]` con sub-campos por encargo (`id_enc`, `mod_horario`, `observ`, titular/suplente, dedicaciones, colaboradores). Horarios por `mod_horario=3` precalculan el texto con `EncargoDominioService::texto_horario`.
- **Backend mutacion:** `CtrFichaUpdate::execute(array $post)` reproduce integramente el flujo `nuevo` / `editar` del antiguo `ctr_ficha_update.php`. Devuelve `['error' => string]`; el controlador HTTP lo envuelve en `ContestarJson::enviar($error, '')`.
- **Endpoints:** `GET|POST /src/encargossacd/ctr_ficha_data`, `GET|POST /src/encargossacd/ctr_get_ficha_data`, `GET|POST /src/encargossacd/ctr_ficha_update` (registrados en `src/encargossacd/config/routes.php`).
- **Frontend:** los tres controladores (`ctr_ficha.php`, `ctr_get_ficha.php`, `ctr_ficha_update.php`) dejan de importar `EncargoAplicacionService`, `EncargoFunciones`, `EncargoGrupo`, `Encargo`, `DateTimeLocal`, `EncargoRepositoryInterface`, `EncargoHorarioRepositoryInterface`, `EncargoSacdRepositoryInterface`, `EncargoSacdHorarioRepositoryInterface`, `EncargoTipoRepositoryInterface`, `PersonaSacdRepositoryInterface`, `CentroDlRepositoryInterface`, `CentroEllasRepositoryInterface`. Cero `use src\\...` en los tres. `ctr_get_ficha.php` arma `web\\Desplegable` titular/suplente y el HTML de colaboradores (`construir_otros_sacd`) a partir de los arrays del payload.
- **Proxy de mutacion:** `frontend/.../ctr_ficha_update.php` hace `PostRequest::getDataFromUrl('/src/encargossacd/ctr_ficha_update', $_POST)`; si `success=false`, `PostRequest` hace `exit($mensaje)` (texto plano) y el JS existente sigue mostrando `alert(rta_txt)` sin cambios. En exito, responde cuerpo vacio. Cuando migremos el JS a JSON estricto (`dataType:'json'`), el proxy podra eliminarse.

## Slice `horario_ver` + `horario_sacd_ver` (completado)

- **Backend:** `EncargoHorarioVerData::cargar` y `EncargoSacdHorarioVerData::cargar` añaden al payload el `dia` calculado (via `EncargoDominioService::calcular_dia`) y las listas `opciones_dia_semana`, `opciones_dia_ref`, `opciones_ordinales` (`EncargoConstants::OPCIONES_*`). Endpoints sin cambios: `GET|POST /src/encargossacd/horario_ver_data` y `GET|POST /src/encargossacd/horario_sacd_ver_data`.
- **Frontend:** `frontend/encargossacd/controller/horario_ver.php` y `horario_sacd_ver.php` dejan de importar `EncargoFunciones` y `EncargoConstants`; toman `dia` y las opciones directamente del payload (`PostRequest`). Cero `use src\...` en los dos controladores.
- **Mutaciones asociadas:** ya eran proxys `PostRequest → /src/encargossacd/horario_update_data` y `/src/encargossacd/horario_sacd_update_data` (sin cambios).

## Slice `encargo_select` + `encargo_ver` + `encargo_horario_select` (completado)

- **Backend lectura:**
  - `EncargoSelectData::execute($desc_enc, $id_tipo_enc)` devuelve `filas[]` con los datos planos de cada encargo listado (`id_enc`, `sf_sv`, `id_ubi`, `desc_enc`, `desc_lugar`, `seccion`, `nombre_ubi`, `idioma`, `idioma_enc`). Usa `EncargoRepository`, `LocalRepository`, `Ubi::NewUbi()` y `EncargoAplicacionService::getArraySeccion()` en el backend.
  - `EncargoVerData::execute($que, $id_enc, $id_tipo_enc, $grupo, $filtro_ctr, $desc_enc, $desc_lugar, $id_zona)` resuelve la ficha (carga el `Encargo` si `editar`, infiere `filtro_ctr` desde el centro via `EncargoGrupo`) y devuelve `grupo_posibles`, `posibles_encargo_tipo`, `opciones_seccion`, `opciones_zonas`, `opciones_locales` ademas del estado canonico (`que`, `id_enc`, `id_tipo_enc`, `grupo`, `filtro_ctr`, `desc_enc`, `desc_lugar`, `idioma_enc`, `id_ubi`, `id_zona`). Encapsula las llamadas a `EncargoTipoRepository::encargo_de_tipo/id_tipo_encargo` y reemplaza las invocaciones rotas `$EncargoTipoRepository->getArraySeccion()` por el servicio correcto.
  - `EncargoHorarioSelectData::execute($id_enc)` devuelve `desc_enc` + `filas[]` de horarios con `texto_horario` ya calculado (via `EncargoDominioService::texto_horario`).
- **Endpoints:** `GET|POST /src/encargossacd/encargo_select_data`, `GET|POST /src/encargossacd/encargo_ver_data`, `GET|POST /src/encargossacd/encargo_horario_select_data` (registrados en `src/encargossacd/config/routes.php`).
- **Frontend:** los tres controladores (`encargo_select.php`, `encargo_ver.php`, `encargo_horario_select.php`) dejan de importar `EncargoRepositoryInterface`, `EncargoTipoRepositoryInterface`, `EncargoHorarioRepositoryInterface`, `LocalRepositoryInterface`, `ZonaRepositoryInterface`, `CentroDlRepositoryInterface`, `CentroEllasRepositoryInterface`, `EncargoGrupo` y `Ubi`. Cero `use src\...` en los tres. Siguen armando `web\Lista` / `web\Desplegable` y los `Hash` locales a partir del payload JSON.
- **Mutaciones asociadas:** `encargo_ver_nuevo|editar|eliminar` ya estaban migrados (slice `encargo_ajax`). El borrado en `encargo_select.phtml` usa `dataType: 'json'` contra `/src/encargossacd/encargo_ver_eliminar`.

## Slice `sacd_ficha_ajax` (completado)

- **Backend lectura:**
  - `SacdSelectData::execute($filtro_sacd, $id_nom)` devuelve `opciones` (personas SACD filtradas por `id_tabla`), `selected` y `label_prefix` para reconstruir el desplegable.
  - `SacdFichaData::execute($id_nom)` devuelve `permiso`, `observ_sacd`, `encargos[]` con `id_enc`, `id_tipo_enc`, `mod_horario`, `modo`, `sf_sv`, `id_ubi`, `desc_enc` (enmascarado segun permiso), `dedic_ctr*`, `dedic_sacd`, `dedic_m|t|v` + `opciones_mas` (constantes desde `EncargoConstants`) y `avisos[]` (antes se hacian `echo` en mitad de la respuesta HTML cuando un horario SACD no tenia `dia_ref`). Precalcula `texto_horario` via `EncargoDominioService` cuando `mod_horario=3`.
- **Backend mutacion:**
  - `SacdFichaUpdate::execute(array $post)` porta la logica de `que=update`: crea encargos genericos (estudio/descanso/otros) si hace falta, aplica `insert_sacd`/`delete_sacd`, modifica dedicaciones `m|t|v` via `EncargoAplicacionService::modificar_horario_sacd` y guarda/actualiza/elimina `EncargoSacdObserv`. Devuelve `['error' => string, 'mensajes' => string]`. De paso se corrigen bugs latentes del original (`Guaradar` -> `Guardar`, `setId_item` aplicado al repo en vez de a la entidad, `newId` -> `getNewId`).
- **Endpoints:** `GET|POST /src/encargossacd/sacd_select_data`, `GET|POST /src/encargossacd/sacd_ficha_data`, `GET|POST /src/encargossacd/sacd_ficha_update` (registrados en `src/encargossacd/config/routes.php`).
- **Frontend:** `frontend/encargossacd/controller/sacd_ficha_ajax.php` queda como dispatcher delgado sobre `que`:
  - `get_select`: llama al endpoint, construye `web\Desplegable` y hace `echo` del HTML (la vista `sacd_ficha.phtml` sigue consumiendo texto HTML con `dataType:'html'`).
  - `ficha`: llama al endpoint, calcula los `Hash::link` para enlazar a `ctr_ficha.php` desde el controlador (para no usar `Hash` dentro del namespace del renderer) y delega la plantilla HTML a la nueva vista `frontend/encargossacd/view/sacd_ficha_ajax_ficha.phtml`.
  - `update`: proxy JSON -> texto plano via `PostRequest`; mantiene la compatibilidad con `fnjs_guardar` (que muestra `alert(rta_txt)`).
  - Cero `use src\...`.

## Slice `sacd_ausencias_*` (completado)

- **Backend lectura:**
  - `SacdAusenciasGetData::execute($id_nom, $historial)` devuelve `array_tipo_ausencias` (encargos de tipo 4/7 disponibles) y `filas[]` con los datos por ausencia (`id_enc`, `id_tipo_enc`, `desc_enc`, `id_item`, `inicio`, `fin`, `dedic_m|t|v`). Incluye el filtrado por vigencia (historial sí/no) y fusiona los tramos de horario (f_fin > hoy + f_fin null).
  - `SacdAusenciasJefeZonaData::execute()` recopila el listado de SACDs que un jefe de zona u oficial DL puede gestionar (zonas suyas + "Oficial_dl" / `is_jefeCalendario()`). Devuelve `a_sacd` (clave `iniciales#id_nom` => nombre completo) ordenado.
- **Backend mutacion:**
  - `SacdAusenciasUpdate::execute(array $post)` reproduce la combinacion `modifica_sacd_ausencias` + `insert_sacd_ausencias` del antiguo controlador. Devuelve `['error' => bool, 'mensajes' => string]`; el HTTP controller lo envuelve en `ContestarJson::enviar`.
- **Endpoints:** `GET|POST /src/encargossacd/sacd_ausencias_get_data`, `GET|POST /src/encargossacd/sacd_ausencias_update`, `GET|POST /src/encargossacd/sacd_ausencias_jefe_zona_data`.
- **Frontend:** `frontend/encargossacd/controller/sacd_ausencias_get.php` y `sacd_ausencias_jefe_zona.php` consumen `PostRequest::getDataFromUrl(...)` y arman `web\Desplegable`/`web\Hash` sobre los arrays del payload. `sacd_ausencias_update.php` es ahora un proxy (`PostRequest` -> texto plano en error / cuerpo vacio en exito) compatible con `fnjs_guardar`.
  - Cero `use src\...` en los tres ficheros.

## Slice `listas_*` (completado)

- **Backend:** cada lista dispone de una clase `*\application\Listas<Tipo>Data` que replica el calculo del antiguo controlador y devuelve los datos estrictamente necesarios para la vista (en general `cabecera_left|right|right_2` + `Html` precompuesto; `ListasComCtrData`/`ListasComSacdData` devuelven arrays estructurados; `ListasComTxtData` devuelve `a_locales` y `texto_inicial`; `ListasClData` reimplementa el SQL crudo sobre `u_centros_dl`/`d_cargos`).
- **Endpoints:** `GET|POST /src/encargossacd/listas_{a,b,c,d}_data`, `.../listas_com_{txt,ctr,sacd}_data`, `.../listas_exigencia_ctr_data`, `.../listas_cl_data`. Todos responden con `ContestarJson::enviar('', $payload)`.
- **Frontend:** los nueve controladores (`listas_a|b|c|d|cl|exigencia_ctr|com_ctr|com_sacd|com_txt`) quedan como shells que leen el payload JSON via `PostRequest` y renderizan la vista generica `listas.phtml` (o las vistas especificas `listas_com_*.phtml`). `listas_d.php` y `listas_cl.php` simplemente hacen `echo` del HTML recibido (igual que antes, pero sin query directa al dominio).
- **Utilidades reutilizadas:** `EncargoAplicacionService` (para `dedicacion`, `dedicacion_ctr`, `getTraduccion`, `getLugar_dl`) y `EncargoDominioService::dedicacion_horas` se invocan directamente desde los servicios `Listas*Data`, en lugar de usar el trait `EncargoFunciones` desde el frontend.
- Cero `use src\...` en ninguno de los `frontend/encargossacd/controller/listas_*.php`.

## Violaciones pendientes (`use src\…` en `frontend/encargossacd/controller`)

Ninguna: `rg "^use src\\\\" frontend/encargossacd/` no reporta coincidencias.

## Slice post-migracion — limpieza / simplificaciones internas (completado)

Cambios sin impacto funcional, centrados en coherencia con `refactor.md` y reducir ruido:

- **URLs rotas a `des/tareas/*`**. Varias vistas JS aun apuntaban a controladores legacy inexistentes (`des/tareas/horario_ver.php`, `des/tareas/horario_update.php`, `des/tareas/horario_excepcion_ver.php`, `des/tareas/encargo_horario`, `des/tareas/sacd_ausencias_get.php`). Se redirigen a los canonicos bajo `frontend/encargossacd/controller/...`. El fallback `mod == 'excepcion'` en `horario_ver.phtml` era codigo muerto (el `action` se sobreescribia en la linea siguiente): eliminado.
- **`web\Desplegable` fuera de `src/application`.** `EncargoCtrSelectData` y `EncargoZonasSelectData` ya no instancian `web\Desplegable` para despues llamar a `->export()`. Devuelven directamente el payload estandar (`refactor.md`: "payload + constructor en frontend"). El frontend (`DesplCentros`, `fnjs_construir_desplegable`) no cambia de contrato.
- **Dispatcher `listas_com_txt_ajax` eliminado.** Se partio el endpoint multiproposito (`que=get_texto|update`) en dos endpoints independientes (`/src/encargossacd/listas_com_txt_get` y `/src/encargossacd/listas_com_txt_update`), con sus dos clases application (`ListasComTxtGet`, `ListasComTxtUpdate`) y sus dos proxies frontend (`listas_com_txt_get.php`, `listas_com_txt_update.php`). Se suprimieron `EncargoTextoListasComAjax`, el HTTP controller y el proxy antiguos. La vista `listas_com_txt.phtml` deja de enviar `que=` y apunta cada accion a su proxy.
- **`listas_index.php` simplificado.** Se extrajo un closure `$lnk(...)` para armar URLs con `Hash::link + http_build_query + poner_empty_on_null`, eliminando el patron duplicado 12 veces (`$aQuery = [...]; if (is_array($aQuery)) array_walk(...);`).
- **Helper `frontend\encargossacd\support\SacdFichaAjaxHashes`**. Centraliza las piezas duplicadas byte a byte en `sacd_ficha.php`, `sacd_ausencias.php` y `sacd_ausencias_jefe_zona.php`: el `<select>` de `filtro_sacd` con sus 4 opciones, los hashes hacia `sacd_ficha_ajax.php` (`h_ficha`, `h_lista`) y hacia `horario_sacd_ver.php` (`h_horario`).
- **`DesplCentros` sin estado mutable.** Se sustituye `new DesplCentros(); setIdZona(); getDesplPorFiltro()` por `DesplCentros::build($filtro_ctr, $id_ubi, $id_zona)`. Dos callers (`ctr_ficha.php`, `encargo_ver.php`) quedan mas compactos y alineados con el resto de helpers frontend (`PeriodoTdHelper`, `CuadriculaZonaRenderer`, `SacdFichaAjaxHashes`, ...).

## Próximos slices sugeridos (sin mezclar en un solo PR)

1. Endpoints JSON para AJAX que aún devuelven HTML (p. ej. partes de `sacd_ficha_ajax`) con contrato `refactor.md`.
2. Mutaciones con `ContestarJson::enviar` donde hoy hay `echo` suelto.
3. Pantallas sueltas que aún dependan de rutas `apps/...` en datos o menús (actualizar seeds/menús si aplica).

## Validación

- `php -l` en ficheros nuevos o tocados.
- Probar una pantalla de menú (listados, ficha, encargo, ausencias) y un flujo AJAX (cambio tipo encargo / selector).

## URLs canónicas (nuevos enlaces)

Prefijo: `frontend/encargossacd/controller/<script>.php`. API JSON: `/src/encargossacd/<acción>` (rutas en `src/encargossacd/config/routes.php`).

---

## Inventario inicial (antes del cierre DI en `src/encargossacd/`)

| Capa | Ficheros con `$GLOBALS['container']` | Ocurrencias |
|------|--------------------------------------|------------:|
| `infrastructure/ui/http/controllers/` | ~34 | ~34 |
| `application/` | ~34 | ~34 |
| `domain/` | ~6 | ~18 |
| **Total container** | **~36 ficheros** | **~86** |

| Capa | Ficheros con `$GLOBALS['oDB*']` |
|------|--------------------------------:|
| `infrastructure/persistence/postgresql/` | 7 |

| Frontend `use src\` | Ficheros |
|--------------------|----------|
| — | **0** |

PHPStan (`phpstan-nobaseline.neon`): **~700** errores en `src/encargossacd/`.

---

## Cierre DI (2026-06-06)

### Patrón aplicado

- HTTP controllers (`34`): `DependencyResolver::get()` + métodos de instancia (sin `::execute()` / `new` use cases).
- Application (`~40` casos de uso / servicios): constructor DI, métodos de instancia.
- Repos Pg* (`7`): `GlobalPdo::get('oDB'|'oDBC'|'oDBC_Select')` + guards `$stmt === false`.
- Cross-module: repos y servicios (`CentroDl`, `Zona`, `PersonaSacd`, `Local`, etc.) inyectados por constructor.
- Facade `EncargoFunciones`: delegación a `EncargoAplicacionService` / `EncargoDominioService` vía DI.

### `src/encargossacd/config/dependencies.php`

Registra **7** repositorios + **~40** entradas `autowire()` (use cases `*Data`, `*Update`, `EncargoAplicacionService`, `EncargoDominioService`, `EncargoFunciones`, `CentrosPorFiltroOpciones`, `InfoEncargoTipo`, etc.).

---

## Resultado del cierre DI

| Criterio | Antes | Después |
|----------|------:|--------:|
| `$GLOBALS['container']` en `src/encargossacd/` | ~86 | **0** |
| `$GLOBALS['oDB*']` en repos | 7 | **0** |
| Controllers HTTP con `DependencyResolver::get()` | 0/34 | **34/34** |
| Casos de uso en `dependencies.php` | 0 | **~47** |
| Frontend `use src\` | 0 | **0** |

---

## PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (pre-cierre) | `composer phpstan:file -- src/encargossacd/` | **~700** |
| 2026-06-06 (cierre DI) | idem | **0** |

Correcciones principales: contratos con tipos de retorno, repos Pg* con guards PDO, entidades con PHPDoc/`getDatosCampos`, application con `input_int`/`input_string` y `ConfigSnapshot` en listados, `DBEsquema::infoTable()` tipado como en `actividadessacd`, duck-typing de `$_SESSION['oPerm']` sin `$GLOBALS['container']`.

---

## Tests

```bash
php libs/vendor/bin/phpunit tests/unit/encargossacd/
php libs/vendor/bin/phpunit tests/integration/encargossacd/
```

Resultado cierre: **208** tests unitarios OK, **62** tests integración OK.
