---
id: "actividades.actividad_importar"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_importar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_importar.php"
entrada: ["post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividades_ActividadImportarData"
respuesta_data: ["error_txt:string, avisos: list<string>"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\actividades\\application\\ActividadImportar"]
tags: ["actividades", "actividad", "importar"]
estado_revision: "generado"
---

# Actividad Importar

Endpoint backend AJAX: importa las actividades seleccionadas y regenera su proceso cuando la app `procesos` esta instalada.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/actividad_importar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_importar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividades_ActividadImportarData`):
  - `error_txt` (`string, avisos: list<string>`)

## Casos De Uso

- `src\actividades\application\ActividadImportar`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.