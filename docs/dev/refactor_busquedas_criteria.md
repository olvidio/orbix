# Refactor: búsquedas en repositorio con objetos de consulta (Criteria)

> **Estado:** diseño aprobado (decisiones cerradas el 2026-10-28, sección 5). Inventario hecho (0.1). Siguiente: paso 1.2.
> **Fecha:** 2026-10-28
> **Cómo retomar:** leer las secciones 3–5, buscar en la sección 7 el primer paso sin marcar (`- [ ]`) y ejecutar solo ese paso. Cada paso termina con tests en verde y un commit propio.

---

## 1. Problema actual

Las búsquedas genéricas de los repositorios reciben dos arrays asociativos:

```php
$aWhere    = ['id_activ' => 123, 'propio' => 't', '_ordre' => 'id_nom', '_limit' => 20];
$aOperador = ['propio' => '!='];
$repo->getAsistentes($aWhere, $aOperador);
```

Cada `Pg*Repository::getXxx()` repite el mismo bucle: recorre `$aWhere`, pide a `Condicion::getCondicion()` el fragmento SQL, quita a mano las claves que no se enlazan como parámetro y concatena `WHERE`, `ORDER BY` y `LIMIT`.

Alcance aproximado: **166 interfaces `*RepositoryInterface`** en `src/`, la mayoría con este contrato. A eso se suman `ClaseRepository::getConjunt()` (une varios repositorios con el mismo `$aWhere`) y el generador de código (`src/devel_codegen`), que sigue produciendo repositorios nuevos con este patrón.

### Problemas concretos

| # | Problema | Dónde se ve |
|---|----------|-------------|
| P1 | **`application/` conoce columnas y operadores SQL.** Contradice las reglas de capas de `AGENTS.md` (application no ejecuta SQL ni conoce detalles de persistencia). | Cualquier caso de uso que monte `$aWhere` |
| P2 | **Claves mágicas mezcladas con campos**: `_ordre`, `_limit` viven en el mismo array que las columnas. | `getXxx()` de cada repositorio |
| P3 | **Valores interpolados en el SQL, no enlazados** → riesgo de inyección y de errores de tipos: `OR`, `BETWEEN`, `IN`/`NOT IN` (parcialmente escapado), `TXT` (SQL crudo), `_ordre`, `_limit`. | `Condicion::getCondicion()` |
| P4 | **Un campo solo puede aparecer una vez** (la clave del array es el nombre del campo y también del parámetro `:campo`). Por eso existen atajos como `BETWEEN` con `"a,b"` en un string. | Contrato de `$aWhere` |
| P5 | **Sin tipado útil para PHPStan**: `array<string, mixed>` + operadores como strings libres; un typo en un operador cae al `default` y genera SQL inválido en tiempo de ejecución. | Firma de los métodos |
| P6 | **Código duplicado** en ~166 repositorios (mismo bucle, mismos `unset`). | `Pg*Repository` |
| P7 | **Formatos ad hoc de valor** según operador: lista separada por comas (`OR`, `BETWEEN`), literal de array PG `{1,2,3}` (`ANY`), mapa id→etiqueta (`IN`). | Comentarios en `Condicion` |

### Operadores soportados hoy (`Condicion`)

`=` (por defecto), `!=`, `<`, `>`, `<=`, `>=` (vía `default`), `IS NULL`, `IS NOT NULL`, `OR` (mismo campo, valores por coma), `BETWEEN` (`"a,b"`), `IN`, `NOT IN`, `ANY` (columna = ANY(array)), `IN_ARRAY` (valor ∈ columna array), `~`, `~*`, `!~`, `!~*`, `~INV` (patrón en la columna), `sin_acentos`, `&` (máscara de bits), `TXT` (SQL libre).

---

## 2. Objetivo

