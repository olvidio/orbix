# Reglas retiradas de `AGENTS.md` (histórico)

Texto que estuvo en [`AGENTS.md`](../../AGENTS.md) mientras duró la migración `apps/` → `frontend/` + `src/` y que se retiró el 2026-11-10 por estar ya cumplido o no coincidir con el código. **No son reglas vigentes**: se conserva literal, solo como consulta.

---

## Proceso de migración `apps/` → `frontend/` + `src/`

*Retirado porque el directorio `apps/` ya no existe: la migración de pantallas terminó.*

### Migración `apps/` → `frontend/` + `src/` (convivencia y slices)

Guía para seguir moviendo pantallas desde `apps/` hacia `frontend/` + `src/` sin mezclar capas ni romper URLs antiguas; convención de proyecto única en este documento.

#### Orden de trabajo
1. **Baseline breve** antes de tocar código: pantalla, parámetros GET/POST, salida HTML o JSON, casos `rstgr` / permisos si aplican. Anotarlo en `docs/dev/` (p. ej. `docs/dev/<modulo>_migracion_baseline.md`).
2. **Separar capas primero**; refactors finos (SRP, tests unitarios) después de que la pantalla viva en `frontend` + `src`.
3. **Un vertical slice por PR o commit lógico** (una pantalla o un flujo filtro+AJAX), sin mezclar varios módulos.

Fila retirada de la tabla «Responsabilidades por capa»:

| Capa | Ruta / carpeta | Responsabilidad |
|------|----------------|-----------------|
| Compatibilidad legacy | `apps/<modulo>/controller/*.php` | Opcional: `require` al controlador `frontend` equivalente. Comentar que la URL `apps/...` está **deprecada** para enlaces nuevos. |

Punto retirado de «Endpoints: un endpoint por acción»:

- Endpoints legacy con dispatcher: mantener solo como wrapper temporal, marcado deprecado; eliminar cuando `rg` no muestre referencias.

##### Playbook: eliminar dispatcher `*_ajax` / `*_update`
1. Mapear cada rama del `switch` a un endpoint `/src/<modulo>/<accion>` y un caso de uso en `src/<modulo>/application/`.
2. Actualizar **todos** los consumidores JS en el mismo cambio (`$.ajax` → URL acorde al `mod`/`que`).
3. Ajustar `.done` a JSON estándar (`ContestarJson`) con `dataType: 'json'`.
4. Borrar el dispatcher cuando `rg "<nombre_dispatcher>"` esté limpio.
5. Si una rama devolvía **HTML inline** (`<form>`, `<select>`, tabla, etc.), esa parte va a `frontend/<modulo>/controller/<accion>_form.php` + vista `.phtml`; los `/src/...` no devuelven HTML de aplicación.

#### URLs canónicas y menús
- Enlaces y menús **nuevos**: rutas bajo `frontend/.../controller/....php`.
- Actualizar plantillas de documentación donde existan (`docs/legacy/obix/menus.csv`, `proves/aux_metamenus.csv`, seeds SQL de referencia). Bases en producción con paths en BD: planificar `UPDATE` acorde; el repo documenta el destino deseado.

#### Migración de vistas y render canónico
- Al migrar un controlador a `frontend/<modulo>/controller`, migrar también la vista a `frontend/<modulo>/view` (`.phtml`); no dejar la vista canónica solo en `apps/<modulo>/view`.
- Patrón de render (p. ej. `encargossacd`, `misas`): en el controlador frontend:
  - `use frontend\shared\model\ViewNewPhtml;`
  - `$oView = new ViewNewPhtml('frontend\\<modulo>\\controller');`
  - `$oView->renderizar('nombre_plantilla.phtml', $a_campos);`
- `ViewNewPhtml` resuelve rutas físicas sustituyendo `controller` por `view` bajo `DOCUMENT_ROOT` + `ConfigGlobal::$web_path`.
- **Twig**: casos excepcionales; si se usa, el loader debe apuntar a un directorio bajo `apps/` donde exista configuración Twig acordada.
- Cuando el frontend renderiza bien, eliminar copia legacy en `apps/<modulo>/view` y actualizar referencias (`grep`, exportaciones, menús). Revisar rutas hardcodeadas `apps/...` en JS/HTML.

#### Convención para legacy en `apps/`
- En `apps/<modulo>/controller`, preferir wrappers mínimos que deleguen en `frontend/...`.
- Lógica antigua solo consulta/rollback: prefijo `z...` y aclarar que no son rutas canónicas.
- Rutas nuevas: `frontend/...` (UI) y `/src/...` (API).
- **No tocar** clases `Info*.php` en `apps/<modulo>/model/` (`Info3010`, …): metadatos de dossier (`extends core\DatosInfo`). El sistema las resuelve por número; aunque `rg` no muestre callers estáticos, **no** moverlas ni eliminarlas en refactors de pantalla.

