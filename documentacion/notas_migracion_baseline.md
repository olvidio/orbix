# Notas — baseline de migracion (revision contra `refactor.md`)

## Estado inicial

`apps/notas/` tiene **30 controladores**, **8 modelos** y **12 vistas** (1 Twig, 11 PHTML), ~9600 LOC en total. `src/notas/` esta escasamente poblado:

```
src/notas/
├── application/
│   ├── example.php                    (fichero vacio, sin usar)
│   └── services/
│       └── ResumenTempTablesService.php (helper de Resumen.php)
├── config/
│   └── dependencies.php               (10 mappings)
├── domain/
│   ├── contracts/                     (11 interfaces)
│   ├── entity/                        (5 entidades)
│   ├── example.php                    (vacio)
│   └── value_objects/                 (17 VOs)
└── infrastructure/
    ├── persistence/postgresql/         (12 repos Pg*)
    └── ui/http/controllers/            (vacio — sin endpoints)
```

**No existe `src/notas/config/routes.php`.** No hay endpoints HTTP en `src/notas/`. Toda la logica vive en `apps/notas/`.

## Inventario de controladores

| Controlador | LOC | Tipo | Patron actual | Prob |
|---|---|---|---|---|
| `acta_2_mpdf.php` | 31 | PDF generator | `$_POST = $_GET;` hack, include `acta_imprimir_mpdf` | hack |
| `acta_ajax.php` | 38 | AJAX dispatcher | `switch($Qque)`, `echo $json` desde repo | dispatcher + plain echo |
| `acta_imprimir.php` | 187 | Render print HTML | `ViewPhtml('notas\\controller')` | vista en `apps/` |
| `acta_imprimir_mpdf.php` | 228 | Print + PDF | renderiza `actas_mpdf.css.php` | OK |
| `acta_listado_anual.php` | 155 | Form + listado | `Periodo`, `Hash`, `ViewPhtml` | OK estructura |
| `acta_pdf_delete.php` | 37 | DELETE acta.pdf | usa `Illuminate\\JsonResponse` | inconsistente |
| `acta_pdf_download.php` | 49 | GET file | binary download | OK |
| `acta_pdf_upload.php` | 61 | POST file | json escrito a mano | inconsistente |
| `acta_select.php` | 240 | Listado actas | `Lista` + `ViewPhtml` en controller | OK estructura |
| `acta_update.php` | 151 | Mutacion acta | `switch($Qmod)` (nueva/eliminar/modificar) + `ContestarJson` | dispatcher |
| `acta_ver.php` | 324 | Form acta | `$Qmod` (nueva/modificar) | dispatcher implicito |
| `asig_faltan_personas_select.php` | 203 | Listado personas | `Lista`, `Hash` | OK estructura |
| `asig_faltan_que.php` | 94 | Form busqueda | `Desplegable`, `Hash` | OK estructura |
| `asig_faltan_select.php` | 226 | Listado personas | `Lista`, `Hash` | OK estructura |
| `asignaturas_pendientes.php` | 87 | Cuadro alumnos | `TablaAlumnosAsignaturas->getTabla*()` | model devuelve `Lista` |
| `asignaturas_pendientes_resumen.php` | 168 | Resumen cuadro | logica inline | mucho codigo en controller |
| `comprobar_notas.php` | 515 | Validador notas | logica inline + DB queries | gigante, mezcla todo |
| `form_1011.php` | 403 | Form notas persona | `$Qmod`, `Hash`, `Desplegable` | dispatcher + URL ajax hardcoded |
| `informe_stgr_agd.php` | 184 | Informe agd | `Resumen->...` | controller arma reporte |
| `informe_stgr_n.php` | 233 | Informe n | `Resumen->...` | controller arma reporte |
| `informe_stgr_profesores.php` | 123 | Informe profesores | `Resumen->...` | controller arma reporte |
| `notas_ajax.php` | 230 | AJAX dispatcher | `switch($Qque)` (buscar_acta/frm_buscar/posibles_opcionales/posibles_preceptores) + HTML inline | dispatcher + HTML en backend |
| `resumen_anual.php` | 113 | Cuadro resumen | `Hash`, `ViewPhtml` y/o `ViewTwig` | OK estructura |
| `tessera_2_mpdf.php` | 31 | PDF tessera | hack `$_POST = $_GET;` | hack |
| `tessera_copiar.php` | 55 | Mutacion copia | json a mano | inconsistente |
| `tessera_copiar_select.php` | 107 | Form copia | **`ViewTwig`** + `Desplegable` | unico Twig |
| `tessera_imprimir.php` | 406 | Render tessera | `Tesera->...` | grande |
| `tessera_imprimir_mpdf.php` | 328 | PDF tessera | similar a anterior | duplicado parcial |
| `tessera_ver.php` | 39 | Lista tessera | thin | OK |
| `update_1011.php` | 127 | Mutacion nota | `switch($Qmod)` (eliminar/nuevo/editar) + `echo $msg_err` | dispatcher + plain echo |

