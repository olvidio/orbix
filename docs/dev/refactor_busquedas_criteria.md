# Refactor: búsquedas en repositorio con objetos de consulta (Criteria)

> **Estado:** diseño aprobado (decisiones cerradas el 2026-10-28, sección 5). Inventario hecho (0.1). Fases 1 y 2 completas; fase 3 completa. Siguiente: fase 4. Presupuesto de IA en la sección 10.
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
- [x] **1.2** `PgCriteriaConverter` + tests unitarios (`tests/unit/shared/infrastructure/persistence/postgresql/PgCriteriaConverterTest.php`, 33 tests). Decisiones del paso:
  - Ubicación: `src/shared/infrastructure/persistence/postgresql/PgCriteriaConverter.php`. Constructor: `new PgCriteriaConverter(AsistenteField::class, ['plaza' => 'plaza_id'])`.
  - API: `convert(Criteria)` → `['sql' => ' WHERE … ORDER BY … LIMIT :pN OFFSET :pM', 'params' => [...]]`; `convertWhere(Criteria)` solo con el WHERE (para `count()`). Claves de `params` sin `:` (`p0`, `p1`…); enteros como `int` (enlazar con `PDO::PARAM_INT`, ver 1.4).
  - Normalización de valores: `DateTimeInterface` → `Y-m-d H:i:s.uP`; `bool` → `'t'`/`'f'`; enum → `->value`; VO → `->value()`.
  - `IN`/`ANY`/`OR` → siempre `IN (:p0, :p1…)`; lista vacía → `FALSE` (`NOT IN` → `TRUE`). `HasBits` usa dos parámetros con el mismo valor.
  - Las columnas del mapa se validan con regex (`col` o `alias.col`); sin comillas, igual que el código antiguo.
  - Eliminado `QueryBindContext` (restos del prototipo, sin usos).
- [x] **1.3** `LegacyWhereAdapter` + `LegacyRawFilter` + tests. Unit: `LegacyWhereAdapterTest` (29, estructura/SQL generado). Integración: `tests/integration/shared/infrastructure/persistence/postgresql/LegacyWhereAdapterEquivalenciaTest.php` (38 casos sobre tabla temporal; compara filas y orden del SQL antiguo —bucle de repositorio + `Condicion`— con adaptador + conversor). Decisiones del paso:
  - Uso: `(new LegacyWhereAdapter(AsistenteField::class, ['plaza' => 'plaza_id']))->toCriteria($aWhere, $aOperators)` (instancia, no estático: necesita la clase del enum y el mapa de columnas).
  - Clases auxiliares `@internal` en `src/shared/infrastructure/persistence/postgresql/`: `LegacyRawFilter` (SQL crudo con `?` enlazados: `TXT`, `~INV`, `LIKE`/`ILIKE`, `FALSE`), `LegacyColumnFilter` (operador sobre una columna sin campo en el enum; solo se valida como identificador) y `LegacyRawOrder` (trozo de `_ordre` no reducible a `columna [ASC|DESC]`).
  - Cambios en el dominio: `OrderExpression` (interfaz; `Order` la implementa), `Criteria::addOrder()` y `Filter::of()` (constructor genérico validado).
  - Compatibilidad conservada: un operador con valor `null` daba `c = NULL` (nunca coincide) → `FALSE`. Diferencia aceptada: `IN` con lista vacía ya no lanza excepción, da `FALSE`.
  - `LIKE`/`ILIKE`/`NOT LIKE`/`NOT ILIKE` pasan por filtro crudo; reescribir con `matches*` al migrar cada uso (sección 9).
- [x] **1.4** Trait `src/shared/traits/RunsCriteriaQueries.php` + `RunsCriteriaQueriesTest` (10 tests de integración). Decisiones del paso:
  - Es un trait (no un método de `ClaseRepository`) que se usa junto a `HandlesPdoErrors`; reutiliza sus `prepareAndExecute()` y `getoDbl_Select()` / `getNomTabla()` de `ClaseRepository`. Mismos errores que hoy: `RuntimeException` (o `false` → `[]` si `throwOnError` es false).
  - API (protegida): `rowsMatching(Criteria, PgCriteriaConverter): list<array<string, mixed>>` (`SELECT *`) y `countMatching(Criteria, PgCriteriaConverter): int`. El repositorio solo hidrata las filas (`Entidad::fromArray`).
  - Los parámetros se pasan en `execute($params)` (todo `PARAM_STR`): PG infiere el tipo de `LIMIT`/`OFFSET` y de las columnas, comprobado en los tests. No se usa `bindValue(PARAM_INT)` porque `execute([])` descarta los enlaces previos.
  - Repositorio típico:
    ```php
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(AsistenteField::class, self::COLUMNAS);
        return array_map(Asistente::fromArray(...), $this->rowsMatching($criteria, $conv));
    }
    ```
  - Los enums de prueba (`TablaField`, `FakeField`) van en ficheros propios para que se autocarguen.

### Fase 2 — Piloto `asistentes`

- [x] **2.0** Red de seguridad **antes** de tocar `PgAsistenteRepository` (ver sección 10.2): test de caracterización de `getAsistentes()` sobre datos sembrados (con `AsistenteFactory`, limpiando al final) que cubra las formas de `$aWhere`/`$aOperators` que usan los llamantes reales. Debe pasar en verde con el código actual; en 2.1 se ejecuta sin cambios tras el envoltorio. Incluir también una pasada por las formas de `getConjunt()`.
  - Hecho en `tests/integration/asistentes/infrastructure/persistence/postgresql/PgAsistenteGetAsistentesCaracterizacionTest.php` (26 tests). Siembra 5 filas con `AsistenteFactory` en `d_asistentes_dl`/`out`/`ex` (ids `99880000x`), limpia en `setUp`/`tearDown`. Cubre: igualdad implícita y `=`, `!=`, `<`, `>`, `BETWEEN` (cadena `a,b`), `IS NULL`/`IS NOT NULL`, `IN`/`NOT IN`, `~`/`~*`/`!~`, booleanos `'t'`/`'f'`, `_ordre` (simple, `DESC`, dos columnas, vacío), `_limit`, clave inexistente, hidratación de la entidad, tabla `all` frente a las hijas y cinco formas de `getConjunt()` (unión por orden de repos, subconjunto, por persona, `_ordre` que reordena, vacío).
  - Decisión: `TXT` no se incluye, porque en el módulo solo lo usa `ListaUltimaActivData` sobre tablas de personas (`$aWhereP`), no sobre asistentes. Tampoco `sin_acentos` ni fechas (los llamantes de asistentes no las usan sobre estas columnas).
  - Las expectativas están escritas a mano y pasan con el código actual; en 2.1 deben pasar sin cambios.
- [x] **2.1** `AsistenteField` en `src/asistentes/domain/criteria/`; `PgAsistenteRepository`: añadir `matching()`, convertir `getAsistentes()` en envoltorio. Test de integración. Decisiones del paso:
  - `AsistenteField` con los 15 campos de la entidad; `COLUMNAS = []` (campo = columna). Las subclases `Dl`/`Ex`/`Out`/`Pub` heredan `matching()` y el envoltorio sin cambios (solo cambian la tabla); su revisión queda para 2.2.
  - `matching()` añadido a `AsistenteRepositoryInterface`; `getAsistentes()` marcado `@deprecated` y reducido a `LegacyWhereAdapter` + `matching()` (usa `RunsCriteriaQueries`). `getConjunt()` no se toca (2.2).
  - Test: `tests/integration/asistentes/infrastructure/persistence/postgresql/PgAsistenteMatchingTest.php` (7 tests, incl. equivalencia con `getAsistentes`). La caracterización de 2.0 (26 tests) pasa sin cambios.
  - Suites verdes: unit e integration de `asistentes`, `actividades`, `planning`, `actividadcargos`, `misas`, `pasarela`; unit de `personas`. `tests/integration/personas` aborta con y sin este cambio (`log/errores.log` inexistente en el contenedor): fallo de entorno previo.
- [x] **2.2** Resto de repositorios del módulo (`Dl`, `Ex`, `Out`, `Pub`) y `getConjunt`. Decisiones del paso:
  - `Dl`/`Ex`/`Out`/`Pub` no necesitan cambios: heredan `matching()` y el envoltorio de `PgAsistenteRepository` y solo difieren en conexión y tabla (revisado; sus métodos propios no usan `$aWhere`).
  - Decisión 5 resuelta: los repositorios que se unen comparten `AsistenteField`. Nuevo `matchingConjunto(list<class-string> $repos, Criteria)` en `AsistenteRepositoryInterface`/`PgAsistenteRepository`: consulta cada repo con el criterio sin orden y ordena el conjunto en memoria por los `Order` (getter, minúsculas, `strcmp`, ASC/DESC; orden estable, así los repos que van antes ganan en empates), igual que el `getConjunt` antiguo. `getConjunt()` queda `@deprecated` (la implementación genérica de `ClaseRepository` no se toca); sus 5 llamadas en `AsistenteActividadService` se migran en 2.3.
  - Test: `PgAsistenteMatchingConjuntoTest` (10 tests). **Corrección posterior:** esta nota afirmaba 4 tests que no existían; se escribieron en la revisión del piloto (ver «Revisión del piloto» tras el paso 2.4). Suites verdes: integration/unit de `asistentes`, integration de `actividades`.
  - Orden en memoria revisado en la revisión del piloto: números como números, textos sin distinguir mayúsculas, nulos como PostgreSQL, getters `getXxx()`/`isXxx()`; `limit`/`offset` se aplican al conjunto unido y un orden no soportado o un repo que no es de asistentes lanzan `InvalidArgumentException`.
- [x] **2.3** Migrar los llamantes de `src/asistentes/application/` a `matching()`. Decisiones del paso:
  - Migrados a `matching(Criteria)` con `AsistenteField`/`Filter::eq`: `ActivPendientesSelectData`, `Select_asistentes_a_una_actividad` (2), `ListaAsistentesData` (2), `FormAsistentesAUnaActividadData`, `ListaEstCtrData`, `ListaPlazasConjuntoActividades` y `AsistenteActividadService` (`getAsistenciasPersonaDeActividades`, `getPlazasOcupadasPorDl`, `getAsistentesDeActividad`, `buscarAsistencia`; 4 de las 5 llamadas a `getConjunt` pasan a `matchingConjunto`).
  - `AsistenteApplicationService` gana `matching()`; su `getAsistentes()` queda `@deprecated`.
  - Pendiente (se queda con arrays legacy): `AsistenteActividadService::getActividadesDeAsistente()` (última llamada a `getConjunt`). Recibe `$aWhereNom`/`$aOperadorNom` y los de actividad desde 8 llamantes de otros módulos (actividadcargos, actividadestudios, `InfoAsistenteDl`…), así que se migra con ellos en la fase 4.
  - Suites verdes: integration/unit de `asistentes`, integration de `actividades` y `actividadcargos`, unit de `ubiscamas`, `actividadcargos` y `actividadestudios`. No se añaden tests nuevos: el comportamiento está cubierto por la caracterización de 2.0 y los tests de `matching`/`matchingConjunto`.
- [x] **2.4** Revisión: ajustar la API si el piloto lo pide y documentar el patrón en `AGENTS.md`. Decisiones del paso:
  - Revisada la API (`Criteria`, `Filter`, `FilterGroup`, `matching`, `matchingConjunto`): el piloto no pide cambios; no se toca código.
  - Nueva sección «Búsquedas con `Criteria`» en `AGENTS.md` (domain/application/infrastructure, `TXT`/`~INV` fuera de `Criteria`, tests). Solo documentación: no se ejecutan suites.
  - Fase 2 completa. Queda el uso de `getConjunt` en `getActividadesDeAsistente()` para la fase 4.


**Revisión del piloto (2026-10-28, `/code-review high` sobre los pasos 2.0–2.4) y correcciones en la rama `refactor/criteria-auto`:**