Punto retirado de «Separación estricta frontend ↔ `src`»:

- Incumplimientos detectados en el pasado (corregir así): p. ej. importar `src/` desde controladores frontend en rutas tipo `actividad_tipo_get` → sustituir por endpoint JSON + `PostRequest`.

#### Validación antes de dar por cerrado un slice migrado
- `php -l` en ficheros tocados.
- Comparar salida relevante con el baseline (ids, columnas, cardinalidad).
- Probar con datos y caso vacío si aplica; si depende de ámbito (`rstgr`, etc.), probar ramas o documentar riesgo.

Punto retirado de «Qué evitar al migrar pantallas»:

- No eliminar wrappers `apps/` hasta que no queden referencias (y BD si aplica).

---

## Excepción de texto plano en `centros_update`

*Retirado porque `centros_update` ya responde con `ContestarJson::enviar`.*

De «Backend (Controladores)»:

- **Prohibido en `src/.../infrastructure/ui/http/controllers`**: `echo` de HTML, `die("msg")`, `print`, respuestas texto arbitrarias. Excepción ya acordada: formularios legacy que lean `.done(rta_txt)` sin JSON (p. ej. rutas tipo `centros_update`); documentar el motivo en el propio fichero.

De «Patrones de referencia en `ubis`»:

- Respuesta **texto plano** solo donde el consumidor legacy lo exige: p. ej. `centros_update` con `Content-Type: text/plain` y cuerpo desde caso de uso; formularios con `HashF` deben usar **URL absoluta** `rtrim(ConfigGlobal::getWeb(), '/') . '/src/ubis/centros_update'` para que el hash coincida.

---

## Enlaces firmados (`HashF::link`): pilotos e inventario

*Retirado porque ya no queda ningún uso real de `HashF` en `src/`; la directiva sigue en `AGENTS.md`.*

4. **Documentación de arquitectura**: `docs/dev/hash_arquitectura.md` (§7.4) y pilotos de referencia: `GruposLista` + `grupo_lista.php`, `usuariosLista` + `usuario_lista.php`, `UbisTablaData` + `ubis_tabla.php` (celdas con `link_spec`; además `pagina_link_spec` → `pagina_link` firmado en el controlador), `ListCtrData` + `list_ctr.php`, `ListaActivTabla` + `lista_activ_datos.php` (JSON con `link_spec`) + firma y `Lista::mostrar_tabla` en `frontend/actividades/controller/lista_activ.php`, `ActividadSelectListado` + `actividad_select_datos.php` (JSON con `link_spec` y `advertencia_demasiadas`) + firma y `Lista::mostrar_tabla` en `frontend/actividades/controller/actividad_select.php`, `ListaActividadesSgListado` + `lista_actividades_sg_datos.php` (JSON con `link_spec` y `advertencia_demasiadas`) + firma y `Lista::mostrar_tabla` en `frontend/actividades/controller/lista_actividades_sg.php`, `HabitacionesCamaLista` + `actividad_habitaciones_lista.php` (`reload_main_link_spec` / `distribucion_open_link_spec` / `nombres_open_link_spec`; firma con `HashFSignedLink` en `frontend/ubiscamas/controller/lista_habitaciones.php`; mutaciones AJAX `update_cama_asistente` y `update_solo_vip` con `HashB::sign` en el endpoint y POST `ctx`), `SelectHabitacionesCdc::getSegmentData()` + tipo `select_habitaciones_cdc` en `DossiersVerPantallaData` → `frontend/ubiscamas/helpers/SelectHabitacionesCdcRender.php` (`HashF` + `Lista` + `SelectHabitacionesCdcUrlSigning` al pintar desde `dossiers_ver.php`), `Select_certificados_de_una_persona` + `frontend/certificados/helpers/SelectCertificadosDeUnaPersonaUrlSigning.php`, `Select_notas_de_una_persona` + `frontend/notas/helpers/SelectNotasDeUnaPersonaUrlSigning.php`, `ActivPendientesSelectData` + `activ_pendientes_select_data.php` (`link_spec` → `home_persona` en `frontend/personas/...`), `ActividadTipo` (Twig de filtros tipo actividad) + `frontend/shared/helpers/ActividadTipoTwigHashCompose.php` (tokens `h` / `h_act` para AJAX); formularios gestión tipo (`TipoActivFormNuevo` / `TipoActivFormModificar`) + `frontend/shared/helpers/TipoActivGestionFormHashCompose.php` (`getCamposHtml`), `FichaProfesorStgr` + `frontend/profesores/controller/ficha_profesor_stgr.php` (`go_cosas_link_specs` / `ficha_self_link_spec`; los enlaces a `tablaDB_lista_ver.php` reciben `go_to` firmado desde la spec de la ficha), módulo dossiers: `DossiersListaFichasData` (`href_*_link_spec`) + firma en `frontend/dossiers/controller/lista_dossiers.php` (`HashFSignedLink::signRowLinkSpecs`); `DossiersVerPantallaData` (datos planos: `top_data`, `ficha_segmentos` con `action_tabla_link_spec` / `ins_traslado_link_spec` / `script_ctx` / `hash`) + firma y render en `frontend/dossiers/controller/dossiers_ver.php` y helper `frontend/dossiers/helpers/DossiersVerFichaDatosTabla.php` (el `<script>` de `DatosTablaRepo` también se compone en frontend); `PermDossiersListaData` (`pagina_link_spec`) + firma en `frontend/dossiers/controller/perm_dossiers.php`; `PermDossierVerFormData` expone `go_to_link_spec` y `hash_config` y el `HashF` se instancia en `frontend/dossiers/controller/perm_dossier_ver.php`; `DossierTipoPublicUrls::formControllerLinkSpec` en los `Select_*` que enlazan al form dossier + `frontend/dossiers/helpers/DossierTipoFormLinkSpecsSigning.php` (`HashF::link` al renderizar `getHtml()`); helper genérico reutilizable `frontend/shared/security/HashFSignedLink.php` (`fromSpec`, `fromSpecMap`, `signRowLinkSpecs`).

