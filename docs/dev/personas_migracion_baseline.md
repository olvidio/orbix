# Baseline migracion `apps/personas` → `frontend/personas` + `src/personas`

Documento de referencia segun `refactor.md` (misma linea que `misas`, `procesos`, `encargossacd`, `planning`).

## Estado de partida

El modulo `personas` esta **100% en `apps/personas/`** desde el punto de vista de UI. `src/personas/` contiene dominio (entidades, value objects, repositorios) y un servicio (`TelecoPersonaService`), pero **no hay `application/*`, `infrastructure/ui/http/controllers/*` ni `config/routes.php`**.

```
apps/personas/
├── controller/                                (9 ficheros, 1.809 lineas)
│   ├── home_persona.php                 (169)
│   ├── personas_editar.php              (420)
│   ├── personas_que.php                 (100)
│   ├── personas_select.php              (528)  — base64 para sWhere/sOperador, web\Lista, 15+ fnjs_*
│   ├── personas_update.php              (155)  — dispatcher $Qque = eliminar | (implicito guardar)
│   ├── stgr_cambio.php                  (101)
│   ├── stgr_update.php                  ( 65)
│   ├── traslado_form.php                (103)
│   └── traslado_update.php              (132)  — echo $error en texto plano
├── model/                                     (2 ficheros, 65 lineas, DEAD CODE)
│   ├── Info1004.php                     (DatosInfo legacy — referencia clase inexistente 'personas\model\entity\Traslado')
│   └── InfoLatin.php                    (DatosInfo legacy — referencia clase inexistente 'personas\model\entity\NombreLatin')
└── view/                                      (11 ficheros, 1.560 lineas)
    ├── home_persona.phtml               ( 44)
    ├── home_persona_lista_dossiers_html.php ( 70)  DEAD CODE — sin includes
    ├── persona_de_paso.phtml            (271)   JS duplicado (fnjs_guardar/eliminar)
    ├── persona_form.phtml               (299)   JS duplicado
    ├── persona_form.old.phtml           (205)   DEAD CODE
    ├── persona_sss_form.phtml           (198)   JS duplicado
    ├── p_public_personas.phtml          (125)   incluido para roles sin permiso
    ├── personas_que.phtml               ( 48)
    ├── personas_select.phtml            (195)   13 fnjs_* condicionales
    ├── stgr_cambio.phtml                ( 33)
    ├── titulo_persona.phtml             ( 15)   DEAD CODE — no se incluye
    └── traslado_form.phtml              (100)
```

### Consumidores externos detectados

- `docs/legacy/obix/menus.csv` con entradas a `apps/personas/controller/personas_que.php` y a `home_persona.php` para distintos roles (dre, sm, sg, agd, nax, est, rstgr, sss).
- `apps/notas/controller/asig_faltan_select.php`, `asig_faltan_personas_select.php` → link directo a `home_persona.php`.
- `apps/dossiers/controller/dossiers_ver.php` → `home_persona.php` y `traslado_form.php`.
- `frontend/planning/controller/planning_persona_select.php` → `home_persona.php`.
- `apps/asistentes/controller/activ_pendientes_select.php` → `home_persona.php`.
- Las vistas que definen `fnjs_*` para que `personas_select.phtml` las invoque estan en `apps/actividadessacd/`, `apps/actividadestudios/`, `apps/actividadplazas/`, `apps/dossiers/`, `apps/notas/`, `frontend/certificados/`, `frontend/profesores/` (via `$(formulario).attr('action', …)`).

### Puntos fuera de `refactor.md`