- Corregido: `PgAsistenteMatchingConjuntoTest` no existía pese a la nota del 2.2; creado (10 tests: unión de Dl/Out/Ex, filtros, orden numérico, nulos, campo booleano, `limit`/`offset` sobre el conjunto, repo no válido, orden crudo). Comprobado que falla contra la versión anterior de `matchingConjunto()`.
- Corregido: `limit`/`offset` se aplicaban a cada repo por separado; ahora se aplican al conjunto unido, tras ordenar.
- Corregido: el orden en memoria comparaba todo como texto (10 antes que 9), ignoraba `NullsPosition` y descartaba sin avisar los campos booleanos (`isXxx()`) y los órdenes crudos. Ahora compara por tipo, respeta los nulos como PostgreSQL y lanza `InvalidArgumentException` si el orden no se puede aplicar.
- Corregido: un repo que no es `AsistenteRepositoryInterface` se omitía en silencio; ahora lanza excepción.
- Corregido (menores): el `Criteria` de `ListaAsistentesData` se construía antes de la guarda `$id_nom !== 0`; FQCN inline en `AsistenteApplicationService::matching()`.
- No cambiado: `COLUMNAS = []` en `PgAsistenteRepository`. Es el punto de extensión documentado del patrón (los repositorios cuyas columnas difieran lo rellenan); quitarlo obligaría a reintroducirlo en la fase 4.
- Pendiente (fase 4): siguen llamando a `getAsistentes(array)`/`getConjunt` `PosiblesAsignaturasCaData`, `PeticionesIncorporar`, `Trasladar` y `AsistenteActividadService::getActividadesDeAsistente()`; migran con sus módulos.
- Lección para `ejecutar_plan_ia.php`: la nota de un paso puede afirmar tests inexistentes y el script solo comprobaba commit, árbol limpio y casilla. Ahora comprueba además que los tests citados con backticks en la nota existen (resultado `INCONSISTENTE` si no).

### Fase 3 — Generador y freno

- [x] **3.1** `devel_codegen` genera `matching()` y el enum `<Entidad>Field` en repositorios nuevos. Decisiones del paso:
  - El generador es `frontend/devel_codegen/controller/factory.php` (no hay código en `src/devel_codegen`, solo config). La lógica pura va a `DevelCodegenSupport::criteriaCaseName()` y `criteriaFieldEnumSource()` para poder probarla; test: `tests/unit/frontend/devel_codegen/DevelCodegenSupportCriteriaTest.php` (4 tests, incluye `php -l` del enum generado).
  - Enum escrito en `src/<grupo>/domain/criteria/<Clase>Field.php` con todas las columnas salvo `id_schema` y los `bytea`.
  - Interfaz generada: `matching(Criteria)` y `get<Plural>(array, array)` marcado `@deprecated`.
  - `Pg<Clase>Repository` generado: usa `RunsCriteriaQueries`, `COLUMNAS = []`, `matching()` con la hidratación de fechas/json/arrays de siempre y `get<Plural>()` como envoltorio de `LegacyWhereAdapter`. Se quita el bucle con `Condicion`.
  - No se ha podido ejecutar el generador (es una pantalla web que escribe ficheros y toca `dependencies.php`); se comprueba `php -l` del controlador y la salida del enum. Los repositorios ya existentes no se regeneran.
  - Suites verdes: unit de `frontend/devel_codegen`, `shared` y `devel`.
- [x] **3.2** Aviso en CI/PHPStan para usos nuevos de `$aWhere`/`$aOperators`. Decisiones del paso:
  - Se elige el `grep` en CI (opción de la sección 5) en vez de una regla PHPStan: `tools/qa/check_criteria_legacy.php` cuenta por fichero `$aWhere`/`$aOperators`/`$aOperador(es)` en `src/` y `frontend/` y compara con `tools/qa/criteria_legacy_baseline.json` (477 ficheros hoy).
  - Avisa si un fichero sube su contador o aparece uno nuevo; `--strict` sale con 1 (CI, `composer qa:criteria-legacy`), `--update` regenera la línea base tras migrar un módulo (en la fase 4 debe ir bajando).
  - No hay carpeta de CI en el repo, así que no se cablea a ningún workflow. Sin test PHPUnit propio (herramienta CLI en `tools/`); comprobado a mano que con la línea base actual sale OK. No se toca código de `src/`: no se ejecutan suites.

### Fase 4 — Resto de módulos

Un bloque por módulo, siguiendo el inventario de la fase 0 (de menor a mayor). Para cada uno: repositorios → llamantes → tests.

Orden por nº de llamadas (src, sin tests). Cada bloque: repositorios → llamantes → tests. Se marca al terminar.

- [x] **4.1 Triviales (≤ 5 llamadas):** troceado en un commit por módulo (cada casilla se marca al terminar). Patrón repetido en los 11 módulos: enum `<Entidad>Field` en `domain/criteria/`, `matching()` en la interfaz y el repo (trait `RunsCriteriaQueries`), `getXxx(array, array)` `@deprecated` como envoltorio de `LegacyWhereAdapter`, llamantes del propio módulo migrados, y los de otros módulos (y los que reciben arrays construidos fuera) se quedan por el envoltorio hasta su módulo. Los repos sin conexión de selección (`tablonanuncios`, `dossiers`, `certificados`) la reciben en el constructor. `tools/qa/criteria_legacy_baseline.json` baja de 477 a 467 ficheros.
  - [x] **4.1.utils_database** Decisiones:
    - `DbSchemaField` (`schema`, `id`) y `MapIdField` (`objeto`, `id_resto`, `id_dl`) en `src/utils_database/domain/criteria/`; `COLUMNAS = []`. `matching()` en ambas interfaces y repos (con `RunsCriteriaQueries`); `getDbSchemas`/`getMapIdes` quedan `@deprecated` como envoltorios de `LegacyWhereAdapter`.
    - Migrada la única llamada interna (`PgDbSchemaRepository::getNext`). Los llamantes de otros módulos (`notas`, `actividadestudios`, `shared`, `devel_db_admin`) siguen con arrays por el envoltorio y migran con sus módulos (sus tests unitarios mockean `getDbSchemas`).
    - Test: `PgUtilsDatabaseMatchingTest` (3 tests, incl. equivalencia con el método legacy). Suites verdes: unit/integration de `utils_database`, unit de `notas`, `shared`, `devel_db_admin`, unit/integration de `actividadestudios`. `tests/integration/notas` aborta por entorno (logger `log/errores.log` y memoria en `PgActaRepository`), ajeno al cambio.
  - [x] **4.1.tablonanuncios** Decisiones:
    - `AnuncioField` (10 campos de la entidad), `COLUMNAS = []`; `matching()` en interfaz y repo, `getAnuncios` `@deprecated` como envoltorio. El repo solo tenía `oDbl`: se añade `setoDbl_select(oDBPC_Select)` porque `RunsCriteriaQueries` lee de la conexión de selección.
    - Única llamada migrada: `TablonAnunciosParaGM::getArray` (`tablon ~*` → `matchesInsensitive`, `t_eliminado IS NULL` → `isNull`; el `'x'` del legacy era valor ficticio).
    - Test: `PgAnuncioMatchingTest` (2 tests, incl. equivalencia con `getAnuncios`). Suites verdes: unit e integration de `tablonanuncios`.
  - [x] **4.1.dossiers** Decisiones:
    - `TipoDossierField` (13 campos; `class` → caso `Class_`, ya que `Class` es palabra reservada) y `DossierField` (7 campos) en `src/dossiers/domain/criteria/`; `COLUMNAS = []`. `matching()` en ambas interfaces y repos; `getTiposDossiers`/`getDossieres` `@deprecated` como envoltorios. `PgDossierRepository` no tenía conexión de selección: se añade `setoDbl_select(oDB)`.
    - Migrados: `PermDossiersListaData`, `DossiersListaFichasData` y `PgTipoDossierRepository::findByCodigo`. `getDossieres` no tenía llamantes.
    - `PermDossierVerFormDataTest` tiene un repo anónimo que implementa la interfaz: se le añade `matching()`.
    - Test: `PgDossiersMatchingTest` (3 tests, incl. equivalencia con legacy). Suites verdes: unit e integration de `dossiers`.
  - [x] **4.1.permisos** Sin cambios de código: el módulo no tiene repositorios ni interfaces con `$aWhere`. Su única llamada (`PermisosActividades:141`, `getUsuariosGrupos(['id_usuario' => …])`) es a un repositorio de `usuarios`, así que migra con `usuarios` (4.3). No se ejecutan suites.
  - [x] **4.1.actividadcargos** Decisiones:
    - `CargoField` (6 campos) y `ActividadCargoField` (7) en `src/actividadcargos/domain/criteria/`; `COLUMNAS = []`. `matching()` en ambas interfaces y repos; `getCargos`/`getActividadCargos` `@deprecated` como envoltorios.
    - Migrados: `FormCargosDeActividadData`, `Select_cargos_de_actividad`, `InfoCargo::getColeccion` (`sin_acentos` → `matchesUnaccented`; `_ordre` → `orderBy`) y las dos llamadas internas por `id_nom` de `PgActividadCargoDlRepository`.
    - Se queda con arrays: `getActividadCargosDeAsistente($aWhereNom, …)` (recibe arrays de `asistentes`/`actividades`; migra con la fase 4 de esos módulos junto a `getActividadesDeAsistente`). Los llamantes de otros módulos (`actividadestudios`, `actividadessacd`, `asistentes`) siguen por el envoltorio; sus tests mockean `getActividadCargos`.
    - Test: `PgActividadCargosMatchingTest` (3 tests, equivalencia con legacy). Suites verdes: unit/integration de `actividadcargos` y `actividadessacd`, unit de `actividadestudios` y `asistentes`.
  - [x] **4.1.actividadtarifas** Decisiones:
    - `TipoTarifaField` (5) y `RelacionTarifaTipoActividadField` (4); `COLUMNAS = []`. `matching()` en interfaces y repos; `getTipoTarifas`/`getTipoActivTarifas` `@deprecated` como envoltorios (el `Set` del código antiguo era una lista simple).
    - Migrado `TipoTarifaListaData` (`_ordre => 'sfsv,letra'` → `orderBy` + `addOrderBy`); su test unitario mockea ahora `matching`. `CalendarioUbiResumenData` (módulo `casas`) sigue por el envoltorio y migra en 4.2. `getTipoActivTarifas` no tiene llamantes fuera del repo.
    - Test: `PgActividadTarifasMatchingTest` (3 tests, equivalencia con legacy). Suites verdes: unit/integration de `actividadtarifas` y `casas`.
  - [x] **4.1.ubiscamas** Decisiones:
    - `HabitacionField` (12 campos; se omite `tipoLavabo`, cuya columna no se escribe como la propiedad y nadie filtra por ella) y `CamaField` (6) en `src/ubiscamas/domain/criteria/`; `COLUMNAS = []`. `matching()` en interfaces y repos (ambos pasan a leer de la conexión de selección ya configurada en el constructor); `getHabitaciones`/`getCamas` `@deprecated` como envoltorios.
    - Migrados: `HabitacionFormData` (`orden DESC` + `limit 1`), `HabitacionesCamaLista` (el `IN` ya no construye un string `'a','b'`: `Filter::in` con los ids), `Select_habitaciones_cdc` y `getHabitacionesByUbi`/`getCamasByHabitacion`.
    - Cambio de comportamiento menor: `getHabitacionesByUbi` pasaba `['_ordre' => 'orden']` como *operadores* (se ignoraba, sin orden); ahora ordena de verdad por `orden`, que era la intención.
    - Tests unitarios (`SelectHabitacionesCdcTest`, `HabitacionFormDataTest`, `HabitacionesCamaListaTest`) mockean ahora `matching` (con `equalTo(Criteria)` donde había `with(...)`). Test nuevo: `PgUbiscamasMatchingTest` (3 tests, equivalencia con legacy incl. `IN`). Suites: unit de `ubiscamas` verde; integration con los mismos 4 errores previos (`PgCamaDl`/`PgHabitacionDl`), los 3 nuevos pasan.
  - [x] **4.1.actividadescentro** Decisiones:
    - `CentroEncargadoField` (4 campos); `COLUMNAS = []`. `matching()` en `CentroEncargadoRepositoryInterface` y `PgCentroEncargadoRepository`; `getCentrosEncargados` `@deprecated` como envoltorio.
    - Migrados: `CentroEncargadoAsignar` (`num_orden DESC`), `CentroEncargadoReordenar` e `Info3010::getColeccion`. Sus tests unitarios mockean ahora `matching`.
    - Se quedan con arrays por pertenecer a otros módulos: `CentrosDisponiblesData` (`CentroDl`/`CentroEllas::getCentros`, con `~` y `&` → 4.4 `ubis`) y `ListaActividadesCtrData` (`getActividades` → 4.3 `actividades`). Los llamantes de `getCentrosEncargados` en `actividades`, `asistentes`, `actividadessacd` y `pasarela` siguen por el envoltorio.
    - Test: `PgCentroEncargadoMatchingTest` (2 tests, equivalencia con legacy). Suites verdes: unit/integration de `actividadescentro`; unit de `cambios`, `actividades`, `actividadessacd`, `pasarela`, `asistentes`.
  - [x] **4.1.asignaturas** Decisiones:
    - Cuatro enums en `src/asignaturas/domain/criteria/`: `AsignaturaField` (valor = columna, p. ej. `nombre_asignatura`, porque la propiedad privada de la entidad se llama `nombre_signatura`; `plan_estudios` se filtra con `Filter::arrayContains`, que sustituye el `IN_ARRAY` legacy), `AsignaturaTipoField` (sin `year`: su columna es `año`), `SectorField` y `DepartamentoField` con mapa campo → columna (`nombre_sector` → `sector`, `nombre_departamento` → `departamento`) en `COLUMNAS` de sus repos.
    - `matching()` en las 4 interfaces y repos; `getAsignaturas`/`getAsignaturaTipos`/`getSectores`/`getDepartamentos` `@deprecated` como envoltorios. `PgAsignaturaRepository::matching` conserva la hidratación con `normalizeRowFromDb` (array `plan_estudios`).
    - Migrados: las 5 clases `Info*` (`sin_acentos` → `matchesUnaccented`, `<`/`>` → `lt`/`gt`, `_ordre` → `orderBy`); `InfoAsignaturasTest` pasa a esperar `matching` con `equalTo(Criteria)`.
    - Se quedan con arrays: `getAsignaturasAsJson` y `getJsonAsignaturas` (consultas JSON específicas, no son `getXxx` genéricos), y `PlanEstudiosFilter` (devuelve el par `[$where, $operators]` que consumen ~10 llamantes de `notas`, `actividadestudios` y `profesores`; migra con ellos).
    - Test: `PgAsignaturasMatchingTest` (5 tests, equivalencia con legacy, incl. `arrayContains` y las columnas remapeadas). Suites verdes: unit e integration de `asignaturas`; unit de `profesores`, `notas`, `actividadestudios`; integration de `profesores`, `actividadestudios`.
  - [x] **4.1.certificados** Decisiones:
    - `CertificadoEmitidoField` y `CertificadoRecibidoField` (10 campos cada uno; sin `documento`, que es bytea); `COLUMNAS = []`. Los repos solo tenían `oDbl`: se añade `setoDbl_select(oDB)` (`ConnectionRepositoryFactory`, usada por `Trasladar`, ya fija ambas conexiones). `matching()` conserva la hidratación (bytea + fechas con `ConverterDate`); `getCertificados` `@deprecated` como envoltorio en ambos.
    - Migrados: `CertificadoEmitidoSelect` (`~` → `Filter::matches`, `BETWEEN`, `IS NULL`, doble orden `DESC`) y `Select_certificados_de_una_persona`. `Trasladar` (módulo `personas`) sigue por el envoltorio.
    - Tests: `PgCertificadosMatchingTest` (recibidos) y `PgCertificadoEmitidoMatchingTest` (emitidos; necesita la conexión de región `H-Hv` como `PgCertificadoEmitidoRepositoryTest`), con equivalencia con legacy. Suites verdes: unit de `certificados`, `personas`, `dossiers`; integration de `PgCertificadoRecibidoRepositoryTest`. Previo, no relacionado: `PgCertificadoEmitidoRepositoryTest::test_get_certificados_sin_filtros` agota la memoria (carga todos los PDF) con y sin este cambio, y aborta el resto de `tests/integration/certificados` si se ejecuta el directorio completo.
  - [x] **4.1.pasarela** Decisiones:
    - `PasarelaConfigField` (solo `nom_parametro`; `json_valor` es jsonb y no se filtra); `COLUMNAS = []`. `matching()` en interfaz y `PgPasarelaConfigRepository`, con la hidratación de `json_valor` por `ConverterJson`; `getPasarelaConfigs` `@deprecated` como envoltorio (sin llamantes fuera del repo).
    - Migrado `ExportarActividadesData` solo en su llamada a `getCentrosEncargados` (`num_orden DESC` → `matching`). Quedan con arrays por ser de otros módulos: `getActividades` (`actividades`, 4.3) en `ExportarActividadesData` y `getTiposDeActividades` en `Conversiones`.
    - Se crean `tests/factories/pasarela/PasarelaConfigFactory.php` y `PgPasarelaConfigMatchingTest` (1 test, equivalencia con legacy y JSON decodificado). Suites verdes: unit e integration de `pasarela`; unit de `actividadescentro`.
