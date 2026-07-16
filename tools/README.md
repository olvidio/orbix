# Herramientas de desarrollo (offline)

Scripts que **no** sirve la aplicación web. Se ejecutan en local o en CI.

| Carpeta | Contenido |
|---------|-----------|
| `audit/` | Auditorías del código (gettext, navegación, PSR-4, etc.) |
| `fix/` | Migraciones y correcciones puntuales (`--dry-run` / `--apply`) |
| `i18n/` | Traducción asistida de ficheros `.po` en `languages/` |
| `phpstan/` | Utilidades sobre la baseline de PHPStan |
| `qa/` | Cobertura de tests, validación VO/DB, comprobación de enlaces |
| `phpunit-docker` | PHPUnit dentro del contenedor Docker |

## Convención

- **`scripts/`** — solo assets que la app incluye o expone (JS vía PHP, menús UDM, etc.). **No** scripts CLI nuevos.
- **`languages/`** — solo catálogos de traducción (`.po`, `.mo`, `.pot`, `textos_*.php`).
- **`docs/scripts/`** — generación de documentación.
- **`tools/`** — todo lo demás de desarrollo (auditorías, migraciones, QA, i18n, PHPStan).

Al crear un script nuevo, elegir subcarpeta según propósito (ver tabla arriba). Documentar en el propio fichero: `php tools/<subcarpeta>/nombre.php`. Raíz del repo desde `tools/<sub>/`: `dirname(__DIR__, 2)`.

Guía para agentes: [`agents.md`](../agents.md) (sección «Scripts y herramientas offline») y [`.cursor/rules/tools-vs-scripts.mdc`](../.cursor/rules/tools-vs-scripts.mdc).

## Traducciones (`tools/i18n/`)

**Guía completa:** [`docs/dev/traducciones_gettext.md`](../docs/dev/traducciones_gettext.md) (Poedit + plantilla + script + repaso manual).

```bash
cd tools/i18n
python3 -m venv .venv
source .venv/bin/activate   # el prompt debe mostrar (.venv), no un venv antiguo de languages/
pip install -r requirements.txt

# Claves en .env (raíz del repo); ver .env.example
#   GROQ_API_KEY=...
#   GEMINI_API_KEY=...

python3 traduir_groq.py --idioma it_IT.UTF-8 --idioma-nom italiano
```

Si ves `ModuleNotFoundError: polib`, el venv activo no es el correcto: `deactivate` y vuelve a `source .venv/bin/activate` desde `tools/i18n/`.

Los scripts leen y escriben en `../../languages/<idioma>/LC_MESSAGES/`.
