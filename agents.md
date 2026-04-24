# Guía de Desarrollo DDD para Orbix

## Objetivo
Mantener una estructura consistente basada en DDD para todo nuevo código en `src/`, reduciendo acoplamientos con legacy y evitando mezclar capas.

## Estructura mínima por módulo
Todo módulo nuevo debe seguir esta base:

```text
src/<modulo>/
  application/
  domain/
    contracts/
    entity/
    value_objects/
  infrastructure/
    controllers/
    repositories/
  config/
    dependencies.php
    routes.php
```

Reglas:
- No crear nuevas carpetas `db/` o `view/` para funcionalidad nueva.
- No dejar archivos vacíos como placeholder.
- Si un módulo no tiene casos de uso, igualmente debe mantener `config/` y `domain/`.

## Reglas de capas

### Domain
- Debe ser independiente de framework/UI/infra.
- Prohibido en código nuevo:
  - `use core\...` (salvo utilidades puramente de dominio justificadas).
  - `use web\...`, `use frontend\...`.
  - `$GLOBALS`, `$_SESSION`, `$_POST`, `$_GET`.
  - Construcción de HTML, rutas URL o renderizado de vistas.
- `contracts/` define puertos de dominio, no detalles técnicos:
  - No exponer `PDO`, `setoDbl`, `getoDbl`, `getNomTabla`, `getErrorTxt`.

### Application
- Orquesta casos de uso y coordina repositorios/servicios de dominio.
- Inyección de dependencias por constructor.
- No usar `$GLOBALS['container']` dentro de casos de uso nuevos.
- No devolver objetos de presentación (`ContestarJson`, `Desplegable`, `Lista`, etc.).
- No ejecutar SQL directo ni manipular conexiones.

### Infrastructure
- Implementa repositorios concretos y adaptadores de entrada/salida.
- Controladores:
  - Leen request.
  - Invocan un caso de uso.
  - Transforman respuesta a JSON/HTML.
  - Sin lógica de negocio compleja.
- Cualquier uso de `core\...`, `web\...`, PDO o globals debe quedar aquí.

### Config
- `dependencies.php`: mapear interfaces de dominio a implementaciones de `infrastructure`.
- `routes.php`: declarar rutas hacia controladores existentes. No dejar rutas huérfanas.

## Convenciones de código nuevo
- Clases en `PascalCase` (archivo = clase).
- Métodos y propiedades en `camelCase`.
- Interfaces con sufijo `Interface`.
- Casos de uso con nombres explícitos: `CrearXxxUseCase`, `ListarXxxUseCase`, etc.
- Evitar métodos estáticos en aplicación salvo utilidades puras.
- Mantener tipado estricto en firmas y retornos.

## Compatibilidad con legado
- No propagar patrones legacy al código nuevo.
- Si un caso de uso necesita integrarse con legacy:
  - Crear un adaptador en `infrastructure`.
  - Encapsular allí globals, formatos antiguos y APIs heredadas.
  - Mantener `domain` y `application` limpios.

## Checklist obligatorio en cada PR
- [ ] ¿El código nuevo respeta separación Domain/Application/Infrastructure?
- [ ] ¿No se añadieron dependencias de UI/DB en `domain`?
- [ ] ¿No se usó `$GLOBALS` en `domain` o `application` nuevos?
- [ ] ¿Interfaces de dominio sin detalles de PDO/tabla?
- [ ] ¿Rutas apuntan a controladores reales?
- [ ] ¿No hay archivos vacíos?
- [ ] ¿Nombres de clase/método siguen convención?
- [ ] ¿Se añadieron pruebas (unitarias o integración) para comportamiento nuevo?
- [ ] ¿Se han ejecutado y pasado los tests existentes al modificar código?
- [ ] ¿Se mantiene la separación estricta: nada de HTML/UI en `src/`, nada de lógica de dominio en `frontend/`?

## Tests: Convenciones y estructura