- [x] **4.2 Pequeños (6–16):** troceado en un commit por módulo (cada casilla se marca al terminar). Mismo patrón que 4.1: enum `<Entidad>Field` en `domain/criteria/`, `matching()` en interfaz y repo (`RunsCriteriaQueries`; los repos que solo tenían `oDbl` fijan también la de selección), `getXxx(array, array)` `@deprecated` como envoltorio y llamantes del propio módulo migrados; los de otros módulos siguen por el envoltorio. `tools/qa/criteria_legacy_baseline.json` baja de 467 a 454 ficheros en el bloque (17 repositorios migrados en 6 módulos; `devel_db_admin` y `planning` no tienen repositorios con `$aWhere`).
  - [x] **4.2.devel_db_admin** Decisiones:
    - Módulo sin repositorios propios con `$aWhere`. Migrada `MigracionesEjecutar` (`getDbSchemas(['_ordre' => 'schema'])` → `matching(orderBy(DbSchemaField::Schema))`). `DbLugarDropdown` llama a `getDelegaciones` (módulo `ubis`, 4.4) y sigue por el envoltorio.
    - Suites verdes: unit e integration de `devel_db_admin`.
  - [x] **4.2.shared** Decisiones:
    - `ColaMailField` (7 campos) en `src/shared/domain/criteria/`; `COLUMNAS = []`. `matching()` en `ColaMailRepositoryInterface` y `PgColaMailRepository` (que pasa a fijar también la conexión de selección, la misma `oDBPC`); `getColaMails` `@deprecated` como envoltorio. Se corrige además el tipo de retorno a `list<ColaMail>` y se elimina el bucle `Set` manual.
    - Migrada `EnviarMailsEnCola::seleccionar` (`sended IS NULL` + `_limit` → `isNull` + `limit`). El resto de ficheros de `shared` con `aWhere` son el propio motor (`LegacyWhereAdapter`, `Condicion`, `ClaseRepository`, `DBTrasvase`), que se retira en la fase 5.
    - Test: `PgColaMailMatchingTest` (equivalencia con legacy, `IN` + `IS NULL`, `limit`). Suites verdes: unit e integration de `shared`; unit de `cambios` y `actividadessacd`. Línea base: 466 ficheros.
  - [x] **4.2.cartaspresentacion** Decisiones:
    - `CartaPresentacionField` (7 campos); `COLUMNAS = []`. `matching()` en interfaz y `PgCartaPresentacionRepository`; `getCartasPresentacion` `@deprecated` como envoltorio. Las subclases `Dl` y `Ex` fijan también su conexión de selección (`oDB`, `oDBR`).
    - Migrados `CartasPresentacionListaData` (3 llamadas; `sin_acentos` → `matchesUnaccented`) y `CartaPresentacionUpdate::sanear`. Siguen con arrays `getCentros`/`getDirecciones` (módulo `ubis`, 4.4).
    - Test: `test_matching_por_ubi_equivale_a_legacy` en `PgCartaPresentacionRepositoryTest`. Suites verdes: unit e integration de `cartaspresentacion`.
  - [x] **4.2.menus** Decisiones:
    - Cinco enums en `src/menus/domain/criteria/` (`MenuDbField`, `GrupMenuField`, `GrupMenuRoleField`, `MetaMenuField`, `TemplateMenuField`); `COLUMNAS = []` en todos. `matching()` en las 5 interfaces y repos; `getMenuDbs`/`getGrupMenus`/`getGrupMenuRoles`/`getMetaMenus`/`getTemplatesMenus` `@deprecated` como envoltorios. `PgTemplateMenuRepository` solo tenía `oDbl`: ahora fija también la de selección. `MenuDb::orden` conserva su hidratación del array PG.
    - Migrados: los 3 casos de uso de layout, `MenusGetPageData`, `GrupMenuColeccionUseCase`, `GrupMenuListaUseCase`, `InfoGrupMenus`, `InfoMetaMenus` y `menus_generar_txt`. El regex `~ '^1$|^N$'` de `MenusLegacyLayoutItemsUseCase` pasa a `Filter::in(IdGrupmenu, [1, N])` (mismas filas, `id_grupmenu` es entero).
    - Cambio de comportamiento menor: `InfoGrupMenus` buscaba por una columna `descripcion` que `aux_grupmenu` no tiene (la consulta fallaba y devolvía vacío); ahora busca por `grup_menu`, que era la intención.
    - Siguen por el envoltorio los llamantes de `usuarios` (`rolesLista`, `role_grupmenu_*`, `usuario_preferencias`, `role_info`) y sus mocks, que migran en 4.3.
    - Tests: `PgMenusMatchingTest` (3 tests, equivalencia con legacy) y los unitarios de `menus` mockean ahora `matching`. Suites verdes: unit e integration de `menus`; unit de `usuarios`.
  - [x] **4.2.configuracion** Decisiones:
    - `AppField`, `ConfigSchemaField`, `ModuloField` y `ModuloInstaladoField` en `src/configuracion/domain/criteria/`; `COLUMNAS = []`. `matching()` en las 4 interfaces y repos; `getApps`/`getConfigSchemas`/`getModulos`/`getModuloInstalados` `@deprecated` como envoltorios. `PgModuloRepository` solo tenía `oDbl`: fija también la conexión de selección. `matching()` de módulos conserva la hidratación de `mods_req`/`apps_req` (arrays enteros PG).
    - Migrados: `ModulosSelectData`, `ModulosConfig`, `InfoApps`, `InfoModsInstalled` y `ApptablesAppsData` (de `devel_db_admin`). `getConfigSchemas` no tiene llamantes fuera del repo. `ModulosConfigTest` mockea ahora `matching`.
    - Test: `PgConfiguracionMatchingTest` (3 tests, equivalencia con legacy). Suites verdes: unit e integration de `configuracion` y `devel_db_admin`; unit de `usuarios`.
  - [x] **4.2.casas** Decisiones:
    - `GrupoCasaField`, `IngresoField` y `UbiGastoField` en `src/casas/domain/criteria/`; `COLUMNAS = []`. `matching()` en las 3 interfaces y repos (ya tenían conexión de selección); `getGrupoCasas`/`getIngresos`/`getUbisGastos` `@deprecated` como envoltorios. `UbiGasto::matching` conserva `f_gasto` con `ConverterDate('date')`.
    - Migrados: `CasasResumenData` (grupos padre/hijo), `GrupoCasaListaData`, `CasaEcGastosFormData` (el `BETWEEN` con strings `'Y/1/1','Y/12/31'` pasa a `Filter::between` con fechas ISO) y `CalendarioUbiResumenData::getTipoTarifas` (pendiente de 4.1 `actividadtarifas`).
    - Siguen con arrays por ser de otros módulos: `getActividades` (`actividades`, 4.3), `getCasas`/`getCentros` (`ubis`, 4.4) y `getTarifaUbis` (`ubis`, 4.4).
    - Test: `PgCasasMatchingTest` (2 tests, incl. `BETWEEN` de fechas y equivalencia con legacy); `GrupoCasaListaDataTest` y `CasaEcGastosFormDataTest` mockean `matching`. Suites verdes: unit e integration de `casas`; unit de `actividadtarifas` y `ubis`.
  - [x] **4.2.zonassacd** Decisiones:
    - `ZonaField`, `ZonaGrupoField` y `ZonaSacdField` en `src/zonassacd/domain/criteria/`; `COLUMNAS = []`. `matching()` en las 3 interfaces y repos (ya tenían conexión de selección); `getZonas`/`getZonasGrupo`/`getZonasSacds` `@deprecated` como envoltorios.
    - Migrados: `ZonaSacdUpdate` (helper privado `buscarZonaSacd`), `ZonaSacdLista`, `ZonaSacdListaTot`, `InfoZona` e `InfoZonaGrupo`. Cambio de comportamiento menor: `InfoZona` buscaba por una columna `nom` que `zonas` no tiene (la consulta fallaba); ahora busca por `nombre_zona`, que era la intención.
    - Siguen por el envoltorio los llamantes de otros módulos (`misas`, `planning`, `encargossacd`, `usuarios`, `cambios`) y sus mocks, que migran con sus módulos. `ZonaCtrLista` recibe el par `[$where, $operators]` de un filtro de `ubis` y se queda con arrays hasta 4.4.
    - Test: `PgZonassacdMatchingTest` (equivalencia con legacy); los unitarios de `zonassacd` mockean ahora `matching`. Suites verdes: unit e integration de `zonassacd`; unit de `misas`, `planning`, `encargossacd` y `usuarios`.
  - [x] **4.2.planning** Decisiones:
    - Módulo sin repositorios propios: solo migra las dos llamadas a `getZonasSacds` de `ActividadesPorZonasService` (`id_zona` y `propia = 't'` → `Filter::eq(..., true)`), ya disponibles tras `zonassacd`. `ActividadesPorZonasServiceTest` mockea ahora `matching`.
    - Siguen por el envoltorio las llamadas a repositorios de otros módulos: `getActividades`/`getUbis` (`actividades`, 4.3), `getCentros`/`getCasas` (`ubis`, 4.4), `getPersonas` (`personas`, 4.3) y `getEncargoSacdHorarios` (`encargossacd`, 4.5).
    - Suites verdes: unit de `planning` y `zonassacd`; `planning` no tiene tests de integración (sin repositorios).
