# Arquitectura de navegación v2 — Guía de implementación

> **Audiencia:** modelo implementador (composer). Este documento es normativo: no improvises
> variantes ni helpers por pantalla. Si un caso no encaja en estas reglas, NO lo parchees:
> márcalo como `TODO-NAV-V2` y sigue.
>
> **Sustituye a:** la semántica de `Posicion::recordar()` + los 95 helpers de
> `frontend/shared/helpers/ListNavSupport.php` (que quedará como fachada de compatibilidad
> y luego se eliminará).

---

## 1. Principios (no negociables)

1. **El servidor es la única fuente de verdad de la pila.** El cliente JAMÁS envía índices
   de pila (`stack`, `Gstack` desaparecen de todos los formularios y hashes).
2. **Identidad ≠ estado.** Cada entrada de la pila separa:
   - `identity`: qué instancia de página es (URL + ids primarios, p. ej. `id_activ`).
   - `state`: cómo estaba (filtros, `id_sel`, `scroll_id`, columnas visibles).
3. **Invariante de deduplicación (regla de oro):** al entrar en una página, si su `pageKey`
   ya existe en la pila, se **trunca todo lo que hay por encima** y se **actualiza** esa
   entrada (identity fija, state nuevo). Si no existe, se **apila**.
4. **La intención se declara, no se adivina.** Todo enlace/menú que navega lleva un
   parámetro firmado `nav` con valor `reset` (menú raíz) o nada (navegación normal, cubierta
   por la regla de oro). No existen `push`/`replace` explícitos: los deduce la regla de oro.
5. **Una sola escritura de sesión por request.** Todas las mutaciones de la pila pasan por
   un único gestor que hace `session_start()` → mutar → `session_write_close()` una vez.
6. **Cero helpers por pantalla.** Si durante la migración sientes la necesidad de crear
   `persistXxxParentReturnToPosicion()`, es que estás violando el diseño.

---

## 2. Modelo de datos

`$_SESSION['nav']` (nueva clave; `$_SESSION['position']` se mantiene intacta hasta el
final de la migración):

```
$_SESSION['nav'] = [
  'stack' => [
    [
      'key'      => string,   // sha1(url + '|' + json identity canónico ordenado)
      'url'      => string,   // PHP_SELF del controlador
      'bloque'   => string,   // '#main' | '#fichaNNNN' ... (normalizado con '#')
      'identity' => array,    // solo ids que definen la instancia
      'state'    => array,    // filtros, id_sel, scroll_id... SIN campos efímeros
      'ts'       => int,
    ],
    ...
  ],
]
```

**Campos efímeros** (lista centralizada en UNA constante, único sitio del código):
`h`, `hh`, `hhc`, `hpos`, `stack`, `Gstack`, `PHPSESSID`, `nav`, `submit`.
Se eliminan SIEMPRE antes de persistir identity/state. Nunca se limpian a mano en
controladores.

**Límite de pila:** 20 entradas; al superarlo se descartan las más antiguas.

---

## 3. API de servidor: clase `NavStack`

Fichero nuevo: `frontend/shared/web/NavStack.php` (namespace `frontend\shared\web`).
Una instancia por request, creada por `FrontBootstrap::boot()` y accesible desde
`Posicion` (ver §6 compatibilidad). Firmas exactas (no añadir métodos sin aprobación):

```php
final class NavStack
{
    /** Llamada única por controlador de pantalla. Aplica la regla de oro. */
    public function enter(string $url, string $bloque, array $identity, array $state): void;

    /** Actualiza el state de la entrada actual (cima). Merge superficial. */
    public function updateState(array $patch): void;

    /** Actualiza el state de la entrada n posiciones por debajo de la cima. */
    public function updateStateAt(int $n, array $patch): void;

    /** Entrada n posiciones bajo la cima (0 = cima). null si no existe. */
    public function peek(int $n = 0): ?array;

    /** Destino de "atrás": url + parametros firmados (HashFront) + bloque. */
    public function backTarget(int $n = 1): ?array; // ['url','parametros','bloque']

    /** Vacía la pila (menú raíz, nav=reset). */
    public function reset(): void;
}
```

Reglas internas de `enter()`:

1. Calcular `pageKey` con `url` + `identity` canonizado (ksort + json_encode).
2. Si `nav=reset` venía en el request firmado → `reset()` antes de todo.
3. Buscar `pageKey` en la pila **de la cima hacia abajo**:
   - Encontrado en posición `i` → `array_splice` para eliminar todo por encima de `i`,
     y reemplazar `state` (no merge: el POST de llegada manda; el caso "volver atrás
     restaurando estado" funciona porque `backTarget()` ya inyecta el state guardado
     en los parámetros del POST de vuelta).
   - No encontrado → push.
