---
id: "notas.tessera_imprimir_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/tessera_imprimir_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/notas/infrastructure/ui/http/controllers/tessera_imprimir_data.php"
entrada: ["post.id_nom:integer"]
entrada_obligatoria: ["id_nom"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se ha encontrado la asignatura con id: %s"]
frontend_referencias: ["frontend/notas/controller/tessera_imprimir.php", "frontend/notas/controller/tessera_imprimir_mpdf.php"]
casos_uso: ["src\\notas\\application\\TesseraImprimirData"]
tags: ["notas", "tessera", "imprimir", "data"]
estado_revision: "revisado"
---

# Tessera Imprimir Data

Datos para imprimir la tessera de estudios de una persona.

Datos imprimibles de tessera ya serializados (sin objetos dominio → JSON estable).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/tessera_imprimir_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/tessera_imprimir_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Helper: `ContestarJson::enviar` (doble `JSON.parse` salvo excepciones).
- Payload impresión en `data`.

## Objetivo funcional

Payload HTML/estructura para `tessera_imprimir` y variantes mpdf.

## Permisos

- Dossier tessera.

## Errores conocidos

- `No se ha encontrado la asignatura con id: %s`

## Casos De Uso

- `src\notas\application\TesseraImprimirData`

## Frontend Relacionado

- `frontend/notas/controller/tessera_imprimir.php`.