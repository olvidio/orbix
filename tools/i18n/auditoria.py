import os
import time
import json
import webbrowser
import polib
from http.server import SimpleHTTPRequestHandler, HTTPServer
from socketserver import ThreadingMixIn
from groq import Groq

# =====================================================================
# CONFIGURACIÓ DE VARIABLES
# =====================================================================
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
LANG_DIR = os.path.normpath(os.path.join(SCRIPT_DIR, '..', '..', 'languages'))

IDIOMA_DESTI = "ca_ES.UTF-8"
MIDA_LOT = 30
PORT = 8080  # Port on s'obrirà la pàgina web de revisió

RUTA_ENTRADA = os.path.join(LANG_DIR, IDIOMA_DESTI, "LC_MESSAGES", "orbix.po")
RUTA_HTML = os.path.join(LANG_DIR, IDIOMA_DESTI, "LC_MESSAGES", "auditoria.html")

GROQ_API_KEY = os.environ.get("GROQ_API_KEY")
if not GROQ_API_KEY:
    raise SystemExit("Define GROQ_API_KEY (variable d'entorn) abans d'executar el script.")

client = Groq(api_key=GROQ_API_KEY)
# =====================================================================

CONTEXT_PROMPT = (
    "Actúa como un revisor lingüístico y auditor experto de software en catalán.\n"
    "El contexto es una aplicación web académica para gestionar estudios y actividades.\n"
    "Devuelve OBLIGATORIAMENTE un objeto JSON con la siguiente estructura interna para cada ID analizada:\n"
    "{\n"
    "  \"id_entrada\": {\n"
    "     \"estado\": \"CORRECTO\" o \"SUSTANCIALMENTE_MEJORABLE\",\n"
    "     \"sugerencia\": \"La traducción propuesta (solo si es mejorable)\",\n"
    "     \"motivo\": \"Breve explicación en castellano del porqué del cambio\"\n"
    "  }\n"
    "}\n"
    "Solo sugiere cambios si hay errores o mejoras reales. No incluyas texto fuera del JSON."
)

def auditar_lot_ia(lot_entrades):
    dades_enviar = {str(idx): {"original": entry.msgid, "actual": entry.msgstr} for idx, entry in enumerate(lot_entrades)}
    try:
        response = client.chat.completions.create(
            model="llama-3.1-8b-instant",
            messages=[{"role": "system", "content": CONTEXT_PROMPT}, {"role": "user", "content": json.dumps(dades_enviar, ensure_ascii=False)}],
            temperature=0.1,
            response_format={"type": "json_object"}
        )
        resultat = json.loads(response.choices[0].message.content.strip())

        propostes = []
        for idx, entry in enumerate(lot_entrades):
            clau = str(idx)
            if clau in resultat and resultat[clau].get("estado") == "SUSTANCIALMENTE_MEJORABLE":
                propostes.append({
                    "msgid": entry.msgid,
                    "actual": entry.msgstr,
                    "sugerencia": resultat[clau].get("sugerencia"),
                    "motivo": resultat[clau].get("motivo")
                })
        return propostes
    except Exception as e:
        print(f" ❌ Error en el lot: {e}")
        return []

