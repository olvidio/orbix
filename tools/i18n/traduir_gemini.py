#!/usr/bin/env python3
"""Traduce entradas vacías de un catálogo .po usando la API de Gemini."""

import argparse
import json
import os
import time

import google.generativeai as genai
import polib

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
        default=40,
        help="Frases por petición a la IA (por defecto 40)",
    )
    return parser.parse_args()


def build_context_prompt(idioma_nom: str) -> str:
    return (
        "Actúa como un traductor experto de software y localización. "
        "El proyecto es una aplicación web para gestionar temas de estudios (colegio, universidad) "
        f"y actividades como congresos o convivencias. El idioma de destino es: '{idioma_nom}'. "
        "Los textos originales están en español. "
        "El tono debe ser formal y cercano a la vez, adaptado al entorno académico.\n\n"
        "Recibirás una lista de textos originales en formato JSON (un objeto con IDs numéricas). "
        "Debes traducir cada texto respetando el sentido original y las siguientes reglas:\n"
        "1. Mantén intactas las variables como %s, %d, %1$s, {username} etc. en su posición lógica exacta.\n"
        "2. Traduce palabras cortas según el contexto de la aplicación.\n"
        "3. Devuelve la respuesta estrictamente en el mismo formato JSON, manteniendo las mismas claves numéricas, donde el valor sea exclusivamente la traducción.\n"
        "No añadas ninguna introducción, ni comentarios, ni bloques de código de markdown (como ```json). Devuelve solo el objeto JSON limpio."
    )


def traduir_lot_ia(model: genai.GenerativeModel, lot_entrades: list) -> int:
    dades_enviar = {str(idx): entry.msgid for idx, entry in enumerate(lot_entrades)}
    text_json = json.dumps(dades_enviar, ensure_ascii=False)

    try:
        response = model.generate_content(text_json)
        resposta_neta = response.text.strip()

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

    except Exception as e:
        print(f" ❌ Error processant aquest lot de traducció: {e}")
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

    api_key = os.environ.get("GEMINI_API_KEY")
    if not api_key:
        raise SystemExit(
            "Define GEMINI_API_KEY en .env (raíz del repo) o como variable de entorno."
        )

    genai.configure(api_key=api_key)
    model = genai.GenerativeModel(
        model_name="models/gemini-2.5-flash-lite",
        system_instruction=build_context_prompt(idioma_nom),
    )

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
    print(f"📦 Traduint cap a {idioma_nom} amb Gemini en paquets de {mida_lot} frases...")

    total_modificats = 0
    num_lot = 0

    for i in range(0, total_cadenes, mida_lot):
        num_lot += 1
        lot_actual = entrades_per_traduir[i : i + mida_lot]

        print(f"🤖 [Consulta #{num_lot}] Traduint frases del {i + 1} al {min(i + mida_lot, total_cadenes)}...")

        modificats_en_lot = traduir_lot_ia(model, lot_actual)
        total_modificats += modificats_en_lot

        if modificats_en_lot > 0:
            po.save(ruta_po)
            print(f"   ↳  Èxit: S'han afegit {modificats_en_lot} traduccions d'aquest lot.")
        else:
            print("   ↳ ⚠️ El lot ha fallat o ha retornat buit. Aturant per precaució de quota.")
            break

        print("⏳ Esperant 8 segons per respectar el límit de velocitat (RPM)...")
        time.sleep(8)

    print("\n✨ Procés finalitzat o pausat per límit diari.")
    print(f"📝 S'han completat un total de {total_modificats} traduccions en aquesta sessió.")
    print(f"💾 El fitxer actualitzat està desat a: {ruta_po}")


def main() -> None:
    args = parse_args()
    idioma_nom = args.idioma_nom or args.idioma
    executar_tot(args.idioma, idioma_nom, args.lot)


if __name__ == "__main__":
    main()
