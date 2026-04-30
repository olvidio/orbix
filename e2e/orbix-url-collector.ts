import type { Page } from '@playwright/test';

/**
 * Recoge URLs absolutas: `a[href]` distintos de `#` y primer argumento de `fnjs_link_submenu('…', …)` en `onclick`.
 * Si `rootSelector` existe (p. ej. `#main`), solo se recorre ese subárbol.
 */
export async function collectOrbixUrls(
  page: Page,
  rootSelector?: string
): Promise<string[]> {
  return page.evaluate((sel: string | undefined) => {
    let root: Document | Element;
    if (sel) {
      const el = document.querySelector(sel);
      if (!el) {
        return [];
      }
      root = el;
    } else {
      root = document;
    }
    const out = new Set<string>();
    const base = window.location.href;
    function addUrl(raw: string) {
      if (!raw || raw.startsWith('javascript')) {
        return;
      }
      try {
        out.add(new URL(raw, base).href);
      } catch {
        /* URL inválida */
      }
    }

    for (const a of root.querySelectorAll('a[href]')) {
      const raw = a.getAttribute('href') ?? '';
      if (raw === '' || raw === '#') {
        continue;
      }
      addUrl(raw);
    }

    const re = /fnjs_link_submenu\s*\(\s*'([^']+)'/g;
    for (const el of root.querySelectorAll('[onclick]')) {
      const oc = el.getAttribute('onclick') ?? '';
      re.lastIndex = 0;
      let m: RegExpExecArray | null;
      while ((m = re.exec(oc)) !== null) {
        addUrl(m[1]);
      }
    }

    return [...out];
  }, rootSelector);
}
