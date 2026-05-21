---
id: "actividadescentro.centros_disponibles_data"
tipo: "endpoint"
modulo: "actividadescentro"
url: "/src/actividadescentro/centros_disponibles_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadescentro/infrastructure/ui/http/controllers/centros_disponibles_data.php"
entrada: ["post.f_ini_act:string", "post.fin:string", "post.id_activ:integer", "post.inicio:string", "post.tipo:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadescentro_CentrosDisponiblesDataData"
respuesta_data: ["0:array"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\actividadescentro\\application\\CentrosDisponiblesData"]
tags: ["actividadescentro", "centros", "disponibles", "data"]
estado_revision: "generado"
---

# Centros Disponibles Data

Endpoint backend: devuelve los centros disponibles (candidatos) para asignar como encargado de una actividad, filtrados por `tipo` (sg / sr / nagd / sssc / sfsg / sfsr / sfnagd).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadescentro/centros_disponibles_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadescentro/infrastructure/ui/http/controllers/centros_disponibles_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `f_ini_act` | `string` | controller+application | No | controller+application |
| `fin` | `string` | controller+application | No | controller+application |
| `id_activ` | `integer` | controller+application | No | controller+application |
| `inicio` | `string` | controller+application | No | controller+application |
| `tipo` | `string` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadescentro_CentrosDisponiblesDataData`):
  - `0` (`array`)

## Casos De Uso

- `src\actividadescentro\application\CentrosDisponiblesData`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.