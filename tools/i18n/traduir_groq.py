#!/usr/bin/env python3
"""Traduce entradas vacías o fuzzy de un catálogo .po usando la API de Groq."""

import argparse
import json
import os
import re
import time

import polib
from groq import Groq

from orbix_env import load_repo_env

load_repo_env()

MODEL = "qwen/qwen3.8-27b"
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
LANG_DIR = os.path.normpath(os.path.join(SCRIPT_DIR, "..", "..", "languages"))


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(
        description="Traduce msgstr vacíos de languages/<idioma>/LC_MESSAGES/orbix.po"
    )
    parser.add_argument(
        "--idioma",
        default=os.environ.get("ORBIX_I18N_LANG", "ca_ES.UTF-8"),
        help="Carpeta bajo languages/ (p. ej. it_IT.UTF-8)",
    )
    parser.add_argument(
        "--idioma-nom",
        default=None,
        help="Nombre del idioma destino para el prompt (p. ej. italiano)",
    )
    parser.add_argument(
        "--lot",
        type=int,
        default=12,
        help="Frases por petición a la IA (por defecto 12)",
    )
    parser.add_argument(
        "--fuzzy",
        action="store_true",
        help="Retraduce entradas fuzzy y les quita la marca al guardar",
    )
    return parser.parse_args()


def build_context_prompt(idioma_nom: str) -> str:
    return (
        "Actúa como un traductor experto de software académico. "
        "Orbix gestiona estudios (colegio y universidad), actas de notas, asignaturas, matrículas, "
        "centros, delegaciones y actividades (congresos, convivencias, cursos). "
        f"Idioma de destino: {idioma_nom}. Origen: español. "
        "Tono formal, claro y propio de una secretaría académica.\n\n"
        "Traduce el texto español de nuevo. No arrastres una traducción vieja si no corresponde al original.\n"
        "Glosario: asignatura = materia del plan de estudios "
        "(en: subject; de: Fach; it: materia); "
        "acta = acta de calificaciones, no un protocolo de reunión "
        "(en: grade record; de: Notenprotokoll; it: verbale d'esame); "
        "matrícula = inscripción "
        "(en: enrollment; de: Einschreibung; it: iscrizione); "
        "centro = sede o colegio; "
        "delegación o dl = delegación (conserva «dl» si aparece como sigla); "
        "sacd y stgr se dejan tal cual; convivencia = actividad de convivencia; "
        "ficha = ficha de datos (en: record; de: Karteikarte; it: scheda).\n\n"
        "Recibirás un JSON indexado por números. Devuelve solo un JSON con las mismas claves "
        "y la traducción de cada texto.\n\n"
        "Reglas:\n"
        "1. Conserva %s, %d, %1$s, %2$d y el resto de placeholders, en el mismo número y tipo.\n"
        "2. No añadas markdown ni explicaciones.\n"
        "3. Traduce también las frases cortas de botones y menús.\n"
        "4. Errores que no debes repetir: "
        "acta no es minutes, act, Protokoll, Akte, atto ni record suelto; "
        "asignatura no es Vorlesung, Modul, soggetto, oggetto ni argomento; "
        "en italiano delegación es delegazione, no delega; "
        "ficha no es tab."
    )


def placeholders(text: str) -> list[str]:
    limpio = text.replace("%%", "")
    found = re.findall(
        r"%(\d+\$)?[-+0# ]*(?:\d+|\*)?(?:\.(?:\d+|\*))?l{0,2}([bcdeEfFgGosuxX])",
        limpio,
    )
    out: list[str] = []
    auto = 1
    for index, letter in found:
        number = int(index[:-1]) if index else auto
        if not index:
            auto += 1
        out.append(f"{number}{letter}")
    return sorted(out)


def aceptar_traduccion(entry: polib.POEntry, traduccion: str, quitar_fuzzy: bool) -> bool:
    texto = traduccion.strip()
    if texto == "":
        return False
    if placeholders(entry.msgid) != placeholders(texto):
        return False
    entry.msgstr = texto
    if quitar_fuzzy:
        entry.flags = [flag for flag in entry.flags if flag != "fuzzy"]
        entry.previous_msgid = None
        entry.previous_msgctxt = None
    return True


def espera_rate_limit(mensaje: str) -> float:
    encontrado = re.search(r"try again in (?:(\d+)m)?(\d+(?:\.\d+)?)s", mensaje)
    if not encontrado:
        return 20.0
    minutos = int(encontrado.group(1) or 0)
    return minutos * 60 + float(encontrado.group(2)) + 2.0