**Inventario — `HashF::link` aún presente en `src/` (pendiente de alinear con esta directiva):**

No quedan excepciones conocidas: menus, encargossacd y notas devuelven datos o `link_spec` sin
firmar, y el frontend compone las URLs `HashF`. Actualizar este inventario si se detecta otra
excepción.

---

## `PermisosActividades`: redacción original de la separación objetivo

*Sustituido en `AGENTS.md` por una versión corta. La separación sigue pendiente (ver `backlog.md`).*

La clase **`src\permisos\domain\PermisosActividades`** nació para **tener en sesión una matriz de permisos** (reglas por `id_tipo_activ_txt`, DL propia / otras delegaciones) cargada desde `aux_usuarios_perm`, de modo que las vistas puedan **consultar permisos sin nueva ida a BD** para cada pintado.

**Separación objetivo (evolución recomendada):**

1. **Read model en sesión (solo datos ya cargados)**  
   Lo que pertenece a la sesión es la **matriz cacheada** (`aPermDl`, `aPermOtras` y la lógica que recorre tipos / fases ref **solo con esos arrays**). Idealmente sería un tipo dedicado (p. ej. *matriz* o *snapshot*) serializable, sin métodos que asuman `$GLOBALS['container']` ni `$GLOBALS['oDBE']` en requests frontend que solo pasan por `FrontBootstrap::boot()` (sin `global_object.inc`).

2. **Resolución con I/O en el backend (`src/` + petición HTTP)**  
   Cualquier cosa que implique **consultar la actividad por `id_activ`**, **estado de fase en proceso** (`faseCompletada`, etc.) o **árbol de procesos / tipos** (`getPermisoCrear` y similares) es **caso de uso de aplicación / infra**: debe ejecutarse en un script bajo `global_object` (o endpoint JSON) y, desde controladores **`frontend/`**, llegar como **`PostRequest::getDataFromUrl`** o datos ya incluidos en un DTO grande (p. ej. ampliar `actividad_ver_datos`), no llamando al contenedor desde un objeto de sesión.

3. **Transición práctica**  
   Mientras la clase siga siendo monolítica, los controladores frontend deben **pasar contexto ya conocido** (`id_tipo_activ`, `dl_org`) cuando el backend ya lo devolvió por JSON, y migrar gradualmente las ramas que hoy usan repositorios dentro de la clase hacia servicios o `*_datos.php` dedicados.

   **Fases de proceso (permisos on/off):** usar **`/src/actividades/actividad_fases_completadas_datos`** (`id_activ` → `fases_completadas`) antes de `getPermisoActual` / `getPermisoOn` en flujos solo-frontend (p. ej. `PrefillPermActividadesFases::desdeBackend`). Consulta unitaria equivalente a `faseCompletada`: **`/src/actividades/actividad_fase_completada_datos`** (`id_activ`, `id_fase` → `completada`).

---

## Camino interno de `PostRequest` descrito como futuro

*Sustituido: `PostRequest::dispatchInProcess()` ya existe y hace `require` del controlador.*

#### Preparar endpoints para un futuro camino interno

