---
id: "notas.buscar_acta"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/buscar_acta"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/notas/infrastructure/ui/http/controllers/buscar_acta.php"
entrada: ["post.acta:string"]
entrada_obligatoria: ["acta"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se ha encontrado la asignatura con id: %s"]
frontend_referencias: ["frontend/notas/controller/form_notas_de_una_persona.php"]
casos_uso: ["src\\notas\\application\\BuscarActaData"]
tags: ["notas", "buscar", "acta"]
estado_revision: "revisado"
---

# Buscar Acta

Resuelve datos de un acta por número (autocomplete/búsqueda).

Busca un acta por su numero abreviado.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/buscar_acta`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/buscar_acta.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `acta` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Helper: `ContestarJson::enviar` (doble `JSON.parse` salvo excepciones).
- Payload acta en `data`.

## Objetivo funcional

Dado `acta`, devuelve metadatos para rellenar formularios de nota.

## Permisos

- Formulario notas / acta_ver.

## Errores conocidos

- `No se ha encontrado la asignatura con id: %s`

## Casos De Uso

- `src\notas\application\BuscarActaData`

## Frontend Relacionado

- Invocado desde JS en formularios de notas.