def traduir_lot_ia(client: Groq, context_prompt: str, lot_entrades: list, quitar_fuzzy: bool) -> int:
    dades_enviar = {str(idx): entry.msgid for idx, entry in enumerate(lot_entrades)}
    text_json = json.dumps(dades_enviar, ensure_ascii=False)
    resposta_neta = ""

    for intento in range(6):
        try:
            response = client.chat.completions.create(
                model=MODEL,
                messages=[
                    {"role": "system", "content": context_prompt},
                    {"role": "user", "content": text_json},
                ],
                temperature=0.1,
                response_format={"type": "json_object"},
            )
            resposta_neta = (response.choices[0].message.content or "").strip()
            if resposta_neta.startswith("```json"):
                resposta_neta = resposta_neta[7:]
            if resposta_neta.endswith("```"):
                resposta_neta = resposta_neta[:-3]
            traduccions_dict = json.loads(resposta_neta.strip())

            comptador_paquet = 0
            for idx, entry in enumerate(lot_entrades):
                valor = traduccions_dict.get(str(idx))
                if not isinstance(valor, str) or valor.strip() == "":
                    print(f"   ↳ vacía: {entry.msgid[:70]!r}")
                    continue
                if placeholders(entry.msgid) != placeholders(valor):
                    print(f"   ↳ placeholders: {entry.msgid[:50]!r} -> {valor[:50]!r}")
                    continue
                if aceptar_traduccion(entry, valor, quitar_fuzzy):
                    comptador_paquet += 1
            return comptador_paquet
        except json.JSONDecodeError as json_err:
            print(f" ❌ Error de format JSON en la resposta de la IA: {json_err}")
            print(f" 🔍 Resposta conflictiva de la IA: {resposta_neta[:200]}...")
            return 0
        except Exception as e:
            mensaje = str(e)
            if "429" in mensaje or "rate_limit" in mensaje:
                espera = espera_rate_limit(mensaje)
                print(f" ⏳ Límite de Groq. Reintento {intento + 1} en {espera:.0f}s.")
                time.sleep(espera)
                continue
            print(f" ❌ Error general de connexió o processament: {e}")
            return 0
    print(" ❌ Lote agotado por límite de peticiones.")
    return 0


def executar_tot(
    idioma_desti: str,
    idioma_nom: str,
    mida_lot: int,
    solo_fuzzy: bool,
) -> None:
    ruta_po = os.path.join(LANG_DIR, idioma_desti, "LC_MESSAGES", "orbix.po")

    if not os.path.exists(ruta_po):
        print(f"❌ Error: No s'ha trobat el fitxer original a {ruta_po}")
        raise SystemExit(1)

    api_key = os.environ.get("GROQ_API_KEY")
    if not api_key:
        raise SystemExit(
            "Define GROQ_API_KEY en .env (raíz del repo) o como variable de entorno."
        )

    client = Groq(api_key=api_key)
    context_prompt = build_context_prompt(idioma_nom)

    print(f"📖 Carregant el fitxer: {ruta_po}")
    po = polib.pofile(ruta_po)

    if not po.metadata or "Content-Type" not in po.metadata:
        po.metadata["Content-Type"] = "text/plain; charset=UTF-8"
        po.metadata["Content-Transfer-Encoding"] = "8bit"

    if solo_fuzzy:
        entrades_per_traduir = [
            entry for entry in po if entry.msgid and not entry.obsolete and "fuzzy" in entry.flags
        ]
    else:
        entrades_per_traduir = [
            entry for entry in po if entry.msgid and not entry.obsolete and not entry.msgstr
        ]
    total_cadenes = len(entrades_per_traduir)

    if total_cadenes == 0:
        print("✅ No hi ha cadenes pendents.")
        return

    print(f"🔗 S'han trobat {total_cadenes} cadenes pendents de traducció.")
    print(f"🚀 Traduint amb Groq ({MODEL}) cap a {idioma_nom} en paquets de {mida_lot} frases...")

    total_modificats = 0
    num_lot = 0

    for i in range(0, total_cadenes, mida_lot):
        num_lot += 1
        lot_actual = entrades_per_traduir[i : i + mida_lot]

        print(f"🤖 [Lot #{num_lot}] Traduint línies del {i + 1} al {min(i + mida_lot, total_cadenes)}...")

        modificats_en_lot = traduir_lot_ia(client, context_prompt, lot_actual, solo_fuzzy)
        total_modificats += modificats_en_lot

        if modificats_en_lot > 0:
            po.save(ruta_po)
            print(f"   ↳  Èxit: {modificats_en_lot} traduccions guardades en aquest lot.")
        else:
            print("   ↳ ⚠️ Aquest lot ha fallat. Esperant abans del següent...")
            time.sleep(40)
            continue

        time.sleep(3.5)

    print("\n✨ Procés finalitzat!")
    print(f"📝 S'han completat un total de {total_modificats} traduccions.")
    print(f"💾 El fitxer actualitzat està desat a: {ruta_po}")


def main() -> None:
    args = parse_args()
    idioma_nom = args.idioma_nom or args.idioma
    executar_tot(args.idioma, idioma_nom, args.lot, args.fuzzy)


if __name__ == "__main__":
    main()
