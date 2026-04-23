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
- **Slice 2 completado**: AJAX dispatchers (`acta_ajax`, `notas_ajax`) con logica extraida a `ActividadesBuscarData`, `BuscarActaData`, `PosiblesOpcionalesData`, `PosiblesPreceptoresData`, `ExaminadoresSearchData`, `AsignaturasSearchData`. `acta_ajax.php` ya eliminado — `acta_ver.phtml` llama directamente a `/src/notas/examinadores_search` y `/src/notas/asignaturas_search`. `notas_ajax.php` se conserva (HTML inline en respuestas + hash firmado contra `apps/...` en JS).
- **Slice 3 completado**: flujo `acta_*` (`acta_listado_anual` + endpoint `/src/notas/acta_listado_anual_data`, `DatosActa`).
- **Slice 4 completado**: `NotaPersonaFormData` + `Select1011Data` extraidos a `src/notas/application`.
- **Slice 5 completado**: `asig_faltan_*` (3) + `tessera_copiar_select` (Twig→PHTML) migrados a `frontend/notas/`.
- **Slice 6 completado**: `resumen_anual`, `informe_stgr_*`, `asignaturas_pendientes*`, `comprobar_notas` migrados a `frontend/notas/`. `CentroEstudios` → `src/notas/application/CentroEstudiosLookup` con shim. `getDatosActa` shim borrado.
- **Slice 7 completado**: `tessera_ver`, `tessera_imprimir`, `tessera_imprimir_mpdf`, `tessera_2_mpdf` migrados a `frontend/notas/`. `Tesera` model sigue en `apps/notas/model` (usa `ViewNewPhtml` apuntando a `frontend/notas/view/tesera_ver.phtml`).
- **Slice 8 completado**: `acta_select`, `acta_ver`, `acta_imprimir`, `acta_imprimir_mpdf`, `acta_2_mpdf`, `acta_pdf_download`, `form_1011` migrados a `frontend/notas/`. Todas las vistas del modulo viven ahora en `frontend/notas/view/` (directorio `apps/notas/view/` eliminado). `Select1011` y `Tesera` renderizan con `ViewNewPhtml` hacia `frontend/notas/view/`. Menus (`Documentacion_Obix/menus.csv`, `proves/aux_metamenus.csv`, `log/menus/comun.sql`) apuntan a `frontend/notas/controller/`. Shims minimos en `apps/notas/controller/` para los consumidores externos (`apps/actividadestudios/...`, JS legacy que firma el hash con URLs `apps/...`).
- **Slice 9 completado**: dispatcher `notas_ajax.php` eliminado y sustituido por 4 endpoints dedicados:
    - `/src/notas/buscar_acta` (JSON `BuscarActaData`).
    - `/src/notas/posibles_opcionales_data` (payload `fnjs_construir_desplegable` para `id_asignatura`).
    - `/src/notas/posibles_preceptores_data` (payload `fnjs_construir_desplegable` para `id_preceptor`).
    - `/src/notas/actividades_buscar_data` (JSON) + `frontend/notas/controller/actividad_buscar_form.php` con vista `actividad_buscar_form.phtml` que arma el `<form>` del dialogo "añadir ca" (la construccion del HTML vive ahora en `frontend/`, no en `src/`).
    Consumidores actualizados: `frontend/notas/view/form_1011.phtml` y `apps/actividadestudios/view/form_1303.phtml` (con helper `fnjs_construir_desplegable` inline). `apps/notas/controller/notas_ajax.php` borrado.
- **Slice 10 completado**: dispatchers `acta_update.php` y `update_1011.php` eliminados. Los consumidores llaman ahora directamente a los endpoints granulares ya existentes en `/src/notas/*`:
    - `acta_select.phtml` → `/src/notas/acta_eliminar` (`fnjs_eliminar`).
    - `acta_ver.phtml` → `/src/notas/acta_nueva` o `/src/notas/acta_modificar` segun el hidden `mod` del form (`fnjs_guardar_acta`).
    - `select1011.phtml` → `/src/notas/persona_nota_eliminar` (`fnjs_borrar`, JSON + `dataType: 'json'`).
    - `form_1011.phtml` → `/src/notas/persona_nota_nueva` o `/src/notas/persona_nota_editar` segun el hidden `mod` (`fnjs_guardar`, JSON + `dataType: 'json'`).
    Los hashes (`h`, `hh`, `hhc`) emitidos por `Hash::getCamposHtml()` no dependen de la URL del `action`, por lo que basta con reescribir el `url` del `$.ajax` y ajustar las callbacks `.done` a la respuesta JSON de `ContestarJson`. `apps/notas/controller/acta_update.php` y `apps/notas/controller/update_1011.php` borrados.
