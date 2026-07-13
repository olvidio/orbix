---
id: "notas.tessera_copiar_select_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/tessera_copiar_select_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/notas/infrastructure/ui/http/controllers/tessera_copiar_select_data.php"
entrada: ["post.id_nom:integer"]
entrada_obligatoria: ["id_nom"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No existe una persona con id_nom: %s"]
frontend_referencias: ["frontend/notas/controller/tessera_copiar_select.php"]
casos_uso: ["src\\notas\\application\\TesseraCopiarSelectData"]
tags: ["notas", "tessera", "copiar", "select", "data"]
estado_revision: "revisado"
---

# Tessera Copiar Select Data

Personas destino posibles para copiar tessera (mismo apellido).

Prepara los datos para elegir a que persona (con el mismo primer apellido) se copiara la tessera de otra persona. Devuelve `['nom' => string, 'posibles_personas' => [id_nom => nombre]]`. Lanza `RuntimeException` si no encuentra la persona origen ni como numerario ni como agregado.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/tessera_copiar_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/tessera_copiar_select_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `integer` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Helper: `ContestarJson::enviar` (doble `JSON.parse` salvo excepciones).
- `nom`, `posibles_personas` (id→nombre).

## Objetivo funcional

Dado `id_nom` origen, lista homónimos por `apellido1`.

## Permisos

- Dossier tessera.

## Errores conocidos

- `No existe una persona con id_nom: %s`

## Casos De Uso

- `src\notas\application\TesseraCopiarSelectData`

## Frontend Relacionado

- `frontend/notas/controller/tessera_copiar_select.php`.