1. Sustituir `$aWhere` / `$aOperators` por un **objeto de consulta tipado e inmutable** que viva en `src/shared/domain`, sin SQL.
2. **Toda traducción a SQL en un único conversor de infraestructura**, con todos los valores enlazados como parámetros y columnas validadas contra una lista blanca.
3. Migración **incremental y con convivencia**: ningún paso rompe a los llamantes que aún usan arrays.
4. Al final, eliminar `Condicion`, el bucle duplicado y la firma `getXxx(array $aWhere, array $aOperators)`.

**Fuera de alcance:** CQRS con buses, ORM, reescribir las consultas SQL hechas a mano para informes (esas siguen siendo métodos específicos del repositorio).

---

## 3. Diseño propuesto

### 3.1 Piezas

```text
src/shared/domain/criteria/
    Criteria.php          # filtros + orden + paginación (inmutable)
    CriteriaField.php     # interfaz que implementan los enums de campos de cada entidad
    Filter.php            # campo + operador + valor
    FilterGroup.php       # AND / OR de filtros o grupos (anidable)
    Operator.php          # enum con los operadores permitidos
    Order.php             # campo + dirección
    OrderDirection.php    # enum ASC / DESC

src/<modulo>/domain/criteria/
    <Entidad>Field.php    # enum con los campos consultables de la entidad (nombres de dominio)

src/shared/infrastructure/persistence/postgresql/
    PgCriteriaConverter.php   # Criteria + mapa campo→columna → ['sql' => ..., 'params' => [...]]
    LegacyWhereAdapter.php    # ($aWhere, $aOperators) → Criteria  (solo transición)
```

### 3.2 Campos con nombre de dominio (decisión B)

Los filtros **no usan strings de columna**. Cada entidad consultable tiene un enum de campos en su dominio:

```php
namespace src\asistentes\domain\criteria;

use src\shared\domain\criteria\CriteriaField;

enum AsistenteField: string implements CriteriaField
{
    case IdActiv = 'id_activ';
    case IdNom = 'id_nom';
    case Propio = 'propio';
    case Plaza = 'plaza';
    // ...solo los campos por los que realmente se busca u ordena
}
```

- El **valor del enum es el nombre del campo en la entidad** (hoy en Orbix coincide con la columna: `Asistente::$id_activ`), no la columna.
- El **repositorio** (infraestructura) tiene el mapa campo → columna. Por defecto es la identidad; solo se declaran las excepciones (`AsistenteField::Plaza → 'plaza_id'`, por ejemplo). Si mañana cambia una columna, se toca solo el mapa.
- Ventajas frente a strings: autocompletado, PHPStan detecta campos inexistentes, y el enum **es** la lista blanca (no hace falta mantener otra).
- Un repositorio solo acepta su propio enum: `Filter` con un `ActividadField` en `AsistenteRepository` → excepción.

### 3.3 API de uso (lado `application/`)

```php
use src\asistentes\domain\criteria\AsistenteField as F;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;

$criteria = Criteria::create()
    ->where(Filter::eq(F::IdActiv, $idActiv))
    ->andWhere(Filter::neq(F::Propio, true))
    ->andWhere(Filter::in(F::IdNom, [1, 3, 7]))
    ->orderBy(F::IdNom)
    ->limit(20);

$asistentes = $asistenteRepository->matching($criteria);
```

OR entre campos distintos (hoy imposible sin `TXT`), anidable a cualquier profundidad:

```php
$criteria = Criteria::create()
    ->where(FilterGroup::anyOf(
        Filter::isNull(F::FFin),
        FilterGroup::allOf(
            Filter::gte(F::FFin, $hoy),
            Filter::eq(F::Propio, true),
        ),
    ));
```

Reglas del objeto:

- **Inmutable**: cada método devuelve una copia, así se puede reutilizar una base y especializarla.
- **Valores PHP nativos o VO** (int, string, bool, `DateTimeInterface`, listas). Si llega un VO, el conversor lo reduce a escalar con su `value()`. Nunca strings con formato (`"a,b"`, `"{1,2}"`).
- **Sin SQL**: ni nombres de función PG ni fragmentos de texto.

