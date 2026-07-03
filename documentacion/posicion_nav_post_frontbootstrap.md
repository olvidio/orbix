# Navegación `Posicion` tras `FrontBootstrap`

Migración de `global_object.inc` / `global_header_front.inc` a `FrontBootstrap::boot()`.

**Helpers:** `frontend/shared/helpers/ListNavSupport.php`  
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

1. **`ListNavSupport::clearInheritedStackForRecordar()`** antes de `recordar()` si el POST trae `stack` (evita `deleteFroward()` accidental).
2. **No** llamar a `ListNavSupport::persistRecordarEntry()` / `replaceStackParametros()` en `dossiers_ver` salvo actualizar solo `id_sel` tras un refresh.
3. **No** `ListNavSupport::persistSelectionToPosicion(..., 1)` en navegación dossier → dossier (p. ej. asistentes → matrículas persona).

`ListNavSupport::persistRecordarEntry()` queda para listados cuyo POST arrastra campos de **otro** formulario (p. ej. `pau`/`obj_pau` del hash de `actividad_select` al abrir `lista_clases_ca`).

---

## Patrón base (cualquier pantalla con `recordar()`)

```php
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

// 1) Leer filtros / estado del POST (y restaurar stack si aplica — ver abajo)

// 2) Limpiar stack heredado + recordar
ListNavSupport::bootRecordar($oPosicion);           // o ListNavSupport::bootRecordar($oPosicion, $refresh)

// 3) Persistir POST limpio en la entrada que acaba de crear recordar() (n=0)
ListNavSupport::persistRecordarEntry(
    $oPosicion,
    ListNavSupport::buildReturnParametrosFromPost(),  // o builder específico
);
```

`ListNavSupport::bootRecordar()` = `ListNavSupport::clearInheritedStackForRecordar()` + `recordar()`.

**Nunca** persistir `$_POST` crudo: quitar meta-hash (`h`, `hh`, `hhc`, …) y campos efímeros (`stack`, `Gstack`). Los helpers `ListNavSupport::build*` y `ListNavSupport::persistRecordarEntry` ya lo hacen.

---

## Tipo A — Formulario de búsqueda (`*_que.php`)

Ejemplo: `actividad_que.php`, `personas_que.php`, `planning_persona_que.php`.

- No suele haber restauración de `stack` compleja.
- Tras leer filtros del POST → `ListNavSupport::bootRecordar()` → persistir con builder explícito o `ListNavSupport::buildReturnParametrosFromPost()`.

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

ListNavSupport::bootRecordar($oPosicion);
ListNavSupport::persistRecordarEntry($oPosicion, $returnParametros);

ListNavSupport::persistSelectionOnListPage($oPosicion, $idSel, $scrollId, $stackFromPost !== 0);

// Actualizar padre SOLO después de recordar(), y solo si la URL padre es la esperada
ListNavSupport::persistParentIfUrl($oPosicion, $filtrosParaQue, 'personas_que.php');
// actividad_select: ListNavSupport::persistActividadQueParent($oPosicion, $aGoBack);
```

**Reglas:**

| Hacer | No hacer |
|--------|----------|
| `goStack` / restaurar **antes** de `recordar()` | `goStack` **después** de `recordar()` |
| `setParametros(..., 1)` **después** de `recordar()` | `setParametros(..., 1)` **antes** de `recordar()` |
| Persistir lista de campos mínimos | `ListNavSupport::buildReturnParametrosFromPost()` con campos de otro formulario (`pau`, `queSel`, …) |

Si el listado envía `Gstack` a pantallas hijas, incluir en el hash del formulario:

```php
'Gstack' => $oPosicion->getStack(),
```

---

## Tipo C — Hijo de un listado (botón en tabla)

Ejemplo: `lista_clases_ca.php`, `lista_asistentes.php`, `resumen_plazas.php` desde `actividad_select`.

```php
ListNavSupport::bootChildFromListRecordar($oPosicion);
// o, para actividad_select: ListNavSupport::bootActividadSelectChildRecordar($oPosicion);

ListNavSupport::persistActividadSelectChildEntry($oPosicion, ['id_activ' => $idActiv]);
// Persistir solo lo necesario para recargar ESTA pantalla (sel, id_activ, …)
// NO incluir pau / queSel / id_dossier del formulario padre
```

`ListNavSupport::bootChildFromListRecordar()`:

1. Re-graba la entrada `Gstack` del listado padre (`ListNavSupport::repersistStackEntryFromGstack`).
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
    ListNavSupport::bootDossiersFromActividadSelect($oPosicion, $Qrefresh);
} else {
    ListNavSupport::bootRecordar($oPosicion, $Qrefresh);
}
```

