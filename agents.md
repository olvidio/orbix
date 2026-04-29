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
- [ ] Si el caso de uso devuelve enlaces a `frontend/` o `apps/`, ¿van como `link_spec` (path + query) y la firma `Hash::link` / `HashF` ocurre solo en `frontend/`?
- [ ] ¿Ningún archivo nuevo en `src/application/` o `src/domain/` importa `web\Hash` para navegación UI?

## Tests: Convenciones y estructura

### Resumen de cobertura esperada
- Cada módulo en `src/` debe tener su carpeta en `tests/unit/<modulo>` y `tests/integration/<modulo>`.
- **Regla de Oro:** Todo código nuevo requiere tests nuevos. Toda modificación requiere la ejecución de los tests existentes para evitar regresiones.
- Usar el script `shell_scripts/check_test_coverage.sh` para detectar **módulos** sin carpeta de tests/factories según convención (resumen rápido).
- Usar `shell_scripts/check_test_granular.sh` para listar **ficheros** fuente (`domain/entity/*.php`, `domain/value_objects/*.php`, `infrastructure/persistence/postgresql/Pg*Repository.php`) sin el `*Test.php` esperado; complementa el anterior. Opcionalmente `STRICT=1 bash shell_scripts/check_test_granular.sh` para salida con código 1 si hay faltantes (útil en CI).
- Equivalente Composer: `composer test:report` (ambos informes seguidos), `composer test:report:buckets`, `composer test:report:granular`.
- Cobertura de **líneas** sobre `src/`: definida en [`phpunit.xml`](phpunit.xml) (`source`/`coverage`); ejecutar por ejemplo `composer test:coverage` o `composer test:coverage:unit` (los scripts configuran `XDEBUG_MODE=coverage` cuando se usa **Xdebug**; con solo **PCOV** no suele hacer falta ese entorno).
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
  - En código migrado o nuevo, cargan datos vía `PostRequest::getDataFromUrl('/src/<modulo>/...', ...)` contra endpoints en `src/`; no instanciar casos de uso ni repositorios de `src/` desde el controlador frontend
  - Preparan arrays para las vistas (incl. componentes `web\Lista`, desplegables, etc.)
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

### Enlaces firmados hacia la UI (`Hash::link` / `HashF`) — directiva

El hash de presentación (**`web\Hash`**, futuro **`frontend\shared\security\HashF`**) es responsabilidad **solo de la capa que sirve HTML al navegador** (`frontend/`, y mientras exista, `apps/` legacy). El backend de dominio/aplicación **no** debe saber cómo se construye esa firma.

**Reglas:**

1. **`src/domain/`** y **`src/application/`**: prohibido `use web\Hash`, `Hash::link`, `Hash::cmdConParametros`, etc. para URLs hacia `frontend/...` o `apps/...`.
2. **Patrón `link_spec`**: los listados / DTOs que hoy ponen `'ira' => Hash::link(...)` deben evolucionar a datos neutros, por ejemplo:
   - `'link_spec' => ['path' => 'frontend/modulo/controller/foo.php', 'query' => ['id' => 123]]`
   El **controlador `frontend/<modulo>/controller/*.php`** (tras `PostRequest` o al montar la vista) convierte cada `link_spec` en URL firmada con `Hash::link(AppUrlConfig::getPublicAppBaseUrl() . '/' . ltrim($path, '/') . '?' . http_build_query($query))` y rellena `ira` / `href` como espera `Lista` o la plantilla.