1. **Toda la UI vive en `apps/`**; ningun endpoint `/src/personas/...` existe todavia.
2. **`personas_update.php` es un dispatcher** (`$Qque === 'eliminar'` vs guardar implicito). Hay que partirlo en `personas_update` + `personas_eliminar`.
3. **`traslado_update.php` responde con `echo $error`** (texto plano). Ademas concentra dos acciones (cambio de centro + cambio de dl) que podrian ser dos endpoints.
4. **Contrato mixto de JSON**: `stgr_update` y `personas_update` devuelven `{success,mensaje}` correcto, pero los consumidores en `persona_form.phtml` y `persona_de_paso.phtml` leen `rta_txt !== ''` como si fuera texto plano — contrato inconsistente (solo la rama `persona_form` moderna usa `json.success`).
5. **`personas_select.php` serializa `$aWhere`/`$aOperador` en base64** (`urlsafe_b64encode(json_encode(...))`) para reenviarlos al mismo endpoint en la siguiente iteracion — patron legacy equivalente al que se eliminó en `planning_casa_*`. Se puede recomputar desde los filtros.
6. **Dispatcher `$Qque` en `personas_que.php`** para `telf` apuntando a `personas_select_telf.php` — **el fichero destino no existe**; rama muerta desde hace tiempo.
7. **Duplicacion de JS** en `persona_form.phtml`, `persona_de_paso.phtml`, `persona_sss_form.phtml`: mismos `fnjs_guardar` / `fnjs_eliminar` / `fnjs_act_ctr` triplicados.
8. **Duplicacion de resolucion `obj_pau → repositorio`**: cada controller (`home_persona`, `personas_editar`, `personas_update`, `stgr_cambio`, `stgr_update`, `traslado_update`) instancia la clase anonima `new class { use ProvidesRepositories; … }`. Es el mismo helper 6 veces copiado.
9. **Duplicacion mapa `id_tabla → obj_pau`** en `stgr_cambio`, `stgr_update` y parcial en `home_persona`.
10. **Vistas con paths `apps/personas/controller/...`** hardcodeados en `fnjs_update_div` y `$(formulario).attr('action', …)`.
11. **Dead code**:
    - `apps/personas/controller/personas_select_telf.php` (referenciado, no existe).
    - `apps/personas/view/persona_form.old.phtml` (205 lineas, sin uso).
    - `apps/personas/view/home_persona_lista_dossiers_html.php` (70 lineas, sin include).
    - `apps/personas/view/titulo_persona.phtml` (15 lineas, sin include).
    - `apps/personas/model/Info1004.php` y `InfoLatin.php` (extienden `DatosInfo`, pero no se instancian desde ningun sitio; apuntan a `personas\model\entity\*` que ya no existe).
12. **`personas_editar.php` instancia `new src\personas\model\entity\PersonaX`** (linea 48) — namespace `src\personas\model\entity` **ya no existe** (el dominio esta en `src\personas\domain\entity`). Esa variable se pasa a la vista como `$obj_txt` y se usa en `fnjs_comprobar_campos` pero solo como string. Quiza es simbolico, pero rompe el grep de consistencia.

## Contrato de salida

- `home_persona`, `personas_editar`, `personas_que`, `personas_select`, `stgr_cambio`, `traslado_form` → HTML (ViewPhtml).
- `personas_update`, `stgr_update` → JSON `{success,mensaje,data:"ok"}` (aunque se lea mal en cliente en dos de las tres vistas).
- `traslado_update` → texto plano (`echo $error`).

## Casos `rstgr` / permisos

- `personas_select` y `personas_editar` ramifican por `$_SESSION['oPerm']->have_perm_oficina('des|agd|sm|sg|est|vcsd|dtor')` y por `ConfigGlobal::mi_ambito()` (`rstgr` recorta botones).
- `personas_update` solo permite eliminar a personas de la misma `dl` (`ConfigGlobal::mi_delef() === $oPersona->getDl()`).
- `traslado_form` bloquea personas de paso (`PersonaPub`).

## Plan de migracion por slices

### Slice 0 — Scaffolding + baseline (este commit)

- Crear `frontend/personas/{controller,view,support}`, `src/personas/{application,infrastructure/ui/http/controllers,config}`.
- `src/personas/config/routes.php` vacio (registro).
- Documento `docs/dev/personas_migracion_baseline.md`.