- **Slice 14 completado**: `apps/notas/model/Select1011.php` (152 LOC) migrado a `src/notas/application/Select1011.php`. Cambios:
    - Para poder mover widgets dossier ya refactorizados a la capa `src/` sin necesidad de mantener shims en `apps/<app>/model/`, se extiende `src/dossiers/application/DossierTipoFileSuffixResolver` con una tercera ruta de lookup: `src/<app>/application/Select<suffix>.php` (FQCN `src\<app>\application\Select<suffix>`) como fallback tras `apps/<app>/{model,domain}/`. Es un cambio de efecto inofensivo: solo se busca un archivo adicional si los dos primeros no existen.
    - Ambos metodos afectados (`resolveSelectClassFqcn()` y `absolutePathSelect()`) se actualizan en coherencia.
    - El widget `Select1011` conserva su comportamiento (mismos setters, misma vista `select1011.phtml`, misma `Lista` + `Hash`). Los unicos cambios estilisticos son: `PERMISO_INSERTAR` (`3`) e `ID_DOSSIER` (`'1011'`) como constantes nombradas, e invierto la guarda de `setLinksInsert()` (early return) para reducir indentacion.
    - La logica de datos sigue en `Select1011Data` (ya estaba en `src/notas/application/` desde el Slice 4).
    - El resolver es invocado desde `apps/dossiers/controller/dossiers_ver.php` (linea 218) y no tiene tests; cambio verificado con `php -l` y `rg` (sin referencias residuales al FQCN antiguo `notas\\model\\Select1011`).
- **Slice 16 completado**: `Resumen.php` aislado en `src/notas/application/legacy/` y frontend desacoplado de la clase legacy.
    - `apps/notas/model/Resumen.php` → `src/notas/application/legacy/Resumen.php` (namespace `src\notas\application\legacy`). La carpeta `legacy/` avisa explicitamente de que es un bloque heredado (1294 LOC de SQL ad-hoc + tablas temporales); no se reescribe, solo se encapsula.
    - Tres nuevos use cases en `src/notas/application/` hacen de fachada tipada sobre `Resumen`:
      - `InformeStgrNumerarios` (ya existia desde el slice 6; ahora tambien se usa de verdad desde el frontend, antes el controller duplicaba los 18 calculos inline).
      - `InformeStgrAgregados` (nuevo).
      - `InformeStgrProfesores` (nuevo).
    - Los tres `frontend/notas/controller/informe_stgr_{n,agd,profesores}.php` pasan de 119-229 LOC con `use notas\model\Resumen;` + calculos inline a 33-43 LOC delegando en `->calcular()` y renderizando la vista compartida `frontend/notas/view/informe_stgr_tabla.phtml` (38 LOC que reemplaza tres copias casi identicas de `<table>` / `foreach`).
    - Ya no hay ningun `use notas\model\Resumen` fuera de `src/notas/application/`: el frontend no conoce la clase legacy.
    - Limpieza interna menor en `Resumen.php` (sin tocar la logica de reportes):
      - Bug real corregido: `setAnyFiCurs($iany2)` asignaba a `$this->iany` en vez de `$this->iany2` (pisaba el año inicial silenciosamente).
      - `exit()` en el constructor reemplazado por `\InvalidArgumentException` con mensaje tipado.
      - `(int)`/`(float)` defensivo en los 5 metodos con interpolacion de parametro numerico en `HAVING`/`WHERE` (`masAsignaturasQue`, `menosAsignaturasQue`, `masCreditosQue`, `menosCreditosQue`, `profesorDeTipo`): elimina el riesgo teorico de SQL injection manteniendo `query()` directo (no puedo saltar a `prepare()` porque el mismo `$ssql` se reutiliza en `Lista($ssql, ...)`).
      - Eliminado codigo muerto: metodo `ListaAsig()` (~50 LOC sin callers en el repo), metodo `enStgrSinO()` (solo devolvia `['num' => '?']`, SQL comentado), propiedades sin usar `$a_asignaturas`, `$a_creditos`, `$diniverano`, comentario con bloque `try/catch` abandonado dentro de `nuevaTablaProfe()`.
      - Eliminado `if/else` con ambas ramas identicas en `profesorEspecialidad()` (`ConfigGlobal::mi_region() === ConfigGlobal::mi_delef()` llamaba a `findById()` en los dos brazos).
    - `Resumen.php` baja de 1294 → 1200 LOC; los 3 controllers del frontend de 528 → 110 LOC combinados. Los 3 use cases + vista compartida son 479 LOC.
