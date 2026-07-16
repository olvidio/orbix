#!/usr/bin/env python3
"""Traduce entradas vacías de un catálogo .po usando la API de Groq."""

import argparse
import json
import os
import time

import polib
from groq import Groq

from orbix_env import load_repo_env

load_repo_env()

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
        default=35,
        help="Frases por petición a la IA (por defecto 35)",
    )
    return parser.parse_args()


def build_context_prompt(idioma_nom: str) -> str:
    return (
        "Actúa como un traductor experto de software y localización. "
        "El proyecto es una aplicación web para gestionar temas de estudios (colegio, universidad) "
        f"y actividades como congresos o convivencias. El idioma de destino es: '{idioma_nom}'. "
        "Los textos originales están en español. "
        "El tono debe ser formal y cercano a la vez, adaptado al entorno académico.\n\n"
        "Recibirás un objeto JSON con textos originales indexados por números. "
        "Debes devolver obligatoriamente un objeto JSON válido con las mismas claves numéricas, "
        "donde cada valor sea exclusivamente la traducción del texto original correspondiente.\n\n"
        "Reglas cruciales:\n"
        "1. Mantén intactas las variables como %s, %d, %1$s, {username} etc. en su posición lógica exacta.\n"
        "2. Traduce palabras cortas según el contexto de la aplicación.\n"
        "3. No incluyas texto fuera del objeto JSON, ni explicaciones, ni bloques de código markdown."
    )


def traduir_lot_ia(client: Groq, context_prompt: str, lot_entrades: list) -> int:
    dades_enviar = {str(idx): entry.msgid for idx, entry in enumerate(lot_entrades)}
    text_json = json.dumps(dades_enviar, ensure_ascii=False)
    resposta_neta = ""

    try:
        response = client.chat.completions.create(
            model="llama-3.1-8b-instant",
            messages=[
                {"role": "system", "content": context_prompt},
                {"role": "user", "content": text_json},
            ],
            temperature=0.1,
            response_format={"type": "json_object"},
        )

        resposta_neta = response.choices[0].message.content.strip()

        if resposta_neta.startswith("```json"):
            resposta_neta = resposta_neta[7:]
        if resposta_neta.endswith("```"):
            resposta_neta = resposta_neta[:-3]
        resposta_neta = resposta_neta.strip()

        traduccions_dict = json.loads(resposta_neta)

        comptador_paquet = 0
        for idx, entry in enumerate(lot_entrades):
            clau = str(idx)
            if clau in traduccions_dict and traduccions_dict[clau]:
                entry.msgstr = str(traduccions_dict[clau]).strip()
                comptador_paquet += 1
        return comptador_paquet

    except json.JSONDecodeError as json_err:
        print(f" ❌ Error de format JSON en la resposta de la IA: {json_err}")
        print(f" 🔍 Resposta conflictiva de la IA: {resposta_neta[:200]}...")
        return 0
    except Exception as e:
        print(f" ❌ Error general de connexió o processament: {e}")
        return 0


def executar_tot(
    idioma_desti: str,
    idioma_nom: str,
    mida_lot: int,
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

    entrades_per_traduir = [entry for entry in po if entry.msgid and not entry.msgstr]
    total_cadenes = len(entrades_per_traduir)

    if total_cadenes == 0:
        print("✅ No hi ha cap cadena buida per traduir. El fitxer ja està complet.")
        return

    print(f"🔗 S'han trobat {total_cadenes} cadenes pendents de traducció.")
    print(f"🚀 Traduint amb Groq (Llama 3.1) cap a {idioma_nom} en paquets de {mida_lot} frases...")

    total_modificats = 0
    num_lot = 0

    for i in range(0, total_cadenes, mida_lot):
        num_lot += 1
        lot_actual = entrades_per_traduir[i : i + mida_lot]

        print(f"🤖 [Lot #{num_lot}] Traduint línies del {i + 1} al {min(i + mida_lot, total_cadenes)}...")

        modificats_en_lot = traduir_lot_ia(client, context_prompt, lot_actual)
        total_modificats += modificats_en_lot

        if modificats_en_lot > 0:
            po.save(ruta_po)
            print(f"   ↳  Èxit: {modificats_en_lot} traduccions guardades en aquest lot.")
        else:
            print("   ↳ ⚠️ Aquest lot ha fallat. Esperant abans del següent...")
            time.sleep(5)
            continue

        time.sleep(3.5)

    print("\n✨ Procés finalitzat!")
    print(f"📝 S'han completat un total de {total_modificats} traduccions.")
    print(f"💾 El fitxer actualitzat està desat a: {ruta_po}")


def main() -> None:
    args = parse_args()
    idioma_nom = args.idioma_nom or args.idioma
    executar_tot(args.idioma, idioma_nom, args.lot)


if __name__ == "__main__":
    main()