- [x] **4.3 Medianos (20–34):** troceado en un commit por módulo (cada casilla se marca al terminar). Mismo patrón que 4.1/4.2.
  - [x] **4.3.dbextern** Decisiones:
    - `IdMatchPersonaField` (`id_listas`, `id_orbix`, `id_tabla`) en `src/dbextern/domain/criteria/`; `COLUMNAS = []`. `matching()` en `IdMatchPersonaRepositoryInterface` y `PgIdMatchPersonaRepository`; `getIdMatchPersonas` `@deprecated` como envoltorio.
    - Migrados los 10 llamantes del módulo (`SincroDB`, `VincularIdMatch` —`primerMatch` recibe ahora un `Criteria`— y 7 casos de uso/data). `VincularIdMatchTest` compara el `Criteria` recibido con `==`.
    - Se quedan fuera: `PgPersonaBDURepository::getIdMatchPersonas` (tabla temporal `tmp_bdu`, sin llamantes) y `OdbcDlListasRepository::getDlListas` (acceso ODBC a una BD externa, sin `RunsCriteriaQueries`; su única llamada no pasa filtros). `SincroDB` sigue llamando a `getPersonas` de `personas` con arrays (4.3.personas).
    - Test: `PgIdMatchPersonaMatchingTest` (2 tests, equivalencia con legacy). Suites verdes: unit e integration de `dbextern`.
  - [x] **4.3.cambios** (`~INV`) Decisiones:
    - Cinco enums en `src/cambios/domain/criteria/` (`CambioField` sin los jsonb `json_fases_*`, `CambioUsuarioField`, `CambioAnotadoField`, `CambioUsuarioObjetoPrefField`, `CambioUsuarioPropiedadPrefField`); `COLUMNAS = []`. `matching()` en las 5 interfaces y repos (`PgCambioDlRepository` hereda el de `PgCambioRepository`, que conserva `hydrateCambioRowFromPg`); `getCambios`/`getCambiosUsuario`/`getCambiosAnotados`/`getCambioUsuarioObjetoPrefs`/`getCambioUsuarioPropiedadPrefs` `@deprecated` como envoltorios.
    - `~INV` (patrón en la columna): método con nombre `CambioUsuarioObjetoPrefRepository::findPorTipoActividad(objeto, dlOrg, idTipoActiv)` con su propio SQL (`:id::text ~ id_tipo_activ_txt`), usado por `AvisosGenerarTabla`. `'avisado' => 'false'` pasa a `Filter::eq(Avisado, false)`.
    - Migrados todos los llamantes de `src/cambios/application/` (incl. `legacy/Avisos`). Fuera del módulo no había llamantes. Tests unitarios (`AvisosEncolarMails`, `CambioParaAvisoLookup`, `CambioUsuarioEliminar`, `UsuarioFormAvisosData`, `AvisosGenerarListaData`) mockean `matching`; los de integración que usan `getXxx` quedan como caracterización sin cambios.
    - Test: `PgCambiosMatchingTest` (5 tests, equivalencia con legacy incl. `findPorTipoActividad` frente a `~INV`). Suites verdes: unit e integration de `cambios` (72 tests; con `stopOnFailure` el directorio aborta de forma intermitente por la inestabilidad conocida, pasa completo sin él).
  - [x] **4.3.actividades** Decisiones:
    - Cuatro enums en `src/actividades/domain/criteria/` (`ActividadAllField`, `ImportadaField`, `RepeticionField`, `TipoDeActividadField`); `COLUMNAS = []`. `matching()` en `ActividadAllRepositoryInterface` (lo heredan `Dl`/`Ex`/`Pub`/`Actividad`), `ImportadaRepositoryInterface`, `RepeticionRepositoryInterface` y `TipoDeActividadRepositoryInterface`; `getActividades`/`getImportadas`/`getRepeticiones`/`getTiposDeActividades` `@deprecated` como envoltorios.
    - `PgActividadAllRepository::matching` no usa `rowsMatching`: lee de `getNomTablaSelect()` (la vista `av_actividades` en `PgActividadRepository`) y conserva la hidratación de `f_ini`/`h_ini`/`f_fin`/`h_fin` con `ConverterDate`. `getUbis` y `getArrayIdsWithKeyFini` (proyecciones, no devuelven entidades) se quedan con arrays.
    - Migrados todos los llamantes del módulo (`CalendarioListasDatos`, `ListaSrCsvListado`, `ListaActivTabla`, `ListaActividadesSgListado`, `ActividadSelectListado`, `ActividadNuevoCurso`/`Ejecutar`, `TipoActivLista`, `TipoActivMetadata`, `InfoTipoRepeticion`), más dos de `actividadtarifas` (`ActividadTipoGetIdTarifa`, `ActividadVerDatos`) y los dos `getTiposDeActividades` de `cambios`. `f_ini, h_ini NULLS LAST` → `NullsPosition::Last`; `ANY '{..}'` → `Filter::in`; `publicado 't'/'f'` → booleano.
    - Cambio de comportamiento menor: `ActividadSelectListado` filtraba `nom_activ ILIKE '%texto%'`; ahora `matchesInsensitive` con el texto escapado (subcadena sin distinguir mayúsculas; un `%`/`_` tecleado ya no actúa de comodín).
    - Siguen por el envoltorio los llamantes de otros módulos (`actividadplazas`, `asistentes`, `casas`, `misas`, `notas`, `pasarela`, `planning`, `personas`, `procesos`, `inventario`, `shared/DBTrasvase`…). `ActividadTipoIdTxt::applyToRepositoryWhere` (devuelve `[$aWhere,$aOperador]`) lo usan `usuarios` y `procesos`: migra con ellos.
    - Tests: `PgActividadesMatchingTest` (3 tests, equivalencia con legacy incl. `NULLS LAST`, regex de tipo y vista `av_actividades`); los unitarios (`ListaActivTablaApplication`, `TipoActivMetadata`, `ActividadTipoGetIdTarifa`, `TiposActividades` —repo anónimo—, `CalendarioListasDatosDmz`, `AvisosGenerarTabla*`) mockean `matching`. Suites verdes: unit e integration de `actividades`; integration de `asistentes`, `casas`, `pasarela`, `actividadcargos`, `procesos`, `actividadessacd`, `misas`, `actividadescentro`. `cambios` integration sigue con su inestabilidad previa (varios tests de `AvisosGenerarTablaIntegracionTest` fallan de forma distinta en cada ejecución, también sin este cambio).
  - [x] **4.3.procesos** Decisiones:
    - Seis enums en `src/procesos/domain/criteria/` (`PermUsuarioActividadField`, `ProcesoTipoField`, `ActividadProcesoTareaField`, `ActividadTareaField`, `ActividadFaseField`, `TareaProcesoField` —sin el jsonb `json_fases_previas`—); `COLUMNAS = []`. `matching()` en las 6 interfaces y repos; `getPermUsuarioActividades`/`getProcesoTipos`/`getActividadProcesoTareas`/`getActividadTareas`/`getActividadFases`/`getTareasProceso` `@deprecated` como envoltorios. `PgTareaProcesoRepository::matching` conserva `json_fases_previas` con `ConverterJson` (`hidratarFila`).
    - Migrados todos los llamantes de `src/procesos/` (≈20, incl. `ProcesoActividadService`, `FasesActivCambioLista` —con su `getActividades`—, `Info*`) y los de `cambios` (`CambioAvisoTxtBuilder`, `AvisosGenerarTabla`). Cambio menor en `InfoFases`/`InfoProcesoTipo`/`InfoTareas`: filtraban por una columna `nom` que esas tablas no tienen (la consulta fallaba); ahora buscan por `desc_fase`/`nom_proceso`/`desc_tarea`, que era la intención.
    - `ActividadTipoIdTxt::filterForRepository(campo, valor)` sustituye a `applyToRepositoryWhere` (queda `@deprecated`; lo siguen usando `perm_activ_guardar.php` y su test, que migran con `usuarios`).
    - Siguen por el envoltorio los 4 controladores de `usuarios` que llaman a `getPermUsuarioActividades` (`perm_activ_lista`, `perm_activ_guardar`, `usuario_form`) → 4.3.usuarios.
    - Tests: `PgProcesosMatchingTest` (5 tests, equivalencia con legacy, incl. JSON de fases previas y alias de tipo con `IN`), `ActividadTipoIdTxtTest::test_filter_for_repository…`; los unitarios de `procesos` y `cambios` mockean `matching`. Suites verdes: unit e integration de `procesos`; unit de `cambios`, `actividades`, `usuarios`, `pasarela`, `actividadessacd`; integration de `usuarios`, `actividades`, `pasarela`, `cambios/infrastructure`.
  - [x] **4.3.actividadessacd** Decisiones:
    - `ActividadSacdTextoField` (`id_item`, `idioma`, `clave`, `texto`) en `src/actividadessacd/domain/criteria/`; `COLUMNAS = []`. `matching()` en `ActividadSacdTextoRepositoryInterface` y `PgActividadSacdTextoRepository` (único repositorio del módulo); `getActividadSacdTextos` `@deprecated` como envoltorio.
    - Migrados los 3 llamantes de textos (`TextoComunicacionData`, `TextoComunicacionGuardar`, `ActividadesSacdHelper`) y, ya disponibles, las llamadas de otros módulos: `getActividades` de `ListaActividadesSacdData` y `SolapesSacdData` (`actividades`) y `getActividadCargos` de `SacdAsignarAuto` (`actividadcargos`; el `IN '1,2'` pasa a `Filter::in` con enteros).
    - Se quedan con arrays: `getArrayIdsWithKeyFini` (proyección de `actividades`), `getCargoDeActividad`/`getAsistenteCargoDeActividad` (métodos con `$aWhereAct` propios de `actividadcargos`, fase 4.1) y `getEncargoSacdHorarios` (`encargossacd`, 4.5) en `ComunicarActividadesSacdService`.
    - Tests: `PgActividadSacdTextoMatchingTest` (2 tests, equivalencia con legacy); `TextoComunicacionData`, `SacdAsignarAuto`, `SolapesSacdData` y `ListaActividadesSacdData` mockean `matching`. Suites verdes: unit e integration de `actividadessacd` y `actividadcargos`.
  - [x] **4.3.profesores** Decisiones:
    - Diez enums en `src/profesores/domain/criteria/` (`ProfesorStgrField`, `ProfesorDirectorField`, `ProfesorAmpliacionField`, `ProfesorCongresoField`, `ProfesorDocenciaStgrField`, `ProfesorJuramentoField`, `ProfesorLatinField`, `ProfesorPublicacionField`, `ProfesorTipoField`, `ProfesorTituloEstField`); `COLUMNAS = []`. `matching()` en las 10 interfaces y repos; los `getXxx` (`getProfesoresStgr`, `getProfesoresDirectores`, `getProfesorAmpliaciones`, `getProfesorCongresos`, `getProfesorDocenciasStgr`, `getProfesorJuramentos`, `getProfesoresLatin`, `getProfesorPublicaciones`, `getProfesorTipos`, `getProfesorTitulosEst`) quedan `@deprecated` como envoltorios. Los repos con columnas `date` hidratan con `ConverterDate` en `hidratarFila()`. Nueve repos solo tenían `oDbl` (`oDB`): fijan también la conexión de selección.
    - Migrados: las 10 clases `InfoProfesor*` (`sin_acentos` → `matchesUnaccented`, `_ordre … DESC` → `OrderDirection::Desc`), `FichaProfesorStgr` (un criterio por repositorio; `f_cese IS NULL` si `print`), `CongresosLista`, `DocenciaLista`, `ProfesoresAsignaturaLista`, `ProfesorStgrService`, `ProfesorAsignaturaService` y el `getProfesorTipos` de `ListaPorDepartamentos`.
    - **Pendiente de decisión (no migrado):** `ListaPorDepartamentos` sigue por el envoltorio para `getProfesoresDirectores`/`getProfesoresStgr`, porque filtra por `id_dl` (`IN`) y ordena por `id_dl`, columna que no existe en `d_profesor_stgr` ni `d_profesor_director` (con un `dl` marcado la consulta falla y devuelve vacío). Hay que decidir por qué columna/relación se filtra por delegación antes de migrarlo.
    - Siguen por el envoltorio los llamantes de otros módulos: `personas/Trasladar`, `notas` (`NotaPersonaFormData`, `legacy/Resumen`) y `actividadestudios/DocenciaActualizar`.
    - Tests: `PgProfesoresMatchingTest` (4 tests, equivalencia con legacy en los 10 repos, incl. fechas); los unitarios de `CongresosLista`, `DocenciaLista`, `ProfesoresAsignaturaLista` y `ListaPorDepartamentos` mockean `matching`. Suites verdes: unit e integration de `profesores`; unit de `personas`, `notas`, `actividadestudios`; integration de `actividadestudios`, `asignaturas`.
  - [x] **4.3.personas** (`TXT`, `OR`) — troceado en dos commits por tamaño (8 repositorios de búsqueda + `PersonaDlRepositoryBase` con 6 subclases):
    - [x] **4.3.personas.catalogos** `Situacion`, `Traslado`, `TelecoPersona` y `UltimaAsistencia` (+ `InfoX` y `TelecoPersonaService`). Decisiones:
      - `SituacionField`, `TrasladoField`, `TelecoPersonaField` y `UltimaAsistenciaField` en `src/personas/domain/criteria/`; `COLUMNAS = []`. `matching()` en las 4 interfaces y repos; `getSituaciones`/`getTraslados`/`getTelecosPersona`/`getUltimasAsistencias` `@deprecated` como envoltorios. `Traslado`/`UltimaAsistencia` hidratan `f_traslado`/`f_ini` con `ConverterDate` (`hidratarFila`). Todos menos `Situacion` solo tenían `oDbl`: fijan también la de selección (incl. `TelecoPersonaDl`/`Ex`, que la sobrescriben).
      - Migrados: `InfoSituacion`/`InfoTraslado`/`InfoUltimaAsistencia`/`InfoTelecoPersona`, `TelecoPersonaService`, y en `Trasladar` las llamadas a teleco, traslados y a los 9 repositorios de `profesores` (la fábrica de conexiones fija ambas). También los llamantes de teleco de `profesores/ProfesoresAsignaturaLista` y `dbextern/SincroDB`.
      - Cambio menor: `InfoTelecoPersona` buscaba por una columna `congreso` que no existe (copiada de congresos); ahora busca por `num_teleco`, que era la intención.
      - Test: `PgPersonasCatalogosMatchingTest` (4 tests). La comparación de situaciones se limita a `A` porque otras filas de la base de pruebas tienen un nombre con `/` que el VO `SituacionName` rechaza (falla igual con el método legacy). Suites verdes: unit de `personas`, `dbextern`, `profesores`; integration de `profesores`, `dbextern`, `actividadestudios`, `TrasladoDlCopyPersonaTest` y los `Pg{Situacion,Traslado,UltimaAsistencia,TelecoPersona}RepositoryTest` (`tests/integration/personas` entero sigue abortando por el logger).
    - [x] **4.3.personas.personas** `PersonaDlRepositoryBase` (N, Agd, S, SSSC, Nax, Dl), `PersonaEx`, `PersonaPub` y `PersonaSacd` (+ llamantes del módulo). Decisiones:
      - Un único enum `PersonaField` (`src/personas/domain/criteria/`, sin el jsonb `publicado_para`) para los cuatro repositorios; `COLUMNAS = []`. `matching()` en `PersonaDlRepositoryInterface` (la heredan N, Agd, S, SSSC, Nax), `PersonaExRepositoryInterface`, `PersonaPubRepositoryInterface` y `PersonaSacdRepositoryInterface`; `getPersonas` `@deprecated` como envoltorio en los cuatro.
      - Trait nuevo `PersonaCriteriaTrait` (`filasPersonasMatching`): ejecuta el criterio con `getoDbl()` (estos repos no fijan conexión de selección y los antiguos `getPersonas` leían de `getoDbl()`; no se toca la conexión) e hidrata `f_nacimiento`/`f_situacion`/`f_inc` con `ConverterDate` y `publicado_para` con `PersonaPublicacion::hydrateRow` (no en `Ex`, como antes). `PersonaPub` aplica su filtro fijo «publicada para mi dl o para todas» envolviendo la tabla en un `SELECT … WHERE <filtro>`, y conserva el `RegionStgrConfigException` → `RuntimeException`.
      - Migrado `PersonaFinderService` (`findFirstPersonaDl`, `buscarEnTodasRegiones`). **No migrado:** `PersonasSelectData` comparte un único `$aWhere`/`$aOperador` (con `ANY`, `IN` y `sin_acentos`) entre `getPersonas` de N/S/SSSC/Nax/Agd y `PersonaPubRepository::getPersonasParaListado(array, array, …)`, método específico del listado público que no entra en esta migración; migra cuando `getPersonasParaListado` tenga su propia firma con `Criteria`. `getSacdsBySelect`/`getPersonasParaListado` y el resto de métodos específicos de los repos se quedan.
      - Siguen por el envoltorio los ~40 llamantes de otros módulos (`actividadessacd`, `actividadestudios`, `asistentes`, `dbextern`, `encargossacd`, `misas`, `notas`, `planning`, `ubis`, `zonassacd`, `profesores`) y sus mocks.
      - Test: `PgPersonasMatchingTest` (5 tests: N, Dl con `sin_acentos`+booleano, Ex, Pub con su filtro, Sacd; equivalencia con legacy). `PersonaFinderServiceLookupTest` mockea `matching`. Suites verdes: unit de `personas`; integration de los `PgPersona*RepositoryTest` salvo `PgPersonaNaxRepositoryTest::test_guardar_eliminar_persona_nax`, que falla igual sin este cambio; integration de `asistentes`, `misas`, `encargossacd`, `actividadessacd`.
  - [x] **4.3.usuarios** Decisiones:
    - Siete enums en `src/usuarios/domain/criteria/` (`UsuarioField`, `GrupoField`, `RoleField`, `LocalField`, `PermMenuField`, `PreferenciaField`, `UsuarioGrupoField`); `COLUMNAS = []`. `matching()` en las 7 interfaces y repos (con `RunsCriteriaQueries`); `getUsuarios`/`getGrupos`/`getRoles`/`getLocales`/`getPermMenus`/`getPreferencias`/`getUsuariosGrupos` quedan `@deprecated` como envoltorios. `PgUsuarioRepository::matching()` conserva la lectura del `password` bytea (resource → string).
    - Migrados: `GruposLista`, `rolesLista`, `usuariosLista`, `usuariosRegionContactos`, `GrupoJefeZona`, `InfoLocales`, los controladores `usuario_grupo_del`, `usuario_grupo_lst`, `usuario_grupo_del_lst`, `perm_menu_lista`, `usuario_info`, `borrar_pwd`, y los llamantes de otros módulos: `permisos/PermisosActividades`, `shared/HydrateMenuPermissions`, `misas` (`PlanDeMisasPantallaData`, `VerEncargosZonaData`), `encargossacd/EncargoSelectData` y `actividadessacd/ComunicarActividadesSacdService`. `id_pau IS NOT NULL` (columna `csv_id_pau` de la entidad) → `isNotNull(CsvIdPau)`. Queda por el envoltorio `tests/myTest.php` (helper de tests) y los tests de caracterización de los repos.
    - Test: `PgUsuariosMatchingTest` (4 tests, equivalencia con legacy en los 7 repos); los unitarios de `GruposLista`, `rolesLista`, `usuariosLista`, `PlanDeMisasPantallaData` y `EncargoSelectData` mockean `matching`. Suites verdes: unit e integration de `usuarios`; unit de `misas`, `encargossacd`, `permisos`, `shared`, `actividadessacd`, `menus`; integration de `misas`, `encargossacd`, `actividadessacd`.
