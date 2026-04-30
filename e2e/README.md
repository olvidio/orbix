# Tests E2E con Playwright

Comprueban Orbix en **navegador real** (Chrome). No sustituyen PHPUnit: detectan fallos de integración que solo se ven con HTTP, HTML y JS (p. ej. fatals PHP en página 200).

## Requisitos

- Node.js y `npm` instalados.
- El servidor Orbix accesible desde la máquina donde corre Playwright (Docker, local, etc.).
- Instalar dependencias y navegador Chromium (una vez):

```bash
npm install
npm run test:e2e:install-browsers
```

## Variables en `e2e/.env` (recomendado)

Para no estar copiando `export` cada vez:

1. Copia la plantilla: `cp e2e/.env.example e2e/.env`
2. Edita **`e2e/.env`** (está ignorado por git — no lo subas con contraseña real).
3. Formato fichero estándar: `NOMBRE=valor` (**sin** `export`).
4. **`PLAYWRIGHT_BASE_URL` es obligatorio**: sin valor, Playwright no arranca (`get-playwright-base-url.ts`). Debe coincidir con la carpeta hasta donde abres Orbix en el navegador (ej. si ves `…/orbix/index.php`, escribe `http://orbix.docker:8003/orbix`; **da igual si añades o no `/` al final**, el código fuerza **`…/orbix/`** internamente porque sin esa barra, `index.php` relativo resolvería contra el host equivocado por reglas estándar de URL).

5. **No uses rutas Playwright tipo `'/algo'`**: el `/` inicial sustituye todo el pathname del servidor (saltas `/orbix`). Usa rutas relativas sin `/` inicial, p. ej. `page.goto('index.php')`.

Playwright lee ese fichero al cargar `e2e/playwright.config.ts` y antes del login en `global-setup.ts`. Si defines la misma variable en el shell o en CI, **gana lo del entorno** (no se sobrescribe).

Ejemplo minimal en `e2e/.env`:

```
PLAYWRIGHT_BASE_URL=http://orbix.docker:8003/orbix
E2E_USER=tuUsuario
E2E_PASSWORD=tuSecreto
E2E_ESQUEMA=H-dlbv
```

Luego basta:

```bash
npm run test:e2e
```

Docker **sin BDU** pero con credenciales en `e2e/.env`: puedes usar **`npm run test:e2e:docker-skip-bdu-menu`**, que equivale a poner `E2E_SKIP_MENU_LINKS_IF_BDU_UNAVAILABLE=1` (el spec de menús queda omitido sólo ante fallo diagnosticado como BDU).

## Sin credenciales (smoke)

Si **`e2e/.env`** no existe o va vacío de usuario/contraseña, solo se ejecuta el test que comprueba el formulario de login (puedes dejar solo `PLAYWRIGHT_BASE_URL` en `.env` para apuntar al servidor).

También puedes forzar la URL sin fichero:

```bash
PLAYWRIGHT_BASE_URL=http://orbix.docker:8003/orbix npm run test:e2e
```

Ajusta la URL a tu entorno (sin barra final al final de `orbix`).

### El smoke no encuentra `#frm_login`

- **`PLAYWRIGHT_BASE_URL`** debe ser exactamente la carpeta donde navegas a **`index.php`** (p. ej. `http://orbix.docker:8003/orbix`). Sin el segmento `/orbix` a menudo se sirve otra página y no hay formulario de login.
- Tras un fallo, el test deja en la traza la **URL final**, el **HTTP** y un **fragmento del cuerpo** para ver si es un `die()` de PHP («no existe este esquema», error de BD, etc.).

## Con usuario y contraseña (enlaces del menú)

En **`e2e/.env`** (o con `export` en shell) define **al menos**:

- `E2E_USER` — nombre de usuario  
- `E2E_PASSWORD` — contraseña  
- `PLAYWRIGHT_BASE_URL` — igual que arriba  

Opcionales:

