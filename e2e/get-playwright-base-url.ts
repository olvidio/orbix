/**
 * URL raíz donde el navegador abre Orbix (`index.php` vive ahí mismo).
 *
 * Siempre termina en `/` para que las rutas relativas (`index.php`) resuelvan bien:
 * sin barra en `…/orbix`, la URL estándar trata `orbix` como fichero y
 * `index.php` cae en la raíz del host (404 típico en Orbix bajo subcarpeta).
 */
export function getPlaywrightBaseUrl(): string {
  const raw = process.env.PLAYWRIGHT_BASE_URL?.trim();
  if (!raw) {
    throw new Error(
      'Falta PLAYWRIGHT_BASE_URL. Copia `e2e/.env.example` → `e2e/.env` y define la misma base que en el navegador, ' +
        'incluyendo el prefijo de ruta donde cuelga Orbix (ej. http://orbix.docker:8003/orbix). Da igual si pones barra final: se normaliza.'
    );
  }
  return `${raw.replace(/\/+$/, '')}/`;
}