- [ ] **4.4 Grandes (39–60):** troceado en un commit por módulo (cada casilla se marca al terminar).
  - [x] **4.4.actividadplazas** Decisiones:
    - `ActividadPlazasField` (5) y `PlazaPeticionField` (4) en `src/actividadplazas/domain/criteria/`; `COLUMNAS = []`. `matching()` en las interfaces y repos (`PgActividadPlazasDlRepository` hereda); `getActividadesPlazas`/`getPlazasPeticion` `@deprecated` como envoltorios. Los repos solo tenían conexión `oDbl` (los `getXxx` antiguos leían de ella): se fija también como conexión de selección en los constructores. `matching()` de plazas conserva la hidratación de `cedidas` con `ConverterJson`.
    - Migrados todos los llamantes del módulo (`PeticionesGuardar`, `PeticionesEliminar`, `PeticionesActivData`, `PeticionesIncorporar` con `~` → `Filter::matches`, `PlazasBalanceData`, `GestionPlazasData`, `PlazasDlEdicion`, `ResumenPlazasService`). Siguen por el envoltorio `actividades/ActividadNueva`, `asistentes/AsistenteMoverData` y `TablaPeticionesData` (otros módulos).
    - Test: `PgActividadPlazasMatchingTest` (2 tests, equivalencia con legacy). Los unitarios de `PeticionesGuardar`, `PeticionesEliminar`, `ResumenPlazasService` y `PlazasCeder` mockean `matching`. Suites verdes: unit/integration de `actividadplazas`, unit de `asistentes` y `actividades`, integration de `asistentes` y `actividades`.
  - [x] **4.4.inventario** Decisiones:
    - Ocho enums en `src/inventario/domain/criteria/` (`ColeccionField`, `DocumentoField`, `EgmField`, `EquipajeField`, `LugarField`, `TipoDocField`, `UbiInventarioField`, `WhereisField`; valor = propiedad de la entidad, `COLUMNAS = []`). `matching()` en las 8 interfaces y repos; `getColecciones`/`getDocumentos`/`getEgmes`/`getEquipajes`/`getLugares`/`getTipoDocs`/`getUbisInventario`/`getWhereare` `@deprecated` como envoltorios. Los repos solo tenían `oDbl` (los `getXxx` antiguos leían de ella): se fija también como conexión de selección. `matching()` de `Documento` y `Equipaje` conserva la hidratación de fechas con `ConverterDate('date')`.
    - Migrados todos los llamantes (no hay de otros módulos): `Info{TipoDoc,UbiInventario,Lugar,Coleccion}` (`sin_acentos` → `matchesUnaccented`), `InfoDocsxCtr` (el `IN` ya no construye un string `"1,2"`: `Filter::in` con la lista de ids; `_limit` → `limit(6)`), `InfoDocsxSigla`, `ListaDocsGrupo` y los ~25 controladores de `infrastructure/ui/http/controllers`. Los booleanos `'t'`/`'f'` pasan a `true`/`false`; `IS NOT NULL` → `isNotNull`; `_ordre 'num_reg DESC'` → `orderBy(NumReg, Desc)`.
    - Los `setMetodoGestor('getLugares'…)` de los `Info*` son cadenas de metadatos, no llamadas: se dejan.
    - Test: `PgInventarioMatchingTest` (5 tests, equivalencia con legacy en los 8 repos incl. `IN`, `IS NULL` y fechas). Suites verdes: unit e integration de `inventario`.
  - [x] **4.4.actividadestudios** Decisiones:
    - `MatriculaField` (12) y `ActividadAsignaturaField` (8) en `src/actividadestudios/domain/criteria/`; `COLUMNAS = []`. `matching()` en las 2 interfaces y repos; `getMatriculas`/`getActividadAsignaturas` `@deprecated` como envoltorios. Como en `actividadplazas`/`inventario`, los repos solo tenían `oDbl` y los `Dl` lo sustituyen en su constructor: la conexión de selección se fija en los cuatro. `PgMatriculaRepository::matching()` conserva el descarte (y borrado) de filas inválidas vía `matriculaFromRow`; `PgActividadAsignaturaRepository::matching()` hidrata `f_ini`/`f_fin` con `ConverterDate('date')`. `getAsignaturasCa` usa `matching` (`tipo` vacío → `isNull`).
    - Migrados los llamantes del módulo (`FormMatriculasDeUnaPersonaData`, `Select_{asignaturas_de_una_actividad,matriculas_de_una_persona,matriculas_de_una_actividad}`, `ActaNotasData`, `ActaNotasDefinitivasGrabar`, `ActividadAsignatura{Eliminar,Nueva}`, `Matricula{Nueva,Eliminar,Automatica}`, `DocenciaActualizar`, `PlanEstudiosCaData`, `ListaClasesCaData`, `E43CertificadoData`, `MatriculasListaData` con `ANY('{…}')` → `Filter::in` con la lista de ids). `MatriculaAutomatica` pasaba `tipo => 'x'` con `IS NULL`: ahora `isNull(Tipo)`.
    - **No migrado** (otros módulos, por el envoltorio): `personas/Trasladar`, `asistentes` (`ListaEstCtrData`, `AsistenteEliminar`), `notas/ActaVerFormData`. Siguen con arrays las llamadas a repos ya migrados de otros módulos que reciben el par `[$where, $operators]` de `PlanEstudiosFilter` (`getAsignaturas`) y `getPersonaNotas`, que migran con `notas`.
    - Test: `PgActividadEstudiosMatchingTest` (3 tests, equivalencia con legacy incl. `IN`/`ANY` e `IS NULL`). Los unitarios de `ActaNotasData`, `ActividadAsignatura{Eliminar,Nueva}`, `Matricula{Nueva,Eliminar,Automatica}`, `FormMatriculasDeUnaPersonaData` y `SelectAsignaturasDeUnaActividadSel` mockean `matching` (con `equalTo(Criteria)` donde había `with(...)`). Suites verdes: unit/integration de `actividadestudios`; unit de `asistentes`, `personas`, `notas`, `profesores`; integration de `asistentes`.
  - [x] **4.4.notas** Decisiones:
    - Cuatro enums en `src/notas/domain/criteria/`: `ActaField` (10 + `IdSchema`, columna de las vistas `*_all` que no es propiedad de la entidad pero la usan `actividadestudios`), `ActaTribunalField` (4), `NotaField` (4) y `PersonaNotaField` (15, compartido por `PersonaNota` y `PersonaNotaOtraRegionStgr`; el jsonb `json_certificados` no es buscable). `COLUMNAS = []`. `matching()` en las interfaces de `Acta`, `ActaTribunal`, `Nota`, `PersonaNota` y `PersonaNotaOtraRegionStgr` (las variantes `Dl`/`Ex`/`Certificado` heredan); `getActas`/`getActasTribunales`/`getNotas`/`getPersonaNotas` `@deprecated` como envoltorios. `ConstantNotaRepository` (segunda implementación de `NotaRepositoryInterface`) recibe también `matching()`.
    - Trait nuevo `NotasCriteriaTrait` (`filasNotasMatching`): ejecuta el criterio con `getoDbl()` en vez de `getoDbl_Select()`, porque estos repos no fijan conexión de selección y varias subclases sustituyen `oDbl` en su constructor (esquema de otra dl, `oDBR`…). Admite `$columnas` y una condición fija previa (envuelve la tabla en un `SELECT … WHERE`). `PgNotaRepository` usa `RunsCriteriaQueries` (ya tenía conexión de selección).
    - `PgPersonaNotaRepository::matching()` aplica su filtro fijo de `e_notas` (`NOT (id_situacion = 13 AND COALESCE(tipo_acta,1) = 2)`) con esa condición previa. `PgActaRepository`: el flag legacy `_sin_pdf` pasa a un método con nombre, `matchingSinPdf()` (solo las columnas del listado, sin leer el bytea); el envoltorio `getActas` lo traduce. `PgPersonaNotaOtraRegionStgrRepository` conserva la hidratación de `json_certificados` y `tipo_acta`.
    - Migrados los llamantes de `src/notas/application` (`ActaSelectData`, `ListadoAnualActasData`, `BuscarActaData`, `ActaVerFormData`, `ActaVerAddPersona{,FormData}`, `ActaVerNotasListadoData`, `ActaImprimirPresentacionData`, `DatosActa`, `ExpedienteNotasPersona`, `EditarPersonaNota`, `NotaPersonaFormData`, `PersonaNotaNueva`, `PlanEstudiosDePersona`, `PosiblesOpcionalesData`, `TablaAlumnosAsignaturas`, `TesseraCopiar`, `AsignaturasPendientesResumenData`, `support/{ActaContenidoImpreso,ActaTribunalSync,LiberarHuecoNivelNota}`). Los `IN` con string `"1,2"` pasan a `Filter::in` con la lista; `BETWEEN` de fechas/niveles a `Filter::between`; `'_ordre' => 'tipo_acta DESC'` a `orderBy(TipoActa, Desc)`. En `ExpedienteNotasPersona` se descarta un operador `['id_asignatura' => '<']` que no correspondía a ningún filtro (no tenía efecto).
    - **No migrado** (siguen por el envoltorio): los llamantes de otros módulos (`actividadestudios`: `ActaNotasData`, `ActaNotasDefinitivasGrabar`, `E43CertificadoData`, `FormMatriculasDeUnaPersonaData`, `MatriculaAutomatica`, `PosiblesAsignaturasCaData`, `PosiblesCa`, `DocenciaActualizar`; `certificados/certificado_emitido_imprimir_mpdf_datos`), `src/notas/application/legacy/Resumen` y las llamadas a `getAsignaturas($where, $operators)` con el par de `PlanEstudiosFilter` (pendiente de un paso propio, ver abajo).
    - **Posible bug heredado, no tocado:** `MatriculaAutomatica` pasa `id_nivel => "'123[012]'"` con `~`: las comillas simples forman parte del patrón, así que el filtro no casa nunca (comportamiento idéntico por el envoltorio). Decidir con el usuario si hay que quitar las comillas.
    - Test: `PgNotasMatchingTest` (5 tests, equivalencia con legacy: actas con/sin pdf, `BETWEEN`, tribunal, persona-nota con `<`, notas). Los unitarios de `ActaContenidoImpreso`, `ActaMarcarImpresa`, `ActaPdfSubir`, `ActaVerAddPersonaFormData`, `ActaVerNotasListadoData`, `AsignaturasSearchData`, `BuscarActaData`, `LiberarHuecoNivelNota`, `NivelCatalogoAsignaturaEnPlan`, `PersonaNotaEliminar`, `PersonaNotaInputParser`, `PosiblesOpcionalesData`, `TesseraVerData` y `TeseraFilasImpresion` mockean `matching`. Suites verdes: unit de `notas`, `actividadestudios`, `certificados`, `asistentes`, `personas`, `profesores`, `asignaturas`; integration de `actividadestudios`, `asignaturas` y los `PgPersonaNota*RepositoryTest` / `PgNotasMatchingTest` ejecutados por fichero. `tests/integration/notas` entero y `tests/integration/certificados` abortan por entorno (también sin este cambio), igual que `PgActaRepositoryTest` por memoria (falla igual en base).
  - [ ] **4.4.planestudiosfilter** `PlanEstudiosFilter` devuelve el par `[$where, $operators]` que consumen ~10 llamantes de `notas`, `actividadestudios` y `profesores` con `getAsignaturas(...)`: pasarlo a devolver un `Criteria` (o un `Filter` de plan) y migrar esas llamadas (pendiente; ver 4.1.asignaturas).
  - [x] **4.4.ubis** Troceado por familias (≈ 38 repos y ≈ 160 llamadas: `getCentros` 64, `getDelegaciones` 31, `getDirecciones` 22, `getTelecos` 12, `getCasas` 8, `getTarifaUbis` 6, `getCasaPeriodos` 5…); un commit por familia. Se marca la casilla padre al cerrar todas.
    - [x] **4.4.ubis.catalogos** `TipoCasa`, `TipoCentro`, `TipoTeleco`, `DescTeleco`, `Region`, `Delegacion`, `TarifaUbi`, `CasaPeriodo`. Decisiones:
      - Ocho enums en `src/ubis/domain/criteria/` (`{TipoCasa,TipoCentro,TipoTeleco,DescTeleco,Region,Delegacion,TarifaUbi,CasaPeriodo}Field`, `COLUMNAS = []`). `matching()` en las 8 interfaces y repos (ya tenían conexión de selección y leían de ella); `getTiposCasa`/`getTiposCentro`/`getTiposTeleco`/`getDescsTeleco`/`getRegiones`/`getDelegaciones`/`getTarifaUbis`/`getCasaPeriodos` `@deprecated` como envoltorios. `CasaPeriodo` hidrata `f_ini`/`f_fin` con `ConverterDate('date')`.
      - Migrados los llamantes del módulo: `Info{Regiones,Delegaciones,TipoCtr,TipoTeleco,TipoCasa}` (`sin_acentos` → `matchesUnaccented`), `CalendarioPeriodos{Get,Get2,Nuevo}Data` (fechas `>=`/`<=` → `gte`/`lte`), `UbisGuardar`, `DelegacionDropdown` (5), `DelegacionQuery`, `RegionDropdown`. `DelegacionDropdown::byRegiones` pasaba las regiones como un string `'a","b'` que el adaptador leía como un único valor (con más de una región no casaba): ahora `Filter::in` con la lista (arregla el caso de varias regiones).
      - **Sin migrar a propósito** (bugs heredados, decidir con el usuario): `InfoDescTeleco` busca por `id_desc_teleco`, columna que no existe (la búsqueda con texto falla igual con el método legacy); se deja por el envoltorio. `DelegacionQuery::arrayDlByRegionStgr` (clase sin usos) filtraba por `status`, columna inexistente; se ha pasado a `active`, que era la intención.
      - Siguen por el envoltorio los llamantes de otros módulos: `personas` (`PersonasEditarData`, `Trasladar`), `notas/ActaPersonaFormListas`, `actividades`, `actividadplazas`, `actividadestudios`, `devel_db_admin`, `dbextern` (`getDelegaciones`), `casas`, `pasarela`, `actividadtarifas` (`getTarifaUbis`).
      - Incidencia al ejecutarlo: el script de recorte automático de métodos se llevó por delante métodos vecinos en `PgActaRepository` (`getUltimoLibro`) y en cuatro repos de `ubis` (`getArrayDescTelecoUbis`, `getArrayTiposCasa`, `getArrayTiposCentro`, `getArrayTiposTeleco`). Restaurados antes de commitear `ubis`; `getUltimoLibro` se restaura en este mismo commit (el de `notas` lo perdió; sin llamantes).
      - Test: `PgUbisCatalogosMatchingTest` (4 tests, equivalencia con legacy incl. booleano, `IN`, fechas). Los unitarios de `CalendarioPeriodosGetData`, `CalendarioPeriodosNuevoData`, `DelegacionQueData`, `UbisBuscarOpcionesData` y `UbisGuardar` mockean `matching`. Suites verdes: unit de `ubis`, `notas`, `personas`, `actividades`, `actividadplazas`, `casas`, `actividadestudios`, `devel_db_admin`, `pasarela`, `dbextern`, `actividadtarifas`, `menus`, `shared`; integration de `ubis/infrastructure/repositories` (214 tests), `actividadplazas`, `casas`, `actividadtarifas`, `actividades`, `asistentes`. `tests/integration/ubis` entero aborta por entorno (también sin este cambio).
    - [x] **4.4.ubis.direcciones** `Direccion` (+ `Casa`/`Centro` × `Dl`/`Ex`) y `Relacion*Direccion`. Decisiones:
      - `DireccionField` (16 campos) en `src/ubis/domain/criteria/`. Un solo `matching()` en `PgDireccionRepository` (las variantes `DireccionCasa`/`CasaEx`/`Centro`/`*Dl`/`*Ex` lo heredan) y en las 4 interfaces independientes (`Direccion`, `DireccionCasa`, `DireccionCasaEx`, `DireccionCentro`; las `*Dl`/`*Ex` extienden la base). `getDirecciones` `@deprecated` como envoltorio. Hidrata `plano_doc` (bytea) y `f_direccion` (`ConverterDate('date')`) como antes. Los `Relacion*Direccion` no tenían búsquedas con `$aWhere` (nada que migrar).
      - Migrados: `DireccionesTablaData` (`c_p LIKE '%x%'` → `Filter::matches(CP, preg_quote(x))`: subcadena; solo difiere si el código postal incluyera `%`/`_`), `cartaspresentacion/CartasPresentacionUbisListaData` y `CartasPresentacionListaData` (deferidos a este paso en 4.2/cartaspresentacion).
      - **Sin migrar a propósito:** `UbisTablaData::buscarUbisPorDireccion` recibe `aWhereD/aOperadorD` ya construidos y los serializa en base64 en las URLs de paginación (`urlsafeB64encode(json_encode(...))`); migrarlo exige cambiar ese contrato de URL (pantalla `ubis_tabla`, junto con `getCentros`/`getCasas` en 4.4.ubis.centros/casas).
      - Test: `PgUbisDireccionesMatchingTest` (3 tests: poblacion `sin_acentos`, subcadena de `c_p` frente a `LIKE`, variantes heredadas). Suites verdes: unit de `ubis`, `cartaspresentacion`, `encargossacd`; integration de `ubis/infrastructure/repositories` (217), `cartaspresentacion`. Nota de datos: la base de pruebas tiene filas de `u_dir_*` con `id_direccion` inválido para el VO (`getDirecciones()` sin filtro lanza igual que antes); el test acota por rango.
    - [x] **4.4.ubis.telecos** `TelecoUbi`, `TelecoCdc*`, `TelecoCtr*`. Decisiones:
      - `TelecoUbiField` (6) en `src/ubis/domain/criteria/`. `matching()` solo en `PgTelecoUbiRepository` (abstracta; los seis `Cdc`/`Ctr` × `Dl`/`Ex`/base la heredan y fijan ambas conexiones en su constructor) y en `TelecoUbiRepositoryInterface` (las otras interfaces la extienden); `getTelecos` `@deprecated` como envoltorio.
      - Migrados: `TelecoTablaData`, `UbiTelecoService`, `UbiContactsTrait` (2) e `InfoTelecoUbi`. En `InfoTelecoUbi`, con texto de búsqueda el filtro `id_pau` se descartaba (`$aWhere = ['num_teleco' => …]`): se conserva ese comportamiento.
      - Test: `PgUbisTelecosMatchingTest` (2 tests, equivalencia con legacy en `TelecoCtrDl` y `TelecoCdcDl`); `TelecoTablaDataTest` mockea `matching`. Suites verdes: unit de `ubis`, `encargossacd`, `personas`; `TelecoTest` y `ubis/infrastructure/repositories`.
    - [x] **4.4.ubis.centros** `Centro`, `CentroDl`, `CentroEx`, `CentroEllas`, `CentroEllos` (operadores `~` y `&` de `CentrosDisponiblesData`). Decisiones:
      - Cinco enums (`CentroField`, `CentroExField`, `CentroDlField`, `CentroEllasField`, `CentroEllosField`), uno por repositorio porque cada `matching()` solo acepta el suyo; los campos salen de las columnas de `Guardar` (más `id_auto` en `Centro`/`Ex`/`Dl`, y las columnas propias de `CentroDl`: `n_buzon`, `num_pi`, `num_cartas`, `observ`, `num_habit_indiv`, `plazas`, `sede`, `num_cartas_mensuales`). `matching()` en las 5 interfaces y repos; `getCentros` `@deprecated` como envoltorio. Los antiguos `getCentros` de `Centro`/`Dl`/`Ex` leían de `getoDbl()` y `PgCentroRepository` no tenía conexión de selección: se fija igual a `oDbl` (las otras ya lo tenían).
      - Migrados: `ubis/{CentrosGetPlazasData,CentrosGetNumData,CentrosGetLaborData,CentrosSListaData}`; y los deferidos a este paso por otros módulos: `cartaspresentacion/{CartasPresentacionListaData (cartasPorCondicionCentro recibe ya un Criteria), CartasPresentacionUbisListaData}` y `actividadescentro/CentrosDisponiblesData` (`~` → `matches`, `&` → `Filter::hasBits`; `filtros()` pasa a `criterio()`, que elige el enum según el repo).
      - **Sin migrar a propósito:** `ubis/{UbisListaData,ListCtrData}` y `UbisTablaData` montan un único par `$aWhere/$aOperador` que se usa por igual con repos de centros y de casas (y `UbisTablaData` lo serializa en la URL de paginación): migran junto con `4.4.ubis.casas`. `zonassacd/ZonaCtrLista` recibe `[$where, $operators]` de `CentrosDeZona::whereActivosIn`, compartido con `encargossacd`/`misas` (4.5). Siguen por el envoltorio los ~40 llamantes de `encargossacd`, `misas`, `asistentes`, `planning`, `personas`, `dbextern`, `actividades`, `actividadessacd` y `casas`.
      - Test: `PgUbisCentrosMatchingTest` (3 tests, equivalencia con legacy en los 5 repos, incl. booleano, `~`, `&` e `IN`). Los unitarios de `CentrosGet{Num,Labor,Plazas}Data` y `CentrosDisponiblesData` mockean `matching`. Suites verdes: unit de `ubis`, `actividadescentro`, `cartaspresentacion`, `encargossacd`, `misas`, `zonassacd`, `planning`, `asistentes`, `actividadessacd`, `casas`, `dbextern`; integration de `ubis/infrastructure/repositories` (222), `actividadescentro`, `cartaspresentacion`, `encargossacd`, `misas`, `zonassacd`, `asistentes`, `actividadessacd`, `casas`. `tests/integration/planning` aborta por entorno (también sin este cambio).
    - [x] **4.4.ubis.casas** `Casa`, `CasaDl`, `CasaEx`. Decisiones:
      - `CasaField` (17) en `src/ubis/domain/criteria/`; `matching()` solo en `PgCasaRepository` (las `Dl`/`Ex` la heredan y fijan ambas conexiones) y en las 3 interfaces independientes; `getCasas` `@deprecated` como envoltorio. Hidrata `f_active` con `ConverterDate('date')`.
      - Migrados `ubis/UbisListaData` (casas y centros con un `Criteria` cada uno) y `ubis/ListCtrData`: el par `$aWhere/$aOperador` único se sustituye por una lista neutra de `[campo, Operator, valor]` + orden, y un método privado `criterio()` la convierte al enum de cada repo (`$enum::from($campo)`), porque cada `matching()` solo acepta su propio enum.
      - Siguen por el envoltorio: `casas/CasasResumenData`, `actividades/CalendarioListasDatos` y `planning/ActividadesPorCasasService`.
      - Test: `PgUbisCasasMatchingTest` (3 tests: booleanos, `~`, `!~`, `sin_acentos` y variantes `Dl`/`Ex`). Suites verdes: unit de `ubis`, `casas`, `actividades`, `planning`, `menus`; integration de `ubis/infrastructure/repositories` (225, estable en 3 pasadas), `ubis/application`, `casas`, `actividades`.
      - Se estabiliza `PgUbisDireccionesMatchingTest`: `DireccionFactory::create()` genera latitudes hasta 100 y falla al azar (el VO admite ±90); el test usa `createSimple()`.
