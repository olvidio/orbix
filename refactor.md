# Criterios para la siguiente refactorizacion (misma linea que `profesores` lote 1)

Este documento resume el patron acordado para seguir migrando pantallas desde `apps/` hacia `frontend/` + `src/`, sin mezclar responsabilidades ni romper convivencia con URLs antiguas.

## Orden de trabajo

1. **Baseline breve** antes de tocar codigo: que pantalla, que parametros GET/POST, que salida HTML o JSON, casos `rstgr` / permisos si aplican. Anotarlo en `documentacion/` junto al modulo (por ejemplo `documentacion/<modulo>_migracion_baseline.md`).
2. **Separar capas primero**; dejar refactors finos (SRP, tests unitarios) para despues de que la pantalla ya viva en `frontend` + `src`.
3. **Un vertical slice por PR o por commit logico** (una pantalla o un flujo filtro+ajax), no mezclar varios modulos.

## Capas y responsabilidades

| Capa | Ruta / carpeta | Responsabilidad |
|------|----------------|-----------------|
| Backend API | `src/<modulo>/infrastructure/ui/http/controllers/*.php` | Solo orquestacion HTTP minima: leer input, llamar a `application`, responder con `ContestarJson::enviar($error, $data)`. **Sin** `echo` de HTML ni `Lista` aqui. |
| Caso de uso | `src/<modulo>/application/*.php` | Montar arrays de datos (`a_cabeceras`, `a_valores`, ids de tabla, etc.) usando repositorios/servicios del contenedor. Devolver datos de dominio/UI listos para serializar; el controlador HTTP es quien llama a `ContestarJson::enviar(...)`. |
| Rutas HTTP | `src/<modulo>/config/routes.php` | Registrar `/src/<modulo>/<nombre>` con GET y POST si hace falta (compatibilidad). |
| Frontend controlador | `frontend/<modulo>/controller/*.php` | `require_once("frontend/shared/global_header_front.inc")`, llamadas `PostRequest::getDataFromUrl('/src/...', $campos)`, construir `web\Lista` u otros componentes UI, pasar variables a la vista. |
| Frontend vista | `frontend/<modulo>/view/*.phtml` | Presentacion: HTML, scripts, `mostrar_tabla()`, sin consultas a BD ni contenedor. |
| Compatibilidad legacy | `apps/<modulo>/controller/*.php` | Opcional: un `require` al controlador `frontend` equivalente. Marcar en comentario que la URL `apps/...` esta **deprecada** para enlaces nuevos. |

## Patron de llamada backend desde frontend

Referencia: `frontend/usuarios/controller/usuario_lista.php`.

- URL backend: cadena que empiece por `/src/<modulo>/...` (sin host; `PostRequest` anade `ConfigGlobal::getWeb()`).
- Parametros: array asociativo; el hash de seguridad lo genera `PostRequest` internamente.
- Respuesta: `json_decode` del campo `data`; comprobar `error` en el array devuelto si se maneja sin `exit`.

## Patron de respuesta JSON en `src`

- En controladores HTTP de `src/.../infrastructure/ui/http/controllers`, preferir `ContestarJson::enviar($error, $data)` directamente. Forma estandar: `{success: bool, mensaje: string, data: string|array}` — el cliente siempre recibe `success` y `mensaje`, y `data` con el payload util.
- Evitar el patron intermedio:
  - `$jsondata = ContestarJson::respuestaPhp(...);`
  - `ContestarJson::send($jsondata);`

  Usar **`ContestarJson::enviar`** (no `send`) para que sea uniforme en todo el repo.
- En refactors nuevos, `application` deberia devolver datos crudos (array/string) o texto de error, no la respuesta JSON ya montada.
- Si existe codigo previo donde `application` ya devuelve `ContestarJson::respuestaPhp(...)`, puede mantenerse temporalmente, pero no usarlo como patron para codigo nuevo.
- **Prohibido en `src/.../controllers`:** `echo` de HTML, `die("msg")`, `print`, respuestas en texto plano salvo el caso excepcional ya documentado (`centros_update`) donde un formulario legacy lee `.done(rta_txt)` sin parsear JSON. En ese caso documentar el motivo en el propio fichero.
- **Mutaciones (eliminar, editar, duplicar, publicar, importar, cambiar_tipo, nuevo, update…):** tambien tienen que devolver JSON con `{success, mensaje}` aunque no haya payload — nunca responder con cuerpo vacio. El JS consumidor debe pintar `mensaje` cuando `success === false` y refrescar UI cuando `success === true`.