3. **`src/.../infrastructure/ui/http/controllers/`**: preferible devolver JSON con `link_spec` y firmar en `frontend/`; si un controlador `src` aún emite HTML legacy, documentar excepción y plan de migración.
4. **Documentación de arquitectura**: `documentacion/hash_arquitectura.md` (§7.4) y pilotos de referencia: `GruposLista` + `grupo_lista.php`, `usuariosLista` + `usuario_lista.php`, `UbisTablaData` + `ubis_tabla.php` (celdas con `link_spec`; además `pagina_link_spec` → `pagina_link` firmado en el controlador), `ListCtrData` + `list_ctr.php`, `ListaActivTabla` + `lista_activ_datos.php` (JSON con `link_spec`) + firma y `Lista::mostrar_tabla` en `frontend/actividades/controller/lista_activ.php`, `ActividadSelectListado` + `actividad_select_datos.php` (JSON con `link_spec` y `advertencia_demasiadas`) + firma y `Lista::mostrar_tabla` en `frontend/actividades/controller/actividad_select.php`, `ListaActividadesSgListado` + `lista_actividades_sg_datos.php` (JSON con `link_spec` y `advertencia_demasiadas`) + firma y `Lista::mostrar_tabla` en `frontend/actividades/controller/lista_actividades_sg.php`, `HabitacionesCamaLista` + `actividad_habitaciones_lista.php` (`reload_main_link_spec` / `distribucion_open_link_spec` / `nombres_open_link_spec`; firma con `HashFrontSignedLink` en `frontend/ubiscamas/controller/lista_habitaciones.php`; mutaciones AJAX `update_cama_asistente` y `update_solo_vip` con `HashB::sign` en el endpoint y POST `ctx`), `SelectHabitacionesCdc::getSegmentData()` + tipo `select_habitaciones_cdc` en `DossiersVerPantallaData` → `frontend/ubiscamas/helpers/SelectHabitacionesCdcRender.php` (`HashFront` + `Lista` + `SelectHabitacionesCdcUrlSigning` al pintar desde `dossiers_ver.php`), `Select_certificados_de_una_persona` + `frontend/certificados/helpers/SelectCertificadosDeUnaPersonaUrlSigning.php`, `Select_notas_de_una_persona` + `frontend/notas/helpers/SelectNotasDeUnaPersonaUrlSigning.php`, `ActivPendientesSelectData` + `activ_pendientes_select_data.php` (`link_spec` → `home_persona` en `frontend/personas/...`), `ActividadTipo` (Twig de filtros tipo actividad) + `frontend/shared/helpers/ActividadTipoTwigHashCompose.php` (tokens `h` / `h_act` para AJAX); formularios gestión tipo (`TipoActivFormNuevo` / `TipoActivFormModificar`) + `frontend/shared/helpers/TipoActivGestionFormHashCompose.php` (`getCamposHtml`), `FichaProfesorStgr` + `frontend/profesores/controller/ficha_profesor_stgr.php` (`go_cosas_link_specs` / `ficha_self_link_spec`; los enlaces a `tablaDB_lista_ver.php` reciben `go_to` firmado desde la spec de la ficha), módulo dossiers: `DossiersListaFichasData` (`href_*_link_spec`) + firma en `frontend/dossiers/controller/lista_dossiers.php` (`HashFrontSignedLink::signRowLinkSpecs`); `DossiersVerPantallaData` (datos planos: `top_data`, `ficha_segmentos` con `action_tabla_link_spec` / `ins_traslado_link_spec` / `script_ctx` / `hash`) + firma y render en `frontend/dossiers/controller/dossiers_ver.php` y helper `frontend/dossiers/helpers/DossiersVerFichaDatosTabla.php` (el `<script>` de `DatosTablaRepo` también se compone en frontend); `PermDossiersListaData` (`pagina_link_spec`) + firma en `frontend/dossiers/controller/perm_dossiers.php`; `PermDossierVerFormData` expone `go_to_link_spec` y `hash_config` y el `HashFront` se instancia en `frontend/dossiers/controller/perm_dossier_ver.php`; `DossierTipoPublicUrls::formControllerLinkSpec` en los `Select_*` que enlazan al form dossier + `frontend/dossiers/helpers/DossierTipoFormLinkSpecsSigning.php` (`HashFront::link` al renderizar `getHtml()`); helper genérico reutilizable `frontend/shared/security/HashFrontSignedLink.php` (`fromSpec`, `fromSpecMap`, `signRowLinkSpecs`).

**Inventario — `Hash::link` aún presente en `src/` (pendiente de alinear con esta directiva):**

| Área | Archivo |
|------|-----------|
| menus | `frontend/menus/controller/menus_importar_de_ficheros_a_ref.php` (ruta HTTP `/src/menus/menus_importar_de_ficheros_a_ref`) |

Actualizar esta tabla conforme se migre cada módulo (o sustituir por enlace a `rg` en el PR si se prefiere no duplicar).

### Ejemplo práctico: módulo ubiscamas

