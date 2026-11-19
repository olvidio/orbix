# Herramientas de desarrollo (offline)

Scripts que **no** sirve la aplicación web. Se ejecutan en local o en CI.

| Carpeta | Contenido |
|---------|-----------|
| `audit/` | Auditorías del código (gettext, navegación, PSR-4, etc.) |
| `fix/` | Migraciones y correcciones puntuales (`--dry-run` / `--apply`) |
| `i18n/` | Traducción asistida de ficheros `.po` en `languages/` |
| `phpstan/` | Utilidades sobre la baseline de PHPStan |
| `qa/` | Cobertura de tests, validación VO/DB, comprobación de enlaces |
| `phpunit-docker` | PHPUnit dentro del contenedor Docker, **contra las BD de desarrollo**: no usar; los tests se lanzan con `php tools/qa/phpunit_protegido.php` ([`docs/dev/bd_pruebas.md`](../docs/dev/bd_pruebas.md), [`docs/dev/procedimientos.md`](../docs/dev/procedimientos.md)) |

## Convención

- **`scripts/`** — solo assets que la app incluye o expone (JS vía PHP, menús UDM, etc.). **No** scripts CLI nuevos.
- **`languages/`** — solo catálogos de traducción (`.po`, `.mo`, `.pot`, `textos_*.php`).
- **`docs/scripts/`** — generación de documentación.
- **`tools/`** — todo lo demás de desarrollo (auditorías, migraciones, QA, i18n, PHPStan).

Al crear un script nuevo, elegir subcarpeta según propósito (ver tabla arriba). Documentar en el propio fichero: `php tools/<subcarpeta>/nombre.php`. Raíz del repo desde `tools/<sub>/`: `dirname(__DIR__, 2)`.

Guía para agentes: [`AGENTS.md`](../AGENTS.md) (sección «Scripts y herramientas offline») y [`.cursor/rules/tools-vs-scripts.mdc`](../.cursor/rules/tools-vs-scripts.mdc).

## Ayuda de usuario (`tools/audit/ayuda_preguntas.php`)

Comprueba que la ayuda de la aplicación responde a las preguntas de los usuarios con el apartado de la guía que toca (`docs/guias/`). La lista de preguntas está en `tools/audit/ayuda_preguntas.tsv`.

```bash
composer qa:ayuda                                            # falla si alguna no la responde su guía
php tools/audit/ayuda_preguntas.php "como apunto a alguien"  # qué responde la ayuda a una pregunta
```

Cómo se arregla una que falla: [`docs/dev/procedimientos.md`](../docs/dev/procedimientos.md), apartado 16.

## Traducciones (`tools/i18n/`)

**Guía completa:** [`docs/dev/traducciones_gettext.md`](../docs/dev/traducciones_gettext.md) (Poedit + plantilla + script + repaso manual).

Pasada completa en un comando (menús de la BD → `.pot` → `msgmerge` → Groq → `.mo` → informe de revisión):

```bash
php tools/i18n/actualizar.php
```

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

### Vocabulario de la casa (`glosario.json`)

`tools/i18n/glosario.json` fija cómo se traduce cada término propio (acta, asistente, cargo, numerario, ctr…) y qué siglas se dejan tal cual. `traduir_groq.py` lo manda siempre al modelo. Para revisarlo:

```bash
composer i18n:glosario                                    # resumen por idioma y término
tools/i18n/.venv/bin/python tools/i18n/auditar_glosario.py --idioma en_US.UTF-8 --termino acta --detalle
tools/i18n/.venv/bin/python tools/i18n/traduir_groq.py --idioma en_US.UTF-8 --idioma-nom inglés --glosario   # retraduce las que no lo siguen
tools/i18n/.venv/bin/python -m unittest tools/i18n/test_glosario.py
```

Con `--glosario` solo se acepta la traducción nueva si sigue el glosario y conserva los placeholders; las que no, se quedan como estaban y salen en pantalla para corregirlas a mano. Después, `msgcat` + `msgfmt --check` como en `actualizar.php`.

Si ves `ModuleNotFoundError: polib`, el venv activo no es el correcto: `deactivate` y vuelve a `source .venv/bin/activate` desde `tools/i18n/`.

Los scripts leen y escriben en `../../languages/<idioma>/LC_MESSAGES/`.
