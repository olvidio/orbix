---
id: "personas.persona_publicar"
tipo: "endpoint"
modulo: "personas"
url: "/src/personas/persona_publicar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/personas/infrastructure/ui/http/controllers/persona_publicar.php"
entrada: ["post.id_nom:integer", "post.id_schema:integer", "post.dl:string|array"]
entrada_obligatoria: ["post.id_nom", "post.id_schema", "post.dl"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Datos de persona no válidos", "Debe indicar al menos una delegación destino", "No se puede publicar hacia la propia delegación", "No se ha podido publicar la persona"]
frontend_referencias: ["frontend/personas/view/persona_publicar.phtml"]
casos_uso: ["src\\personas\\application\\PersonaPublicar"]
tags: ["personas", "persona", "publicar", "mutacion"]
estado_revision: "revisado"
---

# Persona Publicar

Marca una persona como publicada para una o varias DL destino (caso B), actualizando el mapa
`publicado_para` (jsonb) con caducidad por defecto de un mes (`P1M`), salvo destino `*`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Para cada DL en `dl` (string o lista): normaliza el código, rechaza la DL propia, y llama a
`PersonaAllRepository::marcarPublicadoPara`. Destino `*` (`PersonaPublicacion::DL_TODAS`) publica
sin caducidad (`hasta=null`). Resto: TTL `PersonaPublicacion::fechaHastaDefault()` (P1M).

## Endpoint

- URL: `/src/personas/persona_publicar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/personas/infrastructure/ui/http/controllers/persona_publicar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `integer` | controller | Sí | Debe ser > 0 |
| `id_schema` | `integer` | controller | Sí | Debe ser ≥ 1 |
| `dl` | `string` o `array` | controller | Sí | Una o más DL destino; el form FE envía una |

HashFront en UI: campo form `dl` + hidden `id_tabla`, `id_nom`, `id_schema`.

## Salida

- Helper: `ContestarJson::enviar($error_txt, 'ok')`.
- Éxito: `success: true`, `data: "ok"`, `mensaje: ""`.
- Error: `success: false`, texto en `mensaje`.

## Casos particulares

- `id_nom <= 0` o `id_schema < 1` → `Datos de persona no válidos`.
- Lista `dl` vacía tras filtrar blancos → `Debe indicar al menos una delegación destino`.
- `dl === '*'` → publica para todas las DL sin fecha de caducidad; no comprueba DL propia.
- Cualquier otra DL igual a `mi_dele()` → error (no publica hacia la propia).
- Fallo de repositorio en cualquier destino → `No se ha podido publicar la persona` (deja de iterar).
- Validación FE previa: `dl` vacío → alert «Debe elegir una delegación» (no llega al API).

## Permisos

- Sin `perm_*` en el caso de uso. Acceso FE: listado con permiso oficina `est`/`sm`/`agd`.

## Errores conocidos

- `Datos de persona no válidos`
- `Debe indicar al menos una delegación destino`
- `No se puede publicar hacia la propia delegación`
- `No se ha podido publicar la persona`

## Casos De Uso

- `src\personas\application\PersonaPublicar`

## Frontend Relacionado

- `frontend/personas/view/persona_publicar.phtml` (`fnjs_guardar_publicar` → AJAX POST)
