/**
 * Detección de HTML de error de backend (PDO, Postgres, fatals PHP) para fallar con mensaje útil en E2E.
 */

/** Si el HTML es un fatal PDO/Postgres u otro fatal PHP, devuelve mensaje corto para operadores. */
export function diagnoseBackendHtml(html: string): string | null {
  const h = html.slice(0, 120_000);
  if (/faltan par[aá]metros para conectar con la BDU/i.test(h)) {
    return (
      'Error de configuración BDU (faltan parámetros para conectar). ' +
      'Revisa secretos montados en el contenedor PHP, ficheros `*.inc`/`DIR_PWD` y variables de entorno del stack Docker.'
    );
  }
  if (/remaining connection slots are reserved/i.test(h)) {
    return (
      'PostgreSQL no acepta más conexiones (límite max_connections o slots reservados al superusuario). ' +
      'Reinicia o libera conexiones en el contenedor `db` (p. ej. `pg_terminate_backend` sobre sesiones inactivas), ' +
      'aumenta `max_connections` si procede, y revisa el pool de Orbix/PHP-FPM para evitar conexiones colgantes.'
    );
  }
  const connAt = h.match(/connection to server at "([^"]+)", port (\d+)/i);
  if (
    /PDOException|SQLSTATE\[/i.test(h) &&
    /Fatal error/i.test(h) &&
    connAt !== null
  ) {
    const host = connAt[1];
    const port = connAt[2];
    return (
      `PostgreSQL no responde o hace timeout contra **${host}:${port}**. ` +
      'Ese host lo fijan los `*.inc` de Orbix (`DIR_PWD`), incluidos los de la base **`sf`** que se usan incluso con sesión SV (`global_object.inc`). **No** lo define Playwright ni `e2e/.env`. ' +
      'Si aparece una IP de otra LAN (p. ej. `192.168…`), revisa volumen/`docker-compose` y apunta al Postgres de tu stack (habitual: servicio **`db`**).'
    );
  }
  if (/PDOException|SQLSTATE\[/i.test(h) && /Fatal error/i.test(h)) {
    return (
      'Error PDO/SQL durante el arranque (BD inaccesible o fallo antes del formulario). ' +
      'Revisa Postgres, credenciales y que el usuario de aplicación no haya perdido derechos.'
    );
  }
  if (/Fatal error/i.test(h)) {
    return 'PHP fatal en la página — revisa el recorte del cuerpo abajo y los logs del servidor.';
  }
  return null;
}

export async function formatBackendFailureBody(page: {
  url: () => string;
  content: () => Promise<string>;
  textContent: (selector: string) => Promise<string | null>;
}): Promise<string> {
  const url = page.url();
  const body =
    ((await page.textContent('body').catch(() => '')) ?? '')
      .replace(/\s+/g, ' ')
      .trim()
      .slice(0, 2500) || '(vacío)';
  return `URL: ${url}\n--- cuerpo (recorte) ---\n${body}`;
}
