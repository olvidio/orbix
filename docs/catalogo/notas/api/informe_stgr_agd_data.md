---
id: "notas.informe_stgr_agd_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/informe_stgr_agd_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/notas/infrastructure/ui/http/controllers/informe_stgr_agd_data.php"
entrada: ["post.dl:array", "post.lista:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "notas_InformeStgrAgregadosData"
respuesta_data: ["res:array", "textos:array", "curso_txt:string"]
requiere_hashb: false
frontend_referencias: ["frontend/notas/controller/informe_stgr_agd.php"]
casos_uso: ["src\\notas\\application\\InformeStgrAgregados"]
tags: ["notas", "informe", "stgr", "agd", "data"]
estado_revision: "revisado"
---

# Informe Stgr Agd Data

Informe anual STGR de agregados (métricas 1..18 + totales).

Calcula el informe anual STGR de "agregados" (puntos 21..33 + `x`). Encapsula el uso de `src\notas\application\legacy\Resumen` (legacy) para que los controllers del frontend no importen la clase legacy directamente. Devuelve un array neutro `{res, textos, curso_txt}` listo para renderizado.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/informe_stgr_agd_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/informe_stgr_agd_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl` | `array` | controller | No | controller |
| `lista` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Helper: `ContestarJson::enviar` (doble `JSON.parse` salvo excepciones).
- `res`, `textos`, `curso_txt` en `data`.
- Payload en `data` (schema `notas_InformeStgrAgregadosData`):
  - `res` (`array`)
  - `textos` (`array`)
  - `curso_txt` (`string`)

## Objetivo funcional

Parámetros `lista` (0=números, 1=listados HTML), `dl[]`, `ce_lugar`. Usa `Resumen` legacy.

## Permisos

- Menú informe anual agd / previsión asistentes (variante calendario).

## Casos De Uso

- `src\notas\application\InformeStgrAgregados`

## Frontend Relacionado

- `frontend/notas/controller/informe_stgr_agd.php`.