## Endpoints por accion (evitar `que`, `Qmod`, `salida`…)

- Evitar endpoints multiproposito con parametro dispatcher (`que`, `Qmod`, `salida`, `modo`, …).
- Preferir **un endpoint por accion**: p.ej. `/src/<modulo>/<recurso>_lista`, `/src/<modulo>/<recurso>_update`, `/src/<modulo>/<recurso>_eliminar`.
- Tambien aplica a los `switch ($Qmod)` internos: cada rama deberia ser su propio endpoint + use case (ej. `actividad_update` se partio en `actividad_publicar`, `actividad_importar`, `actividad_duplicar`, `actividad_cambiar_tipo`, `actividad_nuevo`, `actividad_editar`, `actividad_eliminar`).
- En `application`, separar tambien clases/casos de uso por accion (`...Lista`, `...Update`, `...Eliminar`) para reducir `switch` y facilitar tests.
- En `frontend`, llamar directamente al endpoint de la accion correspondiente (sin enviar campos de acciones no usadas).
- Si existe un endpoint legacy con dispatcher, mantenerlo solo como wrapper de compatibilidad temporal y marcarlo como deprecado en comentario; cuando no queden referencias, eliminarlo.
- **Excepcion tolerable** temporalmente: dispatcher que agrupa salidas muy relacionadas de lectura (ej. `actividad_tipo_get` con `salida=asistentes|actividad|nom_tipo|...`) si todas las ramas comparten ya el contrato JSON estandar y viven como use cases independientes en `application`. Documentar que es transicion.

### Playbook de eliminacion de dispatcher `*_ajax` / `*_update`

Patron mecanico repetido en `notas` (`acta_ajax`, `notas_ajax`, `acta_update`, `update_1011`). Un dispatcher es un controlador con `switch($Qque)` / `switch($Qmod)` que enruta a acciones diferentes; se desmonta asi:

1. **Mapear cada rama del switch a un endpoint `/src/<modulo>/<accion>` dedicado.** Extraer la logica a un use case en `src/<modulo>/application/` con responsabilidad unica (ej. `ActaNueva`, `ActaModificar`, `ActaEliminar`; `PersonaNotaNueva`, `PersonaNotaEditar`, `PersonaNotaEliminar`).
2. **Actualizar todos los consumidores JS en el mismo commit** — cambiar el `url` del `$.ajax` a la accion concreta segun el hidden `mod` / `que` del form.
3. **Ajustar los `.done` a la respuesta JSON estandar** (`ContestarJson`) con `dataType: 'json'`; dejar de esperar HTML/texto legacy.
4. **Borrar el dispatcher** cuando `rg "<nombre_dispatcher>"` este limpio en todo el repo.

**Excepcion**: si alguna rama del dispatcher devolvia **HTML inline** (construyendo un `<form>`, `<select>`, bloque de tabla, etc.), esa rama no se convierte a endpoint JSON sino a `frontend/<modulo>/controller/<accion>_form.php` + vista `.phtml`. El HTML se monta en el frontend; los endpoints `/src/...` nunca devuelven HTML. Ejemplo real: rama `frm_buscar` de `notas_ajax` → `frontend/notas/controller/actividad_buscar_form.php`.

## Convencion de naming en `src/<modulo>/application/`

La jerarquia tipica de `src/<modulo>/application/` tiene tres zonas con semantica distinta. **El nombre de la clase y su ubicacion indican su rol**:

