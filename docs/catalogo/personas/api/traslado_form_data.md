---
id: "personas.traslado_form_data"
tipo: "endpoint"
modulo: "personas"
url: "/src/personas/traslado_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/personas/infrastructure/ui/http/controllers/traslado_form_data.php"
entrada: ["post.id_pau:integer", "post.sel:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No encuentro a nadie con id_nom: %d", "con las personas de paso no tiene sentido."]
frontend_referencias: ["frontend/personas/controller/traslado_form.php"]
casos_uso: ["src\\personas\\application\\TrasladoFormData"]
tags: ["personas", "traslado", "form", "data"]
estado_revision: "revisado"
---

# Traslado Form Data

Datos iniciales del formulario de traslado de centro y/o delegación (`traslado_form.phtml`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Localiza la persona en el esquema global (`PersonaFinderService::findPersonaEnGlobal`) por
`sel` o `id_pau`. Rechaza `PersonaPub` (de paso publicadas). Precarga centro y delegación
actuales, fecha de hoy, y opciones de centros (excluye tipos c/g/i), delegaciones destino
y situaciones válidas para traslado.

## Endpoint

- URL: `/src/personas/traslado_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/personas/infrastructure/ui/http/controllers/traslado_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `mixed` | application | No | Token `id_nom#…` (usa id_nom como id_pau) |
| `id_pau` | `integer` | application | No | Alias de `id_nom` de la persona |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Claves: `titulo`, `id_ctr`, `nombre_ctr`, `dl`, `hoy`, `id_pau`, `opciones_centros`,
  `opciones_dl`, `opciones_situacion`.

## Permisos

- Sin control en el caso de uso. Enlace «traslado» solo en ficha edición (`personas_editar`)
  con permiso de edición del colectivo.

## Errores conocidos

- `No encuentro a nadie con id_nom: <id>`
- `con las personas de paso no tiene sentido.`

## Casos De Uso

- `src\personas\application\TrasladoFormData`

## Frontend Relacionado

- `frontend/personas/controller/traslado_form.php` (desde ficha `ir_a_traslado`)
