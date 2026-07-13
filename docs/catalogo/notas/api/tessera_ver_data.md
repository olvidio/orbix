---
id: "notas.tessera_ver_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/tessera_ver_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/notas/infrastructure/ui/http/controllers/tessera_ver_data.php"
entrada: ["post.id_nom:integer"]
entrada_obligatoria: ["id_nom"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se ha encontrado la asignatura con id: %s"]
frontend_referencias: ["frontend/notas/controller/tessera_ver.php"]
casos_uso: ["src\\notas\\application\\TesseraVerData"]
tags: ["notas", "tessera", "ver", "data"]
estado_revision: "revisado"
---

# Tessera Ver Data

Datos de visualización de tessera.

Dataset JSON para {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/tessera_ver_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/tessera_ver_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Helper: `ContestarJson::enviar` (doble `JSON.parse` salvo excepciones).
- Estructura tessera en `data`.

## Objetivo funcional

Carga notas agrupadas para pantalla tessera.

## Permisos

- Dossier tessera.

## Errores conocidos

- `No se ha encontrado la asignatura con id: %s`

## Casos De Uso

- `src\notas\application\TesseraVerData`

## Frontend Relacionado

- `frontend/notas/controller/tessera_ver.php`.