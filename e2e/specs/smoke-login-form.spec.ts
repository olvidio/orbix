import { expect, test } from '@playwright/test';

/**
 * No requiere credenciales: comprueba que la app responde y el login está en la página.
 * Útil en CI sin secretos para validar URL base y servidor arriba.
 *
 * `index.php` sin sesión debe pasar por `login.php` y pintar los campos de usuario/clave.
 * La navegación es `page.goto('index.php')` relativa al baseURL (no usar `'/index.php'`).
 */
test.describe('Smoke público', () => {
  test('la portada muestra formulario de inicio de sesión', async ({
    page,
    baseURL,
  }) => {
    // Navegación relativa al baseURL (ver README: "/ruta" ignora el path del base).
    const absolute = `${(baseURL ?? '').replace(/\/$/, '')}/index.php`;

    let response = null as Awaited<ReturnType<typeof page.goto>> | null;

    await test.step('Abrir index.php', async () => {
      response = await page.goto('index.php', {
        waitUntil: 'load',
        timeout: 45_000,
      });

      test.info().annotations.push({
        type: 'e2e',
        description: `URL final=${page.url()} status=${response?.status()}`,
      });

      if (response === null) {
        throw new Error(
          `No hay respuesta al cargar "${absolute}". ¿El servidor está en marcha?`
        );
      }

      if (response.status() >= 400) {
        const body = (await page.textContent('body')) ?? '';
        let orbixHint = '';
        if (response.status() === 404 && baseURL) {
          try {
            const n = baseURL.replace(/\/$/, '');
            const u = new URL(n);
            const withOrbix = `${u.origin}/orbix`;
            if (
              u.pathname === '' ||
              u.pathname === '/' ||
              !n.endsWith('/orbix')
            ) {
              orbixHint = `\n\nSi tu URL en Chrome es …/orbix/index.php, pon en e2e/.env: PLAYWRIGHT_BASE_URL=${withOrbix}`;
            }
          } catch {
            /* noop */
          }
        }
        throw new Error(
          `HTTP ${response.status()} al abrir "${absolute}". ` +
            `La URL indicada no sirve ese fichero desde Playwright.\n\n` +
            `• Usa la misma base que en la barra del navegador (incluido el segmento /orbix si lo hay).\n` +
            `• En la clave PLAYWRIGHT_BASE_URL de e2e/.env (o variables de shell).${orbixHint}\n\n` +
            `Fragmento de respuesta:\n${body.slice(0, 900)}`
        );
      }

      try {
        await expect(page.locator('input[name="username"]')).toBeVisible({
          timeout: 20_000,
        });
      } catch (e: unknown) {
        const snippet =
          ((await page.textContent('body')) ?? '').replace(/\s+/g, ' ').trim().slice(
            0,
            1200
          );
        throw new Error(
          `No se ve el campo de usuario (¿URL base mal o servidor devuelve otra página?).\n\n` +
            `Intentado: ${absolute}\n` +
            `URL final en el navegador: ${page.url()}\n` +
            `Título: ${await page.title()}\n\n` +
            `Fragmento página:\n${snippet}\n\n` +
            `Detalle técnico: ${String(e instanceof Error ? e.message : e)}`
        );
      }

      await expect(page.locator('input[name="password"]')).toBeVisible();
      await expect(page.locator('form#frm_login')).toHaveCount(1);
    });
  });
});
