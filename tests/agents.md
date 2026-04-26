# Criterios para generar y mantener tests (Orbix)

Este documento recoge convenciones y lecciones aprendidas al añadir o refactorizar tests (sobre todo integración en actividades). Sirve de guía para agentes y personas que escriban pruebas en este repositorio.

## Integración frente a unitarios

- Los casos de uso **muy acoplados** a `$GLOBALS`, contenedor DI, sesión, `ConfigGlobal`, repositorios concretos, `TiposActividades`, permisos o módulos opcionales suelen **no ser buenos candidatos a unitarios finos** sin mocks masivos.
- Tiene sentido **complementar** con **tests de integración / smoke** que ejecuten `ejecutar()` (o el método público relevante) con entrada mínima y comprueben forma del resultado (claves, tipos, ausencia de errores fatales).
- Un test que **solo** comprueba una excepción por validación temprana **no cubre** rutas posteriores (p. ej. generación de IDs, `Guardar`, ramas condicionadas por apps). Añade al menos un caso **feliz** o intermedio que llegue hasta esa lógica.

## Contratos de repositorio y nombres de métodos

- Los repositorios suelen exponer **`getNewId()`** y **`getNewIdActividad(int)`**, no `newId()` / `newIdActividad()`. Desalinear nombres provoca fallos solo en producción o en el camino feliz; conviene **afirmar en test** que el flujo llega a esas llamadas (integración) o mockear la interfaz en unitario.

## Resultados de búsqueda: colecciones vs objeto único

- Métodos como **`getActividadesPlazas()`** están tipados como **`array|bool`**: devuelven **lista de entidades** (o `false` en error), no un solo objeto.
- El código de aplicación debe: tratar `false`, usar el **primer elemento** si hay filas, o **crear una entidad nueva** con las claves necesarias si la lista viene vacía; **nunca** asumir un objeto directo.

## Apps instaladas: `ConfigGlobal::is_app_installed()`

- Si el comportamiento depende de si una app está instalada, el test debe cubrir **explícitamente los dos casos**: **instalada** y **no instalada**.
- Patrón recomendado:
  - **`@dataProvider`** con filas del estilo `yield 'no instalada' => [false, …]` y `yield 'instalada' => [true, …]`.
  - **Guardar** `$_SESSION['config']['a_apps']` y `$_SESSION['config']['app_installed']` antes de mutar y **restaurar en `finally`**.
  - Usar **IDs de app ficticios distintos** por app al inyectar en `app_installed`, para no colisionar entre tests.

### App `procesos` y sesión de permisos

- Tras cambiar si `procesos` está instalado, la sesión debe alinearse con lo que hace **`Tests\myTest`**: si `procesos` está instalado → `$_SESSION['oPermActividades'] = new PermisosActividades(…)`; si no → `PermisosActividadesTrue`.
- **Guardar** la referencia anterior de `$_SESSION['oPermActividades']` y **restaurarla en `finally`** junto con la configuración de apps.

## Bootstrap de sesión (integración)

- Si el código usa **`ConfigGlobal::MiUsuario()`**, la sesión de test debe incluir **`$_SESSION['session_auth']['MiUsuario']`** (p. ej. entidad `Usuario` mínima con `id_usuario` / `id_role`), no solo `id_role` suelto en el array.
- Si el código usa **`$_SESSION['oPerm']->have_perm_oficina()`** y la app `menus` no está configurada en la sesión de test, hace falta un **stub** de `oPerm` (objeto con `have_perm_oficina` que devuelva `false` o lo que defina el escenario), para evitar warnings y fatals.

## Fechas y tipos en actividades

- **`DateTimeLocal::createFromLocal()`** espera fechas en **formato local** según sesión (p. ej. `j/n/Y` con `/`), no necesariamente `Y-m-d`. En tests de integración que lleguen hasta el parseo, usar cadenas válidas para ese formato.
- **`ActividadTipoId`**: **6 dígitos**. El **primer dígito** se usa con **`ConfigGlobal::mi_delef($isfsv)`** para alinear **`dl_org`** (sv vs sf / sufijo `f`). Los datos de test deben ser **coherentes** (tipo + `dl_org` + sesión).

