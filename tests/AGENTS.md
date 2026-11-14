# Criterios para generar y mantener tests (Orbix)

Este documento recoge convenciones y lecciones aprendidas al añadir o refactorizar tests (sobre todo integración en actividades). Sirve de guía para agentes y personas que escriban pruebas en este repositorio.

## Integración frente a unitarios

- Los casos de uso **muy acoplados** a `$GLOBALS`, contenedor DI, sesión, `ConfigGlobal`, repositorios concretos, `TiposActividades`, permisos o módulos opcionales suelen **no ser buenos candidatos a unitarios finos** sin mocks masivos.
- Tiene sentido **complementar** con **tests de integración / smoke** que ejecuten `ejecutar()` (o el método público relevante) con entrada mínima y comprueben forma del resultado (claves, tipos, ausencia de errores fatales).
- Un test que **solo** comprueba una excepción por validación temprana **no cubre** rutas posteriores (p. ej. generación de IDs, `Guardar`, ramas condicionadas por apps). Añade al menos un caso **feliz** o intermedio que llegue hasta esa lógica.

## Contratos de repositorio y nombres de métodos

- Los repositorios suelen exponer **`getNewId()`** y **`getNewIdActividad(int)`**, no `newId()` / `newIdActividad()`. Desalinear nombres provoca fallos solo en producción o en el camino feliz; conviene **afirmar en test** que el flujo llega a esas llamadas (integración) o mockear la interfaz en unitario.

## Resultados de búsqueda: colecciones vs objeto único

- Métodos como **`matching()`** (p. ej. `ActividadPlazasRepositoryInterface::matching()`) devuelven **lista de entidades**, no un solo objeto.
- El código de aplicación debe: tratar `false`, usar el **primer elemento** si hay filas, o **crear una entidad nueva** con las claves necesarias si la lista viene vacía; **nunca** asumir un objeto directo.

## `findById` y otros retornos `?Entidad` (repos no encuentran fila)

- Muchos contratos declaran **`findById(...) : ?MiEntidad`** (o métodos equivalentes de “una sola fila”). Un **`null`** es válido cuando el id no existe, hay **FK huérfanas** tras borrados (p. ej. actividad con `id_ubi` pero sin casa en `CasaRepository`), datos migrados incompletos, etc.
- El código en `application/` **no debe** hacer **`$repo->findById($id)->metodo()`** sin comprobar: en PHP revienta con *“Call to a member function … on null”*. En endpoints consumidos como JSON suele aparecer como **/HTML de fatal (Xdebug) con status 200**, y los clientes lo reportan como **respuesta no-JSON**.
- Responsabilidades:
  - **Producción:** ramas explícitas, **`?->`** y valores por defecto (`??`), o mensaje de negocio; no asumir entidad cargada sólo porque el FK no es vacío.
  - **Tests:** donde un caso de uso recorra resultados de un repo y en el bucle consulte **`findById` / equivalencias que devuelven `?`** , añadir un **caso negativo controlado**: mockear **`findById` → `null`** en un escenario donde antes el código llegaba sin protección y asertar **ausencia de fatal** y comportamiento estable (lista vacía, campo vacío, log, etc.). Así las regresiones no dependen sólo de reproducir datos rotos en integración.

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

- En `src/` de negocio ya no se usa `$GLOBALS['container']` (los repositorios llegan por constructor), pero el riesgo se mantiene allí donde un repositorio se resuelve en runtime (`DependencyResolver::get(...)`, código legacy) o se llama sin tipo estático: si un `application/` obtiene un repositorio con `$GLOBALS['container']->get(FooRepositoryInterface::class)`, la variable queda **tipada como `object`** por el contenedor. PHP **no detecta typos** en el nombre del método hasta que la rama concreta se ejecuta. Ejemplo real: `SacdAusenciasJefeZonaData` llamaba `$ZonaSacdRepository->getSacdsZona(...)` cuando el método del contrato se llama `getIdSacdsDeZona(...)`. El fallo solo aparecía cuando el usuario **era jefe de una zona con sacds**, rama que no se recorría nunca en los tests existentes.
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
  - En `setUp`, guardar `$GLOBALS['container']` y `$_SESSION` previos; en `tearDown`, restaurarlos (`unset` si eran `null`). **No es opcional:** `backupGlobals` está desactivado, así que un `$_SESSION['session_auth']['esquema']` que se quede sucio hace que el siguiente test de `myTest` abra **todas** las PDO del proceso contra ese esquema (se abren una sola vez por proceso PHPUnit) y el resto de la suite cae con errores sin relación aparente, tipo `relation "x_config_schema" does not exist`.
  - Montar **`$_SESSION['session_auth']`** mínimo con las claves que realmente lee el código bajo prueba (p. ej. `id_usuario`, `esquema`, `sfsv`, `idioma`). No copiar el setup completo de `myTest`.
  - Para `DateTimeLocal::getFormat()` basta con **`$_SESSION['session_auth']['idioma'] = 'ca'`** (o similar): evita tener que construir `$_SESSION['oConfig']`.
  - Usar un **contenedor anónimo** con `get(string $id)` que lance `RuntimeException` si se pide una clase no mapeada: así cualquier dependencia olvidada se ve al instante.