## Inventario de modelos (`apps/notas/model/`)

| Modelo | LOC | Que hace | Problema arquitectonico |
|---|---|---|---|
| `EditarPersonaNota.php` | 493 | crear/editar/eliminar `PersonaNota` con replicacion en region/dl/certificado | quasi-application; mover a `src/notas/application/` casi tal cual |
| `Resumen.php` | 1294 | Reportes anuales con tablas temporales | mover a `src/notas/application/` por fases |
| `AsignaturasPendientes.php` | 385 | Cuenta asignaturas pendientes por nivel/dl | mover a `src/notas/application/` |
| `TablaAlumnosAsignaturas.php` | 299 | Devuelve **`web\\Lista`** ya construido | violacion: model UI; separar `data` de `Lista` |
| `Tesera.php` | 325 | Renderiza tesera (HTML + datos) | separar datos de presentacion |
| `Select1011.php` | 305 | Construye `web\\Lista` + `web\\Hash` + `ViewPhtml::renderizar()` desde model | violacion: model = controller + vista pegados |
| `CentroEstudios.php` | 54 | Abre PDO propio contra schema `public` para leer `x_config_schema` | mover a `src/notas/application/services/` |
| `getDatosActa.php` | 28 | Thin wrapper sobre `PersonaNotaRepository->getPersonaNotas()` | candidato a borrar (inline en use case) |

## Vistas

12 vistas en `apps/notas/view/`. Solo `tessera_copiar_select.html.twig` es Twig — el resto PHTML. Hay paths hardcodeados `apps/notas/controller/...` en JS de varias vistas:

```
acta_select.phtml: 5 referencias a apps/notas/controller/...
form_1011.phtml: 1 referencia
acta_ver.phtml: ~3 referencias
asig_faltan_*.phtml: 1-2 cada una
```

`form_1011.phtml` ademas hace `use src\\notas\\domain\\value_objects\\NotaEpoca;` y `TipoActa` — aceptable para constantes de VO, pero formalmente viola la regla "vistas no usan `src\\`".

## Violaciones de `refactor.md` detectadas

1. **`apps/notas/controller/` no migrado** — 30 controllers, 0 frontend.
2. **0 endpoints en `src/notas/infrastructure/ui/http/controllers/`**, **0 use cases en `src/notas/application/`**.
3. **Dispatchers `$Qque` / `$Qmod`** en al menos 6 controllers (`acta_update`, `acta_ver`, `update_1011`, `notas_ajax`, `acta_ajax`, `form_1011`).
4. **Models construyen UI** (`web\\Lista`, `web\\Desplegable`):
    - `Select1011.php` instancia `Lista` + `Hash` + `ViewPhtml`.
    - `TablaAlumnosAsignaturas.php` devuelve `Lista`.