| Ubicacion | Sufijo esperado | Rol | Ejemplos |
|-----------|-----------------|-----|----------|
| `application/` (raiz) | **sin sufijo** | Caso de uso publico: accion del modulo (mutacion o lectura compleja). Lo llaman los controladores HTTP de `src/.../controllers/` o los use cases `*Data`. | `ActaNueva`, `ActaEliminar`, `PersonaNotaEditar`, `AsignaturasPendientes`, `TablaAlumnosAsignaturas`, `Tesera`, `Select1011`, `InformeStgrNumerarios` |
| `application/` (raiz) | `*Data` | *Data builder*: junta lecturas de repos + dropdowns en un array serializable que el controlador HTTP pasa a `ContestarJson::enviar(...)`. No tiene efectos secundarios. | `BuscarActaData`, `PosiblesOpcionalesData`, `NotaPersonaFormData`, `ActividadesBuscarData` |
| `application/services/` | **`*Service`** | Helper compartido entre varios use cases (SQL repetido, parseo de input, tablas temporales…). No es un caso de uso en si mismo. | `ResumenTempTablesService` |
| `application/support/` | libre | Soporte interno que solo usan los use cases del modulo (parsers, policies). | `PersonaNotaInputParser` |
| `application/legacy/` | libre | Bloque heredado grande encapsulado tras use cases tipados (ver seccion dedicada mas abajo). | `legacy\Resumen` |

**Reglas derivadas:**

- **No mezclar sufijo `Service` con clases en la raiz.** Si se encuentra un `FooService.php` en `application/`, o se mueve a `application/services/` (es un helper) o se renombra a `Foo` (es un use case). En `notas` se corrigieron cuatro casos (`AsignaturasPendientesService` → `AsignaturasPendientes`, etc.).
- Un use case en la raiz **no puede heredar de una clase de `services/`**; al reves si (`ResumenTempTablesService` se inyecta dentro de un use case, no lo hereda).
- Un use case **no deberia `use` a otro use case de su raiz en tiempo de ejecucion** (si lo hace es señal de que uno de los dos es helper y deberia estar en `services/`). Los use cases se componen via el controlador HTTP o el `*Data`.

## Modulo `ubis` — patrones ya aplicados (retomar en siguientes refactors)

Linea de trabajo: **frontend** delgado (`PostRequest` + vistas) y **src** con casos de uso + controladores HTTP bajo `src/ubis/infrastructure/ui/http/controllers/`. Rutas en `src/ubis/config/routes.php` con prefijo `/src/ubis/<nombre>` (GET y POST si hace falta).

### Servicios `*Dropdown` y desplegables

- En `src/ubis/application/services/*Dropdown` (p. ej. `RegionDropdown`, `DelegacionDropdown`, `TipoCentroDropdown`, …) **solo devolver `array` value => etiqueta**. No instanciar `web\Desplegable` en `src`.
- Montar el `<select>` en **vista** `frontend/ubis/view/*.phtml` con `web\Desplegable::desdeOpciones($opciones, 'nombre_campo')`, luego `setOpcion_sel(...)`, `setAction(...)` si aplica, y `desplegable()`.

### Datos de formulario / listados: `*Data` + `PostRequest`

- Agrupar lecturas de repos + dropdowns en clases `src/ubis/application/*Data.php` con `execute(...)` que devuelvan arrays serializables.
- Controlador HTTP minimo: `ContestarJson::enviar($error, $array)` (salvo excepciones abajo).
- Controlador **frontend** `frontend/ubis/controller/*.php`: `PostRequest::getDataFromUrl('/src/ubis/<endpoint>', $campos)`; si la respuesta trae `error`, tratarla (`exit`, `echo`, etc.) segun la pantalla.
- **Ejemplos de endpoints `_data` (solo lectura / opciones):** `ubis_buscar_data`, `ubis_editar_data`, `delegacion_que_data`, `list_ctr_data`, `lista_ctrs_data`.
- **`ubis_editar`:** calcular `dl`/`region` efectivos para las opciones **antes** del `switch` (`dlOpc` / `regionOpc` segun `tipo_ubi`), **una sola** llamada a `ubis_editar_data`, reutilizar `$dataOpciones` en las tres ramas y comprobar `error`.

### Mutaciones (guardar / trasladar / update direccion)

