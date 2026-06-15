# Navegación `Posicion` tras `FrontBootstrap`

Migración de `global_object.inc` / `global_header_front.inc` a `FrontBootstrap::boot()`.

**Helpers:** `frontend/shared/helpers/list_nav_support.php`  
**Audit:** `php scripts/audit_posicion_nav_migration.php --strict`  
**Referencia canónica:** `frontend/dossiers/controller/dossiers_ver.php`, `frontend/personas/controller/personas_select.php`

---

## Por qué falla la navegación

Con `FrontBootstrap`, cada petición AJAX crea:

```php
$oPosicion = FrontBootstrap::boot(); // new Posicion($PHP_SELF, $_POST)
```

El POST suele incluir `stack` (índice en la pila de sesión). Si llamas a `recordar()` **sin limpiar** ese valor:

1. `Posicion::recordar()` interpreta `stack > 0` como “vuelvo a una entrada existente”.
2. Ejecuta `deleteFroward()` y **borra entradas intermedias** de `$_SESSION['position']`.
3. La flecha atrás apunta a la URL/parámetros equivocados (p. ej. `dossiers_ver` sin `pau` → “pau desconocido”).

Además, si llamas a `setParametros(..., 1)` **antes** de `recordar()`, `Posicion` hace `go(1)` y deja `$surl` apuntando al padre; la entrada que crea `recordar()` se guarda con **URL incorrecta**.

---

## Cómo funciona `Posicion::recordar()` (comportamiento original)

```php
$oPosicion = FrontBootstrap::boot();  // new Posicion($PHP_SELF, $_POST)
$oPosicion->recordar();
```

`recordar()` guarda en `$_SESSION['position'][$stack]`:

| Campo | Valor |
|-------|--------|
| `url` | `$PHP_SELF` del controlador |
| `parametros` | **todo** `$this->aParametros` (copia del POST, incl. `pau`, `queSel`, `sel`, `hh`, …) |
| `stack` | índice en la pila |

Al pintar la flecha atrás, `mostrar_left_slide(1)` hace `go(1)` (lee la entrada anterior) y `HashFront::add_hash($aParam, $url)`:

- elimina meta-hash antiguo (`h`, `hh`, `hhc`, …)
- recalcula firma nueva con `hpos=1`
- devuelve query lista para el POST AJAX de vuelta

**Por tanto:** no hay que sustituir la entrada de `recordar()` por un POST “mínimo” en pantallas como `dossiers_ver`. Eso rompe el atrás (hash inválido → sesión destruida → login).

### Qué sí hace falta tras `FrontBootstrap`

1. **`list_nav_clear_inherited_stack_for_recordar()`** antes de `recordar()` si el POST trae `stack` (evita `deleteFroward()` accidental).
2. **No** llamar a `list_nav_persist_recordar_entry()` / `replaceStackParametros()` en `dossiers_ver` salvo actualizar solo `id_sel` tras un refresh.
3. **No** `list_nav_persist_selection_to_posicion(..., 1)` en navegación dossier → dossier (p. ej. asistentes → matrículas persona).

`list_nav_persist_recordar_entry()` queda para listados cuyo POST arrastra campos de **otro** formulario (p. ej. `pau`/`obj_pau` del hash de `actividad_select` al abrir `lista_clases_ca`).

---

## Patrón base (cualquier pantalla con `recordar()`)

```php
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';

$oPosicion = FrontBootstrap::boot();

// 1) Leer filtros / estado del POST (y restaurar stack si aplica — ver abajo)

// 2) Limpiar stack heredado + recordar
list_nav_boot_recordar($oPosicion);           // o list_nav_boot_recordar($oPosicion, $refresh)

// 3) Persistir POST limpio en la entrada que acaba de crear recordar() (n=0)
list_nav_persist_recordar_entry(
    $oPosicion,
    list_nav_build_return_parametros_from_post(),  // o builder específico
);
```

`list_nav_boot_recordar()` = `list_nav_clear_inherited_stack_for_recordar()` + `recordar()`.

**Nunca** persistir `$_POST` crudo: quitar meta-hash (`h`, `hh`, `hhc`, …) y campos efímeros (`stack`, `Gstack`). Los helpers `list_nav_build_*` y `list_nav_persist_recordar_entry` ya lo hacen.

---

## Tipo A — Formulario de búsqueda (`*_que.php`)