def generar_interficie_html(canvis):
    """Genera la pàgina web local amb l'estil i els botons de Clic"""
    files_html = ""
    for idx, c in enumerate(canvis):
        # Escapem cometes per evitar errors a JavaScript
        msgid_esc = c['msgid'].replace('"', '&quot;')
        sug_esc = c['sugerencia'].replace('"', '&quot;')

        files_html += f"""
        <tr id="row-{idx}">
            <td><code>{c['msgid']}</code></td>
            <td><span class="badge badge-danger">{c['actual']}</span></td>
            <td><span class="badge badge-success">{c['sugerencia']}</span></td>
            <td><small class="text-muted">{c['motivo']}</small></td>
            <td>
                <button class="btn btn-sm btn-success" onclick="aplicarCanvi({idx}, `{msgid_esc}`, `{sug_esc}`)">✓ Acceptar</button>
                <button class="btn btn-sm btn-light" onclick="descartar({idx})">Omet</button>
            </td>
        </tr>
        """

    # 🔥 CORRECCIÓ: Si no hi ha canvis, preparem el text de forma segura fora de l'f-string
    if not files_html:
        files_html = """
        <tr>
            <td colspan="5" class="text-center text-success py-4">
                🎉 <strong>No s'ha trobat cap error estructural ni millora substancial!</strong> Tot el fitxer és correcte.
            </td>
        </tr>
        """

    html_complet = f"""
    <!DOCTYPE html>
    <html lang="ca">
    <head>
        <meta charset="UTF-8">
        <title>Auditoria Interactiva PO</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
        <style>
            body {{ padding: 30px; background-color: #f8f9fa; }}
            .table {{ background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }}
            code {{ color: #e83e8c; }}
        </style>
    </head>
    <body>
        <div class="container-fluid">
            <h2 class="mb-4">🔍 Revisor de Traduccions Interactiu</h2>
            <p class="text-muted">Fes clic a <strong>Acceptar</strong> per aplicar el canvi directament al fitxer <code>orbix.po</code>.</p>

            <table class="table table-hover table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Text Original (Castellà)</th>
                        <th>Traducció Actual</th>
                        <th>Suggeriment IA</th>
                        <th>Motiu del canvi</th>
                        <th>Accions</th>
                    </tr>
                </thead>
                <tbody>
                    {files_html}
                </tbody>
            </table>
        </div>

        <script>
            function aplicarCanvi(id, msgid, novaTraduccio) {{
                fetch('/aplicar', {{
                    method: 'POST',
                    headers: {{ 'Content-Type': 'application/json' }},
                    body: JSON.stringify({{ msgid: msgid, msgstr: novaTraduccio }})
                }})
                .then(res => res.json())
                .then(data => {{
                    if(data.status === 'ok') {{
                        document.getElementById('row-'+id).style.opacity = '0.3';
                        document.getElementById('row-'+id).style.backgroundColor = '#d4edda';
                        document.getElementById('row-'+id).querySelectorAll('button').forEach(b => b.disabled = true);
                    }} else {{
                        alert('Error aplicant el canvi');
                    }}
                }});
            }}

            function descartar(id) {{
                document.getElementById('row-'+id).remove();
            }}
        </script>
    </body>
    </html>
    """
    with open(RUTA_HTML, "w", encoding="utf-8") as f:
        f.write(html_complet)

# =====================================================================
# SERVIDOR BACKEND PER REBRE ELS CLICS DE L'HTML
# =====================================================================
class POUpdateServer(SimpleHTTPRequestHandler):
    def do_GET(self):
        if self.path == '/' or self.path == '/auditoria.html':
            self.path = RUTA_HTML
        return super().do_GET()

    def do_POST(self):
        if self.path == '/aplicar':
            content_length = int(self.headers['Content-Length'])
            post_data = json.loads(self.rfile.read(content_length).decode('utf-8'))

            target_msgid = post_data['msgid']
            target_msgstr = post_data['msgstr']

            # Obrim, modifiquem i desem el fitxer .po real al disc
            po = polib.pofile(RUTA_ENTRADA)
            for entry in po:
                if entry.msgid == target_msgid:
                    entry.msgstr = target_msgstr
                    break
            po.save(RUTA_ENTRADA)

            self.send_response(200)
            self.send_header('Content-Type', 'application/json')
            self.end_headers()
            self.rfile.close()
            self.wfile.write(json.dumps({"status": "ok"}).encode('utf-8'))

class ThreadedHTTPServer(ThreadingMixIn, HTTPServer):
    pass

def iniciar_servidor():
    server_address = ('', PORT)
    httpd = ThreadedHTTPServer(server_address, POUpdateServer)
    print(f"\n🖥️  Servidor actiu a http://localhost:{PORT}")
    print("🌍 S'està obrint el navegador automàticament...")
    webbrowser.open(f"http://localhost:{PORT}")
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        print("\n🛑 Servidor detingut de manera segura.")

# =====================================================================
# EXECUCIÓ PRINCIPAL
# =====================================================================
if __name__ == "__main__":
    if not os.path.exists(RUTA_ENTRADA):
        print(f"❌ No es troba {RUTA_ENTRADA}")
        exit()

    print("📖 Llegint fitxer per llançar auditoria...")
    po = polib.pofile(RUTA_ENTRADA)
    entrades = [e for e in po if e.msgid and e.msgstr]

    print(f"🔍 Analitzant {len(entrades)} frases amb Groq...")
    tots_els_canvis = []

    # Executa l'auditoria per lots (només per recollir propostes)
    for i in range(0, len(entrades), MIDA_LOT):
        lot = entrades[i:i+MIDA_LOT]
        print(f"🤖 Analitzant línies {i+1} a {min(i+MIDA_LOT, len(entrades))}...")
        tots_els_canvis.extend(auditar_lot_ia(lot))
        time.sleep(3)

    print(f"📊 Auditoria completada. S'han trobat {len(tots_els_canvis)} millores.")
    generar_interficie_html(tots_els_canvis)

    # Llança la interfície web de clics
    iniciar_servidor()