### Slice 1 — Helper transversal + pantalla `stgr`

- `src/personas/application/support/PersonaRepositoryResolver.php`: centralizar la resolucion `obj_pau`/`id_tabla` → repositorio (elimina la clase anonima duplicada en 6 sitios).
- `src/personas/application/StgrUpdate.php` + endpoint `/src/personas/stgr_update` → migrar `stgr_update.php` (respuesta JSON ya estandar).
- `frontend/personas/controller/stgr_cambio.php` + `view/stgr_cambio.phtml` (con `ViewNewPhtml`).
- Wrappers legacy en `apps/personas/controller/stgr_{cambio,update}.php`.

### Slice 2 — Flujo `personas_update` dividido

- **Dividir dispatcher `$Qque` en 2 endpoints:**
  - `src/personas/application/PersonaUpdate.php` → `/src/personas/persona_update`.
  - `src/personas/application/PersonaEliminar.php` → `/src/personas/persona_eliminar`.
- Adaptar consumidores JS en `persona_form.phtml` / `persona_de_paso.phtml` / `persona_sss_form.phtml` para llamar al endpoint correspondiente y leer `json.success` / `json.mensaje` (homogeneizar con `fnjs_guardar_stgr`).
- Unificar los 3 scripts duplicados en `frontend/personas/support/persona_form_scripts.phtml` (o un helper JS comun).

### Slice 3 — `home_persona` + `personas_editar` (presentacion)

- `frontend/personas/controller/home_persona.php` + `view/home_persona.phtml`.
- `frontend/personas/controller/personas_editar.php` + `view/persona_form.phtml` + `view/persona_de_paso.phtml` + `view/persona_sss_form.phtml` + `view/p_public_personas.phtml`.
- Eliminar `new src\personas\model\entity\*` inexistente; usar el FQCN correcto `src\personas\domain\entity\*`.
- Wrappers legacy en `apps/personas/controller/{home_persona,personas_editar}.php`.
- Ajustar paths hardcodeados `apps/personas/controller/...` a `frontend/personas/controller/...`.

### Slice 4 — `personas_que` + `personas_select` (listados)

- `frontend/personas/controller/personas_que.php` + `view/personas_que.phtml` (quitar rama muerta `$Qque === 'telf'`).
- `src/personas/application/PersonasBuscarData.php` para devolver la lista de personas segun filtros (puede exponer tabla, cabeceras, valores). **Quitar el base64 de `sWhere`/`sOperador`** — recomputar en backend a partir de los filtros originales.
- `frontend/personas/controller/personas_select.php` delgado: `PostRequest` + construccion de `web\Lista` + `web\Hash` en vista.
- Extraer los `fnjs_*` condicionales a `frontend/personas/support/SeleccionScripts.php` o emitirlos desde un helper uniforme.
- Actualizar paths hardcodeados (`apps/personas/...` → `frontend/personas/...`) en consumidores frontales (`asig_faltan_*`, `dossiers_ver`, `planning_persona_select`, `activ_pendientes_select`).

### Slice 5 — `traslado_form` + `traslado_update`

- Partir `traslado_update.php` en dos acciones (`traslado_centro` y `traslado_dl`) o mantener un solo endpoint + respuesta JSON estandar `{success,mensaje}` (decidir al migrar segun cuantos consumidores hay).
- `src/personas/application/Traslado{Centro,Dl}.php` + endpoint bajo `/src/personas/traslado_update`.
- `frontend/personas/controller/traslado_form.php` + `view/traslado_form.phtml`.
- Adaptar `fnjs_guardar` de `traslado_form.phtml` para consumir JSON (`json.success` / `json.mensaje`) en lugar de `rta_txt`.
- Wrappers legacy en `apps/personas/controller/traslado_{form,update}.php`.

### Slice 6 — Limpieza final