- **Slice 15 completado**: renombrado de use cases en `src/notas/application/` para unificar la convencion de naming con el resto de casos de uso publicos (sin sufijo `Service`): `AsignaturasPendientesService` → `AsignaturasPendientes`, `TablaAlumnosAsignaturasService` → `TablaAlumnosAsignaturas`, `TeseraService` → `Tesera`, `DatosActaService` → `DatosActa`. El sufijo `Service` queda reservado para la subcarpeta `src/notas/application/services/` (helpers compartidos, ej. `ResumenTempTablesService`). Consumidores actualizados: `frontend/notas/controller/{asig_faltan_select,asig_faltan_personas_select,asignaturas_pendientes,tessera_ver,tessera_imprimir,tessera_imprimir_mpdf,acta_imprimir,acta_imprimir_mpdf}.php` y `apps/actividadestudios/controller/posibles_asignaturas_ca.php`.
- **Slice 13 completado**: `apps/notas/model/Tesera.php` (325 LOC) migrado a `src/notas/application/Tesera.php`. Mejoras respecto al legacy:
    - Ya no renderiza vistas (`ViewNewPhtml->renderizar()` desaparece del modelo). `datosParaVistaTesera()` devuelve un array neutro y la vista `frontend/notas/view/tesera_ver.phtml` se encarga de montar el HTML, incluida la cabecera por tramo (`<tr>...ANNUS I</b></tr>`, split de columnas del cuadrienio).
    - Magic numbers encapsulados como constantes: `ID_NIVEL_ASIG_DESDE/HASTA`, `ID_NIVEL_MAX_CUADRIENIO` (`2434`), `ID_ASIG_OPCIONAL_UMBRAL` (`3000`), `ID_ASIG_OPCIONAL_MAX` (`9000`), `ID_ASIG_FIN_CUADRIENIO` (`9998`), `PLAN_NUEVO` (`26`), `PLAN_VIEJO` (`97`), `ID_NIVEL_PLAN97_DESAPARECIDO` (`2114`), `ID_NIVEL_PLAN97_NUEVOS` (`2112,2113`), `FECHA_LIMITE_PLAN97` (`2026-03-30`).
    - Bug latente corregido: el merge de `cAsignaturas` + `aAprobadas` podia acceder fuera de rango cuando la ultima asignatura era pendiente (`$cAsignaturas[$a++]` con `$a == count`). Ahora la condicion del while interno comprueba `$a < $numAsigTotal`.
    - Metodo privado `getCurso()` reemplazado por `cursoActual()` publico tipado (`['inicio', 'fin', 'texto']`) sin mutar estado de la instancia.
    - `getTitulo()` (HTML inline con `<tr>...</tr>` de la tessera vista) movido a la vista como closure local `render_titulo()`: datos y UI separados. `getVariasTesera()` era una funcion vacia; eliminada.
    - `getAsignaturasAprobadas()` valida la asignatura con `findById()` (ya lo hacia el legacy) pero ahora lanza `\RuntimeException` en vez de `\Exception`.
    Consumidores actualizados: `frontend/notas/controller/tessera_{ver,imprimir,imprimir_mpdf}.php` ahora importan `src\notas\application\Tesera`. Fichero legacy borrado.