- `E2E_ESQUEMA` — si tu login muestra un **desplegable** de esquema (`<select name="esquema">`), aquí va el **`value` exacto** de la opción (en **sv** suele ser el nombre de namespace en Postgres + `v`, p. ej. `H-dlbv`; en **sf**, `…f`). El `global-setup` elige esa opción antes de enviar; antes solo se rellenaba un `input`, así que el combo quedaba en la **primera** opción (p. ej. `Ch-crChfv`) y la sesión no coincidía con `.env`. Si el contenedor define `ESQUEMA` y el formulario lleva `input` hidden, se sigue usando `fill` sobre ese campo.
- `E2E_LOGIN_TIMEOUT_MS` — tiempo máximo (ms) esperando el campo de usuario en `global-setup` **y** una de las señales tras enviar el login (`load`, formulario `#frm_login` oculto o quitado del DOM; por defecto `90000`). El botón de login usa `noWaitAfter` para no bloquearse si la página mantiene red activa.  
- `E2E_2FA_CODE` — si el usuario obliga a 2FA en el test  
- `E2E_MAX_LINKS` — máximo de enlaces a probar con **GET** tras filtros (por defecto `100`)  
- `E2E_LINK_GET_TIMEOUT_MS` — milisegundos por petición GET a cada candidato (por defecto `18000`; antes `55000` ayudaba a agotar todo el tiempo del test).  
- `E2E_STRICT_MENU_LINK_GET=1` — **no** aplica la heurística que omite URLs típicas solo-POST (`*_que.php`, `que_*.php`, `*_select.php`, `preferencias`, `avisos_generar`, ciertas `gestion_`/`fases_`/…); el test es más exhaustivo y mucho más propenso a timeouts.  
- `E2E_MENU_LINK_GET_LOG=1` — en el spec de menú: imprime por consola cada **GET** como `OK código url` o `FAIL …`, y adjunta **`menu-links-get.log`** al informe HTML (`npm run test:e2e` seguido de `npx playwright show-report`; en la prueba fallida/pasada, pestaña *Attachments*).
- `E2E_TRACE=1` — guardar trace Playwright en fallos / depuración  
- `E2E_SCREENSHOT=1` / `E2E_VIDEO=1` — capturas o vídeo (más lento)  
- `E2E_SKIP_MENU_LINKS_IF_BDU_UNAVAILABLE=1` — si el índice autenticado sólo denuncia **falta de BDU** (común en Docker sin ese servicio), el spec `menu-links` queda **omitido** (`skipped`), no fallido. **No** lo uses en entornos donde quieras validar la BDU de verdad (p. ej. staging/CI estricto).  
- `E2E_GRUPMENU_ID` — id numérico del grupmenu (`index.php?id_grupmenu=N`, igual que al pulsar la barra en legacy). Recomendado para fijar p. ej. el grupo «vsm» sin depender del texto visible.  
- `E2E_GRUPMENU_LABEL` — texto del grupmenu como se muestra en la barra (**no** pulses salir ni submenús: es el nombre del *grupo*, p. ej. `vsm`). Solo se usa si **no** pusiste `E2E_GRUPMENU_ID`; en legacy busca `#menu li[onclick*="fnjs_link_menu"]`; en hamburguesa, `#groupMenu a`. Si tu usuario sólo tiene un grupmenu, la barra puede no listar otros — entonces usa el id por URL o ninguno.  
- `E2E_MENU_AJAX_DISCOVER=1` — además del HTML inicial, el spec **pulsa hasta N** enlaces del menú que ejecutan `fnjs_link_submenu` (respuesta AJAX en `#main`), y acumula `href` + `onclick` encontrados dentro de `#main`. Límite: `E2E_MAX_MENU_AJAX_CLICKS` (por defecto 30). **Cuidado:** son POST que pueden crear o modificar datos; usa usuario/entorno de prueba y revisa exclusiones (`eliminar`/`delete`/logout en el `onclick` se saltan parcialmente). Tras cada clic se espera `E2E_MENU_AJAX_SETTLE_MS` ms (por defecto `2500`) en lugar de `networkidle` (más estable). Con este modo activo, el proyecto Playwright **`authenticated`** alarga el **timeout por test** (hasta 15 min según `E2E_MAX_MENU_AJAX_CLICKS`). Sin AJAX, el mismo proyecto usa **120 s** para dar margen al bucle de GET sobre muchos enlaces.  
- `E2E_MAX_MENU_AJAX_CLICKS` — tope de clics de menú cuando `E2E_MENU_AJAX_DISCOVER=1` (máximo 200 en código).  
- `E2E_MENU_AJAX_SETTLE_MS` — milisegundos de espera tras cada clic de menú antes de leer `#main` (por defecto `2500`; sube si en tu red el AJAX tarda más).

Ejemplo equivalente solo con shell (si no usas `.env`):

```bash
export PLAYWRIGHT_BASE_URL=http://orbix.docker:8003/orbix
export E2E_USER=admin
export E2E_PASSWORD='***'
export E2E_ESQUEMA=H-dlbv

npm run test:e2e
```
El proyecto `authenticated`:

1. Ejecuta `global-setup.ts`, hace login y guarda cookies en `e2e/.auth/storage.json` (directorio ignorado por git).
2. Abre **`index.php`**, opcionalmente con **`?id_grupmenu=`** (`E2E_GRUPMENU_ID`) o pulsando el grupmenu por **`E2E_GRUPMENU_LABEL`**, y recoge rutas navegables (enlaces `a[href]` que no sean sólo `#` y URLs en `onclick` / `fnjs_link_submenu`, típico del menú legacy).
3. Tras filtros (mismo origen, sin logout/delete, y **salvo** `E2E_STRICT_MENU_LINK_GET=1`, sin muchos `controller` típicos solo-POST), hace **GET** a cada candidato (hasta `E2E_MAX_LINKS`, timeout `E2E_LINK_GET_TIMEOUT_MS`) y falla si hay **≥400** o si el HTML parece fatal PHP/Xdebug. Con **`E2E_MENU_LINK_GET_LOG=1`** se listan los resultados (`OK` / `FAIL`) y se adjunta un log al informe.

### PostgreSQL: `db:5432` en el error vs `localhost:5444`

Playwright **no** configura la conexión a la base de datos. Si en un fatal PDO ves `host "db"` y puerto `5432`, es el **PHP de Orbix** (p. ej. dentro del contenedor web) usando el hostname del servicio Postgres en **Docker Compose** y el puerto **interno** del contenedor `db`. Eso es lo habitual cuando app y Postgres están en la misma red de compose.

Si en cambio ves **`192.168.x.x`** (u otra IP LAN) o **timeout** contra un host que no existe en tu Docker, ese valor sale de los **`*.inc`** de credenciales (a menudo los de la base **`sf`** / **`sf-e`**: aunque entres en ubicación **SV**, `global_object.inc` sigue creando `ConfigDB('sf')` para ciertos usos). Es un **copiado de otro entorno**, no algo que fije el E2E. Debes alinear `host`/`port` en esos ficheros (o en el volumen que montes) con tu Postgres real (`db`, `host.docker.internal`, etc.).

**`localhost:5444`** en tu máquina suele ser el **mapeo de puertos del host** (`5444→5432` en el contenedor). Sirve para `psql`, DBeaver, etc., desde fuera de Docker; **no sustituye** automáticamente a `db:5432` para el proceso PHP que ya corre dentro de otro contenedor (allí `localhost` sería el propio contenedor web, no el Postgres ni el host).

Para que Orbix use otra BD o puerto hay que cambiar los **ficheros de credenciales** que carga la app (p. ej. `*.inc` vía `ConfigGlobal::getDIR_PWD()` según tu despliegue) o cómo montas variables en **`docker-compose`**, no las variables del E2E.

### Limitaciones conocidas

- El menú principal **legacy** no usa `href` en los ítems: la URL va en `onclick` (`fnjs_link_submenu`). El spec recoge esas rutas además de `a[href]` reales. Opcionalmente, con **`E2E_MENU_AJAX_DISCOVER=1`**, después recorre clics en el menú y vuelca enlaces aparecidos en **`#main`** (contenido cargado por AJAX).
- Sin **`E2E_MENU_AJAX_DISCOVER=1`**, solo se recogen enlaces del **`index`** inicial; submenús que solo muestran URL tras clic no aparecen hasta activar ese modo.
- Los **GET** ciegos a muchos `.php` bajo `controller` no equivalen al flujo Orbix real (POST con hash/cookies): por defecto se **saltan** los patrones más problemáticos; el timeout del proyecto `authenticated` ahora contempla **`E2E_MAX_LINKS` × tiempo de GET** además del bloque AJAX de menú.

### Error «remaining connection slots» / PDO al abrir login

El E2E no arregla Postgres: si el HTML devuelve fatal por **agotamiento de conexiones**, primero estabiliza Docker/Postgres (`max_connections`, procesos colgantes, reinicio de `db`) y vuelve a lanzar `npm run test:e2e`. El `global-setup` detecta estos patrones en el HTML y falla al instante con mensaje orientado a operaciones (no tras 90 s de timeout).

### Error «faltan parámetros para conectar con la BDU» en portada

Si tras login el índice incluye ese texto, la app no está en estado completo para recorrer menús con el spec actual. Puedes corregir la configuración BDU en el stack o, **sólo si aceptas no probar menús en ese entorno**, definir `E2E_SKIP_MENU_LINKS_IF_BDU_UNAVAILABLE=1` en `e2e/.env` para que ese test se marque como **omitido** en lugar de fallido (ver lista de variables arriba).

## UI interactiva (depuración)

```bash
npm run test:e2e:ui
```
