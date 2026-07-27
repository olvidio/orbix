---
id: "shared.ayuda_preguntar"
tipo: "endpoint"
modulo: "shared"
url: "/src/shared/ayuda_preguntar"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/shared/infrastructure/ui/http/controllers/ayuda_preguntar.php"
entrada: ["post.pregunta:string", "post.q:string", "post.limite:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/shared/controller/ayuda_preguntar.php", "frontend/shared/view/ayuda_index.phtml"]
casos_uso: ["src\\shared\\application\\AyudaPreguntar"]
tags: ["shared", "ayuda", "documentacion", "busqueda"]
estado_revision: "revisado"
---

# Ayuda Preguntar

Búsqueda léxica sobre `docs/manual` y `docs/ai` instalados en el servidor. Sin LLM ni red externa.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/shared/ayuda_preguntar`
- Metodos: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/shared/infrastructure/ui/http/controllers/ayuda_preguntar.php`

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `pregunta` | string | No | Texto de la pregunta; alias `q` |
| `limite` | int | No | Máx. resultados (1–20, default 8) |

## Salida

Payload (doble `JSON.parse` vía `ContestarJson::enviar`):

| Clave | Descripción |
|-------|-------------|
| `pregunta` | Eco de la consulta |
| `respuesta` | Resumen textual con hasta 3 extractos |
| `modo` | Siempre `busqueda_local` (sin Ollama) |
| `resultados[]` | `titulo`, `tipo` (`manual`\|`ai`), `modulo`, `fuente`, `excerpt`, `score`, `preguntas` |

## Casos particulares

- Pregunta vacía → `resultados: []` y mensaje pidiendo una pregunta.
- Sin carpeta `docs/` en `ConfigGlobal::$directorio` → mensaje de error de configuración.
- Puntuación: título (+4), preguntas del front matter (+5), cuerpo (+1); bonus si coinciden ≥2 términos.
- Stopwords ES/EN descartadas.

## Permisos

- Sin control propio; acceso vía pantalla de ayuda (sesión Orbix).

## Frontend Relacionado

- `frontend/shared/controller/ayuda_preguntar.php` (formulario)
- Enlace desde `ayuda_index` y `public/ayuda/preguntar.php`