- [ ] **4.5 Los más usados (73+):** `misas`, `encargossacd` (127 llamadas)
- [ ] **4.6 Barrido de llamantes residuales** (añadido al cerrar 4.4): llamadas que siguen por el envoltorio `getXxx(array, array)` por pertenecer a otro módulo, y que bloquean la fase 5.1. Pendientes conocidos: `ubis/UbisTablaData` (filtros `aWhere/aWhereD` serializados en la URL de paginación), `zonassacd/ZonaCtrLista` + `CentrosDeZona::whereActivosIn` (par `[$where, $operators]` compartido con `encargossacd`/`misas`), los llamantes de `getDelegaciones` en `personas`, `actividades`, `actividadplazas`, `actividadestudios`, `devel_db_admin`, `dbextern`, `notas/ActaPersonaFormListas`; de `getTarifaUbis` en `casas`, `pasarela`, `actividadtarifas`; de `getCasas` en `casas`, `actividades`, `planning`; de `getCentros` en `encargossacd`, `misas`, `asistentes`, `planning`, `personas`, `actividades`, `actividadessacd`, `dbextern`, `casas`; de `getActas`/`getPersonaNotas` en `actividadestudios` y `certificados`; de `getActividadesPlazas`/`getPlazasPeticion` en `actividades` y `asistentes`; de `getMatriculas`/`getActividadAsignaturas` en `personas`, `asistentes` y `notas`; `InfoDescTeleco` (campo inexistente `id_desc_teleco`); `MatriculaAutomatica` (regex con comillas). Hacerlo con `rg "get(Xxx)\("` por método tras cerrar 4.5.

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

