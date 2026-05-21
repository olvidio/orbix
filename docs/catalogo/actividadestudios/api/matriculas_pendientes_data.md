---
id: "actividadestudios.matriculas_pendientes_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/matriculas_pendientes_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/matriculas_pendientes_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadestudios_MatriculasPendientesDataData"
respuesta_data: ["msg_err:string, a_valores: array<int|string, array<string|int, mixed>>"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadestudios/controller/matriculas_pendientes.php"]
casos_uso: ["src\\actividadestudios\\application\\MatriculasPendientesData"]
tags: ["actividadestudios", "matriculas", "pendientes", "data"]
estado_revision: "generado"
---

# Matriculas Pendientes Data

Filas para `frontend/actividadestudios/controller/matriculas_pendientes.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadestudios/matriculas_pendientes_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/matriculas_pendientes_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadestudios_MatriculasPendientesDataData`):
  - `msg_err` (`string, a_valores: array<int|string, array<string|int, mixed>>`)

## Casos De Uso

- `src\actividadestudios\application\MatriculasPendientesData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/matriculas_pendientes.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.