## Value objects sin `__toString()` (casts implícitos)

- **`TimeLocal`** y **`DateTimeLocal`** heredan de `\DateTime` y **no** implementan `__toString()`. Un cast directo como `(string)$oActividad->getH_ini()` **revienta en runtime** con `Object of class TimeLocal could not be converted to string` — el tipado PHP no lo detecta, solo se ve al ejecutar la rama.
- Patrón correcto (usado en `ActividadesDePersonaService`): **`$oActividad->getH_ini()?->format('H:i')`** antes de cualquier concatenación o cast.
- Cuando un test mockea entidades (`ActividadAll`, `EncargoSacdHorario`, ...) con VOs reales como h\_ini, **devolver instancias reales** (`TimeLocal::fromString('10:30:00')`, `new DateTimeLocal('2030-02-15')`), **no strings**: así el test ejerce la conversión tal como ocurre en producción.

## Cobertura de rutas "felices" en servicios con repos agregadores

- Si un servicio recorre listas provenientes de un repo (p. ej. `getAsistenteCargoDeActividad`, `getActividades`, `getEncargoSacdHorarios`) y los tests dejan esos repos con stubs por defecto, el **bucle interno nunca se ejecuta**. Toda la lógica de formateo (casts, construcción de arrays de salida, llamadas a `new TiposActividades`) queda **sin cubrir** aunque el servicio "pase" todos los tests.
- Regla: al testear un servicio que itera sobre resultados de repositorio, incluir **al menos un caso** que devuelva **una fila real** con todos los campos poblados con sus tipos de producción (VOs, no strings), y **aserciones sobre la forma concreta** del item de salida (p. ej. `'h_ini' => '10:30'`).
- Si el bucle llama a **`new TiposActividades($id_tipo_activ)`** directamente, recordar:
  - Registrar **`TipoDeActividadRepositoryInterface`** en el contenedor (stub), porque el constructor lo pide del container.
  - Usar un `id_tipo_activ` **válido** (6 dígitos coherentes con `aSfsv` / `aAsistentes` / `aActividad1Digito`), p. ej. `141` (sv + s + crt). Un id inválido hace fallar `getAsistentesText()` con `TypeError: Return value must be of type string, null returned` — ruido que despista del bug real.

## Repos resueltos por `$GLOBALS['container']->get(...)` y typos de método

- Cuando un `application/` obtiene un repositorio con `$GLOBALS['container']->get(FooRepositoryInterface::class)`, la variable queda **tipada como `object`** por el contenedor. PHP **no detecta typos** en el nombre del método hasta que la rama concreta se ejecuta. Ejemplo real: `SacdAusenciasJefeZonaData` llamaba `$ZonaSacdRepository->getSacdsZona(...)` cuando el método del contrato se llama `getIdSacdsDeZona(...)`. El fallo solo aparecía cuando el usuario **era jefe de una zona con sacds**, rama que no se recorría nunca en los tests existentes.
- Regla: para cada servicio de `application/`, añade **al menos un test feliz por cada método de repo** que invoque. No basta con la rama de error temprano / parámetro inválido / lista vacía.
- Patrón recomendado en el test:
  - Mockear el repo con **`createMock(FooRepositoryInterface::class)`** y usar **`expects($this->once())->method('nombreDelMetodo')`**. Como `createMock` refleja el contrato, si el servicio llama a un nombre inexistente el test estalla con *"Call to undefined method MockObject_FooRepositoryInterface_...::nombreMal()"*, idéntico a lo que pasaría en producción con la implementación concreta.
  - Tipar los stubs de entidades del grafo (`Zona`, `PersonaSacd`, ...) con `createStub(Clase::class)` para asegurar que los getters del service existen realmente en la entidad.
- Control negativo: si un servicio resuelve repos **perezosamente** (solo en ciertas ramas), registra esos repos también en el contenedor (por ejemplo con un `createStub` por defecto) para que el `->get(...)` no lance `RuntimeException` y puedas seguir testando el camino. Si ves el `RuntimeException` del contenedor helper, significa que el servicio toca un repo que aún no has mapeado: añádelo.