- Logica en `src/ubis/application/<Accion>.php` (`execute` con `array $input` / `$_POST`).
- **JSON estandar:** controlador `src/.../*.php` con `ContestarJson::enviar($errorTxt, $data)`. El **proxy** frontend (p. ej. `direccion_update.php`, `trasladar_ubis.php`) hace `PostRequest` y, si hace falta, `echo` solo el error o cuerpo vacio (compatibilidad con AJAX que no parsea JSON).
- **Respuesta texto plano (legacy AJAX):** si el JS espera string en `.done(rta_txt)` sin JSON — caso **`centros_update`**: el controlador en `src` hace `header('Content-Type: text/plain; charset=UTF-8')` y `echo CentrosUpdate::execute($_POST)`. Los formularios que postean con `web\Hash` deben usar **URL absoluta** `rtrim(ConfigGlobal::getWeb(), '/') . '/src/ubis/centros_update'` para que el hash coincida con el destino.
- **Direcciones:** `DireccionesResolver` centraliza repos por `obj_dir`; reutilizar en nuevos casos de uso de direcciones.

### Checklist al mover otro controlador `ubis` desde `frontend`

1. `grep` de la ruta antigua (`frontend/ubis/controller/<nombre>.php`) y actualizar llamadas / `Hash::setUrl` / JS.
2. Añadir ruta en `src/ubis/config/routes.php`.
3. `php -l` en ficheros tocados.
4. Decidir tipo de respuesta (JSON `ContestarJson` vs texto plano) segun el consumidor (proxy `PostRequest` vs navegador directo).

## Patron JavaScript para guardar (sin `trigger("submit")`)

- En vistas frontend, evitar el patron `form.one("submit") + trigger("submit") + off()`.
- Para acciones de guardado, usar llamada directa con `$.ajax(...)` y manejar respuesta en `.done(...)`.
- Construir `data` con `$(formulario).serialize()` (o parametros explicitos cuando convenga) y enviar a la URL de accion (`..._update`, `..._guardar`, etc.).
- Hacer el refresco de lista/UI dentro de `.done(...)` para mantener el flujo asíncrono claro y evitar dobles envíos.

## URLs canonicas y menus

- **Enlaces y menus nuevos:** siempre rutas bajo `frontend/.../controller/....php`.
- **Actualizar plantillas** donde existan (`documentacion/Documentacion_Obix/menus.csv`, `proves/aux_metamenus.csv`, seeds SQL si el repo los usa como referencia).
- **Bases ya en produccion:** si los menus estan en tablas con paths `apps/...`, planificar un UPDATE SQL acorde; el repo solo documenta el destino deseado.

## Validacion antes de dar por cerrado un slice

- `php -l` en todos los ficheros nuevos o tocados.
- Comparar salida relevante (ids de tabla, columnas, cardinalidad de filas) con el baseline.
- Probar al menos un caso con datos y uno vacio si aplica.
- Si la pantalla depende de ambito (`rstgr`, etc.), probar ambas ramas o documentar riesgo.

## Que evitar en esta fase

- No mover logica de negocio a `.phtml`.
- No hacer que `src` renderice HTML de aplicacion. En particular, **prohibido** en `src/.../application` y `src/.../controllers`:
  - Instanciar `web\Desplegable`, `web\Lista` u otros componentes de UI (son responsabilidad del frontend).
  - `echo`, `print` o `die("html")` en controladores HTTP — siempre `ContestarJson::enviar`.
  - Devolver strings con HTML desde use cases (`return $oDespl->desplegable();`, `return $oLista->mostrar_tabla();`).
- No instanciar clases de `src/` directamente desde `frontend/controller` ni `frontend/view`: usar `PostRequest` contra un endpoint.
- No cambiar un endpoint backend sin actualizar **a la vez** todos los consumidores JS/PHP.
- No eliminar de golpe los wrappers `apps/` hasta que no queden referencias (grep en repo y, si aplica, datos en BD).

## Migracion de vistas y namespace de render