4. Toda la operación dentro de UNA sección `session_start()`/`session_write_close()`.

`backTarget()` NO muta la pila (no hay pop). La consistencia la garantiza la regla de
oro cuando la página destino ejecute su `enter()`. Esto elimina `deleteFroward()`,
`goEnd()`, `olvidar()` y toda la contabilidad de índices.

`backTarget()` construye los parámetros como `identity + state` de la entrada destino,
firmados con `HashFront::add_hash($params, $url)` (no tocar `HashFront`).

---

## 4. Contrato de cada controlador de pantalla

Patrón único, sin variantes por tipo de pantalla:

```php
$oPosicion = FrontBootstrap::boot();

$nav = $oPosicion->nav();                 // NavStack del request
$nav->enter(
    url:      $_SERVER['PHP_SELF'],
    bloque:   '#main',                    // o el segmento que corresponda
    identity: ['id_activ' => $idActiv],   // SOLO ids de instancia; [] en listados raíz
    state:    $filtrosYSeleccionDelPost,  // el propio controlador declara sus campos
);
```

- **Qué es `identity` en cada tipo de pantalla:**
  - Formulario de búsqueda (`*_que.php`): `[]` (una sola instancia posible).
  - Listado (`*_select.php`, `lista_*.php`): los parámetros que definen QUÉ lista es
    (p. ej. `lista_asistentes` → `['id_activ' => ...]`). Los filtros NO son identidad.
  - Ficha/formulario (`*_ver.php`, `form_*.php`): el id del registro
    (p. ej. `['id_asistente' => ...]`). Así, editar el asistente 7 y luego el 9 son
    entradas distintas, y volver a la lista las trunca a ambas.
  - Impresión: id del documento.
- `state` se construye con una lista **explícita** de campos por controlador (constante
  local `NAV_STATE_FIELDS` o array literal). Prohibido volcar `$_POST` entero.
- Popups (`#div_modificar`) y bloques fuera de `#main` **no llaman a `enter()`**: no
  entran en la pila.

---

## 5. Cliente (JS): contrato único

Sustituir progresivamente los 4 divs ocultos (`#ir_atras`, `#ir_atras2`, `#go_atras`,
`#js_atras`) por UN mecanismo:

1. **Endpoint nuevo** `frontend/shared/controller/nav_atras.php`:
   entrada `n` (opcional, por defecto 1); salida JSON
   `{url, parametros, bloque}` desde `NavStack::backTarget(n)`, o `{url: null}` si no hay.
2. **Función JS nueva** `fnjs_nav_atras(n)` en `scripts/index.js.php`:
   - marca `sessionStorage.is_back_navigation = 'true'` (se mantiene: el mecanismo de
     restauración de scroll/selección de `fnjs_guardar_estado()` sigue igual),
   - pide `nav_atras.php`, y con la respuesta hace el mismo POST AJAX que hoy hace
     `fnjs_ir_a` (url + parametros → `fnjs_mostra_resposta(resp, bloque)`).
3. La flecha lateral y las flechas de las vistas de impresión pasan a llamar a
   `fnjs_nav_atras()`. Las funciones `fnjs_borrar_posibles_atras`,
   `fnjs_ir_atras_activo_en_main`, `fnjs_left_slide_atras` quedan obsoletas al final
   de la migración (no borrarlas hasta la fase 4).
4. **Selección de filas slickgrid / estado modificable en página:** endpoint
   `frontend/shared/controller/nav_state.php` que recibe `{patch}` firmado y llama a
   `NavStack::updateState($patch)`. El JS lo invoca con debounce (500 ms) al cambiar
   la selección. El scroll puro sigue en `sessionStorage` (no molesta al servidor).
5. Enlaces de menú (`fnjs_link_submenu`): añadir `nav=reset` a los parámetros que ya
   genera el PHP del menú (dentro del hash firmado).

---

## 6. Compatibilidad durante la migración

- `FrontBootstrap::boot()` sigue devolviendo `Posicion`. Se añade `Posicion::nav(): NavStack`.
- `NavStack` escribe en `$_SESSION['nav']`; el código legado sigue usando
  `$_SESSION['position']`. **No comparten datos**: una pantalla está migrada o no lo está.
