"""Lista las traducciones que no siguen el vocabulario de la casa (tools/i18n/glosario.json).

Uso (desde la raíz del repo):
    tools/i18n/.venv/bin/python tools/i18n/auditar_glosario.py                    # resumen de los 4 idiomas
    tools/i18n/.venv/bin/python tools/i18n/auditar_glosario.py --idioma en_US.UTF-8 --detalle
    tools/i18n/.venv/bin/python tools/i18n/auditar_glosario.py --termino acta --detalle
    tools/i18n/.venv/bin/python tools/i18n/auditar_glosario.py --estricto        # código 1 si hay alguna

Solo lee los .po (no los modifica). Para corregirlas con el modelo:
    tools/i18n/.venv/bin/python tools/i18n/traduir_groq.py --idioma en_US.UTF-8 --idioma-nom inglés --glosario
"""

from __future__ import annotations

import argparse
import collections
import os
import sys

import polib

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import glosario  # noqa: E402

RAIZ = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))


def ruta_po(locale: str) -> str:
    return os.path.join(RAIZ, "languages", locale, "LC_MESSAGES", "orbix.po")


def auditar(locale: str, g: dict, termino: str | None = None) -> list[tuple[polib.POEntry, list[glosario.Incumplimiento]]]:
    salida = []
    for entry in polib.pofile(ruta_po(locale)):
        if entry.obsolete or not entry.msgid or not entry.msgstr:
            continue
        fallos = glosario.incumplimientos(g, entry.msgid, entry.msgstr, locale)
        if termino is not None:
            fallos = [f for f in fallos if f.termino == termino]
        if fallos:
            salida.append((entry, fallos))
    return salida


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    parser.add_argument("--idioma", action="append", choices=list(glosario.IDIOMAS), help="uno o varios; por defecto los cuatro")
    parser.add_argument("--termino", help="solo este término (su primera forma en español, p. ej. acta)")
    parser.add_argument("--detalle", action="store_true", help="lista cada cadena con su traducción")
    parser.add_argument("--estricto", action="store_true", help="sale con código 1 si hay alguna")
    args = parser.parse_args()

    g = glosario.cargar()
    total = 0
    for locale in args.idioma or list(glosario.IDIOMAS):
        resultado = auditar(locale, g, args.termino)
        total += len(resultado)
        por_termino = collections.Counter(f.termino for _, fallos in resultado for f in fallos)
        print(f"== {locale}: {len(resultado)} cadenas")
        for t, n in por_termino.most_common():
            print(f"   {t}: {n}")
        if args.detalle:
            for entry, fallos in resultado:
                terminos = ", ".join(f"{f.termino} → {' / '.join(f.esperado)}" for f in fallos)
                print(f"\n   msgid:  {entry.msgid}\n   msgstr: {entry.msgstr}\n   falta:  {terminos}")
            print()
    return 1 if args.estricto and total > 0 else 0


if __name__ == "__main__":
    raise SystemExit(main())