- Esto mantiene el test **rápido** (sin PDO, sin views, sin refresco de matviews) y **determinista**, que es el objetivo de los unitarios donde compensa.

## Localizadores estáticos y clases `final`

- Hay casos de uso que resuelven dependencias con un localizador estático en lugar de recibirlas por constructor: `Persona::findPersonaEnGlobal()` (→ `PersonaFinderService`), `Ubi::NewUbi()` (→ `UbiFactory` → `CentroRepositoryInterface` / `CasaRepositoryInterface`), `DossierTipoPublicUrls`, `DependencyResolver::get(...)`. Un mock del constructor no los alcanza.
- Patrón: en un test que extiende `myTest`, sustituir el servicio en el contenedor con `$GLOBALS['container']->set(X::class, $doble)` y **reponer el original en `tearDown()`**. El trait `Tests\support\SustituyeServiciosDelContenedor` lo hace (`sustituirServicio()` / `reponerServicios()`). Para lo que se crea con `DependencyResolver::make()` (repositorios que reciben el esquema como parámetro) `get()` no sirve: usar `sustituirDefinicion()`, que repone al final la definición del `dependencies.php` del módulo. Si no se repone, los tests siguientes del mismo proceso reciben el doble.
- Las clases `final` (`UbiFactory`, `CentroNombreResolver`, `ProfesoresDesplegableData`, `EditarPersonaNota` y las clases de soporte de notas, `UbiRepositoryResolver`…) no se pueden simular: construir **la real** con sus repositorios simulados. Así se ejerce además su lógica. Para `UbiRepositoryResolver` hay una fábrica: `Tests\unit\ubis\support\UbiResolverFactory`.

## Aserciones sobre `Criteria`

- Para comprobar qué consulta hace un caso de uso, capturar el `Criteria` en el `willReturnCallback` del mock y describirlo con `Tests\support\DescribeCriteria::filtros($c)` / `::orden($c)` (devuelven `["campo operador valor", …]` y `["campo asc", …]`).
- Afirmar la lista **completa** con `assertSame` (filtros y orden), no solo `assertContains` de un filtro: así se detectan filtros que sobran, que faltan o que cambian de operador.

## Lo que un repositorio simulado no detecta

- Un mock acepta cualquier valor en un `Filter`. Dos bugs reales de la fase 1 pasaban todos los tests con mocks y fallaban en producción:
  - `Filter::in(PersonaField::NivelStgr, NivelStgrId::getArrayNivelStgrOn())` pasaba las **etiquetas** («Bienio»…) y no los ids: error SQL `invalid input syntax for type integer`. Los `getArrayXxx()` de los VO devuelven `id => etiqueta`; para filtrar, `array_keys(...)`.
  - Un `switch` comparaba `getNivel_stgr()` (entero) con las letras `'r'` / `'n'` del modelo antiguo: nunca coincidía.
- Reglas:
  - En los datos del test usar **los valores y tipos de producción** (constantes del VO, `NivelStgrId::R`, no `'r'` ni `4` suelto), para que una comparación con literales antiguos falle en el test.
  - Si un `matching()` recibe valores calculados (de un VO, de un mapa, de la sesión), añadir **un caso contra el repositorio real** del contenedor que solo compruebe que la consulta se ejecuta (ejemplo: `CaPosiblesDataTest::test_la_consulta_de_numerarios_funciona_contra_el_repositorio_real`).