- Migrar por **flujos completos**, nunca pantallas sueltas (una pantalla migrada cuyo
  "atrás" apunta a una sin migrar debe seguir encontrando destino: por eso el piloto es
  un flujo cerrado).
- En una pantalla migrada: eliminar TODAS las llamadas a `ListNavSupport::*` y a
  `recordar()/setParametros(...,1)/replaceStackParametros()`. Sustituir por el patrón §4.
- Los formularios de pantallas migradas dejan de incluir `stack`/`Gstack` en sus hashes.

---

## 7. Plan de fases (cada fase termina con la validación de §8 en verde)

**Fase 0 — Núcleo.** `NavStack`, endpoint `nav_atras.php`, endpoint `nav_state.php`,
`fnjs_nav_atras()`, constante de campos efímeros. Sin migrar ninguna pantalla.
PHPStan nivel del proyecto en verde.

**Fase 1 — Piloto: flujo actividades** (el caso problemático del usuario):
`actividad_que.php` → `actividad_select.php` → `lista_asistentes.php` →
`asistencia_form` (el formulario de asistencia del flujo real) → y también
`lista_clases_ca.php`. Validar los escenarios E1–E5 de §8.

**Fase 2 — Dossiers:** `dossiers_ver.php` + formularios hijos de segmento
(`form_cargos_de_actividad.php`, etc.). Los hijos en sub-bloque hacen `enter()` normal
con su propio `bloque` (`#fichaNNNN`); "atrás" desde el hijo restaura el padre en su
bloque gracias a `backTarget()`.

**Fase 3 — Impresiones y resto** (pantallas con `mostrar_back_arrow`), módulo a módulo,
guiándose por `php tools/audit/audit_posicion_nav_migration.php` para inventariar lo pendiente.

**Fase 4 — Limpieza:** eliminar `ListNavSupport`, los métodos de pila de `Posicion`
(`recordar`, `go`, `goStack`, `olvidar`, `deleteFroward`, `js_atras`, `go_atras`,
`mostrar_left_slide`, `mostrar_back_arrow`, `replaceStackParametros`), los divs ocultos
y las funciones JS obsoletas de §5.3, y `$_SESSION['position']`.

---

## 8. Escenarios de aceptación (probar manualmente o con browser automation tras cada fase)

- **E1 (bucle hermanos — el caso crítico):** actividades → seleccionar actividad →
  lista de asistentes → editar asistente A (form) → guardar/volver a asistentes →
  editar asistente B → volver a asistentes → **atrás** ⇒ lista de actividades con sus
  filtros y fila seleccionada. NUNCA al formulario de A o B.
- **E2 (estado modificado en página):** en un listado, seleccionar otra fila slickgrid,
  navegar a la ficha, atrás ⇒ el listado muestra la fila nueva seleccionada y el scroll.
- **E3 (recarga de la misma página):** cambiar filtros de un listado (re-POST a sí mismo)
  ⇒ la pila NO crece (misma `pageKey` ⇒ se actualiza la entrada).
- **E4 (menú):** clicar cualquier entrada de menú ⇒ pila = [página nueva]; atrás no
  ofrece destino (o flecha oculta).
- **E5 (profundidad):** que → select → hijo → ficha → atrás×3 vuelve exactamente por
  el mismo camino con parámetros correctos (sin "pau desconocido" ni logout por hash).
- **E6 (hash):** todo POST de vuelta pasa `HashFront::validatePost` (si falla, se ve
  logout: es regresión bloqueante).
- **E7 (concurrencia):** con la página cargada, dos AJAX simultáneos (p. ej. selección
  slickgrid + abrir hijo) no corrompen la pila.

---

## 9. Reglas para el implementador

1. PHP 8, tipos estrictos, PHPStan al nivel del proyecto sin nuevos errores.
2. NO tocar: `HashFront`, el mecanismo de sesión/login de `FrontBootstrap`,
   `fnjs_guardar_estado`/restauración de scroll en sessionStorage.
3. NO crear helpers específicos de pantalla. NO añadir parámetros "por si acaso".
4. Commits pequeños: núcleo primero, luego un commit por flujo migrado.
5. Si una pantalla legada hace algo que no encaja (p. ej. depende de `Gstack`),
   anotar `// TODO-NAV-V2: <motivo>` y no migrarla en esa pasada.
6. Cada fase termina ejecutando los escenarios de §8 que apliquen y
   `php tools/audit/audit_posicion_nav_migration.php` para verificar que las pantallas
   migradas ya no aparecen.