Ejemplo: `actividad_que.php`, `personas_que.php`, `planning_persona_que.php`.

- No suele haber restauración de `stack` compleja.
- Tras leer filtros del POST → `list_nav_boot_recordar()` → persistir con builder explícito o `list_nav_build_return_parametros_from_post()`.

---

## Tipo B — Listado (`*_select.php`, `lista_*.php`)

Ejemplo canónico: `personas_select.php`, `actividad_select.php`.

```php
// ANTES de recordar(): si el POST trae stack, restaurar id_sel / filtros
$stackFromPost = isset($_POST['stack']) ? (int) filter_input(...) : 0;
if ($stackFromPost !== 0) {
    $oRestore = new Posicion();
    if ($oRestore->goStack($stackFromPost)) {
        // ... copiar parámetros a variables locales ...
        $oRestore->olvidar($stackFromPost);
    }
}

// Construir $returnParametros explícitos (filtros + id_sel + scroll_id)
$returnParametros = [ /* ... */ ];

list_nav_boot_recordar($oPosicion);
list_nav_persist_recordar_entry($oPosicion, $returnParametros);

list_nav_persist_selection_on_list_page($oPosicion, $idSel, $scrollId, $stackFromPost !== 0);

// Actualizar padre SOLO después de recordar(), y solo si la URL padre es la esperada
list_nav_persist_parent_if_url($oPosicion, $filtrosParaQue, 'personas_que.php');
// actividad_select: list_nav_persist_actividad_que_parent($oPosicion, $aGoBack);
```

**Reglas:**

| Hacer | No hacer |
|--------|----------|
| `goStack` / restaurar **antes** de `recordar()` | `goStack` **después** de `recordar()` |
| `setParametros(..., 1)` **después** de `recordar()` | `setParametros(..., 1)` **antes** de `recordar()` |
| Persistir lista de campos mínimos | `list_nav_build_return_parametros_from_post()` con campos de otro formulario (`pau`, `queSel`, …) |

Si el listado envía `Gstack` a pantallas hijas, incluir en el hash del formulario:

```php
'Gstack' => $oPosicion->getStack(),
```

---

## Tipo C — Hijo de un listado (botón en tabla)

Ejemplo: `lista_clases_ca.php`, `lista_asistentes.php`, `resumen_plazas.php` desde `actividad_select`.

```php
list_nav_boot_child_from_list_recordar($oPosicion);
// o, para actividad_select: list_nav_boot_actividad_select_child_recordar($oPosicion);

list_nav_persist_actividad_select_child_entry($oPosicion, ['id_activ' => $idActiv]);
// Persistir solo lo necesario para recargar ESTA pantalla (sel, id_activ, …)
// NO incluir pau / queSel / id_dossier del formulario padre
```

`list_nav_boot_child_from_list_recordar()`:

1. Re-graba la entrada `Gstack` del listado padre (`list_nav_repersist_stack_entry_from_gstack`).
2. Limpia `stack` heredado y llama a `recordar()`.

---

## Tipo D — `dossiers_ver.php` y formularios de dossier

Referencia: `frontend/dossiers/controller/dossiers_ver.php`.

```php
// Restaurar selección ANTES de recordar (si POST trae stack)
if ($returningViaStack) {
    $oPosicionRestore = new Posicion();
    if ($oPosicionRestore->goStack((int) $stackFromPost)) {
        $restoredIdSel = ...;
        $oPosicionRestore->olvidar((int) $stackFromPost);
    }
}

if ($gstackFromPost > 0) {
    // Abierto desde actividad_select (u otro listado con Gstack)
    list_nav_boot_dossiers_from_actividad_select($oPosicion, $Qrefresh);
} else {
    list_nav_boot_recordar($oPosicion, $Qrefresh);
}
```

Formularios hijos del dossier (`form_cargos_de_actividad.php`, `form_asignaturas_de_una_actividad.php`, etc.) cargados en **sub-bloque** del segmento:

```php
list_nav_boot_dossier_child_recordar($oPosicion);
```

Eso hace:

1. Si no hay `dossiers_ver` en la pila, inserta una entrada con el contexto del POST (evita atrás a `actividad_select`).
2. `recordar()` con POST completo del hijo.
3. Solo si el padre (n=1) es `dossiers_ver.php`, fusiona `id_sel` / `scroll_id`.