### Resumen de cobertura esperada
- Cada módulo en `src/` debe tener su carpeta en `tests/unit/<modulo>` y `tests/integration/<modulo>`.
- **Regla de Oro:** Todo código nuevo requiere tests nuevos. Toda modificación requiere la ejecución de los tests existentes para evitar regresiones.
- Usar el script `shell_scripts/check_test_coverage.sh` para detectar módulos sin tests.
- Módulos excluidos del chequeo automático: `layouts` (código de presentación legacy), `pasarela` (vacío).

### Tests unitarios (`tests/unit/<modulo>/`)
- Cubren `domain/entity/` y `domain/value_objects/`.
- Extienden `Tests\myTest`.
- Sin dependencias de BD ni de `$GLOBALS['container']`.
- Namespace: `Tests\unit\<modulo>\domain\entity` / `Tests\unit\<modulo>\domain\value_objects`.

#### Patrón para Value Objects
```php
namespace Tests\unit\<modulo>\domain\value_objects;
use src\<modulo>\domain\value_objects\MiVo;
use Tests\myTest;

class MiVoTest extends myTest {
    public function test_create_valid() { /* ... */ }
    public function test_invalid_throws_exception() { /* ... */ }
    public function test_to_string() { /* ... */ }
    public function test_fromNullableString_null() { /* ... */ }
}
```

#### Patrón para Entidades
```php
namespace Tests\unit\<modulo>\domain\entity;
use src\<modulo>\domain\entity\MiEntidad;
use Tests\myTest;

class MiEntidadTest extends myTest {
    private MiEntidad $entidad;

    public function setUp(): void {
        parent::setUp();
        $this->entidad = new MiEntidad();
        // Setear campos obligatorios...
    }
    // test_set_and_get_<campo>() por cada propiedad
}
```

### Tests de integración (`tests/integration/<modulo>/`)
- Cubren `infrastructure/persistence/postgresql/Pg*Repository`.
- Usan `$GLOBALS['container']->get(InterfaceClass::class)` para obtener el repositorio.
- Usan factories de `tests/factories/<modulo>/` para crear instancias.
- Namespace: `Tests\integration\<modulo>\infrastructure\persistence\postgresql`.
- Siempre limpiar los datos creados (llamar `Eliminar` al final).

#### Patrón para tests de repositorio
```php
namespace Tests\integration\<modulo>\infrastructure\persistence\postgresql;
use src\<modulo>\domain\contracts\MiRepositoryInterface;
use src\<modulo>\domain\entity\MiEntidad;
use Tests\myTest;
use Tests\factories\<modulo>\MiEntidadFactory;

class PgMiEntidadRepositoryTest extends myTest {
    private MiRepositoryInterface $repository;
    private MiEntidadFactory $factory;

    public function setUp(): void {
        parent::setUp();
        $this->repository = $GLOBALS['container']->get(MiRepositoryInterface::class);
        $this->factory = new MiEntidadFactory();
    }

    public function test_guardar_nuevo() { /* crear → guardar → findById → eliminar */ }
    public function test_actualizar_existente() { /* crear → guardar → guardar con mismo ID → eliminar */ }
    public function test_find_by_id_existente() { /* crear → guardar → findById → assertNotNull → eliminar */ }
    public function test_find_by_id_no_existente() { /* findById con ID inexistente → assertNull */ }
    public function test_eliminar() { /* crear → guardar → eliminar → findById → assertNull */ }
}
```

### Factories (`tests/factories/<modulo>/`)
- Una factory por entidad: `<Entidad>Factory.php`.
- Método `createSimple(?$id = null)`: datos mínimos válidos.
- Para entidades con IDs de secuencia BD: llamar `$repository->getNewId()` cuando `$id === null`.
- Para entidades con UUID: generar con `Uuid::uuid4()->toString()` o similar.
- Para entidades con clave compuesta: pasar todos los campos de la clave al método.
- **No** dejar FK con valores hardcodeados si pueden fallar; crear la entidad padre primero en `setUp()` y limpiarla en `tearDown()`.