- **Slice 12 completado**: `apps/notas/model/TablaAlumnosAsignaturas.php` (299 LOC) migrado a `src/notas/application/TablaAlumnosAsignaturas.php`. Mejoras respecto al legacy:
    - Ya no devuelve `web\\Lista`; devuelve arrays neutros (`cabeceras` + `filas`). La `Lista` se construye en `frontend/notas/controller/asignaturas_pendientes.php`.
    - `getTablaCr()` y `getTablaDl()` (duplicados al 95 %) se unifican en un unico pipeline privado `construirTabla()`, parametrizado por filtros de persona y por un flag `usarDlComoCentro` (dl vs centro de estudios).
    - Se elimina el N+1: antes se hacian `2 * N` consultas (una a `PersonaNota` para los marcadores `fin_bienio/cuadrienio` y otra para todas las notas de la persona); ahora se carga todo en un unico `getPersonaNotas(..., 'id_nom' => 'IN')` y se agrupa en memoria.
    - Los magic numbers (`3000`, `9990`, `9998`, `9999`, `2000`, `1100`, `2500`, `1`, `2`) pasan a ser constantes nombradas (`ID_ASIG_FIN_BIENIO`, `ID_ASIG_FIN_CUADRIENIO`, `ID_ASIG_OPCIONAL_UMBRAL`, `ID_NIVEL_MARCADOR`, `ID_NIVEL_BIENIO_MAX`, `ID_NIVEL_ASIG_DESDE/HASTA`, `CELDA_PENDIENTE/CURSADA/APROBADA`).
    - Se pierde la propiedad mutable `a_delegacionesStgr` (setter/getter): ahora el mapa `id_dl => cod_dl` se pasa como argumento explicito a `paraRegionStgr()`.
    - `getTablaDl()` cargaba `$a_Asig_status` (via `isActive()`) y nunca lo usaba: codigo muerto eliminado.
    Consumidor actualizado: `frontend/notas/controller/asignaturas_pendientes.php`. Fichero legacy borrado.
- **Slice 11 completado**: `apps/notas/model/AsignaturasPendientes.php` (385 LOC) migrado a `src/notas/application/AsignaturasPendientes.php`. Mejoras respecto al legacy:
    - Sin estado mutable compartido entre getters (bug real: `aIdNivel` se pisaba al alternar tramos cacheados).
    - API tipada con enum `src\notas\domain\value_objects\CursoStgr` (`BIENIO | CUADRIENIO | C1 | C2`) en vez de strings magicos. Los rangos de `id_nivel` y los `nivel_stgr` asociados viven en el propio enum.
    - `personasQueLesFalta()` partido en dos metodos tipados: `contarFaltantesPorPersona(): array<int,int>` y `listarFaltantesPorPersona(): array<int,array<string>>`; desaparece el flag `setLista()`.
    - Prepared statements para `id_asignatura`, `id_nom`, `min_aprobadas` y `num_curso`.
    - `createAsignaturasTemp()` se ejecuta una unica vez por instancia (antes se reconstruia en cada llamada a `asignaturasQueFaltanPersona()` — cuello de botella en `posibles_asignaturas_ca.php`) y envuelve los `INSERT` del catalogo en una transaccion.
    - Warnings de PHP 8 corregidos: inicializacion de `$condicion`/`$condicion_stgr` en el path `default`, `$aId_nom[$id_nom]++` sobre clave indefinida.
    Consumidores actualizados (`frontend/notas/controller/asig_faltan_{,personas_}select.php`, `apps/actividadestudios/controller/posibles_asignaturas_ca.php`). Fichero legacy borrado.
- **Slice 17 completado**: `form_1011.php` deja de importar `NotaPersonaFormData` directamente. Se expone via endpoint HTTP y se consume con `PostRequest::getDataFromUrl()`, en linea con `refactor.md` §"Patron de llamada backend desde frontend":
    - Nuevo controlador `src/notas/infrastructure/ui/http/controllers/nota_persona_form_data.php` que recibe por POST los 5 campos que leia la use case (`id_pau`, `id_asignatura_real`, `sel`, `pau`, `mod`), llama a `NotaPersonaFormData::execute()` y añade los helpers `opcionalesGenericasHelpers()` dentro del mismo payload (clave `helpers`), devolviendo todo con `ContestarJson::enviar('', $data)`.
    - Ruta `/src/notas/nota_persona_form_data` registrada en `src/notas/config/routes.php`.
    - `frontend/notas/controller/form_1011.php` cambia `use src\\notas\\application\\NotaPersonaFormData;` por `use frontend\\shared\\PostRequest;`; la llamada directa `NotaPersonaFormData::execute($_POST)` + `NotaPersonaFormData::opcionalesGenericasHelpers()` se sustituye por una unica invocacion `PostRequest::getDataFromUrl('/src/notas/nota_persona_form_data', [...])` y la lectura de `$datos['helpers']`.
    - `rg 'use src\\\\[a-zA-Z_]+\\\\application\\\\[A-Za-z_]+Data;' frontend apps` no devuelve resultados: el antipatron queda eliminado en todo el repo (no solo en `notas`). Los otros controladores que se mencionaban como sospechosos (`acta_imprimir`, `acta_ver`, `acta_select`) ya importan solo use cases sin sufijo `Data` (`DatosActa`, repos, VOs), asi que no requieren cambios.