5. **Mutaciones sin `ContestarJson`**:
    - `update_1011.php` → `echo $msg_err` (texto plano).
    - `acta_pdf_delete.php` → `Illuminate\\JsonResponse->send()`.
    - `acta_pdf_upload.php` → `echo json_encode($outData)` a mano.
    - `tessera_copiar.php` → `echo json_encode($jsondata)` a mano + `header('Content-type: ...')` manual.
6. **AJAX dispatchers devuelven HTML** (no payload):
    - `notas_ajax.php` case `frm_buscar` → emite `<form>` con `Desplegable->desplegable()` directamente al cliente.
    - `notas_ajax.php` case `posibles_opcionales` → emite `<select>` HTML.
    - `notas_ajax.php` case `posibles_preceptores` → emite `<select>` HTML.
    - `acta_ajax.php` → `$repo->getJsonExaminadores()` (devuelve string ya formateado por el repositorio).
7. **`$_POST = $_GET` hack**: `acta_2_mpdf.php` y `tessera_2_mpdf.php` (workaround para pasar `global_header` con GET).
8. **Hash de URL absoluta hardcoded en JS**:
    - `apps/notas/controller/notas_ajax.php` referenciado por `form_1011.phtml` con `$url_ajax = ConfigGlobal::getWeb() . '/apps/notas/controller/notas_ajax.php'`.
    - `apps/notas/controller/update_1011.php` en `form_1011.phtml`.
    - `apps/notas/controller/acta_select.php`, `acta_ver.php`, `acta_imprimir.php`, `acta_pdf_download.php`, `acta_update.php` en `acta_select.phtml`.
    - `apps/notas/controller/tessera_copiar.php` en `tessera_copiar_select.html.twig`.
9. **Vistas con `use src\\...`** — `form_1011.phtml` (constantes VO).
10. **Twig solitario**: `tessera_copiar_select.html.twig` debe migrarse a PHTML.
11. **Patron JS legacy `form.one('submit') + trigger('submit') + off()`** en `form_1011.phtml`.
12. **Codigo posiblemente muerto**: bloque comentado en `notas_ajax.php` case `posibles_preceptores`; `example.php` vacios en `src/notas/application` y `src/notas/domain`.

## Menus / consumidores externos

```
documentacion/Documentacion_Obix/menus.csv → 7 entradas a apps/notas/controller/...
proves/aux_metamenus.csv                  → ?
log/menus/comun.sql                        → seeds con paths apps/notas/...
apps/actividadestudios/controller/form_1303.php   → llama acta_ver.php / form_1011.php
apps/actividadestudios/view/acta_notas.phtml      → llama acta_ver.php
apps/actividadestudios/controller/acta_notas.php  → integra apps/notas/controller/acta_ver
```

Los wrappers en `apps/notas/controller/*` deberan mantenerse hasta vaciar todos esos consumidores externos.

## Plan de migracion por slices

**Total estimado:** 9 slices. Es un modulo grande; se propone empezar por las mutaciones (impacto contractual) y dejar los reportes pesados para el final.

### Slice 0 — Scaffolding + baseline

- Crear `frontend/notas/{controller,view,support}` y `src/notas/application/support`.
- Crear `src/notas/config/routes.php` (vacio para empezar).
- Borrar `src/notas/application/example.php` y `src/notas/domain/example.php`.
- Borrar `apps/notas/db/notas_otra_region.sql` solo si se confirma que es seed historico (no tocar de momento; es SQL).

### Slice 1 — Mutaciones criticas (split dispatchers + JSON estandar)

Objetivo: cumplir "una accion = un endpoint" y `ContestarJson::enviar` en todas las mutaciones.

- `acta_update.php` (`$Qmod`) → split en:
    - `src/notas/application/ActaNueva.php` + `/src/notas/acta_nueva`
    - `src/notas/application/ActaModificar.php` + `/src/notas/acta_modificar`
    - `src/notas/application/ActaEliminar.php` + `/src/notas/acta_eliminar`
    - Wrapper legacy: `apps/notas/controller/acta_update.php` queda como dispatcher temporal hasta migrar `acta_select.phtml` y `acta_ver.phtml`.
