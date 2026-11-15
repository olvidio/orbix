# BD de pruebas y segunda instancia web

PHPUnit y Playwright no trabajan contra las BD de desarrollo sino contra **clones** suyos, que se pueden
regenerar en un minuto. Así un test que pisa o borra una fila no toca los datos de desarrollo, y se puede
seguir trabajando en la instancia de desarrollo mientras corren los tests. Fase 6, paso 6.1, del
[plan de revisión de tests](revision_tests_plan.md).

## Qué hay

| | Desarrollo | Pruebas |
|---|---|---|
| BD (mismo servidor, contenedor `orbix-db-1`) | `comun`, `sv`, `sv-e`, `sf` | `pruebas-comun`, `pruebas-sv`, `pruebas-sv-e`, `pruebas-sf` |
| Web | `http://orbix.docker:8003/orbix` | `http://orbix.local:8003/orbix` |
| Código | el mismo árbol de trabajo (`/var/www/orbix`) | el mismo |
| Ficheros de conexión (`/var/www/conf`) | `comun.conn.inc`, `sv.conn.inc`… | `pruebas-comun.conn.inc`, `pruebas-sv.conn.inc`… |

La aplicación ya tenía un entorno «pruebas»: con `WEBDIR=pruebas`, `ConfigDB` lee los ficheros
`pruebas-<base>.conn.inc` (los roles, `<base>.roles.inc`, son comunes porque los usuarios de PostgreSQL
son del servidor, no de cada BD). Se usa tal cual:

- **PHPUnit**: `tools/qa/phpunit_protegido.php` lo lanza con `docker exec -e WEBDIR=pruebas -e ORBIX_WEB_SERVER=http://orbix.local …` (lo segundo, para que una llamada HTTP hecha desde un test vaya a la instancia de pruebas).
- **Web**: un segundo `server` de nginx (`orbix-pruebas.conf`) con `fastcgi_param WEBDIR pruebas;` y
  `fastcgi_param ORBIX_WEB_SERVER http://orbix.local;` (el servidor con el que la instancia construye
  sus URLs; lo lee `ConfigGlobal::webServer()`). Usa el mismo php-fpm y el mismo puerto; al ser otro
  nombre de servidor, las cookies de sesión de una instancia no valen en la otra.

## Uso

```bash
php tools/qa/phpunit_protegido.php tests/integration/notas          # PHPUnit contra las BD de pruebas
php tools/qa/phpunit_protegido.php --comando "npx playwright test --config=e2e/playwright.config.ts e2e/specs/authenticated/ --grep-invert enlaces"
ORBIX_ENTORNO=dev php tools/qa/phpunit_protegido.php …               # como antes, contra desarrollo (no usar sin motivo)

php tools/qa/bd_pruebas.php estado       # qué clones hay y si conviene regenerarlos
php tools/qa/bd_pruebas.php regenerar    # rehacerlos ya
```

El envoltorio, antes de cada pasada:

1. llama a `bd_pruebas.php asegurar`, que crea los clones que falten y los regenera si hace falta (abajo);
2. ejecuta el test-guardia `ConexionesDelEntornoTest`: **todas** las conexiones tienen que ir a `pruebas-*`
   (si no, no ejecuta nada);
3. cuenta las filas de las BD de pruebas (como antes: falla si una tabla pierde filas) y las de desarrollo.

Al acabar avisa si alguna tabla de **desarrollo** ha cambiado de número de filas (no debería, salvo que
alguien estuviera trabajando en desarrollo a la vez). Si una tabla de pruebas ha perdido filas, el clon
queda marcado y la pasada siguiente lo regenera.

A `--comando` le pasa `PLAYWRIGHT_BASE_URL` (la instancia de pruebas) y `E2E_BD_PREFIJO=pruebas-`, que
`sqlDev()` antepone al nombre de la BD.

## Cuándo se regeneran los clones

`bd_pruebas.php asegurar` regenera un clon cuando:

- no existe;
- el esquema de su BD de desarrollo ha cambiado desde que se clonó (una migración aplicada en desarrollo);
- una pasada lo dejó marcado como dañado;
- tiene más de 15 días (`ORBIX_PRUEBAS_MAX_DIAS`).