El contrato vigente sigue siendo HTTP: `frontend/` llama a `/src/...` con `PostRequest`, y el endpoint responde con `ContestarJson`. Para dejar abierta una optimización futura sin HTTP cuando frontend y API estén en la misma instalación, los endpoints nuevos o migrados deben cumplir:

1. **Controlador HTTP fino:** no meter lógica de negocio en `src/.../infrastructure/ui/http/controllers/*.php`. El controlador lee `$_POST`/request, construye input, llama a `application` y responde.
2. **Caso de uso invocable directamente:** la lógica debe vivir en una clase de `application/` que devuelva arrays/DTOs serializables y no dependa de `header()`, `echo`, `exit`, `ContestarJson`, `$_POST` ni de una subpetición HTTP.
3. **Sin incluir controladores como atajo:** si algún día `PostRequest` tiene un dispatcher interno, debe mapear rutas preparadas a casos de uso; las rutas no registradas harán fallback a HTTP.
4. **`ContestarJson` no decide el dispatch:** su responsabilidad es envolver la respuesta JSON del camino HTTP, no ejecutar casos de uso ni elegir entre HTTP/camino interno.

---

## Búsquedas con `Criteria`: estado de la migración

*Retirado: la migración está completada; el plan sigue en `refactor_busquedas_criteria.md`.*

Plan y estado de la migración: [`docs/dev/refactor_busquedas_criteria.md`](docs/dev/refactor_busquedas_criteria.md). Migración completada (fases 1–5): ya no existen en `src/` los `getXxx(array $aWhere, array $aOperators)`, ni `LegacyWhereAdapter`, ni `Condicion`. Módulo de referencia: `asistentes` (`AsistenteField`, `PgAsistenteRepository::matching()`, `AsistenteActividadService`).

- **Tests:** un `matching()` nuevo lleva test de integración; al migrar, el test de caracterización del `getXxx` antiguo debe seguir en verde sin cambios.

---

## Namespaces `core\` y `web\`

*Retirado porque no queda ningún `use core\...` ni `use web\...` en `src/` ni `frontend/`.*

De «Reglas de capas»:

  - `use core\...` (salvo utilidades puramente de dominio justificadas).
  - `use web\...`, `use frontend\...`.
- Cualquier uso de `core\...`, `web\...`, PDO o globals debe quedar aquí.

De «Pitfalls»:

4. **`src/` sin `core\` implícito**  
   Clases en `src/domain` (o servicios usados solo vía JSON) no deben extender o importar **`core\...`** que no esté garantizado por el autoload de esa petición; si hace falta un helper, usar uno en `src/shared/domain/helpers/` o un adaptador en `infrastructure`.

---

## Estructura mínima por módulo (versión anterior)

*Sustituido: ningún módulo usa `infrastructure/controllers/` ni `infrastructure/repositories/`.*

```text
src/<modulo>/
  application/
  domain/
    contracts/
    entity/
    value_objects/
  infrastructure/
    controllers/
    repositories/
  config/
    dependencies.php
    routes.php
```

Del ejemplo de ubiscamas:

```text
frontend/ubiscamas/
  controller/
    habitacion_form.php    ← Prepara datos para el formulario
    cama_form.php         ← Prepara datos para el formulario
    lista_habitaciones.php ← PostRequest a `actividad_habitaciones_lista`; convierte `*_link_spec` con `HashFSignedLink`
  helpers/
    SelectHabitacionesCdcUrlSigning.php
    SelectHabitacionesCdcRender.php ← Bloque dossier habitaciones (`HashF` + `select_habitaciones_cdc.phtml`)
    UbiscamasFormHashCompose.php ← `HashF` para `habitacion_form` / `cama_form` (datos desde `HabitacionFormData` / `CamaFormData`)
  view/
    habitacion_form.phtml ← Vista HTML del formulario
    cama_form.phtml      ← Vista HTML del formulario
    select_habitaciones_cdc.phtml ← Vista de listado

src/ubiscamas/
  domain/
    contracts/
      HabitacionRepositoryInterface.php
      CamaRepositoryInterface.php
    entity/
      Habitacion.php
      Cama.php
    value_objects/
      HabitacionId.php
      CamaId.php
      BañoTipo.php
    SelectHabitacionesCdc.php ← `getSegmentData()` (sin `HashF`; render en frontend)
  infrastructure/
    ui/http/controllers/
      actividad_habitaciones_lista.php ← `link_spec` + `HashB` (`ctx` para update cama / solo VIP); sin `HashF`
    controllers/
      habitacion_update.php ← Lógica de guardado
      cama_update.php      ← Lógica de guardado
      cama_delete.php      ← Lógica de eliminación
    repositories/
      PgHabitacionRepository.php
      PgCamaRepository.php
  config/
    dependencies.php
```