- Al migrar un controlador a `frontend/<modulo>/controller`, migrar tambien su vista a `frontend/<modulo>/view` (plantillas **`.phtml`**; no dejar la vista canónica solo en `apps/<modulo>/view`).
- **Render canónico (patrón `encargossacd`, `misas`, etc.):** en el controlador `frontend/<modulo>/controller/*.php` usar siempre:
  - `use frontend\shared\model\ViewNewPhtml;`
  - `$oView = new ViewNewPhtml('frontend\\<modulo>\\controller');`
  - `$oView->renderizar('nombre_plantilla.phtml', $a_campos);`
- `ViewNewPhtml` resuelve la ruta física sustituyendo `controller` (o `model`) por `view` bajo `DOCUMENT_ROOT` + `ConfigGlobal::$web_path`, es decir las plantillas viven en **`frontend/<modulo>/view/`**, no en `apps/`.
- **Twig:** reservado para casos excepcionales; si hace falta, `new ViewTwig(...)` debe apuntar a un directorio bajo `apps/` donde exista el loader Twig (no mezclar con la convención `ViewNewPhtml` + `frontend/.../view`).
- Cuando el frontend ya renderiza bien, eliminar la copia legacy en `apps/<modulo>/view` y actualizar referencias (`grep`, exportación ODT, menús).
- Revisar rutas hardcodeadas dentro de vistas JS/HTML (`apps/...`) y cambiarlas a `frontend/...` para evitar llamadas mixtas.

## Convencion para legacy en apps

- En `apps/<modulo>/controller`, preferir wrappers minimos que deleguen a `frontend/...`.
- Si se necesita preservar temporalmente logica antigua para consulta o rollback, moverla a archivos con prefijo `z...` y dejar claro que no son rutas canonicas.
- Rutas canonicas para nuevas llamadas: siempre `frontend/...` (UI) y `/src/...` (API).
- **No tocar las clases `Info*.php` de `apps/<modulo>/model/`** (`Info3010`, `Info1011`, …). Son metadatos de dossier (`extends core\DatosInfo` con `getId_dossier()`, titulos, textos, clase y metodo gestor) que el sistema de dossiers resuelve dinamicamente por numero. Aunque parezcan huerfanas (`rg` no siempre encuentra callers estaticos), son usadas en runtime y **no** deben moverse ni eliminarse durante un refactor de la pantalla; se mantienen en `apps/<modulo>/model/` tal cual.

## Bloques heredados encapsulados en `src/<modulo>/application/legacy/`

A veces un modelo legacy es tan grande (cientos/miles de LOC de SQL ad-hoc + tablas temporales) que reescribirlo no aporta valor inmediato, **pero** el frontend no deberia seguir importandolo como si fuera una clase de dominio. Para ese caso hay una tercera via entre *mover tal cual a `application/`* y *dejarlo en `apps/.../model/`*: **aislarlo en `src/<modulo>/application/legacy/`** detras de wrappers tipados.

Caso real: `apps/notas/model/Resumen.php` (1294 LOC) → `src/notas/application/legacy/Resumen.php`, encapsulado por `InformeStgrNumerarios`, `InformeStgrAgregados`, `InformeStgrProfesores` en `application/` raiz.

### Reglas

- El **frontend nunca** hace `use src\<modulo>\application\legacy\...`. La unica capa que conoce el legacy es `application/` (raiz), que expone casos de uso con API tipada.
- Cada flujo del frontend que necesite el legacy tiene su **wrapper tipado** en `application/` que:
    - Recibe input simple (enteros, arrays, DTOs), no `$_POST` ni propiedades mutables.
    - Devuelve **arrays neutros** (datos), nunca HTML. Si el legacy aun emite HTML (`Lista()`, `mostrar_tabla()`, …) queda como deuda interna pero **no se propaga al wrapper**: el wrapper pone el HTML dentro del array como string y la vista lo imprime con `<?= $datos['lista'] ?>`. Mas adelante se puede convertir a estructura si compensa.
    - No expone setters/getters del legacy al caller: el wrapper encapsula la secuencia `setX() → nuevaTabla() → enY()` que el legacy requiere.
- Los **use cases wrapper siguen la convencion de naming** (`InformeStgrNumerarios`, sin `Service`). Solo el bloque heredado vive en `legacy/`.
- **No es deuda arquitectonica a ojos del resto del modulo**: la capa `legacy/` puede vivir indefinidamente. Lo que si es deuda es importar `legacy/` desde fuera de `application/`.

