"""Vocabulario de la casa para las traducciones (tools/i18n/glosario.json).

Lo usan:
- tools/i18n/auditar_glosario.py: lista las traducciones que no siguen el glosario.
- tools/i18n/traduir_groq.py: lo manda al modelo y, con --glosario, retraduce las que no lo siguen.

Regla: si el msgid contiene una forma en español de un término (palabra entera, sin distinguir
mayúsculas), la traducción debe contener alguna de las formas acordadas para ese idioma (sin
distinguir mayúsculas y también dentro de una palabra, para los compuestos alemanes). Las siglas
de dos o más letras que aparecen en el msgid deben aparecer también en la traducción. Las
expresiones de «ignorar» se quitan del msgid antes de comprobar.
"""

from __future__ import annotations

import json
import os
import re
from dataclasses import dataclass

RUTA_GLOSARIO = os.path.join(os.path.dirname(os.path.abspath(__file__)), "glosario.json")

IDIOMAS = {
    "ca_ES.UTF-8": "ca",
    "en_US.UTF-8": "en",
    "de_DE.UTF-8": "de",
    "it_IT.UTF-8": "it",
}

_LETRA = r"[\wáéíóúüñàèòïç]"


@dataclass(frozen=True)
class Incumplimiento:
    termino: str
    esperado: tuple[str, ...]
    nota: str = ""


def cargar(ruta: str = RUTA_GLOSARIO) -> dict:
    with open(ruta, encoding="utf-8") as f:
        return json.load(f)


def idioma_de(locale: str) -> str:
    """'ca_ES.UTF-8' → 'ca'. Acepta también el código corto."""
    if locale in IDIOMAS.values():
        return locale
    if locale not in IDIOMAS:
        raise ValueError(f"Idioma sin glosario: {locale}")
    return IDIOMAS[locale]


def _patron_es(formas: list[str]) -> re.Pattern[str]:
    alternativas = "|".join(re.escape(f) for f in sorted(formas, key=len, reverse=True))
    return re.compile(rf"(?<!{_LETRA})(?:{alternativas})(?!{_LETRA})", re.IGNORECASE)


def _patron_sigla(sigla: str) -> re.Pattern[str]:
    return re.compile(rf"(?<![\w%]){re.escape(sigla)}(?!\w)")


def incumplimientos(glosario: dict, msgid: str, msgstr: str, idioma: str) -> list[Incumplimiento]:
    """Términos del glosario que el msgid usa y la traducción no respeta."""
    idioma = idioma_de(idioma)
    if not msgid or not msgstr:
        return []
    traduccion = msgstr.lower()
    # Expresiones donde un término no tiene su sentido de la casa («en lugar de», «NOTA:», «zona horaria»…).
    for expresion in glosario.get("ignorar", []):
        msgid = re.sub(re.escape(expresion), " ", msgid, flags=re.IGNORECASE)
    salida: list[Incumplimiento] = []
    for t in glosario["terminos"]:
        formas = t.get(idioma) or []
        if not formas or not _patron_es(t["es"]).search(msgid):
            continue
        if not any(f.lower() in traduccion for f in formas):
            salida.append(Incumplimiento(t["es"][0], tuple(formas), t.get("nota", "")))
    for sigla in glosario.get("siglas", []):
        if len(sigla) < 2 or not _patron_sigla(sigla).search(msgid):
            continue
        if sigla.lower() not in traduccion:
            salida.append(Incumplimiento(sigla, (sigla,), "sigla: se deja tal cual"))
    return salida


def texto_para_prompt(glosario: dict, idioma: str) -> str:
    """Glosario en una línea por término, para el prompt del modelo."""
    idioma = idioma_de(idioma)
    lineas = []
    for t in glosario["terminos"]:
        formas = t.get(idioma) or []
        if not formas:
            continue
        linea = f"- {' / '.join(t['es'])} → {' / '.join(formas)}"
        if t.get("nota"):
            linea += f" ({t['nota']})"
        lineas.append(linea)
    siglas = ", ".join(glosario.get("siglas", []))
    return (
        "Glosario obligatorio (español → término que hay que usar):\n"
        + "\n".join(lineas)
        + f"\nSiglas que se dejan tal cual: {siglas}."
    )
