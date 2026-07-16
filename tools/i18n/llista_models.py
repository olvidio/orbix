import os

import google.generativeai as genai

from orbix_env import load_repo_env

load_repo_env()

api_key = os.environ.get("GEMINI_API_KEY")
if not api_key:
    raise SystemExit(
        "Define GEMINI_API_KEY en .env (raíz del repo) o como variable de entorno."
    )

genai.configure(api_key=api_key)

for m in genai.list_models():
    if "generateContent" in m.supported_generation_methods:
        print(m.name)
