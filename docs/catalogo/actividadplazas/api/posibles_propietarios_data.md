---
id: "actividadplazas.posibles_propietarios_data"
tipo: "endpoint"
modulo: "actividadplazas"
url: "/src/actividadplazas/posibles_propietarios_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadplazas/infrastructure/ui/http/controllers/posibles_propietarios_data.php"
entrada: ["post.id_activ:integer", "post.id_nom:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadplazas_PosiblesPropietariosDataData"
respuesta_data: ["id:string", "opciones:array", "selected:string", "blanco:boolean", "val_blanco:string"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\actividadplazas\\application\\PosiblesPropietariosData"]
tags: ["actividadplazas", "posibles", "propietarios", "data"]
estado_revision: "generado"
---

# Posibles Propietarios Data

Endpoint backend: devuelve el payload JSON estandar de desplegable (`id`, `opciones`, `selected`, `blanco`, `val_blanco`) con los posibles propietarios de plaza para la persona+actividad indicadas.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadplazas/posibles_propietarios_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/posibles_propietarios_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller+application | No | controller+application |
| `id_nom` | `integer` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadplazas_PosiblesPropietariosDataData`):
  - `id` (`string`)
  - `opciones` (`array`)
  - `selected` (`string`)
  - `blanco` (`boolean`)
  - `val_blanco` (`string`)

## Efectos colaterales

- Devuelve el payload del desplegable "posibles propietarios de plaza" usado por `apps/asistentes` al asignar plaza a una asistencia.

## Casos De Uso

- `src\actividadplazas\application\PosiblesPropietariosData`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.