- `update_1011.php` (`$Qmod`) → split en:
    - `src/notas/application/PersonaNotaNueva.php` (mover `EditarPersonaNota::nuevo()`).
    - `src/notas/application/PersonaNotaEditar.php` (mover `EditarPersonaNota::editar()`).
    - `src/notas/application/PersonaNotaEliminar.php` (mover `EditarPersonaNota::eliminar()`).
    - Endpoints `/src/notas/persona_nota_*`. Mover `apps/notas/model/EditarPersonaNota.php` a `src/notas/application/EditarPersonaNota.php` (cambiar namespace).
- `acta_pdf_delete.php` → `src/notas/application/ActaPdfEliminar.php` + `/src/notas/acta_pdf_eliminar` con `ContestarJson` (eliminar `Illuminate\\JsonResponse`).
- `acta_pdf_upload.php` → `src/notas/application/ActaPdfSubir.php` + `/src/notas/acta_pdf_subir` con `ContestarJson`.
- `tessera_copiar.php` → `src/notas/application/TesseraCopiar.php` + `/src/notas/tessera_copiar` con `ContestarJson`.

### Slice 2 — AJAX dispatchers (`notas_ajax`, `acta_ajax`)

- Convertir cada `case` en endpoint independiente bajo `/src/notas/`:
    - `notas_ajax::buscar_acta` → `/src/notas/acta_buscar` (devuelve datos JSON, no HTML).
    - `notas_ajax::frm_buscar` → `/src/notas/actividad_lista_data` (devuelve `opciones` para desplegables; el `<form>` lo construye `frontend/notas/view/`).
    - `notas_ajax::posibles_opcionales` → `/src/notas/asignaturas_opcionales_data` (payload con contrato `Desplegable` estandar).
    - `notas_ajax::posibles_preceptores` → `/src/notas/preceptores_data` (idem).
    - `acta_ajax::examinadores` → `/src/notas/examinadores_data`.
    - `acta_ajax::asignaturas` → `/src/notas/asignaturas_data` (autocomplete; ya devuelve JSON, normalizar contrato).
- Adaptar `form_1011.phtml`, `acta_ver.phtml` y demas consumidores a llamar a los nuevos endpoints + `fnjs_construir_desplegable`.
- Eliminar `notas_ajax.php` y `acta_ajax.php` cuando no queden referencias.

### Slice 3 — Flujo `acta_*` (lecturas + vistas)

- `frontend/notas/controller/acta_select.php` + `view/acta_select.phtml` (mover paths JS a `frontend/notas/...` o a `/src/notas/...`).
- `frontend/notas/controller/acta_ver.php` + `view/acta_ver.phtml` (split implicit `$Qmod`: leer parametros y derivar el `mod` para la vista; los botones de la vista llaman a los endpoints split del Slice 1).
- `frontend/notas/controller/acta_listado_anual.php` + `view/acta_listado_anual.phtml`.
- `frontend/notas/controller/acta_imprimir.php` + `view/acta_imprimir.phtml`.
- `acta_imprimir_mpdf.php` y `acta_2_mpdf.php` (PDF) — quitar el hack `$_POST = $_GET;` y montar como controlador frontend que valida hash con GET (similar a `leyenda` en planning).
- `acta_pdf_download.php` → `frontend/notas/controller/acta_pdf_download.php` (descarga binaria).

### Slice 4 — Form notas (`form_1011`)

- `frontend/notas/controller/form_1011.php` + `view/form_1011.phtml`:
    - Cargar opciones desde repos directamente (es vista de oficina).
    - Ajustar URLs JS a los nuevos endpoints `/src/notas/*` del slice 1 y 2.
    - Reescribir `fnjs_guardar` con patron `$.ajax(...).done(...)` (sin `form.one("submit") + trigger`).
