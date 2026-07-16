import os

import google.generativeai as genai

api_key = os.environ.get("GEMINI_API_KEY")
if not api_key:
    raise SystemExit("Define GEMINI_API_KEY (variable d'entorn) abans d'executar el script.")

genai.configure(api_key=api_key)

for m in genai.list_models():
    if "generateContent" in m.supported_generation_methods:
        print(m.name)