Los datos de desarrollo que cambian sin cambiar el esquema no provocan una regeneración: si un test
necesita un dato nuevo de desarrollo, `php tools/qa/bd_pruebas.php regenerar`.

Clonar es `CREATE DATABASE "pruebas-sv" TEMPLATE sv` (copia de ficheros, 1 min para todo). PostgreSQL no
deja clonar una BD con conexiones abiertas: si las hay, `asegurar` sigue con el clon que tiene y avisa;
`regenerar` falla y dice quién está conectado. La aplicación no mantiene conexiones entre peticiones, así
que basta con que no haya una petición en curso ni un cliente SQL abierto contra desarrollo.

Tras clonar se ajustan las secuencias atrasadas de `pruebas-comun` y `pruebas-sv-e` (en desarrollo son
copias de réplicas). Las de `pruebas-sv` no se tocan: si alguna va por detrás, hay que mirar producción.

## Varios agentes a la vez

- **Uno prueba y otros trabajan en desarrollo:** sí. Los tests ya no leen ni escriben las BD de desarrollo.
  Lo único compartido es el árbol de código: la instancia de pruebas sirve el mismo `/var/www/orbix`,
  así que un cambio a medias de otro agente en ese árbol se ve en los tests.
- **Dos pasadas de tests a la vez sobre los mismos clones:** no. Se pisarían (los tests usan ids fijos y
  el envoltorio cuenta filas antes y después). Para eso haría falta un juego de clones y de ficheros
  `*.conn.inc` por agente (el prefijo está fijado a `pruebas-` en `ConfigDB`).

## Montaje (fuera del repositorio)

En `/home/dani/docker_images/orbix/` (lo que monta `docker-compose.yml`):

- `web/html/conf/pruebas-{comun,comun_select,sv,sv-e,sv-e_select,sf,sf-e}.conn.inc`: copia del bloque
  `default` de su fichero de desarrollo con `dbname` → `pruebas-…`. Sin `pruebas-comun_select.conn.inc` y
  `pruebas-sv-e_select.conn.inc` las conexiones de solo lectura seguirían yendo a desarrollo (lo detecta
  `ConexionesDelEntornoTest`).
- `web/html/conf/pruebas-importar.inc`: como `importar.inc` (conexiones de mantenimiento: copias, roles, vistas, zonas) con los `dbname` → `pruebas-…`. Contiene contraseñas, igual que el original.
- `web/sites-available/orbix-pruebas.conf`: el `server` de nginx de la instancia de pruebas
  (`server_name orbix.local orbix-pruebas.docker`). Tras tocarlo: `docker exec orbix-web-1 nginx -t && docker exec orbix-web-1 nginx -s reload`.
- `orbix.local` ya estaba en `/etc/hosts` (127.0.0.1). Para usar `orbix-pruebas.docker` basta añadirlo
  a `/etc/hosts` y cambiar `ORBIX_WEB_SERVER` en `orbix-pruebas.conf` y `ORBIX_PRUEBAS_URL`.

No se ha tocado `docker-compose.yml` ni se ha reiniciado ningún contenedor.

## Cambios de código que lo hacen posible

- `ConfigGlobal::webServer()`: el servidor de las URLs sale de `ORBIX_WEB_SERVER` si está definido.
- `PostRequest::rewriteLoopbackHost()`: una llamada HTTP de la aplicación a su propio servidor va, dentro
  de un contenedor, al puerto publicado del host (`host.docker.internal`) y no a `127.0.0.1`, donde no
  escucha nadie. Ya se hacía con los nombres `*.docker`; faltaba para otro nombre (`orbix.local`). Lo
  detectó `usuarios-alta-baja.spec.ts` (la ficha de usuario pide por HTTP la lista de permisos).
- `tests/integration/shared/infrastructure/ConexionesDelEntornoTest.php`: test-guardia de las conexiones
  (las de `$GLOBALS['oDB*']` y las de mantenimiento de `importar`).