Formularios hijos del dossier (`form_cargos_de_actividad.php`, `form_asignaturas_de_una_actividad.php`, etc.) cargados en **sub-bloque** del segmento:

```php
ListNavSupport::bootDossierChildRecordar($oPosicion);
```

Eso hace:

1. Si no hay `dossiers_ver` en la pila, inserta una entrada con el contexto del POST (evita atrás a `actividad_select`).
2. `recordar()` con POST completo del hijo.
3. Solo si el padre (n=1) es `dossiers_ver.php`, fusiona `id_sel` / `scroll_id`.

**No** usar `ListNavSupport::persistDossierReturnToPosicion()` ni `ListNavSupport::persistCleanReturnToPosicion()` en el padre dossier: sobrescribían la entrada con un POST mínimo o, peor, corrompían `actividad_select` cuando el dossier no estaba en la pila.

---

## Tipo E — Impresión / vista con `mostrar_back_arrow`

Pantallas como `tessera_imprimir.php`, `acta_imprimir.php`, `certificado_emitido_imprimir.php`.

Tras `recordar()`, persistir en la entrada **padre** (n=1) con el helper de impresión correspondiente:

- `ListNavSupport::persistTesseraImprimirParentReturnToPosicion()`
- `ListNavSupport::persistActaImprimirParentReturnToPosicion()`
- etc.

El audit marca `imprimir_sin_padre_limpio` si la vista usa `mostrar_back_arrow` y el controlador no persiste el padre.

---

## Checklist por controlador

Al migrar o corregir un `frontend/*/controller/*.php`:

- [ ] `require_once` de `FrontBootstrap.php` y `ListNavSupport.php`
- [ ] `$oPosicion = FrontBootstrap::boot();` (no `global_object` para sesión/hash)
- [ ] Si usa `stack` en POST: restaurar estado **antes** de `recordar()`
- [ ] `ListNavSupport::bootRecordar()` (o variante `_child_` / `_dossiers_`)
- [ ] `ListNavSupport::persistRecordarEntry()` o `ListNavSupport::persistCleanReturnToPosicion()` con parámetros **explícitos**
- [ ] Listados: `ListNavSupport::persistSelectionOnListPage()` si hay tabla con selección
- [ ] `setParametros(..., 1)` solo **después** de `recordar()`, vía `ListNavSupport::persistParentIfUrl()`
- [ ] Hijos de listado: `Gstack` en el formulario padre + `ListNavSupport::bootChildFromListRecordar()`

---

## Herramientas

| Comando | Uso |
|---------|-----|
| `php scripts/audit_posicion_nav_migration.php` | Hallazgos por categoría |
| `php scripts/audit_posicion_nav_migration.php --only=sin_clear_stack` | Sin `ListNavSupport::bootRecordar` / `clear_inherited` |
| `php scripts/audit_posicion_nav_migration.php --only=padre_antes_recordar` | `setParametros(...,1)` antes de `recordar()` |
| `php scripts/fix_recordar_sin_post_limpio.php --apply` | Inserta `ListNavSupport::persistRecordarEntry` (ya aplicado en bloque) |
| `php scripts/fix_stack_before_recordar_v3.php --apply` | Reordena `goStack` antes de `recordar()` |

---

## Mapa de helpers frecuentes

| Helper | Cuándo |
|--------|--------|
| `ListNavSupport::bootRecordar()` | Casi siempre antes de `recordar()` |
| `ListNavSupport::bootChildFromListRecordar()` | Hijo con `Gstack` en POST |
| `ListNavSupport::persistRecordarEntry()` | POST limpio en entrada actual (n=0) |
| `ListNavSupport::buildReturnParametrosFromPost()` | Formularios genéricos sin campos cruzados |
| `ListNavSupport::persistParentIfUrl()` | Actualizar `*_que` padre tras listado |
| `ListNavSupport::repersistStackEntryFromGstack()` | Re-grabar listado padre al abrir hijo |
| `ListNavSupport::buildActividadSelectReturnParametros()` | Volver a `actividad_select` |
| `ListNavSupport::bootDossierChildRecordar()` | Form hijo de segmento `dossiers_ver` (sub-bloque) |
| `ListNavSupport::persistDossierReturnToPosicion()` | Obsoleto (no-op); usar `ListNavSupport::bootDossierChildRecordar()` |

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
