---
id: "actividadplazas.peticiones_incorporar"
tipo: "endpoint"
modulo: "actividadplazas"
url: "/src/actividadplazas/peticiones_incorporar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadplazas/infrastructure/ui/http/controllers/peticiones_incorporar.php"
entrada: ["post.sactividad:string", "post.sasistentes:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadplazas_PeticionesIncorporarData"
respuesta_data: ["incorporadas:int, mensaje_final:string, error:string"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadplazas/controller/incorporar_peticion.php"]
casos_uso: ["src\\actividadplazas\\application\\PeticionesIncorporar"]
tags: ["actividadplazas", "peticiones", "incorporar"]
estado_revision: "generado"
---

# Peticiones Incorporar

Endpoint backend: incorpora las primeras peticiones de plaza de cada persona como asistencia con plaza asignada/pedida (segun si la actividad es de midele o de otra dl).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadplazas/peticiones_incorporar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/peticiones_incorporar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sactividad` | `string` | controller+application | No | controller+application |
| `sasistentes` | `string` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadplazas_PeticionesIncorporarData`):
  - `incorporadas` (`int, mensaje_final:string, error:string`)

## Casos De Uso

- `src\actividadplazas\application\PeticionesIncorporar`

## Frontend Relacionado

- `frontend/actividadplazas/controller/incorporar_peticion.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.