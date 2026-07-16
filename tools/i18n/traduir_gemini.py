import os
import time
import json
import polib
import google.generativeai as genai

# =====================================================================
# CONFIGURACIÓ DE VARIABLES
# =====================================================================
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
LANG_DIR = os.path.normpath(os.path.join(SCRIPT_DIR, '..', '..', 'languages'))

IDIOMA_DESTI = "ca_ES.UTF-8"  # Idioma de destí (nom de la carpeta)
MIDA_LOT = 40                  # Quantes frases enviar juntes per ESTALVIAR QUOTA diària (RPD)

# Rutes dels fitxers
RUTA_ENTRADA = os.path.join(LANG_DIR, IDIOMA_DESTI, "LC_MESSAGES", "orbix.po")
RUTA_SORTIDA = os.path.join(LANG_DIR, IDIOMA_DESTI, "LC_MESSAGES", "orbix.po")

GEMINI_API_KEY = os.environ.get("GEMINI_API_KEY")
if not GEMINI_API_KEY:
    raise SystemExit("Define GEMINI_API_KEY (variable d'entorn) abans d'executar el script.")

genai.configure(api_key=GEMINI_API_KEY)
# =====================================================================

# Prompt del sistema optimitzat en castellà per rebre format JSON estrictament
CONTEXT_PROMPT = (
    "Actúa como un traductor experto de software y localización. "
    "El proyecto es una aplicación web para gestionar temas de estudios (colegio, universidad) "
    f"y actividades como congresos o convivencias. El idioma de destino es: '{IDIOMA_DESTI}'. "
    "El tono debe ser formal y cercano a la vez, adaptado al entorno académico.\n\n"
    "Recibirás una lista de textos originales en formato JSON (un objeto con IDs numéricas). "
    "Debes traducir cada texto respetando el sentido original y las siguientes reglas:\n"
    "1. Mantén intactas las variables como %s, %d, %1$s, {username} etc. en su posición lógica exacta.\n"
    "2. Traduce palabras cortas según el contexto de la aplicación.\n"
    "3. Devuelve la respuesta estrictamente en el mismo formato JSON, manteniendo las mismas claves numéricas, donde el valor sea exclusivamente la traducción.\n"
    "No añadas ninguna introducción, ni comentarios, ni bloques de código de markdown (como ```json). Devuelve solo el objeto JSON limpio."
)

# Inicialització del model
model = genai.GenerativeModel(
    model_name="models/gemini-2.5-flash-lite",
    system_instruction=CONTEXT_PROMPT
)

def traduir_lot_ia(lot_entrades):
    """Prepara un objecte JSON amb les frases del lot i demana la traducció en bloc"""
    # Creem un diccionari de la forma { "0": "frase 1", "1": "frase 2" ... }
    dades_enviar = {str(idx): entry.msgid for idx, entry in enumerate(lot_entrades)}
    text_json = json.dumps(dades_enviar, ensure_ascii=False)

    try:
        response = model.generate_content(text_json)
        resposta_neta = response.text.strip()

        # Netejem possibles marcadors de markdown que de vegades posen les IAs de forma rebel
        if resposta_neta.startswith("```json"):
            resposta_neta = resposta_neta[7:]
        if resposta_neta.endswith("```"):
            resposta_neta = resposta_neta[:-3]
        resposta_neta = resposta_neta.strip()

        # Decodifiquem la llista traduïda
        traduccions_dict = json.loads(resposta_neta)

        # Apliquem les traduccions corresponents de tornada al lot
        comptador_paquet = 0
        for idx, entry in enumerate(lot_entrades):
            clau = str(idx)
            if clau in traduccions_dict and traduccions_dict[clau]:
                entry.msgstr = traduccions_dict[clau]
                comptador_paquet += 1
        return comptador_paquet

    except Exception as e:
        print(f" ❌ Error processant aquest lot de traducció: {e}")
        return 0

def executar_tot():
    if not os.path.exists(RUTA_ENTRADA):
        print(f"❌ Error: No s'ha trobat el fitxer original a {RUTA_ENTRADA}")
        return

    print(f"📖 Carregant el fitxer: {RUTA_ENTRADA}")
    po = polib.pofile(RUTA_ENTRADA)

    # Assegurem la capçalera UTF-8 correcta
    if not po.metadata or 'Content-Type' not in po.metadata:
        po.metadata['Content-Type'] = 'text/plain; charset=UTF-8'
        po.metadata['Content-Transfer-Encoding'] = '8bit'

    # Busquem només les línies que cal traduir
    entrades_per_traduir = [entry for entry in po if entry.msgid and not entry.msgstr]
    total_cadenes = len(entrades_per_traduir)

    if total_cadenes == 0:
        print("✅ No hi ha cap cadena buida per traduir. El fitxer ja està complet.")
        return

    print(f"🔗 S'han trobat {total_cadenes} cadenes pendents de traducció.")
    print(f"📦 Mode d'estalvi de quota actiu: Enviant paquets de {MIDA_LOT} frases per consulta...")

    # Processem per lots (paquets de 40 en 40 frases)
    total_modificats = 0
    num_lot = 0

    for i in range(0, total_cadenes, MIDA_LOT):
        num_lot += 1
        lot_actual = entrades_per_traduir[i:i + MIDA_LOT]

        print(f"🤖 [Consulta RPD #{num_lot}] Traduint lot de frases del {i+1} al {min(i + MIDA_LOT, total_cadenes)}...")

        # Envia el bloc sencer a Gemini
        modificats_en_lot = traduir_lot_ia(lot_actual)
        total_modificats += modificats_en_lot

        if modificats_en_lot > 0:
            # Desem immediatament el fitxer corregit
            po.save(RUTA_SORTIDA)
            print(f"   ↳  Èxit: S'han afegit {modificats_en_lot} traduccions d'aquest lot.")
        else:
            print("   ↳ ⚠️ El lot ha fallat o ha retornat buit. Aturant per precaució de quota.")
            break

        # 🕒 Pausa de seguretat entre lots per no superar les 10 consultes per minut (RPM)
        # Com que cada lot triga una mica a processar-se, amb 8 segons de pausa anem completament segurs
        print("⏳ Esperant 8 segons per respectar el límit de velocitat (RPM)...")
        time.sleep(8)

    print(f"\n✨ Procés finalitzat o pausat per límit diari.")
    print(f"📝 S'han completat un total de {total_modificats} traduccions en aquesta sessió.")
    print(f"💾 El fitxer actualitzat està desat a: {RUTA_SORTIDA}")

if __name__ == "__main__":
    executar_tot()