- Borrar `apps/notas/model/Select1011.php` (split en data + view): el listado se monta en `frontend/notas/view/select1011.phtml` con `web\\Lista` instanciado en el controller.

### Slice 5 — `asig_faltan_*` + `tessera_copiar_select`

- `frontend/notas/controller/asig_faltan_que.php` + `view/asig_faltan_que.phtml`.
- `frontend/notas/controller/asig_faltan_select.php` + `view/asig_faltan_select.phtml`.
- `frontend/notas/controller/asig_faltan_personas_select.php` + `view/asig_faltan_personas_select.phtml`.
- `frontend/notas/controller/tessera_copiar_select.php` + `view/tessera_copiar_select.phtml` (migrar de Twig a PHTML; el endpoint `tessera_copiar` ya esta en slice 1).

### Slice 6 — Reportes pesados

- `comprobar_notas.php` (515 LOC) → `src/notas/application/ComprobarNotasService.php` + `frontend/notas/controller/comprobar_notas.php` + `view/`.
- `resumen_anual.php` → `frontend/notas/controller/...` + `view/resumen_anual.phtml`.
- `informe_stgr_n.php`, `informe_stgr_agd.php`, `informe_stgr_profesores.php` → mover. Mantener temporalmente `Resumen.php` en `apps/notas/model/` y crear shim en `src/notas/application/Resumen.php` (renombrar al final).
- `asignaturas_pendientes.php` y `asignaturas_pendientes_resumen.php` → mover. Split `TablaAlumnosAsignaturas.php`: data en use case, `Lista` en view.
- Mover `apps/notas/model/AsignaturasPendientes.php` a `src/notas/application/AsignaturasPendientesData.php` (sin `Lista`).
- Mover `apps/notas/model/CentroEstudios.php` a `src/notas/application/services/CentroEstudiosService.php`.
- Mover `apps/notas/model/getDatosActa.php` a `src/notas/application/services/DatosActaService.php` o inlinearlo.

### Slice 7 — Tessera (read flow)

- `tessera_ver.php`, `tessera_imprimir.php`, `tessera_imprimir_mpdf.php`, `tessera_2_mpdf.php` → `frontend/notas/controller/...` + vistas.
- Mover `apps/notas/model/Tesera.php` a `src/notas/application/Tesera.php` (separar generacion de datos de plantilla HTML donde sea posible; las plantillas finales pueden quedarse en frontend/view).

### Slice 8 — Limpieza final

- Wrappers `apps/notas/controller/*.php` reducidos a `require __DIR__ . '/../../../frontend/notas/controller/X.php';`.
- `apps/notas/view/` vacio.
- `apps/notas/model/` vacio (todos movidos).
- Actualizar `documentacion/Documentacion_Obix/menus.csv` y `proves/aux_metamenus.csv` a `frontend/notas/controller/...`.
- Actualizar referencias en `apps/actividadestudios/`, `apps/dossiers/`, etc. (si las hay).
- `php -l` en todo `frontend/notas`, `src/notas`, `apps/notas`.
- Cerrar este baseline con estado final.

## Principios reiterados

- `src/notas/application/*` devuelve arrays/strings; nunca HTML ni `web\\Lista` / `web\\Desplegable`.
- `src/notas/infrastructure/ui/http/controllers/*` solo llama al caso de uso y responde `ContestarJson::enviar($error, $data)`.
- `frontend/notas/controller/*` delgado: `PostRequest::getDataFromUrl('/src/notas/...', $campos)` o, cuando es pantalla de oficina, instancia repos directamente + `web\\Desplegable` / `web\\Lista` para UI.
- Mutaciones (`*_update`, `*_eliminar`, `*_nueva`, `*_modificar`, `*_subir`, `*_copiar`) devuelven `{success, mensaje}` — nunca `echo` ni cuerpo vacio.
- JS consumidor uniforme: `dataType: 'json'`, rama `success === true` / `success === false`, sin `form.one('submit') + trigger`.