---

## 10. Presupuesto de IA y red de seguridad (añadido 2026-10-28)

### 10.1 Medición

`php tools/qa/medir_consumo_ia.php` lee las transcripciones de Claude Code (`~/.claude/projects/<proyecto>/*.jsonl`) y muestra, por turno de usuario, llamadas a la API, tokens de entrada, escritura y lectura de caché, salida y **u.eq.** (unidades equivalentes de entrada = ent + 1,25·escritura + 0,1·lectura + 5·salida). Opciones: `--session=<uuid>`, `--all`, `--json`.

**Limitación:** u.eq. es una referencia *relativa* para comparar pasos entre sí; no es el cupo real del plan, que no se publica en tokens. Para calibrar: anotar el porcentaje de `/usage` antes y después de un paso y dividir por sus u.eq.

Medido en esta sesión (2026-10-28):

| Paso | u.eq. | Llamadas API | Notas |
|---|---:|---:|---|
| 0.1 inventario | 144.000 | 6 | solo lectura y documento |
| 1.1 clases base | 212.000 | 9 | incluye decisión sobre el prototipo |
| 1.2 conversor | 140.000 | 5 | |
| 1.3 adaptador + equivalencia | 291.000 | 9 | el más caro de la fase 1 |
| 1.4 trait de ejecución | 255.000 | 11 | |

