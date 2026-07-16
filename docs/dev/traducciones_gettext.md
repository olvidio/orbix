# Traducciones gettext (Orbix)

Guía para añadir o actualizar un idioma de la interfaz web usando **Poedit** y los scripts de `tools/i18n/`.

**Alcance:** ficheros `languages/<locale>/LC_MESSAGES/orbix.po` y `orbix.mo`.  
**Fuera de alcance:** textos de certificados PHP (`languages/es_ES.UTF-8/textos_certificados.php`), que son un catálogo aparte.

---

## Estructura de carpetas

```text
languages/
  es_ES.pot                          # Plantilla maestra (todos los msgid)
  es_ES.po / es_ES.mo                # Español (idioma fuente en msgid)
  it_IT.UTF-8/LC_MESSAGES/orbix.po   # Italiano
  it_IT.UTF-8/LC_MESSAGES/orbix.mo   # Binario que carga la app
  it -> it_IT.UTF-8                  # Enlace simbólico
  ca_ES.UTF-8/ … en_US.UTF-8/ …      # Otros idiomas
```

| Concepto | Fichero |
|----------|---------|
| Plantilla (una vez actualizada sirve para todos) | `languages/es_ES.pot` |
| Catálogo del idioma destino | `languages/<locale>/LC_MESSAGES/orbix.po` |
| Binario en runtime | `languages/<locale>/LC_MESSAGES/orbix.mo` |

Los `msgid` están en **español** (texto que aparece en el código con `_('…')`). El script de IA rellena `msgstr` en el idioma destino.

### Idiomas configurados

| Carpeta | Idioma |
|---------|--------|
| `ca_ES.UTF-8` | Catalán |
| `de_DE.UTF-8` | Alemán |
| `en_US.UTF-8` | Inglés |
| `it_IT.UTF-8` | Italiano |
| `es_ES` / `es_ES.UTF-8` | Español |

---

## Requisitos