## Resultados que se combinan de varias fuentes

- Si el caso de uso junta listas de dos repositorios (publicadas + de la dl, dl + otras regiones…), el test debe devolver **filas en las dos** y comprobar que salen todas. Unir listas con `+` conserva solo las claves de la izquierda y pierde las filas de la derecha con el mismo índice (bug real en `CaPosiblesData`); en código, `array_merge`.

## Tokens `HashB` en la respuesta

- Los casos de uso devuelven cápsulas firmadas (`ctx_editar`, `ctx_eliminar`, `ctx_update`…). No basta con comprobar que la clave existe: abrirla con `HashB::open($token, 'accion')` y afirmar el payload exacto (ids y acción). Un token firmado para otra acción o con otro id pasa desapercibido si solo se mira que no esté vacío.

## `ConfigSnapshot` en la sesión

- El constructor tiene 15 argumentos posicionales y es fácil equivocarse de posición (el 1.º es `gesCalendario`, el 7.º `ambito`, el 8.º y 9.º `notaCorte` / `notaMax`, el 11.º `idiomaDefault`, el 14.º y 15.º los cursos stgr y crt). Un valor en la posición equivocada no da error: el test prueba otra configuración. Preferir **argumentos con nombre** (`new ConfigSnapshot(gesCalendario: 'central', ...)`) o copiar una construcción que ya funcione, y guardar/restaurar `$_SESSION['oConfig']`.

## Caracterizar comportamiento incorrecto (hallazgos)

- Cuando un test destapa un comportamiento erróneo que no se corrige en ese momento, **fijarlo tal como es** con un nombre que lo describa (`test_..._se_compara_con_el_texto_entrecomillado`) y un docblock que empiece por `Hallazgo:` y explique qué debería pasar. Así el bug queda localizable con `rg "Hallazgo:" tests/` y, al corregirlo, el test falla y obliga a revisarlo.
- La corrección va en un commit `fix(<modulo>)` aparte que cambia ese test (y lo mueve de «hallazgos» a «bugs corregidos» en el plan).
- Para fijar un error fatal (`$lista[0]` sobre lista vacía, método sobre `null`): `$this->expectException(\Error::class);` y llamar con `@` para silenciar el warning previo.
- Comprobar que el test del arreglo **falla sin el arreglo** (p. ej. `git stash -- <fichero>` y volver a ejecutar) antes de dar el fix por cubierto.

## Tests en `tests/unit` que llegan a la BD

- Algunos casos de uso tocan la BD aunque sus repositorios estén simulados (p. ej. `AsignaturasPendientes` crea tablas temporales y consulta `e_notas` con el PDO global). Si el test extiende `myTest` y llega a esa SQL, usar **ids que no pueden existir** (negativos tipo `-999000001`) y no escribir nada. Si tiene que escribir, es un test de integración: va a `tests/integration`, con datos fijos y limpieza en `finally`.
- Contraejemplo: `Tests\unit\notas\notasTest::test_modificar_nota_dl_persona_otra_region_desde_dl_stgr` escribe notas con datos aleatorios del factory sobre la BD real y falla de forma intermitente («Ya existe esta nota»).

## No cambiar la conexión de un repositorio del contenedor

- `$GLOBALS['container']->get(...)` devuelve **la misma instancia** a todos los tests del proceso. Un `setoDbl()` sobre ella hace que todos los tests siguientes lean y escriban en ese otro esquema.
- Caso real: `notasTest` cambiaba la conexión del repositorio de notas para comprobar el esquema de Galbel; el test siguiente buscaba duplicados en las notas reales de esa persona y fallaba de forma intermitente («Ya existe esta nota») cuando la asignatura aleatoria coincidía con una aprobada.
- Si solo se necesita para consultar en el test: `$repo = clone $GLOBALS['container']->get(X::class); $repo->setoDbl(...)`.
- Si el caso de uso tiene que ver la conexión cambiada a través del contenedor: guardar `getoDbl()` en `setUp()` y restaurarla en `tearDown()` (ejemplo: `BuscaPersonaNotasTrait::guardarConexionNotasDl()` / `restaurarConexionNotasDl()`).
- Datos aleatorios de factory sobre personas reales: limpiar antes **y** después de cada test con una marca propia de la factory (p. ej. `id_activ` fijo), no solo al final del test.