### 3.4 Contrato del repositorio

```php
interface AsistenteRepositoryInterface
{
    /** @return list<Asistente> */
    public function matching(Criteria $criteria): array;

    public function count(Criteria $criteria): int;       // opcional, útil para paginar

    /** @deprecated usar matching(Criteria) */
    public function getAsistentes(array $aWhere = [], array $aOperators = []): array;
    // ...
}
```

Durante la transición `getAsistentes()` queda como una línea:

```php
public function getAsistentes(array $aWhere = [], array $aOperators = []): array
{
    return $this->matching(LegacyWhereAdapter::toCriteria($aWhere, $aOperators));
}
```

Así el bucle duplicado desaparece desde el primer módulo migrado, aunque sus llamantes sigan usando arrays.

### 3.5 Conversor (infraestructura)

`PgCriteriaConverter` recibe el `Criteria`, la clase del enum de campos que acepta el repositorio y el mapa campo → columna:

- Genera parámetros con nombres únicos (`:p0`, `:p1`, …) → resuelve P4.
- Todos los valores van enlazados, incluidos `IN`, `BETWEEN`, `LIMIT` y `OFFSET` → resuelve P3.
- `ORDER BY` solo con campos del enum y `ASC`/`DESC` → resuelve P3 para `_ordre`.
- Campo de otro enum → `InvalidArgumentException` (falla en desarrollo, no genera SQL roto).
- `IN` con lista vacía: genera `FALSE` (sin resultados), no excepción.
- Grupos anidados → paréntesis en cada nivel.

### 3.5.1 `LegacyWhereAdapter` (solo transición)

Traduce `$aWhere`/`$aOperators` (que llegan con **nombres de columna**) a `Criteria`:

- Columna → campo con el mapa inverso del repositorio (o `Enum::tryFrom()` si es identidad).
- Parsea los formatos antiguos: `"a,b"` de `BETWEEN`/`OR`, `{1,2}` de `ANY`, mapas id→etiqueta de `IN`.
- `_ordre`: separa por comas y traduce cada columna; si es una expresión (función, `::cast`…), lo pasa como orden crudo.
- `TXT`, `~INV`, `_ordre` complejos y columnas sin campo en el enum: se representan con un **filtro crudo interno** (`LegacyRawFilter`) que **solo puede construir el adaptador** (no forma parte de la API de dominio). Así los llamantes antiguos siguen funcionando, y el inventario dice cuántos casos hay que reescribir a mano.

### 3.6 Correspondencia de operadores

| Hoy (`Condicion`) | Nuevo | SQL generado | Nota |
|---|---|---|---|
| *(vacío)* / `=` | `Filter::eq` | `c = :p` | |
| `!=` | `Filter::neq` | `c <> :p` | |
| `<` `<=` `>` `>=` | `lt` `lte` `gt` `gte` | `c < :p` … | |
| `IS NULL` / `IS NOT NULL` | `isNull` / `isNotNull` | | |
| `IN` / `NOT IN` | `in` / `notIn` | `c IN (:p0, :p1…)` | Valores enlazados; acepta solo listas |
| `ANY` | `in` | `c = ANY(:p)` | Mismo significado que `IN`; el conversor elige la forma |
| `OR` (mismo campo) | `in` | | `OR` con valores por coma era un `IN` |
| `BETWEEN` | `between` | `c BETWEEN :p0 AND :p1` | Enlazado |
| `IN_ARRAY` | `arrayContains` | `:p = ANY(c)` | Para columnas `integer[]` |
| `~` / `~*` | `matches` / `matchesInsensitive` | `c::text ~ :p` | |
| `!~` / `!~*` | `notMatches` / `notMatchesInsensitive` | | |
| `sin_acentos` | `matchesUnaccented` | `public.sin_acentos(c::text) ~* public.sin_acentos(:p)` | Nombre de dominio, SQL en infra |
| `&` | `hasBits` | `(c & :p) = :p` | |
| `~INV` | — | | **Sin equivalente genérico** → método específico del repositorio |
| `TXT` | — | | **Sin equivalente** → método específico del repositorio o `FilterGroup` |
| `_ordre` | `orderBy()` | | Lista blanca |
| `_limit` | `limit()` / `offset()` | | Enlazado |

