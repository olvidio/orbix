---
id: "actividadestudios.ca_posibles_que_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/ca_posibles_que_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/ca_posibles_que_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadestudios_CaPosiblesQueDataData"
respuesta_data: ["grupo_estudios:?string", "mi_grupo:string", "aCentrosNExt:array", "aCentrosAgdExt:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadestudios/controller/ca_posibles_que.php"]
casos_uso: ["src\\actividadestudios\\application\\CaPosiblesQueData"]
tags: ["actividadestudios", "ca", "posibles", "que", "data"]
estado_revision: "generado"
---

# Ca Posibles Que Data

Desplegables y texto de grupo para `ca_posibles_que.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadestudios/ca_posibles_que_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/ca_posibles_que_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadestudios_CaPosiblesQueDataData`):
  - `grupo_estudios` (`?string`)
  - `mi_grupo` (`string`)
  - `aCentrosNExt` (`array`)
  - `aCentrosAgdExt` (`array`)

## Casos De Uso

- `src\actividadestudios\application\CaPosiblesQueData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/ca_posibles_que.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.