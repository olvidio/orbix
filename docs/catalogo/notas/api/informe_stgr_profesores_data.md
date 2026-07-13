---
id: "notas.informe_stgr_profesores_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/informe_stgr_profesores_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/notas/infrastructure/ui/http/controllers/informe_stgr_profesores_data.php"
entrada: ["post.lista:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "notas_InformeStgrProfesoresData"
respuesta_data: ["res:array", "textos:array", "curso_txt:string"]
requiere_hashb: false
frontend_referencias: ["frontend/notas/controller/informe_stgr_profesores.php"]
casos_uso: ["src\\notas\\application\\InformeStgrProfesores"]
tags: ["notas", "informe", "stgr", "profesores", "data"]
estado_revision: "revisado"
---

# Informe Stgr Profesores Data

Informe anual del claustro/profesores.

Calcula el informe anual STGR de "profesores" (puntos 36..47). Encapsula el uso de `src\notas\application\legacy\Resumen` (legacy) para que los controllers del frontend no importen la clase legacy directamente. Devuelve un array neutro `{res, textos, curso_txt}` listo para renderizado. Tipos de profesor utilizados: 1 Ordinario 2 Extraordinario 3 Adjunto 4 Encargado 5 Ayudante 6 Asociado 0 (todos)

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/informe_stgr_profesores_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/informe_stgr_profesores_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `lista` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Helper: `ContestarJson::enviar` (doble `JSON.parse` salvo excepciones).
- `res`, `textos`, `curso_txt`.
- Payload en `data` (schema `notas_InformeStgrProfesoresData`):
  - `res` (`array`)
  - `textos` (`array`)
  - `curso_txt` (`string`)

## Objetivo funcional

Métricas docentes con variante `lista` números vs listados.

## Permisos

- Menú informe anual profesores.

## Casos De Uso

- `src\notas\application\InformeStgrProfesores`

## Frontend Relacionado

- `frontend/notas/controller/informe_stgr_profesores.php`.