- Eliminar dead code:
  - `apps/personas/controller/personas_select_telf.php` (la rama en `personas_que.php` al migrarse).
  - `apps/personas/view/persona_form.old.phtml`.
  - `apps/personas/view/home_persona_lista_dossiers_html.php`.
  - `apps/personas/view/titulo_persona.phtml`.
  - `apps/personas/model/Info1004.php` y `InfoLatin.php` (verificar grep final).
- Actualizar `docs/legacy/obix/menus.csv` y `proves/aux_metamenus.csv` a `frontend/personas/controller/...`.
- `php -l` en todo `frontend/personas`, `src/personas`, `apps/personas`.
- Actualizar este baseline con estado final y estructura canonica resultante.

## Principios aplicados

- `src/personas/application/*` devuelve arrays o strings; nunca HTML ni `web\Lista`/`web\Desplegable`.
- `src/personas/infrastructure/ui/http/controllers/*` solo llama al caso de uso y responde `ContestarJson::enviar($error, $data)`.
- `frontend/personas/controller/*` delgado: `PostRequest` a `/src/personas/...` + `web\Desplegable` / `web\Lista` cuando la pantalla lo requiera.
- Mutaciones (`*_update`, `*_eliminar`) devuelven `{success,mensaje}` — nunca `echo`, nunca cuerpo vacio.
- JS consumidor uniforme: `dataType: 'json'`, rama `success === true` / `success === false`.

## Riesgos y consideraciones

- Muchos consumidores externos apuntan a `home_persona.php` y `traslado_form.php` — la convivencia con wrappers tiene que ser sostenida al menos hasta actualizar todos los `Hash::link` emitidos en otros modulos.
- Hay tests probablemente dependientes de los paths legacy; comprobar `proves/` y `test/` antes de eliminar wrappers.
- `personas_editar.php` usa `DBPropiedades` para filtrar dl disponibles en caso `PersonaEx` nuevo — mantener ese comportamiento al mover la logica.
- El campo `edad` de `persona_de_paso.phtml` no aparece en `$a_campos` del controller (posible bug latente heredado).

## Estado del slice actual

- **Completados**: Slice 0, 1, 2, 3, 4, 5 y 6.
- **No tocado (por decision del usuario)**: `apps/personas/model/Info1004.php` y `apps/personas/model/InfoLatin.php`.

### Resumen del Slice 6 (limpieza final)

- Eliminado `apps/personas/controller/personas_update.php` (dispatcher sustituido por `persona_update.php` + `persona_eliminar.php`).
- Eliminado `apps/personas/controller/traslado_update.php` (sustituido por el endpoint JSON en `src/personas/infrastructure/ui/http/controllers/traslado_update.php`).
- Vaciado completo de `apps/personas/view/` (todas las vistas estan ahora en `frontend/personas/view/`).
- `apps/personas/controller/` contiene solo wrappers thin (`home_persona`, `personas_editar`, `personas_que`, `personas_select`, `stgr_cambio`, `traslado_form`).
- `docs/legacy/obix/menus.csv` y `proves/aux_metamenus.csv` ya apuntan a `frontend/personas/controller/...`.
- `php -l` limpio en `frontend/personas`, `src/personas`, `apps/personas`.

### Slice 4b — Completar separacion `personas_select` (seguimiento)