---

## 4. Cuándo NO usar Criteria

Criteria es para **filtros dinámicos** (pantallas de búsqueda, listados con filtros opcionales). Para búsquedas con significado de negocio fijo es mejor un **método con nombre** en el repositorio:

```php
// Mejor que montar el Criteria en cada caso de uso:
public function findAsistentesPropiosDeActividad(int $idActiv): array;
```

Internamente ese método puede construir un `Criteria`, pero el caso de uso no lo ve. Regla práctica: si el mismo `Criteria` se monta en dos sitios, conviértelo en método con nombre.

---

## 5. Decisiones (cerradas el 2026-10-28)

| # | Pregunta | Decisión | Motivo |
|---|----------|----------|--------|
| 1 | ¿`Query` o `Criteria`? | **`Criteria`** | En DDD/CQRS *Query* es un mensaje de lectura con *QueryHandler*; evitar la confusión. |
| 2 | ¿Clase propia o `doctrine/collections`? | **Clase propia**, API parecida a la de Doctrine | Sin dependencias externas en `domain/`; cubre regex, `sin_acentos` y máscaras de bits; mejor tipado. |
| 3 | ¿Columnas o nombres de dominio? | **Nombres de dominio (opción B)** con enum `<Entidad>Field` por entidad | Desacopla `application/` de la BD; el enum sirve de lista blanca y PHPStan lo comprueba. Ver 3.2. |
| 4 | ¿Grupos OR? | **Anidables desde el principio** (`FilterGroup::anyOf` / `allOf`) | Poco código y elimina la mayoría de usos de `TXT`. |
| 5 | `getConjunt()` | Pasará a recibir un `Criteria` | Pendiente de confirmar con el inventario que los repositorios que une comparten enum de campos. |

---

## 6. Estrategia de migración

- **Convivencia:** los repositorios migrados exponen `matching()` y mantienen `getXxx()` como envoltorio `@deprecated` sobre `LegacyWhereAdapter`. Los llamantes se migran después, a su ritmo.
- **Orden:** empezar por **`asistentes`** (piloto de referencia del proyecto, ver `docs/dev/asistentes_migracion_baseline.md`), después módulos pequeños y al final los grandes (`actividades`, `personas`, `procesos`, `notas`).
- **Freno a código nuevo:** actualizar `devel_codegen` pronto para que los repositorios nuevos ya nazcan con `matching()`. Opcional: regla PHPStan o `grep` en CI que avise de nuevos `$aOperador`/`$aOperators` fuera de `LegacyWhereAdapter`.
- **Unidad de trabajo:** un repositorio por paso; los llamantes de un módulo, en pasos aparte.

---

## 7. Plan por pasos

Marcar `- [x]` al terminar cada paso, con el hash del commit.

### Fase 0 — Medir

- [x] **0.1** Inventario: `php tools/audit/audit_criteria_inventario.php` → `docs/dev/reports/criteria_inventario.md` (commit: ver `git log` de este fichero). Resultados y revisión de casos especiales en la sección 9.
- [x] **0.2** Cerrar las decisiones de la sección 5 (2026-10-28).

### Fase 1 — Infraestructura común

