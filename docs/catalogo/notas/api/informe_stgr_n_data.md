---
id: "notas.informe_stgr_n_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/informe_stgr_n_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/notas/infrastructure/ui/http/controllers/informe_stgr_n_data.php"
entrada: ["post.dl:array", "post.lista:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "notas_InformeStgrNumerariosData"
respuesta_data: ["res:array", "textos:array", "curso_txt:string"]
requiere_hashb: false
frontend_referencias: ["frontend/notas/controller/informe_stgr_n.php"]
casos_uso: ["src\\notas\\application\\InformeStgrNumerarios"]
tags: ["notas", "informe", "stgr", "n", "data"]
estado_revision: "generado"
---

# Informe Stgr N Data

Calcula el informe anual STGR de "numerarios" (puntos 1..18 + `x`). Encapsula el uso de `src\notas\application\legacy\Resumen` (legacy) para que los controllers del frontend no importen la clase legacy directamente. Devuelve un array neutro `{res, textos, curso_txt}` listo para renderizado.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/informe_stgr_n_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/notas/infrastructure/ui/http/controllers/informe_stgr_n_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl` | `array` | controller | No | controller |
| `lista` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `notas_InformeStgrNumerariosData`):
  - `res` (`array`)
  - `textos` (`array`)
  - `curso_txt` (`string`)

## Casos De Uso

- `src\notas\application\InformeStgrNumerarios`

## Frontend Relacionado

- `frontend/notas/controller/informe_stgr_n.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.