## `is_true()` y valores aceptados

- **`src\shared\domain\helpers\is_true($val)`** usa `filter_var(..., FILTER_VALIDATE_BOOLEAN)`. **Reconoce** `'t'`, `'true'`, `'1'`, `'yes'`, `'on'`; **no reconoce** `'si'` (devuelve `null`, que es *falsy*). Si un test necesita forzar la rama "propuesta" o similar pasando un flag tipo `Qpropuesta`, usar **`'true'`** (o `true`), no `'si'`. Pasar `'si'` a una rama controlada por `is_true()` manda el flujo al *else* silenciosamente y el test acaba fallando por una dependencia aguas abajo que parecía no relacionada.

## Limpieza y aislamiento

- Tests que **inserten** en BD: usar nombres o fechas **únicos** donde haga falta; en **`finally`**, borrar actividades / plazas / entidades creadas para no contaminar otras pruebas.
- Al manipular configuración o sesión, **siempre** restaurar estado en **`finally`**.

## Ramas condicionadas por rol de usuario

- Los use cases que discriminan por **rol del usuario en sesión** (`RoleRepository::getArrayRoles()` + `Usuario::getId_role()`) deben tener al menos un test por rama de rol. PHP no detecta typos en nombres de método hasta ejecutar la rama exacta, así que sin un test específico bugs como `Usuario::getCsv_id_pau()` (el getter correcto es `getCsvIdPauAsString()` / `getCsvIdPauVo()`) solo revientan en producción cuando entra un usuario con ese rol.
- Patrón recomendado (unitario, sin `myTest`):
  - Mockear **`UsuarioRepositoryInterface`** para que `findById($id)` devuelva una entidad `Usuario` con `id_role` y los VOs poblados (`setCsvIdPauVo(...)`, etc.).
  - Mockear **`RoleRepositoryInterface`** con `getArrayRoles()` devolviendo `[id_role => 'nombre_rol']`.
  - Inyectar ambos en un **contenedor DI minimal** (ver abajo).
- Añade también un **control negativo** (rol distinto) y un test para **`findById()===null`** que verifique que el código no explota si el usuario no se encuentra.

## Unitarios sobre use cases con `ConfigGlobal` / sesión

- Un use case en `application/` que solo consulta 2–4 repos + `ConfigGlobal::mi_id_usuario()` / `mi_delef()` **sigue siendo unitariable** sin arrancar `myTest` (BD real, DI completo). Patrón:
  - Extender **`PHPUnit\Framework\TestCase`** directamente.
  - En `setUp`, guardar `$GLOBALS['container']` y `$_SESSION` previos; en `tearDown`, restaurarlos (`unset` si eran `null`).
  - Montar **`$_SESSION['session_auth']`** mínimo con las claves que realmente lee el código bajo prueba (p. ej. `id_usuario`, `esquema`, `sfsv`, `idioma`). No copiar el setup completo de `myTest`.
  - Para `DateTimeLocal::getFormat()` basta con **`$_SESSION['session_auth']['idioma'] = 'ca'`** (o similar): evita tener que construir `$_SESSION['oConfig']`.
  - Usar un **contenedor anónimo** con `get(string $id)` que lance `RuntimeException` si se pide una clase no mapeada: así cualquier dependencia olvidada se ve al instante.
- Esto mantiene el test **rápido** (sin PDO, sin views, sin refresco de matviews) y **determinista**, que es el objetivo de la fase "unitarios donde compense" de `refactor.md`.

## Ejecutar PHPUnit

- Desde la raíz del proyecto: **`libs/vendor/bin/phpunit`**, con ruta al fichero o directorio de tests según `phpunit.xml`.

---

*Última actualización: criterios alineados con tests de integración en `tests/integration/actividades/application/`, unitarios de `tests/unit/actividadessacd/application/` y bootstrap en `tests/myTest.php`.*