- [x] **1.1** `CriteriaField` (interfaz), `Operator`, `OrderDirection` (enums), `Filter`, `FilterGroup`, `Order`, `Criteria` en `src/shared/domain/criteria/` + tests unitarios (`tests/unit/shared/domain/criteria/`, 26 tests). Decisiones del paso:
  - Se eliminó el prototipo anterior (`Criteria` como interfaz, `AndCriteria`, `OrCriteria`, `FieldFilter`, `LegacyArrayCriteriaFactory`, `PostgresCriteriaRenderer` y su test): sin usos en producción.
  - Añadidos al diseño: `FilterExpression` (interfaz común de `Filter` y `FilterGroup`), `LogicalOperator` (And/Or) y `NullsPosition` (First/Last, usado por `Order` para `NULLS LAST`).
  - `CriteriaField extends BackedEnum`: solo lo pueden implementar enums.
  - No se añadió `like`/`ilike` a `Operator`; se decide al migrar los 3 usos (ver sección 9).
  - `Criteria::where()` y `orderBy()` sustituyen; `andWhere()` y `addOrderBy()` acumulan. `Filter` rechaza `null` (usar `isNull`) y tipos no escalares; los VO se aceptan si tienen `value()`.
- [ ] **1.2** `PgCriteriaConverter` + tests unitarios (SQL y parámetros por operador, mapa campo→columna, campo de otro enum, grupos anidados, IN vacío, orden y límite).
- [ ] **1.3** `LegacyWhereAdapter` + `LegacyRawFilter` + tests que comparen, para cada operador de `Condicion`, el resultado del adaptador con el SQL antiguo (mismas filas).
- [ ] **1.4** Trait o método en `ClaseRepository` que ejecute un `Criteria` (prepara, ejecuta, devuelve filas) para no repetirlo en cada repositorio.

### Fase 2 — Piloto `asistentes`

- [ ] **2.1** `AsistenteField` en `src/asistentes/domain/criteria/`; `PgAsistenteRepository`: añadir `matching()`, convertir `getAsistentes()` en envoltorio. Test de integración.
- [ ] **2.2** Resto de repositorios del módulo (`Dl`, `Ex`, `Out`, `Pub`) y `getConjunt`.
- [ ] **2.3** Migrar los llamantes de `src/asistentes/application/` a `matching()`.
- [ ] **2.4** Revisión: ajustar la API si el piloto lo pide y documentar el patrón en `AGENTS.md`.

### Fase 3 — Generador y freno

- [ ] **3.1** `devel_codegen` genera `matching()` y el enum `<Entidad>Field` en repositorios nuevos.
- [ ] **3.2** Aviso en CI/PHPStan para usos nuevos de `$aWhere`/`$aOperators`.

### Fase 4 — Resto de módulos

Un bloque por módulo, siguiendo el inventario de la fase 0 (de menor a mayor). Para cada uno: repositorios → llamantes → tests.

Orden por nº de llamadas (src, sin tests). Cada bloque: repositorios → llamantes → tests. Se marca al terminar.

- [ ] **4.1 Triviales (≤ 5 llamadas):** `utils_database`, `tablonanuncios`, `dossiers`, `permisos`, `actividadcargos`, `actividadtarifas`, `ubiscamas`, `actividadescentro`, `asignaturas`, `certificados`, `pasarela`
- [ ] **4.2 Pequeños (6–16):** `devel_db_admin`, `shared`, `cartaspresentacion`, `menus`, `configuracion`, `casas`, `zonassacd`, `planning`
- [ ] **4.3 Medianos (20–34):** `dbextern`, `cambios` (`~INV`), `actividades`, `procesos`, `actividadessacd`, `profesores`, `personas` (`TXT`, `OR`), `usuarios`
- [ ] **4.4 Grandes (39–60):** `actividadplazas`, `ubis` (38 interfaces), `inventario`, `actividadestudios`, `notas`
- [ ] **4.5 Los más usados (73+):** `misas`, `encargossacd` (127 llamadas)

### Fase 5 — Limpieza

