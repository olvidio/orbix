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

- En controladores HTTP de `src/.../infrastructure/ui/http/controllers`, preferir `ContestarJson::enviar($error, $data)` directamente.
- Evitar el patron intermedio:
  - `$jsondata = ContestarJson::respuestaPhp(...);`
  - `ContestarJson::send($jsondata);`
- En refactors nuevos, `application` deberia devolver datos crudos (array/string) o texto de error, no la respuesta JSON ya montada.
- Si existe codigo previo donde `application` ya devuelve `ContestarJson::respuestaPhp(...)`, puede mantenerse temporalmente, pero no usarlo como patron para codigo nuevo.

## Endpoints por accion (evitar `que`)

- Evitar endpoints multiproposito con parametro `que` (ej. `get_lista`, `update`, ...).
- Preferir **un endpoint por accion**: p.ej. `/src/<modulo>/<recurso>_lista` y `/src/<modulo>/<recurso>_update`.
- En `application`, separar tambien clases/casos de uso por accion (`...Lista`, `...Update`) para reducir `switch` y facilitar tests.
- En `frontend`, llamar directamente al endpoint de la accion correspondiente (sin enviar campos de acciones no usadas).
- Si existe un endpoint legacy con `que`, mantenerlo solo como wrapper de compatibilidad temporal y marcarlo como deprecado en comentario.

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
- No hacer que `src` renderice HTML de aplicacion (salvo respuestas realmente API-only que ya sean JSON).
- No eliminar de golpe los wrappers `apps/` hasta que no queden referencias (grep en repo y, si aplica, datos en BD).

## Migracion de vistas y namespace de render

- Al migrar un controlador a `frontend/<modulo>/controller`, migrar tambien su vista a `frontend/<modulo>/view` (incluyendo `*.phtml` y `*.html.twig`).
- En controladores frontend, usar:
  - `new ViewNewPhtml('frontend\\<modulo>\\controller')` para vistas `*.phtml`.
  - `new ViewTwig('frontend/<modulo>/controller')` para vistas `*.html.twig` temporales o casos legacy.
- Evitar dejar la misma vista activa en `apps/<modulo>/view` y `frontend/<modulo>/view` a la vez; cuando el frontend ya renderiza bien, eliminar la copia legacy de `apps/<modulo>/view`.
- Revisar rutas hardcodeadas dentro de vistas JS/HTML (`apps/...`) y cambiarlas a `frontend/...` para evitar llamadas mixtas.

## Convencion para legacy en apps

- En `apps/<modulo>/controller`, preferir wrappers minimos que deleguen a `frontend/...`.
- Si se necesita preservar temporalmente logica antigua para consulta o rollback, moverla a archivos con prefijo `z...` y dejar claro que no son rutas canonicas.
- Rutas canonicas para nuevas llamadas: siempre `frontend/...` (UI) y `/src/...` (API).

## Siguiente refactor sugerido en `profesores`

1. `lista_por_departamentos.php` (mismo patron: `application` + JSON + `frontend` + vista).
2. `ficha_profesor_stgr.php` / vistas asociadas (slice mas grande; posible division en sub-flujos).

Tras estabilizar capas, una **fase 2** puede extraer clases mas pequeñas en `application`, inyectar dependencias en lugar de `$GLOBALS['container']` en sitios calientes, y añadir tests sobre los casos de uso con dobles de repositorio.
