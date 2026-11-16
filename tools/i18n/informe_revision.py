#!/usr/bin/env python3
"""Informe para revisar las cadenas nuevas de una pasada de gettext.

Compara el .pot anterior con el actual y, para cada msgid nuevo, muestra la traducción de cada
catálogo con avisos automáticos (vacía, fuzzy, placeholders distintos, igual al español).

Uso (lo llama tools/i18n/actualizar.php):
    tools/i18n/.venv/bin/python tools/i18n/informe_revision.py \
        --pot-anterior /tmp/es_ES.pot.antes --salida /tmp/revision.md

Sale con código 1 si alguna cadena nueva queda vacía o fuzzy (no entraría en el .mo).
"""

from __future__ import annotations

import argparse
import os
import re
import sys

import polib

SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
LANG_DIR = os.path.normpath(os.path.join(SCRIPT_DIR, "..", "..", "languages"))
LOCALES = ["ca_ES.UTF-8", "en_US.UTF-8", "de_DE.UTF-8", "it_IT.UTF-8"]

# Una sigla interna (o varias) se puede quedar igual al español sin que sea un error.
SIGLA = re.compile(r"^[a-z0-9/+.\-|() ]{1,12}$|^[A-Z]{2,5}$")


def placeholders(text: str) -> list[str]:
    return sorted(re.findall(r"%(?:\d+\$)?[-+0# ]*(?:\d+)?(?:\.\d+)?[bcdeEfFgGosuxX]", text.replace("%%", "")))


def avisos(entry: polib.POEntry | None) -> list[str]:
    if entry is None:
        return ["falta en el catálogo"]
    out = []
    if not entry.msgstr:
        out.append("vacía")
    if "fuzzy" in entry.flags:
        out.append("fuzzy")
    if entry.msgstr and placeholders(entry.msgid) != placeholders(entry.msgstr):
        out.append("placeholders distintos")
    if entry.msgstr and entry.msgstr == entry.msgid and not SIGLA.match(entry.msgid):
        out.append("igual al español")
    return out


def celda(texto: str) -> str:
    return texto.replace("|", "\\|").replace("\n", "\\n")


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--pot-anterior", required=True)
    parser.add_argument("--salida", required=True)
    args = parser.parse_args()

    antes = {e.msgid for e in polib.pofile(args.pot_anterior) if e.msgid}
    pot = polib.pofile(os.path.join(LANG_DIR, "es_ES.pot"))
    nuevas = [e.msgid for e in pot if e.msgid and e.msgid not in antes]
    quitadas = len(antes - {e.msgid for e in pot if e.msgid})

    catalogos = {}
    for loc in LOCALES:
        po = polib.pofile(os.path.join(LANG_DIR, loc, "LC_MESSAGES", "orbix.po"))
        catalogos[loc] = {e.msgid: e for e in po if not e.obsolete}

    bloqueantes = 0
    con_aviso = 0
    lineas = [
        "# Revisión de cadenas nuevas",
        "",
        f"Cadenas nuevas en `es_ES.pot`: **{len(nuevas)}** (quitadas: {quitadas}).",
        "",
        "| español | " + " | ".join(l.split("_")[0] for l in LOCALES) + " | avisos |",
        "|---|" + "---|" * len(LOCALES) + "---|",
    ]
    for msgid in nuevas:
        fila = [celda(msgid)]
        todos = []
        for loc in LOCALES:
            entry = catalogos[loc].get(msgid)
            fila.append(celda(entry.msgstr if entry else ""))
            av = avisos(entry)
            if any(a in ("vacía", "fuzzy", "falta en el catálogo") for a in av):
                bloqueantes += 1
            todos += [f"{loc.split('_')[0]}: {a}" for a in av]
        if todos:
            con_aviso += 1
        fila.append(celda("; ".join(todos)))
        lineas.append("| " + " | ".join(fila) + " |")

    with open(args.salida, "w", encoding="utf-8") as fh:
        fh.write("\n".join(lineas) + "\n")

    print(f"Cadenas nuevas: {len(nuevas)}; con avisos: {con_aviso}; vacías o fuzzy: {bloqueantes}.")
    print(f"Informe para revisar: {args.salida}")
    return 1 if bloqueantes else 0


if __name__ == "__main__":
    sys.exit(main())