**No** usar `list_nav_persist_dossier_return_to_posicion()` ni `list_nav_persist_clean_return_to_posicion()` en el padre dossier: sobrescribían la entrada con un POST mínimo o, peor, corrompían `actividad_select` cuando el dossier no estaba en la pila.

---

## Tipo E — Impresión / vista con `mostrar_back_arrow`

Pantallas como `tessera_imprimir.php`, `acta_imprimir.php`, `certificado_emitido_imprimir.php`.

Tras `recordar()`, persistir en la entrada **padre** (n=1) con el helper de impresión correspondiente:

- `list_nav_persist_tessera_imprimir_parent_return_to_posicion()`
- `list_nav_persist_acta_imprimir_parent_return_to_posicion()`
- etc.

El audit marca `imprimir_sin_padre_limpio` si la vista usa `mostrar_back_arrow` y el controlador no persiste el padre.

---

## Checklist por controlador

Al migrar o corregir un `frontend/*/controller/*.php`:

- [ ] `require_once` de `FrontBootstrap.php` y `list_nav_support.php`
- [ ] `$oPosicion = FrontBootstrap::boot();` (no `global_object` para sesión/hash)
- [ ] Si usa `stack` en POST: restaurar estado **antes** de `recordar()`
- [ ] `list_nav_boot_recordar()` (o variante `_child_` / `_dossiers_`)
- [ ] `list_nav_persist_recordar_entry()` o `list_nav_persist_clean_return_to_posicion()` con parámetros **explícitos**
- [ ] Listados: `list_nav_persist_selection_on_list_page()` si hay tabla con selección
- [ ] `setParametros(..., 1)` solo **después** de `recordar()`, vía `list_nav_persist_parent_if_url()`
- [ ] Hijos de listado: `Gstack` en el formulario padre + `list_nav_boot_child_from_list_recordar()`

---

## Herramientas

| Comando | Uso |
|---------|-----|
| `php scripts/audit_posicion_nav_migration.php` | Hallazgos por categoría |
| `php scripts/audit_posicion_nav_migration.php --only=sin_clear_stack` | Sin `list_nav_boot_recordar` / `clear_inherited` |
| `php scripts/audit_posicion_nav_migration.php --only=padre_antes_recordar` | `setParametros(...,1)` antes de `recordar()` |
| `php scripts/fix_recordar_sin_post_limpio.php --apply` | Inserta `list_nav_persist_recordar_entry` (ya aplicado en bloque) |
| `php scripts/fix_stack_before_recordar_v3.php --apply` | Reordena `goStack` antes de `recordar()` |

---

## Mapa de helpers frecuentes

| Helper | Cuándo |
|--------|--------|
| `list_nav_boot_recordar()` | Casi siempre antes de `recordar()` |
| `list_nav_boot_child_from_list_recordar()` | Hijo con `Gstack` en POST |
| `list_nav_persist_recordar_entry()` | POST limpio en entrada actual (n=0) |
| `list_nav_build_return_parametros_from_post()` | Formularios genéricos sin campos cruzados |
| `list_nav_persist_parent_if_url()` | Actualizar `*_que` padre tras listado |
| `list_nav_repersist_stack_entry_from_gstack()` | Re-grabar listado padre al abrir hijo |
| `list_nav_build_actividad_select_return_parametros()` | Volver a `actividad_select` |
| `list_nav_boot_dossier_child_recordar()` | Form hijo de segmento `dossiers_ver` (sub-bloque) |
| `list_nav_persist_dossier_return_to_posicion()` | Obsoleto (no-op); usar `list_nav_boot_dossier_child_recordar()` |

---

## Flujo de referencia: actividades

```
actividad_que  →  actividad_select  →  lista_clases_ca (u otro hijo)
     ↑                    ↑                      ↓
  (atrás)              (atrás)               (atrás)
```

Pila esperada tras abrir `lista_clases_ca`:

| n | URL | Parámetros |
|---|-----|------------|
| 0 | `lista_clases_ca.php` | `id_activ`, `sel`, … |
| 1 | `actividad_select.php` | filtros búsqueda, `id_sel`, `sasistentes`, … |
| 2 | `actividad_que.php` | filtros del formulario |

Si falta la entrada 1 o tiene URL `dossiers_ver.php`, el atrás falla.
