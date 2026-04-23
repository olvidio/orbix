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