### Cuando elegir `application/legacy/` vs otra opcion

| Situacion | Destino |
|-----------|---------|
| Clase legacy pequeña (<300 LOC) y sin SQL raro | Reescribir en `application/` raiz, borrar legacy. Patron `Tesera`, `TablaAlumnosAsignaturas`, `AsignaturasPendientes`. |
| Clase legacy grande, pero con 3-4 responsabilidades separables por reescritura | Partir en use cases en `application/` raiz + helpers en `application/services/`; borrar legacy. |
| Clase legacy grande, SQL muy especifico, reescribir no aporta valor (ej. >1000 LOC de reportes con tablas temporales) | **`application/legacy/<Clase>.php`** + wrappers tipados. |
| Widget `SelectNNNN.php` usado por `DossierTipoFileSuffixResolver` | `application/<Clase>.php` (resolver extendido para mirar tambien en `application/`); ver `src/dossiers/application/DossierTipoFileSuffixResolver::resolveSelectClassFqcn()`. |

### Limpieza minima que si conviene hacer en el legacy al moverlo

Aunque no se reescribe la logica de reportes, **si** vale la pena hacer una pasada mecanica al moverlo a `legacy/`:

- Arreglar bugs locales evidentes (pisado de propiedades, `exit()` en constructor → `\InvalidArgumentException`, typos).
- `(int)` / `(float)` defensivo en parametros que se interpolan en `WHERE` / `HAVING` cuando no se puede migrar a `prepare()` (ver punto sobre N+1 / prepared abajo).
- Eliminar codigo muerto evidente: metodos sin callers (`rg`), propiedades no asignadas, bloques comentados.
- Eliminar `if`/`else` tautologicos.

Reescribir logica de reportes o partir la clase en dos se considera *fase 2* y solo se aborda si hay necesidad concreta.

## Separacion frontend ↔ backend: nunca instanciar `src` desde `frontend/view/controller`

- Las vistas (`.phtml`, `.html.twig`) y los controladores frontend **no pueden** hacer `new src\...\application\...` ni `use src\...`. Toda obtencion de datos pasa por **`frontend\shared\PostRequest`** contra un endpoint `/src/<modulo>/<accion>`.
- En revisiones recientes se detecto esta violacion en `actividad_tipo_get` y `actividad_que` (se importaban clases de `src/` desde el controlador frontend). Patron correcto:
  1. Crear o reutilizar un endpoint backend en `src/.../controllers/` que devuelva JSON.
  2. El frontend controlador hace `PostRequest::getDataFromUrl('/src/<modulo>/<accion>', $campos)` y decodifica `json.data`.
  3. La vista recibe ya el array final y solo pinta.
- Regla practica al migrar una pantalla: `grep -n "use src\\\\" frontend/<modulo>/` debe dar **cero** resultados fuera de lo permitido (interfaces de contrato muy estables del dominio si las hubiera).

## Desplegables devueltos por endpoints AJAX: payload + constructor en frontend

Los endpoints `src/.../controllers/*` **no** deben devolver HTML de `<select>`. Los use cases de `application` **no** deben instanciar `web\Desplegable`.

### Contrato de payload

Cada endpoint que alimente un `<select>` dinamico devuelve, dentro de `json.data`, un objeto con esta forma (campos ausentes → valor por defecto):

```json
{
  "id":         "iasistentes_val",
  "opciones":   { "value1": "Etiqueta 1", "value2": "Etiqueta 2" },
  "selected":   ".",
  "blanco":     true,
  "val_blanco": ".",
  "action":     "fnjs_actividad(false)"
}
```

- `id` / `name`: identificador del `<select>` resultante.
- `opciones`: mapa `value => label` tal como lo devuelve un servicio `*Dropdown` en `src/.../application/services/`.
- `selected`: valor inicial marcado (`''` si no procede).
- `blanco`: si se anade opcion en blanco al inicio.
- `val_blanco`: el value de la opcion en blanco (para casos como `'.'`, `'..'`, `'...'` de `TiposActividades`).
- `action`: handler `onchange` en el cliente (ej. `fnjs_nom_tipo()`).

