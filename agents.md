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
          alert(<?= json_encode(_("respuesta")) ?> + ': ' + json.mensaje);
      } else {
          // Lógica de éxito (ej: refrescar, volver atrás, alert de guardado)
          alert(<?= json_encode(_("guardado")) ?>);
      }
  });
  ```
- **Validaciones Previas**: Realizar validaciones de campos obligatorios en el JavaScript antes de iniciar la petición AJAX para evitar viajes innecesarios al servidor.