### Checklist de tests por módulo nuevo
- [ ] `tests/unit/<modulo>/domain/entity/` — un `*Test.php` por entidad
- [ ] `tests/unit/<modulo>/domain/value_objects/` — un `*Test.php` por value object
- [ ] `tests/factories/<modulo>/` — una `*Factory.php` por entidad con repositorio
- [ ] `tests/integration/<modulo>/infrastructure/repositories/` — un `Pg*RepositoryTest.php` por repositorio

## Criterio de excepción
Si una regla no puede cumplirse por dependencia legacy:
- Documentar la excepción en el PR.
- Limitarla a `infrastructure`.
- Añadir tarea técnica para eliminarla.

## Arquitectura Frontend/Backend para nuevos módulos

### Separación de responsabilidades
Los nuevos módulos deben separar claramente la presentación (frontend) de la lógica de negocio (backend):

**Frontend (presentación):**
- `frontend/<modulo>/controller/` - Controladores que preparan datos para las vistas
  - Reciben parámetros del request
  - Cargan datos desde repositorios
  - Preparan arrays para las vistas
  - Renderizan plantillas `.phtml`
- `frontend/<modulo>/view/` - Vistas (archivos `.phtml`)
  - Solo HTML, CSS y JavaScript
  - No contienen lógica de negocio

**Backend (lógica de negocio):**
- `src/<modulo>/infrastructure/ui/http/controllers/` - Controladores de lógica
  - `*_update.php` - Procesar guardado/actualización de datos
  - `*_delete.php` - Procesar eliminación de datos
  - Usan casos de uso y repositorios
  - Devuelven respuestas puras (texto, JSON, etc.)
  - **Prohibido:** Generar HTML, usar `frontend/...`, o interactuar directamente con la UI.

### Ejemplo práctico: módulo ubiscamas

```text
frontend/ubiscamas/
  controller/
    habitacion_form.php    ← Prepara datos para el formulario
    cama_form.php         ← Prepara datos para el formulario
  view/
    habitacion_form.phtml ← Vista HTML del formulario
    cama_form.phtml      ← Vista HTML del formulario
    select_habitaciones_cdc.phtml ← Vista de listado

src/ubiscamas/
  domain/
    contracts/
      HabitacionRepositoryInterface.php
      CamaRepositoryInterface.php
    entity/
      Habitacion.php
      Cama.php
    value_objects/
      HabitacionId.php
      CamaId.php
      BañoTipo.php
  infrastructure/
    controllers/
      habitacion_update.php ← Lógica de guardado
      cama_update.php      ← Lógica de guardado
      cama_delete.php      ← Lógica de eliminación
    repositories/
      PgHabitacionRepository.php
      PgCamaRepository.php
  config/
    dependencies.php
```

### Reglas de rutas en vistas
- Formularios (form): `frontend/<modulo>/controller/<entidad>_form.php`
- Guardado/Update: `src/<modulo>/infrastructure/ui/http/controllers/<entidad>_update.php`
- Eliminación: `src/<modulo>/infrastructure/ui/http/controllers/<entidad>_delete.php`

### Generación de IDs
Para entidades con UUID:
```php
use Ramsey\Uuid\Uuid;

// Generar nuevo UUID
$newId = Uuid::uuid4()->toString();
$oEntidad->setIdVo($newId);
```

Para entidades con id_schema:
```php
use src\shared\config\ConfigGlobal;

// Obtener id_schema de la configuración
$miRegionDl = ConfigGlobal::mi_region_dl();
$id_schema = ConfigGlobal::idSchemaDl($miRegionDl);
$oEntidad->setIdSchema($id_schema);
```

### Herencia en entidades y repositorios
Cuando hay tablas con herencia (tabla padre y tablas hijas):

**Entidades:**
```php
// Entidad base
class Habitacion { /* ... */ }

// Entidad derivada (hereda de la base)
class HabitacionDl extends Habitacion { }
```

**Interfaces:**
```php
// Interface base
interface HabitacionRepositoryInterface { /* ... */ }

// Interface derivada (hereda de la base)
interface HabitacionDlRepositoryInterface extends HabitacionRepositoryInterface { }
```