Mediana de los pasos de código (1.1–1.4): ~230.000 u.eq.; máximo medido: 291.000.

**Piloto real (fase 2, ejecutada con `ejecutar_plan_ia.php` el 2026-10-28):**

| Paso | u.eq. real | Estimación inicial | Coste USD | Denegaciones Bash |
|---|---:|---:|---:|---:|
| 2.0 red de seguridad | 341.600 | 300.000 | 0,71 | 3 |
| 2.1 `matching()` en `PgAsistenteRepository` | 344.800 | 325.000 | 0,65 | 5 |
| 2.2 subclases + `matchingConjunto` | 258.900 | 200.000 | 0,52 | 3 |
| 2.3 llamantes de `application/` | 418.200 | 400.000 | 0,74 | 8 |
| 2.4 revisión + `AGENTS.md` | 116.800 | 125.000 | 0,29 | 0 |
| **Total** | **1.480.000** | 1.350.000 | **2,90** | 19 |

La estimación de la sección 10.2 (1,0–1,6 M) acertó; cada paso quedó entre −7 % y +29 % de lo previsto. Todo el piloto movió el uso semanal del plan Pro de ~4 % a ~5 %: un orden de magnitud de **~0,7 % de la semana por cada millón de u.eq.** (con la granularidad de 1 % de `/usage`, el error es grande; mejora al acumular más pasos). Con ese ritmo, las fases 3–5 caben holgadamente en una semana.

### 10.2 Estimación de la fase 2 (aproximada)

Tamaño real del piloto: 5 repositorios (`PgAsistenteRepository` 293 líneas + 4 subclases de 32–66), 20 llamadas en 8 ficheros de `src/asistentes/application/`, 5 llamadas a `getConjunt()` en un servicio. Pero el envoltorio hace que **los ~45 ficheros de otros módulos que llaman a `getAsistentes()` pasen por el adaptador desde 2.1**, aunque solo se migren 8: ahí está el riesgo.

| Paso | Estimación (u.eq.) | Comentario |
|---|---:|---|
| 2.0 red de seguridad | 250.000–350.000 | leer el repo, la factory y los llamantes; escribir y ejecutar los tests |
| 2.1 `AsistenteField` + `matching()` + envoltorio | 250.000–400.000 | toca un repositorio de producción; ejecutar suites de módulos dependientes |
| 2.2 subclases + `getConjunt` | 150.000–250.000 | mayormente mecánico |
| 2.3 llamantes de `application/` | 300.000–500.000 | 8 ficheros; **dividir en dos pasos de 4** si el primero supera 300.000 |
| 2.4 revisión + `AGENTS.md` | 100.000–150.000 | |
| **Total fase 2** | **~1,0–1,6 M** | unas 4–7 veces un paso medio de la fase 1 |

Son estimaciones mías a partir de cinco mediciones: tratarlas como orden de magnitud, no como compromiso.

### 10.3 Cómo asegurar que la fase termina

- Cada paso termina con tests en verde y un commit propio; parar entre pasos siempre deja el repositorio consistente (el envoltorio mantiene la compatibilidad).
- **Antes de cada paso:** ejecutar `php tools/qa/medir_consumo_ia.php` y comprobar que lo que queda de cupo supera ~1,5 × el máximo medido para ese tipo de paso (≈ 450.000 u.eq.). Si no, esperar al siguiente periodo.
- Orden recomendado para una semana con poco margen: 2.0 → 2.1 (el par que da valor: la red de seguridad y el primer repositorio migrado), y dejar 2.2–2.4 para otra tanda. No hay que acabar la fase de una vez.
- No empezar 2.1 sin 2.0: sin la red de seguridad, un fallo del adaptador se descubriría en producción por cualquiera de los ~45 llamantes.

### 10.4 Por qué hace falta el paso 2.0

Los tests de la fase 1 prueban el adaptador sobre una **tabla temporal con un enum de prueba**. No prueban: la tabla real `d_asistentes_all`, el mapa real campo→columna, ni las formas concretas de `$aWhere` que usan los llamantes (fechas `DateTimeLocal`, booleanos `'t'`, `BETWEEN` con fechas entrecomilladas, `~` con regex, `TXT` de `ListaUltimaActivData`…). Con el envoltorio, cualquiera de esas formas que el adaptador no entienda lanza una excepción en tiempo de ejecución en un módulo ajeno. Red mínima:

1. **Test de caracterización** (2.0): expectativas fijadas a mano sobre datos sembrados, independientes de la implementación; verde antes y después del cambio.
2. **Ejecutar las suites de los módulos dependientes** tras 2.1 (`tests/unit` e `integration` de `asistentes`, `actividades`, `planning`, `actividadcargos`, `personas`, `misas`, `pasarela`), no solo `asistentes`.
3. **Revisar con `rg`** los `$aOperators` de los 8 ficheros de `application/` y los de módulos ajenos que llaman a `getAsistentes()`: cualquier operador fuera de la tabla 3.6 se detecta antes de ejecutar nada.

---

## 11. Ejecución automática por tandas (añadido 2026-10-28)

`php tools/qa/ejecutar_plan_ia.php` ejecuta los pasos `- [ ] **N.M**` pendientes de este plan, uno a uno, lanzando `claude -p`, hasta que se acaba el plan, el presupuesto o el cupo.

**Primera vez (piloto, con terminal):**

```bash
php tools/qa/ejecutar_plan_ia.php --dry-run --fase=2      # ver qué haría, sin gastar
php tools/qa/ejecutar_plan_ia.php --fase=2                # pregunta el % semanal usado y, tras cada paso, el % nuevo
```

Las respuestas «% antes / % después» de cada paso son lo que calibra la conversión **u.eq. → % de semana**. Con 2–3 pasos medidos ya hay calibración; a partir de ahí el script decide solo si el siguiente paso cabe (`estimación × 1,3 ≤ cupo libre − reserva del 5 %`).

**Cron (martes 14:30; el cupo semanal del plan Pro se renueva el martes a las 16:00):**

```cron
30 14 * * 2 PATH=/home/dani/.local/bin:/usr/local/bin:/usr/bin:/bin cd /home/dani/orbix_local/orbix && php tools/qa/ejecutar_plan_ia.php --fase=2 >> docs/dev/reports/ejecutar_plan_ia.log 2>&1
```

Sin terminal el script no puede preguntar. Usa, por orden: `--usado=NN`, el último `% anotado` de esta semana (`php tools/qa/ejecutar_plan_ia.php --anotar-uso=63`, válido si es posterior al último reset) o, si no hay dato, trabaja hasta que `claude` avise de que se agotó el cupo. Consejo: anotar el % el martes por la mañana.

**Qué hace y qué no:**

- Trabaja en la rama `refactor/criteria-auto` (la crea desde la rama actual). **No hace push, merge ni toca `main`**; el resultado se revisa y se fusiona a mano.
- Cada paso tiene que acabar con commit nuevo, árbol limpio y casilla marcada; si `claude` dice «OK» pero no se cumple, se registra `INCONSISTENTE` y se para.
- Si queda trabajo sin commitear (paso cortado por el límite), la siguiente ejecución se niega a continuar hasta que se revise (`--reanudar` para que `claude` lo complete).
- No empieza pasos después de la hora límite (por defecto, el próximo reset): un paso que cruza el reset sigue, porque ya gasta cupo nuevo.
- En los bloques de la fase 4 pide a `claude` que primero los trocee en un paso por módulo y que pare cuando el siguiente módulo no quepa en el presupuesto restante: así un presupuesto pequeño se aprovecha módulo a módulo.
- Registro: `docs/dev/reports/consumo_ia.jsonl` (un registro por paso: u.eq., coste USD, turnos, duración, % antes/después, resultado, commit). Estado: `docs/dev/reports/consumo_ia_estado.json`. Las estimaciones por paso se corrigen con el cociente real/estimado de lo ya medido.

**Verificado con el piloto real:**

1. `modelUsage` del JSON es el total de la ejecución: `ueq` y `ueq_transcripcion` coinciden exactamente en los cinco pasos.
2. El aviso de límite **no se ha podido comprobar** (no se alcanzó el cupo). Si ocurre, el paso quedará como `ERROR`/`INCONSISTENTE` con el texto en `detalle`; ajustar entonces la expresión de detección.
3. Las denegaciones (19 en total) **no venían de la lista de herramientas**, sino de las comprobaciones de seguridad del propio Bash en modo `acceptEdits`: comandos con variables de shell o bucles (`for m in …`), `cd … && git …`, `git -C …`, compuestos con `;`/`&&` y heredocs de python. `claude` los rodeaba con otros comandos, pero costó turnos y, en el 2.3, dejó sin ejecutar la tanda de suites de módulos dependientes. Corrección: el prompt incluye reglas de «un comando por llamada, sin variables ni bucles, edita con Edit/Write» y existe `php tools/qa/phpunit_modulos.php <rutas…>` (autorizado en la lista) para lanzar varias suites de una vez. El registro guarda ahora `denegaciones_detalle` (qué comando se denegó).
4. El plan Pro tiene además un límite por ventana de 5 horas: si se alcanza, el script lo ve como cupo agotado y para aunque quede cupo semanal (sin comprobar).

**Revisión posterior del piloto (suites de módulos dependientes, rama `refactor/criteria-auto` en 2.4 frente a `ab1b301c1`, antes del piloto):** `asistentes`, `actividades`, `planning`, `actividadcargos`, `misas`, `pasarela`, `actividadestudios` y `ubiscamas` (unit) en verde. Tres suites de integración fallan **igual antes y después**, así que no son regresión:

- `tests/integration/ubiscamas`: 4 errores (`PgCamaDl`/`PgHabitacionDl`).
- `tests/integration/cambios`: inestable; falla un test distinto en cada ejecución y aborta (1, 4, 8 o 16 tests ejecutados).
- `tests/integration/personas`: aborta porque `GestorErrores` escribe en `/home/dani/orbix_local/orbix/log/errores.log`, que no existe dentro del contenedor. Mientras tanto, esa suite no puede validar cambios en `personas`.