### Helper JS estandar

Cada vista que consuma este contrato define un helper reutilizable:

```js
fnjs_construir_desplegable = function (json) {
    if (!json || json.success !== true) { return ''; }
    try {
        var data = JSON.parse(json.data);
        if (!data) { return ''; }
        var $sel = $('<select></select>').attr({ id: data.id, name: data.id });
        if (data.action) { $sel.attr('onchange', data.action); }
        if (data.blanco) {
            var vb = (data.val_blanco !== undefined && data.val_blanco !== null) ? data.val_blanco : '';
            $sel.append($('<option></option>').val(vb).text(''));
        }
        $.each(data.opciones || {}, function (value, label) {
            var $opt = $('<option></option>').val(value).text(label);
            if (data.selected !== undefined && data.selected !== '' && String(data.selected) === String(value)) {
                $opt.prop('selected', true);
            }
            $sel.append($opt);
        });
        return $sel.prop('outerHTML');
    } catch (e) { return ''; }
}
```

### Donde inyectar el resultado

- Si el ancla es un **contenedor** (`<td id="lst_xxx">`, `<span>`, `<div>`): `$('#lst_xxx').html(fnjs_construir_desplegable(json))`.
- Si el ancla es el **propio `<select>`** (`#dl_org`, `#filtro_lugar`, `#id_ubi`): `$('#sel').replaceWith(fnjs_construir_desplegable(json))` (y solo si el html devuelto no esta vacio). `$(select).html(<select>…</select>)` produce selects anidados invalidos — nunca usarlo.

### Baseline previo antes de tocar un `Desplegable`

Al refactorizar una clase `application` que devuelve HTML de desplegable:

1. Localizar **todos** los consumidores JS (`rg "salida=<nombre>"`, `rg "fnjs_<nombre>"`).
2. Convertir la clase para devolver array con el contrato de arriba; tipo de retorno `array`, no `string`.
3. Ajustar el endpoint para que envie el payload **directamente** bajo `data` (no envolver en `{content: ...}`): `ContestarJson::enviar('', $payload)`.
4. Actualizar cada consumidor JS al helper `fnjs_construir_desplegable` (y decidir `html` vs `replaceWith` segun el ancla).
5. Solo cuando todos los consumidores esten migrados, quitar el HTML del backend.

## Tipos en propiedades de `application` que reciben `$_POST`

- `$_POST` y `filter_input(INPUT_POST, ...)` llegan **siempre como string** (o `null`), aunque el campo del formulario sea numerico.
- Si una clase `application` tiene setters que reciben directamente valores POST, sus propiedades deben declararse con tipos tolerantes: `int|string`, `?string`, etc., e inicializarse con un valor neutro (`''`, `0`, `null`).
- Ejemplo real: `ActividadLugar::$opcion_sel` estaba declarada `int` y provocaba `TypeError: Cannot assign string to property ... of type int` al recibir `$_POST['opcion_sel']`. Solucion: `private int|string $opcion_sel = '';`.
- Alternativa mas limpia cuando se controla la frontera: castear en el controlador HTTP (`(int)($_POST['x'] ?? 0)`) y mantener tipos estrictos en `application`. Elegir una de las dos estrategias por use case y documentar.

## Checklist al cambiar el contrato de un endpoint backend

Cuando se cambia que devuelve un endpoint en `src/` (p.ej. de HTML a JSON, o de `{content}` a payload estructurado), **no es opcional** actualizar los consumidores:

1. `rg "<endpoint o salida>"` en todo el repo (`frontend/`, `apps/`, `templates/`, `*.js`).
2. Listar cada `.done(...)` / `.success(...)` / `$.ajax` que lo llame y anotar el ancla DOM donde inyecta el resultado.
3. Cambiar backend + **todos** los consumidores en el mismo commit; dejar uno obsoleto rompe la pantalla silenciosamente.
4. Forzar `dataType: 'json'` en las llamadas AJAX y manejar `success === false` mostrando `mensaje`.
5. Si varias vistas comparten consumidor (varios `_actividad_tipo.js.html.twig` en distintos modulos), tocar todos — suelen ser copias divergentes con pequenas diferencias.
6. `php -l` en los ficheros tocados y abrir al menos una pantalla por consumidor para verificar que se sigue pintando.