**Repositorios:**
```php
// Repositorio base
class PgHabitacionRepository implements HabitacionRepositoryInterface {
    protected $nomTabla = 'du_habitaciones';
}

// Repositorio derivado (hereda e implementa la interface derivada)
class PgHabitacionDlRepository extends PgHabitacionRepository
    implements HabitacionDlRepositoryInterface {
    public function __construct() {
        parent::__construct();
        $this->setNomTabla('"H-dlbv".du_habitaciones_dl'); // Sobrescribe la tabla
    }
}
```

### Value Objects con listas
Para campos con valores predefinidos, crear value objects con método estático:

```php
final class BañoTipo {
    public static function getArrayBañoTipo(): array {
        return [
            1 => _("NO"),
            2 => _("completo"),
            3 => _("sin ducha"),
            4 => _("exterior"),
        ];
    }

    private int $value;

    public function __construct(int $value) {
        if (!array_key_exists($value, self::getArrayBañoTipo())) {
            throw new InvalidArgumentException("Valor no válido");
        }
        $this->value = $value;
    }

    public function getDescripcion(): string {
        return self::getArrayBañoTipo()[$this->value];
    }
}
```

## Manejo de Navegación y Estado ($oPosicion)

### Conceptos Clave
- **$oPosicion**: Es el objeto principal para gestionar el historial de navegación y la persistencia de parámetros entre controladores backend. Se define en `web\Posicion`.
- **js_atras(n)**: Método fundamental para retornar `n` pasos en el historial. Genera el código JS necesario para la navegación.
- **addParametro($key, $valor, $fila)**: Permite persistir datos. Si `$fila = 1`, el parámetro se guarda para la posición actual, facilitando su recuperación al volver atrás.

### Estrategia de Persistencia Híbrida
Para una experiencia de usuario fluida, combinamos el estado del backend con el estado del frontend:

1.  **Estado Backend ($oPosicion)**: Gestiona la jerarquía de páginas, IDs principales (como `id_activ`) y la lógica de "volver".
2.  **Estado Frontend (SessionStorage)**: Gestiona el estado volátil de la UI (scroll, selección). **Ver detalles en [frontend/agents.md](file:///home/dani/orbix_local/orbix/frontend/agents.md)**.

### Seguridad (Hash.php)
Cuando se añaden campos de estado en el frontend (ej: `<input type="hidden" name="scroll_id_...">`), estos campos deben excluirse de la validación del hash para evitar errores de "Hash mismatch".
- Modificar `web\Hash::isValid()` para ignorar prefijos específicos (como `scroll_id_`).


## Comunicación Frontend-Backend (AJAX y JSON)

Para la comunicación asíncrona entre las vistas (`.phtml`) y los controladores de lógica del backend:

### Backend (Controladores)
- **Clase Estándar**: Usar `web\ContestarJson` para todas las respuestas JSON.
- **Método**: `ContestarJson::enviar($error_txt, $data = 'ok')`.
  - Si `$error_txt` no está vacío, la respuesta tendrá `success: false` y el mensaje de error en la clave `mensaje`.
  - Si `$error_txt` está vacío, la respuesta tendrá `success: true`.
- **Ubicación**: Se aplica en los controladores de `src/` (infraestructura) como `*_update.php` o `*_delete.php`.
- **Importante**: No usar `echo json_encode()` ni `exit($msg)` de forma manual; delegar en `ContestarJson`.

### Frontend (JavaScript)
- **Llamada**: Usar `$.ajax` especificando siempre `dataType: 'json'`.
- **Estructura de manejo**:
  ```javascript
  let request = $.ajax({
      data: $(formulario).serialize(),
      url: 'ruta/al/controlador.php',
      method: 'POST',
      dataType: 'json'
  });
  request.done(function (json) {
      if (json.success !== true) {
          alert("<?= _("respuesta") ?>: " + json.mensaje);
      } else {
          // Lógica de éxito (ej: refrescar, volver atrás, alert de guardado)
          alert("<?= _("guardado") ?>");
      }
  });
  ```
- **Validaciones Previas**: Realizar validaciones de campos obligatorios en el JavaScript antes de iniciar la petición AJAX para evitar viajes innecesarios al servidor.