## Riesgos y consideraciones

- `Resumen.php` es 1294 LOC con SQL ad-hoc + tablas temporales. La migracion debe ser conservadora: mover el namespace y el path, sin reescribir la logica de negocio.
- `EditarPersonaNota.php` ya tiene buena cohesion; el riesgo principal es que es invocado desde `update_1011` y posiblemente desde tests/scripts (`grep` antes de mover).
- Hay multiples consumidores externos (`apps/actividadestudios/...`) con paths hardcoded a `apps/notas/controller/`. Necesitamos mantener wrappers hasta el slice 8.
- `tessera_copiar_select.html.twig` es Twig. Su migracion a PHTML es estetica pero rompe la convencion del modulo, asi que mejor migrarlo aunque sea simple.
- Las URLs de mpdf (`acta_2_mpdf.php`, `tessera_2_mpdf.php`) tienen el hack `$_POST = $_GET;`. Hay que eliminarlo y firmar el hash con GET (patron de `leyenda` en planning).

## Estado del slice actual

- **Slice 0 completado** (22/04/2026): scaffolding + `src/notas/config/routes.php`.
- **Slice 1 completado**: `acta_*`, `persona_nota_*`, `acta_pdf_*`, `tessera_copiar` en `src/notas/application` + endpoints `/src/notas/*`. Shims en `apps/` con `ContestarJson`.
- **Slice 2 completado**: AJAX dispatchers (`acta_ajax`, `notas_ajax`) con logica extraida a `ActividadesBuscarData`, `BuscarActaData`, `PosiblesOpcionalesData`, `PosiblesPreceptoresData`, `ExaminadoresSearchData`, `AsignaturasSearchData`. Los shims `notas_ajax.php` / `acta_ajax.php` se conservan hasta migrar los JS consumidores (pendiente).
- **Slice 3 completado**: flujo `acta_*` (`acta_listado_anual` + endpoint `/src/notas/acta_listado_anual_data`, `DatosActaService`).
- **Slice 4 completado**: `NotaPersonaFormData` + `Select1011Data` extraidos a `src/notas/application`.
- **Slice 5 completado**: `asig_faltan_*` (3) + `tessera_copiar_select` (Twig→PHTML) migrados a `frontend/notas/`.
- **Slice 6 completado**: `resumen_anual`, `informe_stgr_*`, `asignaturas_pendientes*`, `comprobar_notas` migrados a `frontend/notas/`. `CentroEstudios` → `src/notas/application/CentroEstudiosLookup` con shim. `getDatosActa` shim borrado.
- **Slice 7 completado**: `tessera_ver`, `tessera_imprimir`, `tessera_imprimir_mpdf`, `tessera_2_mpdf` migrados a `frontend/notas/`. `Tesera` model sigue en `apps/notas/model` (usa `ViewNewPhtml` apuntando a `frontend/notas/view/tesera_ver.phtml`).
- **Slice 8 completado**: `acta_select`, `acta_ver`, `acta_imprimir`, `acta_imprimir_mpdf`, `acta_2_mpdf`, `acta_pdf_download`, `form_1011` migrados a `frontend/notas/`. Todas las vistas del modulo viven ahora en `frontend/notas/view/` (directorio `apps/notas/view/` eliminado). `Select1011` y `Tesera` renderizan con `ViewNewPhtml` hacia `frontend/notas/view/`. Menus (`Documentacion_Obix/menus.csv`, `proves/aux_metamenus.csv`, `log/menus/comun.sql`) apuntan a `frontend/notas/controller/`. Shims minimos en `apps/notas/controller/` para los consumidores externos (`apps/actividadestudios/...`, JS legacy que firma el hash con URLs `apps/...`).

## Estado final del modulo `notas`

### Ficheros en `apps/notas/` (legacy)