El slice 4 original migro `personas_select.php` a `frontend/personas/` pero dejo
en el controlador frontend llamadas directas a `src\*\domain\contracts\*RepositoryInterface`
y al contenedor via `$GLOBALS['container']`, en contra del patron de
`refactor.md` ("Separacion frontend <-> backend: nunca instanciar `src` desde
`frontend/view/controller`"). Esta iteracion cierra ese hueco:

- `src/personas/application/PersonasSelectData.php` encapsula la logica de
  acceso a `src/` (resolucion de `tabla` segun `PauType::PAU_NOM`, filtros
  construidos sobre `aWhere`/`aOperador`, busqueda de centros, consulta de
  personas por tabla, preferencia `tabla_presentacion` y traduccion de
  `nivel_stgr`). Devuelve un array neutro con las filas listas para la
  `web\Lista`.
- `src/personas/infrastructure/ui/http/controllers/personas_select_data.php` es
  un endpoint JSON minimo que responde con `ContestarJson::enviar`; traduce el
  caso especial `tabla = 'nada'` a `success: false` + mensaje localizado.
- Ruta `/src/personas/personas_select_data` anadida a `src/personas/config/routes.php`.
- `frontend/personas/controller/personas_select.php` queda delgado:
  `PostRequest::getDataFromUrl('/src/personas/personas_select_data', $campos)`
  + construccion de `web\Lista`, `web\Hash`, botones/scripts y render. Ya
  **no** importa `use src\...` ni toca el contenedor.

### Slice 4c — Completar separacion de los 4 controllers restantes

Los controllers `stgr_cambio`, `traslado_form`, `personas_editar` y
`home_persona` tambien arrastraban `use src\...` y `$GLOBALS['container']`
directo en la capa frontend. Esta iteracion aplica el mismo patron que
`personas_select` a los cuatro. Queda toda la capa `frontend/personas/`
libre de imports `src\` y de acceso al contenedor.

- **`stgr_cambio`**:
  - `src/personas/application/StgrCambioData.php` resuelve el repositorio,
    recupera la persona y devuelve `nom`, `nivel_stgr` actual y
    `opciones_nivel_stgr` (mapa `value => etiqueta`).
  - `src/personas/infrastructure/ui/http/controllers/stgr_cambio_data.php`
    expone `/src/personas/stgr_cambio_data`.
  - `frontend/personas/controller/stgr_cambio.php` construye el
    `web\Desplegable` + `web\Hash` con lo recibido.
- **`traslado_form`**:
  - `src/personas/application/TrasladoFormData.php` localiza la persona con
    `Persona::findPersonaEnGlobal`, bloquea `PersonaPub`, y devuelve
    `titulo`, `id_ctr`, `nombre_ctr`, `dl`, `hoy` y las tres listas de
    opciones (centros, delegaciones via `DelegacionDropdown::listaRegDele(false)`,
    situaciones via `SituacionRepository::getArraySituaciones(traslado: true)`).
  - `src/personas/infrastructure/ui/http/controllers/traslado_form_data.php`
    expone `/src/personas/traslado_form_data`.
  - `frontend/personas/controller/traslado_form.php` solo construye
    `web\Desplegable` / `web\Hash` / links.
- **`personas_editar`**:
  - `src/personas/application/PersonasEditarData.php` gestiona los dos modos
    (`nuevo=1` y edicion): en alta genera `id_nom` via
    `getNewIdNom(getNewId())` y aplica defaults (`situacion='A'`,
    `idioma_preferido = ConfigGlobal::mi_Idioma()`, `dl = ConfigGlobal::mi_dele()`);
    en edicion lee todos los getters y resuelve `nom_ctr` con el repo segun
    ambito. Calcula `id_tabla` canonico (`PersonaAgd => 'a'`, etc, con el
    fallback `PersonaEx => 'pn'` cuando no viene) y devuelve las listas de
    delegaciones (aplicando `DBPropiedades::array_posibles_dl_de_esquemas`
    para el caso alta de `PersonaEx`), centros (solo si aun no se conoce
    el centro), situacion, idioma, nivel_stgr e inc.
  - `src/personas/infrastructure/ui/http/controllers/personas_editar_data.php`
    expone `/src/personas/personas_editar_data`.
  - `frontend/personas/controller/personas_editar.php` conserva unicamente
    la logica dependiente del frontend: `Posicion`/`stack`, el
    `switch($Qobj_pau)` que decide `presentacion` y `botones` segun
    `$_SESSION['oPerm']`, construccion de `web\Desplegable` / `web\Hash` y
    generacion de links con `AppUrlConfig` + `Hash::link`.
- **`home_persona`**:
  - `src/personas/application/HomePersonaData.php` resuelve el repositorio,
    carga la persona, traduce `nivel_stgr` a su etiqueta, consulta los
    telecos (`telf` / `movil` / `e-mail`) via `TelecoPersonaService`, y si
    procede resuelve el nombre del centro. Tambien normaliza `Qobj_pau`
    cuando la entidad es el alias `PersonaDl`.
  - `src/personas/infrastructure/ui/http/controllers/home_persona_data.php`
    expone `/src/personas/home_persona_data`.
  - `frontend/personas/controller/home_persona.php` queda reducido a
    `Posicion`, session `session_go_to`, `PostRequest` y construccion de
    links (`gohome`, `go_ficha`, `godossiers`).

Principios respetados en todos ellos:

- El backend (`*Data`) devuelve arrays neutros; no construye
  `web\Desplegable` ni `web\Lista`.
- El frontend no importa nada de `src\` y no usa `$GLOBALS['container']`.
- Los errores del backend viajan como `success=false` + `mensaje` a traves
  de `ContestarJson::enviar($error)`, de modo que `PostRequest` los
  propaga con el mismo trato que en el resto del modulo.
- El `switch` de permisos y la seleccion de plantilla siguen en el
  frontend porque dependen de `$_SESSION['oPerm']` y son decisiones de
  capa UI.

### Estructura canonica resultante

- `frontend/personas/controller/`: `home_persona.php`, `personas_editar.php`, `personas_que.php`, `personas_select.php`, `stgr_cambio.php`, `traslado_form.php` — todos sin `use src\` ni `$GLOBALS['container']`.
- `frontend/personas/view/`: vistas PHTML + partials (`_persona_form_js.phtml`, `_persona_header.phtml`, `_persona_form_botones.phtml`).
- `src/personas/application/`: `HomePersonaData`, `PersonaEliminar`, `PersonaUpdate`, `PersonasEditarData`, `PersonasSelectData`, `StgrCambioData`, `StgrUpdate`, `TrasladoFormData`, `TrasladoUpdate` + `support/PersonaRepositoryResolver`.
- `src/personas/infrastructure/ui/http/controllers/`: `home_persona_data.php`, `persona_eliminar.php`, `persona_update.php`, `personas_editar_data.php`, `personas_select_data.php`, `stgr_cambio_data.php`, `stgr_update.php`, `traslado_form_data.php`, `traslado_update.php` (todos `ContestarJson::enviar`).
- `src/personas/config/routes.php`: 9 rutas registradas.
- `apps/personas/controller/`: 6 wrappers legacy delgados (`require` al `frontend`).
- `apps/personas/model/`: intacto (`Info*` pendiente de decision posterior).

## Cierre DI (junio 2026)

Migracion al patron de modulos cerrados (`cambios`, `casas`, `asignaturas`):
constructor DI en application/domain, `DependencyResolver::get()` en controllers HTTP,
`GlobalPdo::get()` en repos `Pg*`, 0 `$GLOBALS['container']` en todo `src/personas/`.

### Resultado del cierre DI

| Criterio | Estado |
|----------|--------|
| `$GLOBALS['container']` en `src/personas/` | **0** (antes ~53) |
| Controllers HTTP con `DependencyResolver::get()` | **9/9** |
| `application/` con constructor DI | **9** clases + `PersonaRepositoryResolver` |
| Casos de uso en `config/dependencies.php` | **28** entradas `autowire()` |
| Pg repos con `GlobalPdo` | **16** repos |
| Frontend sin `use src\...` en controladores | **0** imports |
| Tests `tests/unit/personas/` | **262 OK** (4 warnings clases vacias) |

### `src/personas/config/dependencies.php`

Registra 16 repositorios del modulo + `TelecoPersonaService`, `Trasladar`, `PersonaFinderService`,
`PersonaRepositoryResolver`, 4 clases `Info*` y 9 casos de uso HTTP.

Repos cross-modulo (`Centro*`, `Delegacion*`, `Dossier*`, `Preferencia*`, `Local*`, `DescTeleco*`,
`Asignatura*`, `ActividadAll*`, etc.) se resuelven por autowire desde los `dependencies.php`
de sus modulos (merge en bootstrap).

### Application layer (constructor DI)

| Clase | Dependencias inyectadas |
|-------|------------------------|
| `HomePersonaData` | `PersonaRepositoryResolver`, `PersonaPubRepositoryInterface`, `CentroDlRepositoryInterface`, `TelecoPersonaService` |
| `PersonaEliminar` | `PersonaRepositoryResolver` |
| `PersonaUpdate` | `PersonaRepositoryResolver` |
| `PersonasEditarData` | `PersonaRepositoryResolver`, `CentroDl/Centro`, `Delegacion`, `Situacion`, `Local` repos |
| `PersonasSelectData` | 7 repos persona + `CentroDl/Centro`, `PreferenciaRepositoryInterface` |
| `StgrCambioData` | `PersonaRepositoryResolver` |
| `StgrUpdate` | `PersonaRepositoryResolver` |
| `TrasladoFormData` | `PersonaFinderService`, `CentroDlRepositoryInterface`, `SituacionRepositoryInterface` |
| `TrasladoUpdate` | `PersonaRepositoryResolver`, `Centro`, `Traslado`, `Dossier`, `Trasladar` |

`PersonaRepositoryResolver` usa `DependencyResolver::get()` (no `$GLOBALS['container']`).

### Domain

| Clase | Cambio |
|-------|--------|
| `Trasladar` | Constructor DI (6 repos + `GlobalPdo::get('oDBR')`) |
| `TelecoPersonaService` | `TelecoPersonaRepositoryInterface` inyectado |
| `InfoSituacion`, `InfoTraslado`, `InfoUltimaAsistencia`, `InfoTelecoPersona` | Repo inyectado en constructor (patron `InfoZona`) |
| `Persona` (deprecated) | `DependencyResolver::get(PersonaFinderService::class)` |
| Entidades `PersonaPub/Ex/Global/Sacd` | `getCentro_o_dl()` via `DependencyResolver::get(Centro*RepositoryInterface::class)` |

### Repositorios `Pg*`

Todos usan `GlobalPdo::get('oDB'|'oDBR'|'oDBP'|'oDBPC'|'oDBC'|'oDBF')` segun tabla.
`PgPersonaPubRepository` y `PgPersonaAllRepository` inyectan repos cross-modulo por constructor.

### HTTP controllers

Los 9 controllers en `infrastructure/ui/http/controllers/` usan
`DependencyResolver::get()` (sin `::execute()` / `::build()` estaticos).
Entrada POST via `input_int` / `input_string` donde aplica.

### PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------|
| 2026-06-06 (inicio cierre DI) | `composer phpstan:file -- src/personas/` | **755** |
| 2026-06-06 (cierre DI) | `composer phpstan:file -- src/personas/` | **0** |

Areas abordadas:

- **Application:** constructor DI; `instanceof XPermisos` para sesion; tipos en payloads.
- **Repos `Pg*`:** `GlobalPdo`, guards `PDOStatement|false`, PHPDoc retorno, `PersonaGlobalListsTrait`.
- **Interfaces / entity / VOs:** PHPDoc retorno; nullable setters; `Trasladar` tipado.
- **HTTP controllers:** `DependencyResolver::get()` + `input_*`.
- **Info* / services:** DI por constructor.

### Checklist de cierre

Ver [`REFACTOR_INDICE.md`](REFACTOR_INDICE.md#checklist-cerrar-un-módulo).

- [x] `$GLOBALS['container']` migrado a DI por constructor en `application/` y domain
- [x] Controllers HTTP sin `$GLOBALS` directo (`DependencyResolver`)
- [x] `dependencies.php` con todos los use cases
- [x] Tests unitarios application pasan (`tests/unit/personas/application/`: 24 tests)
- [x] PHPStan `src/personas/` en 0 (phpstan-nobaseline.neon)