- [Poedit](https://poedit.net/) 3.x
- Python 3.12+ y entorno en `tools/i18n/` (ver abajo)
- Claves API en **`.env`** en la raíz del proyecto (plantilla: `.env.example`)

```bash
cp .env.example .env
# Editar .env y rellenar GROQ_API_KEY=gsk_...
```

| Variable | Script | Dónde obtenerla |
|----------|--------|-----------------|
| `GROQ_API_KEY` | `traduir_groq.py`, `auditoria.py` | [console.groq.com/keys](https://console.groq.com/keys) |
| `GEMINI_API_KEY` | `traduir_gemini.py` | [Google AI Studio](https://aistudio.google.com/apikey) |

Los scripts cargan `.env` automáticamente al arrancar (`tools/i18n/orbix_env.py`). Un `export GROQ_API_KEY=...` en la shell tiene prioridad sobre `.env`.

```bash
cd tools/i18n
python3 -m venv .venv
source .venv/bin/activate    # debe ser tools/i18n/.venv (no languages/venv)
pip install -r requirements.txt
```

Si aparece `ModuleNotFoundError: polib`, ejecuta `deactivate` y activa de nuevo el `.venv` de esta carpeta.

---

## Flujo completo

### 1. Actualizar la plantilla maestra (`es_ES.pot`)

Solo cuando hay **cadenas nuevas en el código** (nuevos `_('…')` en `frontend/`, `src/` o `public/`).

1. Abrir en Poedit `languages/es_ES.po` (o la plantilla si trabajas directamente sobre el `.pot`).
2. Menú **Catálogo → Actualizar desde el código fuente**.
   - Rutas de búsqueda (ya configuradas en el proyecto): `frontend`, `src`, `public`.
   - Palabra clave: `_`.
3. Guardar. Poedit actualiza `languages/es_ES.pot` y `languages/es_ES.po`.

Este paso **se hace una vez** por tanda de cambios en el código; la misma plantilla sirve para catalán, italiano, inglés, etc.

### 2. Sincronizar el idioma destino con la plantilla

Ejemplo **italiano**:

1. Abrir `languages/it_IT.UTF-8/LC_MESSAGES/orbix.po` en Poedit.
2. **Catálogo → Actualizar desde archivo de traducción…** (o *Update from POT*).
3. Seleccionar `languages/es_ES.pot`.
4. Guardar.

Poedit añade las entradas nuevas con `msgstr` vacío y marca obsoletas las que ya no están en la plantilla.

Comprobar cuántas quedan sin traducir (con el venv activo):

```bash
cd tools/i18n && source .venv/bin/activate
python3 -c "
import polib
po = polib.pofile('../../languages/it_IT.UTF-8/LC_MESSAGES/orbix.po')
print('Pendientes:', sum(1 for e in po if e.msgid and not e.msgstr))
"
```

### 3. Traducción asistida (Python)

El script solo traduce entradas con `msgid` y **`msgstr` vacío**.

**Groq (recomendado):**

```bash
cd tools/i18n
source .venv/bin/activate
# GROQ_API_KEY en ../../.env (ver .env.example)

python3 traduir_groq.py --idioma it_IT.UTF-8 --idioma-nom italiano
```

**Gemini (alternativa, cuota distinta):**

```bash
cd tools/i18n
source .venv/bin/activate
# GEMINI_API_KEY en ../../.env

python3 traduir_gemini.py --idioma it_IT.UTF-8 --idioma-nom italiano
```

Opciones:

| Opción | Descripción |
|--------|-------------|
| `--idioma` | Carpeta bajo `languages/` (p. ej. `it_IT.UTF-8`) |
| `--idioma-nom` | Nombre legible para el prompt de la IA (p. ej. `italiano`) |
| `--lot N` | Frases por petición (por defecto 35 Groq / 40 Gemini) |

Valores habituales de `--idioma` / `--idioma-nom`:

| Idioma | `--idioma` | `--idioma-nom` |
|--------|------------|----------------|
| Catalán | `ca_ES.UTF-8` | `catalán` |
| Italiano | `it_IT.UTF-8` | `italiano` |
| Inglés | `en_US.UTF-8` | `inglés` |
| Alemán | `de_DE.UTF-8` | `alemán` |

También se puede usar la variable de entorno `ORBIX_I18N_LANG=it_IT.UTF-8` en lugar de `--idioma`.

El script guarda el `.po` tras cada lote correcto; si un lote falla, se puede relanzar (solo procesa vacíos).

### 4. Repaso manual

1. Abrir de nuevo `orbix.po` en Poedit.
2. Revisar traducciones dudosas, entradas **fuzzy** y cadenas con `%s`, `%d`, `%1$s`, etc.
3. Buscar entradas aún vacías y completarlas a mano o relanzar el script.
4. **Guardar** en Poedit: genera o actualiza `orbix.mo` automáticamente.

Repaso opcional con IA (revisor, hoy orientado a catalán; adaptar `IDIOMA_DESTI` en el script si se reutiliza):

```bash
python auditoria.py   # requiere GROQ_API_KEY; abre interfaz web en :8080
```

### 5. Probar en la aplicación

1. En Orbix, cambiar idioma de usuario a italiano (o el locale correspondiente).
2. Recorrer pantallas críticas: menús, formularios, mensajes de error.
3. Si no se ven cambios, comprobar que existe `orbix.mo` junto al `.po` y que la fecha es reciente.

---

## Ejemplo rápido: italiano de principio a fin

```bash
# 1–2: Poedit (manual)
#   - Actualizar es_ES.pot desde código (si hay cadenas nuevas)
#   - Abrir it_IT.UTF-8/.../orbix.po → Actualizar desde es_ES.pot

# 3: Script
cd tools/i18n && source .venv/bin/activate
# GROQ_API_KEY en ../../.env

python3 traduir_groq.py --idioma it_IT.UTF-8 --idioma-nom italiano

# 4: Poedit → revisar → Guardar (genera orbix.mo)

# 5: Probar en el navegador con usuario en italiano
```

---

## Auditoría de cadenas no envueltas en `_()`

Para detectar textos en PHP/JS que **aún no están** en el catálogo:

```bash
php tools/audit/audit_untranslated_strings.php
php tools/audit/audit_untranslated_strings.php --path=frontend/usuarios
```

Tras corregir el código, volver al paso 1 (actualizar plantilla).

---

## Solución de problemas

| Problema | Qué hacer |
|----------|-----------|
| «No hi ha cap cadena buida» | El `.po` está al día con la plantilla; repetir paso 2 si acabas de actualizar el `.pot`. |
| Muchas cadenas faltan respecto al `.pot` | Paso 2 incompleto: *Actualizar desde* `es_ES.pot` en Poedit. |
| La IA rompe `%s` / placeholders | Corregir en Poedit; acortar `--lot` si las frases son muy largas. |
| Error `GROQ_API_KEY` | Añadir `GROQ_API_KEY=gsk_...` en `.env` (raíz del repo) o `export` en la shell. |
| La app no muestra traducciones | Verificar `orbix.mo`, locale del usuario y caché del navegador. |

---

## Referencias

- Scripts: [`tools/i18n/`](../../tools/i18n/)
- Resumen en [`tools/README.md`](../../tools/README.md)
- Catálogos: solo ficheros en [`languages/`](../../languages/) (no scripts Python ahí)