## Estado final del modulo `notas`

### Ficheros en `apps/notas/` (legacy)

- `apps/notas/controller/` — 27 ficheros, casi todos shims (`require_once 'frontend/notas/controller/X.php';`). Sobreviven con logica propia:
    - `acta_pdf_upload.php`, `acta_pdf_delete.php`, `tessera_copiar.php`: shims con `ContestarJson::enviar` delegando en `ActaPdfSubir`, `ActaPdfEliminar`, `TesseraCopiar`. Conservados para compatibilidad con JS legacy.
- `apps/notas/model/` — 2 ficheros legacy:
    - `CentroEstudios.php`, `EditarPersonaNota.php`: shims que extienden las clases movidas a `src/notas/application/`.

### Ficheros en `frontend/notas/` y `src/notas/`

- `frontend/notas/controller/` — 25 controllers delgados, todos con header `frontend/shared/global_header_front.inc` y render `ViewNewPhtml('frontend\\notas\\controller')`. Incluye `actividad_buscar_form.php` (dialogo "añadir ca").
- `frontend/notas/view/` — 12 vistas PHTML (+ `tesera_ver.phtml` usada por `Tesera` model; + `actividad_buscar_form.phtml`).
- `src/notas/application/` — use cases + `services/` (helpers compartidos) + `support/` + `legacy/` (bloque `Resumen.php` heredado, encapsulado tras los use cases `InformeStgr*`). Los casos de uso publicos van sin sufijo (`AsignaturasPendientes`, `TablaAlumnosAsignaturas`, `Tesera`, `DatosActa`, `Select1011`, `ActaNueva`, `InformeStgrNumerarios/Agregados/Profesores`, etc.); el sufijo `Service` queda reservado para los helpers dentro de `application/services/` (ej. `ResumenTempTablesService`).
- `src/notas/infrastructure/ui/http/controllers/` — 18 endpoints HTTP registrados en `src/notas/config/routes.php` (incluye `buscar_acta`, `posibles_opcionales_data`, `posibles_preceptores_data`, `actividades_buscar_data`, `nota_persona_form_data`).

### Deuda tecnica pendiente (post-refactor)

1. **`Resumen.php` (bloque legacy en `src/notas/application/legacy/`)**: aislado y encapsulado tras los use cases `InformeStgr{Numerarios,Agregados,Profesores}`, el frontend ya no lo conoce. Queda pendiente (deuda tecnica interna, sin impacto arquitectonico):
    - Emite HTML (`Lista()`) desde la capa `application/`. Candidato a que los use cases consuman arrays y el renderizado pase a la vista PHTML.
    - Mezcla alumnos + profesores en la misma clase; partir en `ResumenAlumnos` + `ResumenProfesores` es mecanico.
    - N+1 en `profesorEspecialidad()` (1 query + 2 `findById` por profesor).
    - Extraer `comprobar_notas.php` (~500 LOC inline en `frontend/`) a un `ComprobarNotas` use case tambien esta pendiente; no consume `Resumen` pero es el mismo patron.
2. ~~**`form_1011.php`, `acta_imprimir.php`, `acta_ver.php`, `acta_select.php`**: frontend controllers que importan `src\\notas\\application\\*Data` directamente en vez de usar `PostRequest::getDataFromUrl()`.~~ Resuelto en Slice 17: `form_1011.php` ya consume el nuevo endpoint `/src/notas/nota_persona_form_data`. Los otros tres controladores que se señalaban en esta deuda no importan realmente ningun `*Data` (solo `DatosActa`, VOs y repos), asi que la lista original estaba sobreestimada.