```text
frontend/ubiscamas/
  controller/
    habitacion_form.php    ← Prepara datos para el formulario
    cama_form.php         ← Prepara datos para el formulario
    lista_habitaciones.php ← PostRequest a `actividad_habitaciones_lista`; convierte `*_link_spec` con `HashFrontSignedLink`
  helpers/
    SelectHabitacionesCdcUrlSigning.php
    SelectHabitacionesCdcRender.php ← Bloque dossier habitaciones (`HashFront` + `select_habitaciones_cdc.phtml`)
    UbiscamasFormHashCompose.php ← `HashFront` para `habitacion_form` / `cama_form` (datos desde `HabitacionFormData` / `CamaFormData`)
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
    SelectHabitacionesCdc.php ← `getSegmentData()` (sin `HashFront`; render en frontend)
  infrastructure/
    ui/http/controllers/
      actividad_habitaciones_lista.php ← `link_spec` + `HashB` (`ctx` para update cama / solo VIP); sin `HashFront`
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
- **$oPosicion**: Objeto principal para gestionar el historial de navegación y la persistencia de parámetros entre páginas. Se define en `frontend\shared\web\Posicion` y se instancia en `frontend/shared/global_header_front.inc`. Usa `$_SESSION['position']` como backing store.
- **js_atras(n)**: Método fundamental para retornar `n` pasos en el historial. Genera el código JS necesario para la navegación.
- **addParametro($key, $valor, $fila)** / **setParametros($aVars, $fila)**: Permiten persistir datos en una fila concreta de la pila. Si `$fila = 1`, el parámetro se guarda en la posición anterior, facilitando su recuperación al volver atrás. `setParametros` además persiste en sesión (`guardar()`).

### Responsabilidad exclusiva del frontend
`$oPosicion` y `$_SESSION['position']` son responsabilidad exclusiva de `frontend/`:

- `src/domain/` y `src/application/` **no pueden** importar `frontend\shared\web\Posicion` ni tocar `$_SESSION['position']` (ni directa ni indirectamente).
- La única ubicación autorizada que accede a `$_SESSION['position']` es `frontend/shared/web/Posicion.php`.
- Si un builder/caso de uso en `src/` necesita un valor derivado del historial (clave de stack, parámetros restaurados, etc.), el controller frontend lo resuelve con `$oPosicion` y lo pasa como `$input` al builder (p. ej. `stack_actual`, `restored_id_sel`, `restored_scroll_id`).
- El HTML de navegación (`js_atras`, `mostrar_left_slide`, `mostrar_back_arrow`) se emite desde vistas `.phtml`/`.twig` en `frontend/` o desde `src/.../view/*.phtml` **recibiendo `$oPosicion` como parámetro de vista**, nunca generado desde `domain/`/`application/`.

### Patrón canónico en controllers frontend
```php
// frontend/<modulo>/controller/<pagina>.php
require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar((int)filter_input(INPUT_POST, 'refresh'));

$campos = array_merge($_GET, $_POST);

// Resolver aquí cualquier estado de navegación que el builder necesite:
$stackFromPost = isset($campos['stack']) ? (string) filter_var($campos['stack'], FILTER_SANITIZE_NUMBER_INT) : '';
if ($stackFromPost !== '' && $oPosicion->goStack($stackFromPost)) {
    $campos['restored_id_sel']    = $oPosicion->getParametro('id_sel');
    $campos['restored_scroll_id'] = $oPosicion->getParametro('scroll_id');
    $oPosicion->olvidar($stackFromPost);
}
$campos['stack_actual'] = $oPosicion->getStack(0);

// El builder en src/ recibe datos planos; no toca sesión.
$data = PostRequest::getDataFromUrl('/src/<modulo>/<endpoint>', $campos);
```

### Estrategia de Persistencia Híbrida
Para una experiencia de usuario fluida, combinamos el estado del backend con el estado del frontend:

1.  **Estado de navegación ($oPosicion en frontend)**: Gestiona la jerarquía de páginas, IDs principales (como `id_activ`) y la lógica de "volver". Vive sólo en `frontend/`.
2.  **Estado Frontend (SessionStorage)**: Gestiona el estado volátil de la UI (scroll, selección). **Ver detalles en** `frontend/agents.md`.

### Permisos de actividad en sesión (`PermisosActividades`) — caché vs. backend

La clase **`src\permisos\domain\PermisosActividades`** nació para **tener en sesión una matriz de permisos** (reglas por `id_tipo_activ_txt`, DL propia / otras delegaciones) cargada desde `aux_usuarios_perm`, de modo que las vistas puedan **consultar permisos sin nueva ida a BD** para cada pintado.

**Separación objetivo (evolución recomendada):**

1. **Read model en sesión (solo datos ya cargados)**  
   Lo que pertenece a la sesión es la **matriz cacheada** (`aPermDl`, `aPermOtras` y la lógica que recorre tipos / fases ref **solo con esos arrays**). Idealmente sería un tipo dedicado (p. ej. *matriz* o *snapshot*) serializable, sin métodos que asuman `$GLOBALS['container']` ni `$GLOBALS['oDBE']` en requests que solo pasan por `frontend/shared/global_header_front.inc`.

2. **Resolución con I/O en el backend (`src/` + petición HTTP)**  
   Cualquier cosa que implique **consultar la actividad por `id_activ`**, **estado de fase en proceso** (`faseCompletada`, etc.) o **árbol de procesos / tipos** (`getPermisoCrear` y similares) es **caso de uso de aplicación / infra**: debe ejecutarse en un script bajo `global_object` (o endpoint JSON) y, desde controladores **`frontend/`**, llegar como **`PostRequest::getDataFromUrl`** o datos ya incluidos en un DTO grande (p. ej. ampliar `actividad_ver_datos`), no llamando al contenedor desde un objeto de sesión.

3. **Transición práctica**  
   Mientras la clase siga siendo monolítica, los controladores frontend deben **pasar contexto ya conocido** (`id_tipo_activ`, `dl_org`) cuando el backend ya lo devolvió por JSON, y migrar gradualmente las ramas que hoy usan repositorios dentro de la clase hacia servicios o `*_datos.php` dedicados.

   **Fases de proceso (permisos on/off):** usar **`/src/actividades/actividad_fases_completadas_datos`** (`id_activ` → `fases_completadas`) antes de `getPermisoActual` / `getPermisoOn` en flujos solo-frontend (p. ej. `PrefillPermActividadesFases::desdeBackend`). Consulta unitaria equivalente a `faseCompletada`: **`/src/actividades/actividad_fase_completada_datos`** (`id_activ`, `id_fase` → `completada`).

### Seguridad (Hash.php)
Cuando se añaden campos de estado en el frontend (ej: `<input type="hidden" name="scroll_id_...">`), estos campos deben excluirse de la validación del hash para evitar errores de "Hash mismatch".
- Modificar `web\Hash::isValid()` para ignorar prefijos específicos (como `scroll_id_`).
- Quién **firma** URLs hacia `frontend/` no debe ser `src/application` ni `src/domain`: ver la directiva **Enlaces firmados hacia la UI** en esta misma sección.

### Pitfalls: salida parcial, JSON y tema CSS (legado)

Patrones que han roto producción (avisos `session_id()` / JSON corrupto / hash POST); útiles al tocar controladores-vista o includes de color:

1. **`ViewNewPhtml::renderizar` y `Select_*::getHtml()`**  
   Si el resultado se concatena, se devuelve como string o alimenta un pipeline JSON, usar **`renderizar(..., false)`** y `return` del HTML. Un `echo` intermedio mezcla cuerpo de respuesta, rompe `Content-Type: application/json` y puede hacer que un `include` posterior ejecute código de bootstrap en el momento equivocado.

2. **`PostRequest` y firma**  
   En POST internos desde `frontend/`, revisar que los metadatos de navegación/hash que no pertenecen al formulario destino (p. ej. **`hpos`**) se normalicen en `PostRequest` para que la URL firmada coincida con la que valida el endpoint; si no, errores de hash o redirecciones (302) inesperadas.

3. **Includes de tema después de `echo`**  
   Tras cualquier salida (`mostrar_left_slide`, `echo` previo, etc.), **no** cargar **`global_object.inc`** (ni stack que llame a `session_id()` / rearranque de sesión) desde rutas como **`css/colores.php`** o entrypoints de estilos. Resolver estilo con bootstrap mínimo (p. ej. autoload + lectura de preferencia sin DI pesada, como **`css/colores_estilo_desde_sesion.php`**). Los `.css.php` servidos como recurso **no** deben pasar por `global_header_front.inc` si eso imprime HTML o cierra sesión antes de servir CSS.

4. **`src/` sin `core\` implícito**  
   Clases en `src/domain` (o servicios usados solo vía JSON) no deben extender o importar **`core\...`** que no esté garantizado por el autoload de esa petición; si hace falta un helper, usar uno en `src/shared/domain/helpers/` o un adaptador en `infrastructure`.


## Comunicación Frontend-Backend (AJAX y JSON)

Para la comunicación asíncrona entre las vistas (`.phtml`) y los controladores de lógica del backend:

### Backend (Controladores)
- **Clase estándar**: `web\ContestarJson` para respuestas JSON.
- **Método preferido**: `ContestarJson::enviar($error_txt, $data)` directamente. Forma habitual del payload: el cliente recibe `success`, `mensaje` y `data` con el cuerpo útil.
- Evitar el patrón intermedio `$jsondata = ContestarJson::respuestaPhp(...);` + `ContestarJson::send($jsondata)`; unificar con **`enviar`** (no `send`).
- Los casos de uso en `application` deben devolver datos listos para serializar (arrays/strings) o texto de error, no la respuesta JSON ya montada. Si hay código previo que aún devuelve `respuestaPhp`, puede convivir temporalmente, pero no como patrón para código nuevo.
- **Mutaciones** (eliminar, editar, duplicar, publicar, importar, cambiar tipo, alta, update, etc.): siempre JSON con `{success, mensaje}` aunque no haya payload; nunca cuerpo vacío sin contrato. El JS debe mostrar `mensaje` si `success === false` y refrescar la UI si `success === true`.
- **Prohibido en `src/.../infrastructure/ui/http/controllers`**: `echo` de HTML, `die("msg")`, `print`, respuestas texto arbitrarias. Excepción ya acordada: formularios legacy que lean `.done(rta_txt)` sin JSON (p. ej. rutas tipo `centros_update`); documentar el motivo en el propio fichero.
- **Ubicación**: controladores HTTP bajo `src/<modulo>/infrastructure/ui/http/controllers/` (y análogos).
- No usar `echo json_encode()` ni `exit($msg)` manual para el caso JSON estándar; delegar en `ContestarJson::enviar`.

### Frontend (JavaScript)
- **Llamada**: `$.ajax` con `dataType: 'json'` cuando el endpoint devuelve `ContestarJson`.
- **Patrón de guardado**: evitar `form.one("submit")` + `trigger("submit")` + `off()`; preferir `$.ajax` con `$(formulario).serialize()` (o parámetros explícitos) hacia la URL de la acción (`..._update`, `..._guardar`, …) y manejar la respuesta en `.done(...)`.
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
          alert(<?= json_encode(_("respuesta")) ?> + ': ' + json.mensaje);
      } else {
          // Lógica de éxito (ej: refrescar, volver atrás, alert de guardado)
          alert(<?= json_encode(_("guardado")) ?>);
      }
  });
  ```
- **Validaciones previas**: en cliente, antes de la petición, para evitar viajes innecesarios.

### Patrón de llamada backend desde frontend
Referencia: `frontend/usuarios/controller/usuario_lista.php`.
- URL backend: cadena que empiece por `/src/<modulo>/...` (sin host; `PostRequest` añade `ConfigGlobal::getWeb()`).
- Parámetros: array asociativo; el hash de seguridad lo genera `PostRequest` internamente donde aplique.
- Respuesta: decodificar el JSON; si no se usa un helper que ya trate errores con `exit`, comprobar `success` / `error` según el endpoint.

---

## Migración `apps/` → `frontend/` + `src/` (convivencia y slices)

Guía para seguir moviendo pantallas desde `apps/` hacia `frontend/` + `src/` sin mezclar capas ni romper URLs antiguas; convención de proyecto única en este documento.

### Orden de trabajo
1. **Baseline breve** antes de tocar código: pantalla, parámetros GET/POST, salida HTML o JSON, casos `rstgr` / permisos si aplican. Anotarlo en `documentacion/` (p. ej. `documentacion/<modulo>_migracion_baseline.md`).
2. **Separar capas primero**; refactors finos (SRP, tests unitarios) después de que la pantalla viva en `frontend` + `src`.
3. **Un vertical slice por PR o commit lógico** (una pantalla o un flujo filtro+AJAX), sin mezclar varios módulos.

### Responsabilidades por capa (resumen)

| Capa | Ruta / carpeta | Responsabilidad |
|------|----------------|-----------------|
| Backend API | `src/<modulo>/infrastructure/ui/http/controllers/*.php` | Orquestación HTTP mínima: leer input, llamar a `application`, responder con `ContestarJson::enviar($error, $data)`. Sin `echo` de HTML ni `Lista` aquí. |
| Caso de uso | `src/<modulo>/application/*.php` | Montar arrays de datos usando repositorios/servicios del contenedor. Devolver datos listos para serializar; el controlador HTTP llama a `ContestarJson::enviar`. |
| Rutas HTTP | `src/<modulo>/config/routes.php` | Registrar `/src/<modulo>/<nombre>` con GET y POST si hace falta (compatibilidad). |
| Frontend controlador | `frontend/<modulo>/controller/*.php` | `require_once("frontend/shared/global_header_front.inc")`, `PostRequest::getDataFromUrl('/src/...', $campos)`, construir `web\Lista` u otros componentes UI, pasar variables a la vista. |
| Frontend vista | `frontend/<modulo>/view/*.phtml` | Presentación: HTML, scripts, `mostrar_tabla()`, sin consultas a BD ni contenedor. |
| Compatibilidad legacy | `apps/<modulo>/controller/*.php` | Opcional: `require` al controlador `frontend` equivalente. Comentar que la URL `apps/...` está **deprecada** para enlaces nuevos. |

### Endpoints: un endpoint por acción
- Evitar endpoints multiuso con parámetro dispatcher (`que`, `Qmod`, `salida`, `modo`, …).
- Preferir **un endpoint por acción**: p. ej. `/src/<modulo>/<recurso>_lista`, `_update`, `_eliminar`. Lo mismo para `switch ($Qmod)` internos: cada rama → endpoint + caso de uso (p. ej. `actividad_publicar`, `actividad_importar`, …).
- En `application`, separar clases por acción (`...Lista`, `...Update`, `...Eliminar`) para reducir `switch` y facilitar tests.
- En `frontend`, llamar al endpoint concreto sin enviar campos de acciones no usadas.
- Endpoints legacy con dispatcher: mantener solo como wrapper temporal, marcado deprecado; eliminar cuando `rg` no muestre referencias.
- **Excepción tolerable** (transición): dispatcher que agrupe salidas de lectura muy relacionadas si todas las ramas comparten el contrato JSON y casos de uso independientes en `application`; documentar que es transición.

#### Playbook: eliminar dispatcher `*_ajax` / `*_update`
1. Mapear cada rama del `switch` a un endpoint `/src/<modulo>/<accion>` y un caso de uso en `src/<modulo>/application/`.
2. Actualizar **todos** los consumidores JS en el mismo cambio (`$.ajax` → URL acorde al `mod`/`que`).
3. Ajustar `.done` a JSON estándar (`ContestarJson`) con `dataType: 'json'`.
4. Borrar el dispatcher cuando `rg "<nombre_dispatcher>"` esté limpio.
5. Si una rama devolvía **HTML inline** (`<form>`, `<select>`, tabla, etc.), esa parte va a `frontend/<modulo>/controller/<accion>_form.php` + vista `.phtml`; los `/src/...` no devuelven HTML de aplicación.

### Convención de naming en `src/<modulo>/application/`

| Ubicación | Sufijo / convención | Rol | Ejemplos |
|-----------|---------------------|-----|----------|
| `application/` (raíz) | sin sufijo | Caso de uso público (mutación o lectura compleja). Lo invocan controladores HTTP o builders `*Data`. | `ActaNueva`, `AsignaturasPendientes`, `Select_notas_de_una_persona` |
| `application/` (raíz) | `*Data` | *Data builder*: lecturas + dropdowns → array serializable para `ContestarJson::enviar`. Sin efectos secundarios. | `BuscarActaData`, `NotaPersonaFormData` |
| `application/services/` | `*Service` | Helper compartido entre use cases (SQL repetido, parseo, tablas temporales). No es un caso de uso por sí solo. | `ResumenTempTablesService` |
| `application/support/` | libre | Soporte interno (parsers, policies). | `PersonaNotaInputParser` |
| `application/legacy/` | libre | Bloque heredado grande detrás de wrappers tipados (ver más abajo). | `legacy\Resumen` |

Reglas: no mezclar `*Service` en la raíz de `application/` (mover a `services/` o renombrar a caso de uso); un use case en raíz no debe heredar de `services/`; evitar que un use case de raíz `use` otro use case de raíz en runtime (señal de que uno debería ser helper en `services/`); componer vía controlador HTTP o `*Data`.

### Patrones de referencia en `ubis` (resumen)
- Servicios `*Dropdown` en `src/.../application/services/`: **solo** `array` value => etiqueta; no instanciar `web\Desplegable` en `src`. El `<select>` se monta en `frontend/ubis/view/*.phtml` con `web\Desplegable::desdeOpciones`, etc.
- Lecturas agrupadas en clases `*Data` + controlador HTTP mínimo con `ContestarJson::enviar`; frontend con `PostRequest::getDataFromUrl('/src/ubis/<endpoint>', ...)`.
- Mutaciones: JSON estándar desde `src/`; proxies frontend pueden adaptar errores para AJAX antiguo.
- Respuesta **texto plano** solo donde el consumidor legacy lo exige: p. ej. `centros_update` con `Content-Type: text/plain` y cuerpo desde caso de uso; formularios con `web\Hash` deben usar **URL absoluta** `rtrim(ConfigGlobal::getWeb(), '/') . '/src/ubis/centros_update'` para que el hash coincida.
- Direcciones: reutilizar `DireccionesResolver` donde aplique.

### URLs canónicas y menús
- Enlaces y menús **nuevos**: rutas bajo `frontend/.../controller/....php`.
- Actualizar plantillas de documentación donde existan (`documentacion/Documentacion_Obix/menus.csv`, `proves/aux_metamenus.csv`, seeds SQL de referencia). Bases en producción con paths en BD: planificar `UPDATE` acorde; el repo documenta el destino deseado.

### Migración de vistas y render canónico
- Al migrar un controlador a `frontend/<modulo>/controller`, migrar también la vista a `frontend/<modulo>/view` (`.phtml`); no dejar la vista canónica solo en `apps/<modulo>/view`.
- Patrón de render (p. ej. `encargossacd`, `misas`): en el controlador frontend:
  - `use frontend\shared\model\ViewNewPhtml;`
  - `$oView = new ViewNewPhtml('frontend\\<modulo>\\controller');`
  - `$oView->renderizar('nombre_plantilla.phtml', $a_campos);`
- `ViewNewPhtml` resuelve rutas físicas sustituyendo `controller` por `view` bajo `DOCUMENT_ROOT` + `ConfigGlobal::$web_path`.
- **Twig**: casos excepcionales; si se usa, el loader debe apuntar a un directorio bajo `apps/` donde exista configuración Twig acordada.
- Cuando el frontend renderiza bien, eliminar copia legacy en `apps/<modulo>/view` y actualizar referencias (`grep`, exportaciones, menús). Revisar rutas hardcodeadas `apps/...` en JS/HTML.

### Convención para legacy en `apps/`
- En `apps/<modulo>/controller`, preferir wrappers mínimos que deleguen en `frontend/...`.
- Lógica antigua solo consulta/rollback: prefijo `z...` y aclarar que no son rutas canónicas.
- Rutas nuevas: `frontend/...` (UI) y `/src/...` (API).
- **No tocar** clases `Info*.php` en `apps/<modulo>/model/` (`Info3010`, …): metadatos de dossier (`extends core\DatosInfo`). El sistema las resuelve por número; aunque `rg` no muestre callers estáticos, **no** moverlas ni eliminarlas en refactors de pantalla.

### Bloques heredados: `src/<modulo>/application/legacy/`
Para modelos legacy muy grandes (>~1000 LOC, SQL ad-hoc, tablas temporales) sin valor inmediato de reescritura, **aislar** en `application/legacy/` detrás de wrappers tipados en la raíz de `application/` (ej. `notas`: `Resumen` → wrappers `InformeStgrNumerarios`, etc.).

- El **frontend nunca** hace `use src\<modulo>\application\legacy\...`. Solo `application/` (raíz) conoce el legacy.
- Cada flujo expone un wrapper que recibe input simple, devuelve datos (arrays neutros); si el legacy aún emite HTML, el wrapper puede poner HTML en un slot del array para la vista hasta poder estructurarlo.
- Los wrappers siguen naming de caso de uso (sin `Service`); el bloque pesado vive en `legacy/`.
- **No** considerar deuda que exista `legacy/`; sí lo es usarlo desde fuera de `application/`.
- Elección rápida: legacy pequeño → reescribir en raíz; grande pero separable → partir en use cases + `services/`; grande y específico → `legacy/` + wrappers. Widgets `SelectNNNN` resueltos por `DossierTipoFileSuffixResolver`: ver `src/dossiers/application/DossierTipoFileSuffixResolver`.
- Al mover a `legacy/`, pasada mecánica recomendada: typos, `exit` en constructor → excepción, casts defensivos en SQL si no hay `prepare()`, código muerto, condiciones tautológicas. Reescritura profunda = fase 2 opcional.

### Separación estricta frontend ↔ `src`
- Vistas y controladores frontend **no** instancian `src\...\application\...` ni `use src\...` para lógica de aplicación. Toda obtención de datos vía **`PostRequest`** a `/src/<modulo>/<accion>`.
- Comprobación práctica al migrar: `grep -n "use src\\\\" frontend/<modulo>/` debe dar **cero** resultados salvo contratos de dominio muy estables explícitamente permitidos.
- Incumplimientos detectados en el pasado (corregir así): p. ej. importar `src/` desde controladores frontend en rutas tipo `actividad_tipo_get` → sustituir por endpoint JSON + `PostRequest`.

### Desplegables devueltos por endpoints AJAX
Los controladores `src/...` **no** devuelven HTML de `<select>`; `application` **no** instancia `web\Desplegable`.

**Contrato** (dentro de `data` del JSON, tras parsear si viene serializado): objeto con campos opcionales con defaults:

```json
{
  "id": "campo_select",
  "opciones": { "value1": "Etiqueta 1" },
  "selected": ".",
  "blanco": true,
  "val_blanco": ".",
  "action": "fnjs_algo(false)"
}
```

- `opciones`: mapa value => label (como un `*Dropdown` en `services/`).
- Inyectar: contenedor → `.html(helper)`; si el ancla es el propio `<select>`, usar `.replaceWith(...)` — **no** `$(select).html(innerSelect)` (selects anidados inválidos).

Helper JS típico reusable por vista:

```js
fnjs_construir_desplegable = function (json) {
    if (!json || json.success !== true) { return ''; }
    try {
        var data = typeof json.data === 'string' ? JSON.parse(json.data) : json.data;
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
};
```

**Baseline al refactorizar** un use case que devolvía HTML de desplegable: localizar todos los consumidores (`rg "salida=..."`, `rg "fnjs_..."`), devolver array con este contrato, endpoint con `ContestarJson::enviar('', $payload)` sin envolturas innecesarias `{content:...}`, migrar JS al helper, y solo entonces quitar HTML del backend.

### Tipos y valores procedentes de `$_POST`
- `$_POST` / `filter_input(INPUT_POST, ...)` llegan como **string** o `null` aunque el campo sea numérico.
- Propiedades de casos de uso alimentados por POST: tipos tolerantes (`int|string`, `?string`, …) e inicialización neutra, **o** cast explícito en el controlador HTTP y tipos estrictos en `application`. Elegir una estrategia por caso (evitar `TypeError` en propiedades `int` rellenadas con string).

### Checklist al cambiar el contrato de un endpoint en `src/`
1. `rg "<endpoint o salida>"` en `frontend/`, `apps/`, plantillas, `*.js`.
2. Listar cada `$.ajax` / `.done` y el ancla DOM.
3. Backend + **todos** los consumidores en el mismo cambio.
4. `dataType: 'json'` y manejo de `success === false` con `mensaje`.
5. Vistas/twigs duplicados entre módulos: actualizar todas las copias.
6. `php -l` y prueba manual por consumidor.

### Hash al mover endpoints AJAX (`Hash::getCamposHtml` vs `Hash::linkSinVal`)
- **`Hash::getCamposHtml($aCampos, $aHidden)`**: firma campos del formulario (no la URL). Para **POST** con URL fija.
- **`Hash::linkSinVal($url, $aCampos)`**: firma **URL + nombres de campo**; fragmento para GET/AJAX; cuidado con `?` vs `&` al concatenar.
- Una URL nueva suele implicar un **Hash nuevo** (no reaprovechar el del dispatcher monolítico partido).
- No incluir en `setCamposForm` campos que a veces no viajan.
- Preferir pasar URLs ya construidas desde PHP a la vista (`$a_campos['url_foo']`) para facilitar `rg` y coherencia.
- Modos de formulario distintos (`nueva` / `modificar`): generar dos URLs/hashes en PHP y elegir en JS.

#### Checklist URL + AJAX
1. Nuevo `Hash` en controlador frontend para esa URL.
2. Pasar `url_xxx` en `$a_campos`.
3. En JS: `var url_xxx = '<?= $url_xxx ?>';`
4. `dataType: 'json'` y parseo de `ContestarJson`.
5. Eliminar endpoint huérfano cuando no queden referencias.

### Validación antes de dar por cerrado un slice migrado
- `php -l` en ficheros tocados.
- Comparar salida relevante con el baseline (ids, columnas, cardinalidad).
- Probar con datos y caso vacío si aplica; si depende de ámbito (`rstgr`, etc.), probar ramas o documentar riesgo.

### Qué evitar al migrar pantallas
- No mover lógica de negocio a `.phtml`.
- No hacer que `src` renderice HTML de aplicación: prohibido en `application` y controladores HTTP instanciar `web\Desplegable`, `web\Lista`, `echo`/`print` de marcado, o devolver HTML desde use cases para tablas/listados.
- No instanciar clases de `src/` desde `frontend/controller` ni `frontend/view` salvo excepción documentada: usar `PostRequest`.
- No cambiar un endpoint sin actualizar **a la vez** todos los consumidores JS/PHP.
- No eliminar wrappers `apps/` hasta que no queden referencias (y BD si aplica).