- `apps/notas/controller/` — 30 ficheros, casi todos shims (`require_once 'frontend/notas/controller/X.php';`). Sobreviven con logica propia:
    - `acta_update.php`, `update_1011.php`: dispatchers `$Qmod` que delegan en los use cases split de `src/notas/application/`. Se conservan porque las vistas JS (`frontend/notas/view/acta_*.phtml`, `form_1011.phtml`, `select1011.phtml`) firman el hash contra la URL `apps/...`.
    - `acta_ajax.php`, `notas_ajax.php`: dispatchers `$Qque` que delegan en `ExaminadoresSearchData`, `AsignaturasSearchData`, `BuscarActaData`, `ActividadesBuscarData`, `PosiblesOpcionalesData`, `PosiblesPreceptoresData`. Conservados por el mismo motivo (JS legacy + formato de respuesta no-JSON).
    - `acta_pdf_upload.php`, `acta_pdf_delete.php`, `tessera_copiar.php`: shims con `ContestarJson::enviar` delegando en `ActaPdfSubir`, `ActaPdfEliminar`, `TesseraCopiar`. Conservados para compatibilidad con JS legacy.
- `apps/notas/model/` — 7 ficheros legacy:
    - `Resumen.php` (1294 LOC): SQL ad-hoc + tablas temporales para reportes. Refactor no trivial; se usa desde `frontend/notas/controller/{informe_stgr_*,resumen_anual,comprobar_notas}.php`.
    - `TablaAlumnosAsignaturas.php` (299 LOC): construye `web\\Lista`; usada por `frontend/notas/controller/asignaturas_pendientes.php`.
    - `AsignaturasPendientes.php` (385 LOC): conteo de asignaturas pendientes por nivel/dl; usada por `frontend/notas/controller/asig_faltan_*.php` y `asignaturas_pendientes_resumen.php`.
    - `Tesera.php` (325 LOC): genera HTML de tessera, renderiza `frontend/notas/view/tesera_ver.phtml` via `ViewNewPhtml`.
    - `Select1011.php` (152 LOC): widget dossier, construye `web\\Lista` y renderiza `frontend/notas/view/select1011.phtml` via `ViewNewPhtml`. Instanciado dinamicamente por `DossierTipoFileSuffixResolver`.
    - `CentroEstudios.php`, `EditarPersonaNota.php`: shims que extienden las clases movidas a `src/notas/application/`.

### Ficheros en `frontend/notas/` y `src/notas/`

- `frontend/notas/controller/` — 24 controllers delgados, todos con header `frontend/shared/global_header_front.inc` y render `ViewNewPhtml('frontend\\notas\\controller')`.
- `frontend/notas/view/` — 11 vistas PHTML (+ `tesera_ver.phtml` usada por `Tesera` model).
- `src/notas/application/` — 21 use cases + `services/` y `support/`.
- `src/notas/infrastructure/ui/http/controllers/` — 13 endpoints HTTP registrados en `src/notas/config/routes.php`.

### Deuda tecnica pendiente (post-refactor)

1. **Dispatchers `acta_ajax`, `notas_ajax`, `acta_update`, `update_1011`**: migrar cuando los JS consumidores (vistas con `apps/...` hardcoded + hash firmado) se reescriban para llamar a los endpoints granulares `/src/notas/*`.
2. **`Resumen.php`, `AsignaturasPendientes.php`, `TablaAlumnosAsignaturas.php`, `Tesera.php`, `Select1011.php`**: pendientes de mover a `src/notas/application/` separando datos y UI. Riesgo alto por volumen y acoplamiento al resolver de dossiers (`Select1011`).
3. **`form_1011.php`, `acta_imprimir.php`, `acta_ver.php`, `acta_select.php`**: frontend controllers que importan `src\\notas\\application\\*Data` directamente en vez de usar `PostRequest::getDataFromUrl()`. Pragmatico de momento (estas pantallas son renderizados side-effect-free con mucho contexto PHP) pero viola el principio estricto de `refactor.md` §133. Candidatos a exponer como endpoint `/src/notas/*_data` si se necesita aislamiento o testing independiente.