## Tests-guardia globales (recorren todo `src/`)

Fallan en cuanto un fichero nuevo o tocado rompe una regla del proyecto; si un caso es una excepción legítima, se añade a su lista **con el motivo**, no se relaja el test.

- `tests/integration/shared/infrastructure/ControladoresConTokenSinTokenTest`: todo controlador de `src/` que abre una cápsula `HashB`, llamado sin cápsula, responde JSON «Operación no autorizada» sin cortar la petición. Los de cápsula por fila u opcional (`CAPSULA_NO_OBLIGATORIA`) solo tienen que responder JSON.
- `tests/integration/shared/infrastructure/ControladoresLecturaSinParametrosTest`: todo controlador de lectura de `src/` (sin cápsula `HashB` y sin nombre de acción que escribe) llamado con el POST vacío responde JSON, sin excepciones ni avisos de PHP. Si falta un parámetro obligatorio: `ContestarJson::enviar(sprintf(_("Faltan parámetros: %s"), 'nombre'), 'none')`.
- `tests/unit/shared/infrastructure/ControladoresSinExitTest`: ningún controlador de `src/` usa `exit`/`die` (en el despacho en proceso de `PostRequest` cortaría al frontend que llama), salvo las descargas de binarios y los casos de `PERMITIDOS`.
- `tests/unit/shared/domain/DatosCamposMetodosTest`: en las entidades con `getDatosCampos()`, el getter y el setter de cada `DatosCampo` existen (si no, la pantalla genérica de tablas da un error fatal al abrirla).
- `tests/unit/usuarios/infrastructure/RecuperacionAccesoGuardTest`: las páginas y endpoints de recuperación de acceso no toman `url_base` de la petición, no exigen ni abren sesión (`FrontBootstrap::boot()`, `session_start`, `$_SESSION`) y abrir un enlace (GET) solo pide confirmación.
- `NombresPermisoTest` (permisos): los nombres de oficina que se pasan a `have_perm_oficina()` / `permOficina()` existen.

## Escapar el HTML que se monta en `src/`

- Si un caso de uso o servicio de `src/` monta HTML (listados legacy, correos), los textos de la BD que escriben los usuarios van con `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`. Los nombres de centro (`UbiNombreText`) y de persona (`PersonaTextoChars`) tienen lista blanca sin `<`/`>`; observaciones, descripciones, lugares, cartas de presentación y similares **no**. Añadir un test que meta `<script>` en el texto y compruebe que sale escapado (ejemplos: `CartasPresentacionListaDataEscapadoTest`, `PropuestasEncargosUbiHtmlTest`).

## Fechas que no escribe el usuario

- `DateTimeLocal::createFromLocal()` usa el formato del **idioma del usuario** (en inglés `n/j/Y`). No sirve para textos con formato fijo (ISO de la BD, `j/m/Y` de la BDU): con otro idioma cambia día y mes o desborda a otro año. Para esos textos, parsear el formato conocido (ejemplos: `PersonaBDU::getFechaNacimientoVo()`, `SincroDB::fechaIncorporacion()`) y probarlo con la sesión en `en_US`.

## Los tests escriben en la BD de desarrollo: no pisar filas reales

Los tests de integración crean filas, las guardan y las borran en la BD de desarrollo. `Guardar` actualiza la fila si el id ya existe, así que **un test que use un id que ya está en la tabla sobrescribe una fila real y, al limpiar, la borra**. Pasa de dos maneras:

- **Secuencia por detrás de los ids reales.** `comun` y `sv-e` son réplicas y al restaurarlas las secuencias quedan sin ajustar (casi todas en 1): `getNewId()` devuelve ids que ya existen. Tras restaurar hay que poner cada secuencia al máximo de su tabla.
- **Ids fijos** en la factory o en el test que coinciden con datos reales.

En noviembre de 2026 se perdieron así, sin que fallara ningún test, diseños de procesos, usuarios, preferencias y avisos de la BD de desarrollo (hubo que recrearla). Por eso:

- **Lanzar PHPUnit con `php tools/qa/phpunit_protegido.php <ruta>`** (mismos argumentos que PHPUnit; `--comando "<orden>"` para envolver otra cosa, p. ej. los specs de Playwright). No ejecuta nada si hay secuencias atrasadas en `comun` / `sv-e`, cuenta las filas de todas las tablas antes y después, y termina con código 1 si alguna tabla ha perdido filas. Además calcula una huella del contenido de cada tabla y lista (como aviso) las que han cambiado sin perder filas: ahí se ve una fila real sobrescrita o un resto de prueba. La huella cuesta unos 50 s; `ORBIX_HUELLA=0` la omite.
- **Secuencias que no son el valor por defecto de ninguna columna**: el id de usuarios y grupos es «4» / «5» seguido de `aux_grupos_y_usuarios_id_usuario_seq`; tras restaurar queda en 1 y cada alta de usuario de un test pisaba a un usuario real. La comprobación previa la incluye, y los repositorios de usuarios, grupos y encargos se saltan los ids en uso.
- Un test que ejecuta de verdad un caso de uso con el usuario de la sesión (un usuario real) puede cambiarle datos: p. ej. el listado de actividades de sr guarda la búsqueda como preferencia. Guardar el valor anterior y reponerlo en un `finally`.
- En un test nuevo, el id de una fila propia sale de `getNewId()` (o de un rango que no pueda existir) y se comprueba que no existe antes de guardar. No usar ids «pequeños» escritos a mano.
- Un test que trabaja sobre una persona **real** y vacía sus filas antes de crear las de prueba (traslados de notas) usa el trait `Tests\support\ConservaFilasDePersona`: guarda las filas reales antes de vaciar y las repone al acabar la clase.
- Las purgas de la propia aplicación que los tests ejecutan de verdad salen como **aviso**, no como fallo: el generador de avisos borra los cambios de más de un año (`av_cambios*`) y «nuevo curso» borra los procesos de actividades que no existen (`a_actividad_proceso_*`; en dev, las de delegaciones no cargadas).
- Si un módulo deja una fila de prueba sin limpiar y otro la borra, el segundo aparece como si hubiera perdido una fila: comprobar si la tabla coincide con la copia antes de reponer nada.
- Si la comprobación falla: reponer las filas desde las copias (`~/trasvases/*.sql`) y corregir el test que las borró.
- **Módulos que no están instalados en la dl de la sesión de los tests** (`H-dlb`): no crear sus tablas en esa dl. Para ubiscamas (habitaciones y camas), el trait `Tests\support\UbiscamasEnDlInstalada` pone en el contenedor repositorios conectados a la dl que sí lo tiene (`H-dlmE` por defecto; `ORBIX_TEST_ESQUEMA_UBISCAMAS` para otra) y salta el test si no está.

## Ejecutar PHPUnit

- PHPUnit está en **`libs/vendor/bin/phpunit`** y usa `phpunit.xml`; se le pasa la ruta al fichero o directorio de tests. Con PHP y BD en Docker se ejecuta dentro del contenedor PHP, p. ej. `docker exec -w /var/www/orbix <contenedor-php-fpm> php -d xdebug.mode=off libs/vendor/bin/phpunit -c phpunit.xml tests/unit/<modulo>`.
- **Suite completa:** necesita más memoria que los 128 MB por defecto del contenedor (se corta hacia la mitad con `Allowed memory size … exhausted`). Añadir `-d memory_limit=1G`. Tarda unos 2–3 minutos (≈ 9000 tests).
- **`phpunit.xml` tiene `stopOnFailure="true"`**: se para en el primer fallo y PHPUnit 11 no tiene opción para desactivarlo. Para ver todos los fallos de una vez, copiar la configuración con `stopOnFailure="false"` a un fichero temporal dentro del repo (el contenedor solo ve el repo), pasarlo con `-c` y **borrarlo después**.
- **Playwright (E2E)** en **`e2e/`**: servidor Orbix por URL configurable; sin credenciales solo smoke del formulario de login; con `E2E_USER`/`E2E_PASSWORD` se recorren enlaces de `/index.php` vía GET. Detalle en **`e2e/README.md`**. Comando: `npm run test:e2e` (antes `npm run test:e2e:install-browsers` una vez).