## `Hash` y reescritura de URLs de AJAX al mover un endpoint

Al migrar una ruta de `apps/.../controller/foo_ajax.php` a `/src/<modulo>/<accion>`, la URL cambia **pero tambien la firma del hash**. Hay que saber que hace cada helper antes de copiar codigo a ciegas:

- **`Hash::getCamposHtml($aCampos, $aHidden)`** — hashea **campos del form** y hidden; **no** incluye la URL. Genera un `<input type="hidden" name="hash" value="...">` que se envia con el form. **Sirve para `POST`** donde los datos viajan en el body y la URL es fija.
- **`Hash::linkSinVal($url, $aCampos)`** — hashea **URL + nombres de campos** (los valores viajan aparte). Devuelve un fragmento `...&hash=X&campos=...` o `?hash=...&campos=...` **dependiendo de si `$url` ya lleva `?`**. Sirve para `GET` / AJAX donde la URL y los nombres de parametros forman parte de la firma.

### Reglas practicas

1. **Una URL nueva = un Hash nuevo.** No reutilizar el `oHash` del dispatcher viejo cuando se migra a endpoints dedicados. Si `acta_ajax.php` tenia un hash que cubria `examinadores` + `asignaturas`, al partirlo en `/src/notas/examinadores_search` y `/src/notas/asignaturas_search` hacen falta **dos `Hash` distintos**, cada uno con sus campos.
2. **`linkSinVal` y `?`/`&`.** Si se concatena el resultado de `linkSinVal` a una URL que ya llevaba query string (`?foo=bar`), se queda con `?` duplicado. Dos soluciones:
    - Pasar `$url` sin query a `linkSinVal`, y añadir los parametros dinamicos como `data` del `$.ajax`.
    - Si la URL ya tiene `?`, concatenar el hash string manualmente con `&` y llamar al `$.ajax` **sin `data`** (todo va en la URL). Patron usado en `form_1011.phtml` / `form_1303.phtml` para `posibles_preceptores`.
3. **No meter campos opcionales en `setCamposForm`.** El hash se calcula con los nombres declarados; si un campo no siempre viaja, romperia la verificacion. Solo declarar los campos que viajan *siempre*.
4. **Pasar las URLs construidas al frontend como strings**, no reconstruir en JS. El controlador hace `$a_campos['url_foo'] = $oHash->...()` y la vista / JS las consume por nombre. Esto ata el hash a su URL y facilita el `rg`.
5. **Cuando una misma vista elige URL segun el modo del form** (`mod=nueva` vs `mod=modificar`), generar los dos hashes en el controlador (`url_acta_nueva`, `url_acta_modificar`) y que el JS haga `var url = ($form.find('[name=mod]').val() === 'nueva') ? url_acta_nueva : url_acta_modificar;`. No se vale un solo hash con URL dinamica.

### Checklist al cambiar la URL de un endpoint consumido por AJAX

1. Generar el nuevo `Hash` en el controlador frontend para esa URL concreta.
2. Pasar `url_xxx` al `.phtml` via `$a_campos`.
3. En JS leer `url_xxx` (declarado por la vista como `var url_xxx = '<?= $url_xxx ?>';`).
4. Ajustar el `$.ajax` a `dataType: 'json'` y parsear la respuesta de `ContestarJson` (`if (!response.success) { … }`).
5. Si el endpoint viejo queda sin callers → borrarlo en el mismo commit (`rg` final).

## Siguiente refactor sugerido en `profesores`

1. `lista_por_departamentos.php` (mismo patron: `application` + JSON + `frontend` + vista).
2. `ficha_profesor_stgr.php` / vistas asociadas (slice mas grande; posible division en sub-flujos).

Tras estabilizar capas, una **fase 2** puede extraer clases mas pequeñas en `application`, inyectar dependencias en lugar de `$GLOBALS['container']` en sitios calientes, y añadir tests sobre los casos de uso con dobles de repositorio.