- [ ] **5.1** Eliminar los envoltorios `getXxx(array, array)` sin llamantes.
- [ ] **5.2** Eliminar `LegacyWhereAdapter` y `Condicion`.
- [ ] **5.3** Actualizar `AGENTS.md`, guía de onboarding y `supervision_arquitectura.md`.

---

## 8. Notas para quien ejecute un paso

- Leer antes `AGENTS.md` (reglas de capas, tests, checklist de PR).
- Un paso = un commit; tests unitarios e integración del módulo en verde antes de marcarlo.
- Si un paso resulta más grande de lo previsto, dividirlo aquí antes de seguir.
- No cambiar comportamiento: el objetivo de cada paso de migración es que las consultas devuelvan **las mismas filas** que antes.

---

## 9. Resultados del inventario (2026-10-28)

Informe completo: [`reports/criteria_inventario.md`](reports/criteria_inventario.md). Cifras aproximadas (regex).

- **183** interfaces de repositorio, **130** con `(array $aWhere, array $aOperators)`; **120** métodos de búsqueda distintos; **~1019** llamadas fuera de repositorios (incluye ~270 en `tests/`).
- `_ordre`: 312 literales simples, 3 complejos, 9 no literales. `_limit`: 16 usos. `getConjunt()`: 5 llamadas, **todas en `AsistenteActividadService`** y un único interface lo declara (`AsistenteRepositoryInterface`), así que la decisión 5 se resuelve en la fase 2.2.
- Operadores más usados: `~` (95), `sin_acentos` (71), `BETWEEN` (47), `IS NULL` (24), `IN` (17). Los raros (`TXT` 3, `~INV` 1, `IN_ARRAY` 1, `&` 3, `LIKE`/`ILIKE` 3) son pocos.

### Casos especiales y cómo resolverlos

| Caso | Dónde | Resolución propuesta |
|---|---|---|
| `TXT` `COALESCE(eap,'x') ~* 'C\d\d'` y su negado | `asistentes/ListaUltimaActivData` (2) | `FilterGroup::anyOf(isNull(Eap), notMatchesInsensitive(Eap, 'C\d\d'))` y `matchesInsensitive(Eap, ...)`. Sin SQL crudo. |
| `TXT` | `PgPersonaDlRepositoryBase:70` | Es el bucle duplicado (`unset` del operador), no un uso real. Desaparece al migrar. |
| `~INV` (patrón en la columna) | `cambios/AvisosGenerarTabla:172` | Método con nombre en `CambioUsuarioObjetoPrefRepository` (p. ej. `findPorTipoActividad`). Único uso. |
| `IN_ARRAY` | `asignaturas/PlanEstudiosFilter` | `Filter::arrayContains`. |
| `&` | `actividadescentro/CentrosDisponiblesData` (3) | `Filter::hasBits`. |
| `LIKE` / `ILIKE` | `ubis/DireccionesTablaData`, `ActividadSelectListado`, `ListaAsisConjuntoActivData` | Revisar uno a uno: si el valor es un patrón simple, `matches*`; si no, añadir `like`/`ilike` a `Operator` (decidir en 1.1). |
| `OR` (3), `ANY` (7) | ver informe | `Filter::in` (ver tabla 3.6). |
| `_ordre` complejos | `f_ini, h_ini NULLS LAST`; `substring(id_tipo_activ::text,1)`; `(json_fases_previas::json->0)::text DESC` | Pasan por `LegacyRawFilter`/orden crudo del adaptador; reescribir a mano al migrar el repositorio (o añadir `nulls` a `Order`). |
| `_ordre` no literal (9) | ver informe | Revisar al migrar cada módulo. |

### Ajustes derivados al diseño

- `Order` debería admitir `NULLS FIRST/LAST` (ya hay un uso real). Decidir en 1.1.
- Los llamantes en `tests/` son ~27 % del total: migrarlos con su módulo (fase 4), no al final.
