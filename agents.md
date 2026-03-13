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
- `src/<modulo>/infrastructure/controllers/` - Controladores de lógica
  - `*_update.php` - Procesar guardado/actualización de datos
  - `*_delete.php` - Procesar eliminación de datos
  - Usan casos de uso y repositorios
  - Devuelven respuestas (texto, JSON, etc.)

### Ejemplo práctico: módulo ubiscamas

```text
frontend/ubiscamas/
  controller/
    habitacion_form.php    ← Prepara datos para el formulario
    cama_form.php         ← Prepara datos para el formulario
  view/
    habitacion_form.phtml ← Vista HTML del formulario
    cama_form.phtml      ← Vista HTML del formulario
    select2006.phtml     ← Vista de listado

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
- Guardado/Update: `src/<modulo>/infrastructure/controllers/<entidad>_update.php`
- Eliminación: `src/<modulo>/infrastructure/controllers/<entidad>_delete.php`

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
use core\ConfigGlobal;

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
