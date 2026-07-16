import os
import time
import json
import polib
from groq import Groq

# =====================================================================
# CONFIGURACIÓ DE VARIABLES
# =====================================================================
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
LANG_DIR = os.path.normpath(os.path.join(SCRIPT_DIR, '..', '..', 'languages'))

IDIOMA_DESTI = "ca_ES.UTF-8"  # Idioma de destí (nom de la carpeta)
MIDA_LOT = 35                  # Baixem lleugerament a 35 per evitar que la IA talli el JSON si la frase és molt llarga

# Rutes dels fitxers
RUTA_ENTRADA = os.path.join(LANG_DIR, IDIOMA_DESTI, "LC_MESSAGES", "orbix.po")
RUTA_SORTIDA = os.path.join(LANG_DIR, IDIOMA_DESTI, "LC_MESSAGES", "orbix.po")

GROQ_API_KEY = os.environ.get("GROQ_API_KEY")
if not GROQ_API_KEY:
    raise SystemExit("Define GROQ_API_KEY (variable d'entorn) abans d'executar el script.")

client = Groq(api_key=GROQ_API_KEY)
# =====================================================================

# Prompt del sistema optimitzat i simplificat per a mode JSON estricte
CONTEXT_PROMPT = (
    "Actúa como un traductor experto de software y localización. "
    "El proyecto es una aplicación web para gestionar temas de estudios (colegio, universidad) "
    f"y actividades como congresos o convivencias. El idioma de destino es: '{IDIOMA_DESTI}'. "
    "El tono debe ser formal y cercano a la vez, adaptado al entorno académico.\n\n"
    "Recibirás un objeto JSON con textos originales indexados por números. "
    "Debes devolver obligatoriamente un objeto JSON válido con las mismas claves numéricas, "
    "donde cada valor sea exclusivamente la traducción del texto original correspondiente.\n\n"
    "Reglas cruciales:\n"
    "1. Mantén intactas las variables como %s, %d, %1$s, {username} etc. en su posición lógica exacta.\n"
    "2. Traduce palabras cortas según el contexto de la aplicación.\n"
    "3. No incluyas texto fuera del objeto JSON, ni explicaciones, ni bloques de código markdown."
)

def traduir_lot_ia(lot_entrades):
    """Prepara un objecte JSON amb les frases del lot i demana la traducció forçant el mode JSON"""
    dades_enviar = {str(idx): entry.msgid for idx, entry in enumerate(lot_entrades)}
    text_json = json.dumps(dades_enviar, ensure_ascii=False)

    try:
        # Crida a l'API de Groq FORÇANT el mode JSON
        response = client.chat.completions.create(
            model="llama-3.1-8b-instant",
            messages=[
                {"role": "system", "content": CONTEXT_PROMPT},
                {"role": "user", "content": text_json}
            ],
            temperature=0.1,
            response_format={"type": "json_object"}  # 🔥 AIXÒ OBLIGA A LA IA A RETORNAR UN JSON PERFECTE
        )

        resposta_neta = response.choices[0].message.content.strip()

        # Per si de cas, netegem brossa típica de Markdown si aparegués
        if resposta_neta.startswith("```json"):
            resposta_neta = resposta_neta[7:]
        if resposta_neta.endswith("```"):
            resposta_neta = resposta_neta[:-3]
        resposta_neta = resposta_neta.strip()

        # Decodifiquem la llista JSON traduïda
        traduccions_dict = json.loads(resposta_neta)

        # Apliquem les traduccions de tornada al fitxer
        comptador_paquet = 0
        for idx, entry in enumerate(lot_entrades):
            clau = str(idx)
            if clau in traduccions_dict and traduccions_dict[clau]:
                # Assegurem-nos que guardem el text net de cometes extres de la IA
                entry.msgstr = str(traduccions_dict[clau]).strip()
                comptador_paquet += 1
        return comptador_paquet

    except json.JSONDecodeError as json_err:
        print(f" ❌ Error de format JSON en la resposta de la IA: {json_err}")
        # Mostrem un tros de la resposta per saber què ha fallat exactament
        print(f" 🔍 Resposta conflictiva de la IA: {resposta_neta[:200]}...")
        return 0
    except Exception as e:
        print(f" ❌ Error general de connexió o processament: {e}")
        return 0

def executar_tot():
    if not os.path.exists(RUTA_ENTRADA):
        print(f"❌ Error: No s'ha trobat el fitxer original a {RUTA_ENTRADA}")
        return

    print(f"📖 Carregant el fitxer: {RUTA_ENTRADA}")
    po = polib.pofile(RUTA_ENTRADA)

    if not po.metadata or 'Content-Type' not in po.metadata:
        po.metadata['Content-Type'] = 'text/plain; charset=UTF-8'
        po.metadata['Content-Transfer-Encoding'] = '8bit'

    # Filtrem les entrades buides pendents de traducció
    entrades_per_traduir = [entry for entry in po if entry.msgid and not entry.msgstr]
    total_cadenes = len(entrades_per_traduir)

    if total_cadenes == 0:
        print("✅ No hi ha cap cadena buida per traduir. El fitxer ja està complet.")
        return

    print(f"🔗 S'han trobat {total_cadenes} cadenes pendents de traducció.")
    print(f"🚀 Traduint amb Groq (Llama 3.1 en Mode JSON Estricte) en paquets de {MIDA_LOT} frases...")

    total_modificats = 0
    num_lot = 0

    for i in range(0, total_cadenes, MIDA_LOT):
        num_lot += 1
        lot_actual = entrades_per_traduir[i:i + MIDA_LOT]

        print(f"🤖 [Lot #{num_lot}] Traduint línies del {i+1} al {min(i + MIDA_LOT, total_cadenes)}...")

        modificats_en_lot = traduir_lot_ia(lot_actual)
        total_modificats += modificats_en_lot

        if modificats_en_lot > 0:
            po.save(RUTA_SORTIDA)
            print(f"   ↳  Èxit: {modificats_en_lot} traduccions guardades en aquest lot.")
        else:
            print("   ↳ ⚠️ Aquest lot ha fallat el control de qualitat JSON. Saltant al següent o reintentant...")
            # En lloc d'aturar de cop, deixem que continuï amb el següent lot per no bloquejar l'script
            time.sleep(5)
            continue

        # Pausa breu de 3.5 segons pel límit de velocitat RPM
        time.sleep(3.5)

    print(f"\n✨ Procés finalitzat!")
    print(f"📝 S'han completat un total de {total_modificats} traduccions.")
    print(f"💾 El fitxer actualitzat està desat a: {RUTA_SORTIDA}")

if __name__ == "__main__":
    executar_tot()
