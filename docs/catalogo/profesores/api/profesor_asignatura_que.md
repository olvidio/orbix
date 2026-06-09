---
id: "profesores.profesor_asignatura_que"
tipo: "endpoint"
modulo: "profesores"
url: "/src/profesores/profesor_asignatura_que"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/profesores/infrastructure/ui/http/controllers/profesor_asignatura_que.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "profesores_ProfesorAsignaturaQueDataData"
respuesta_data: ["aOpciones:array"]
requiere_hashb: false
frontend_referencias: ["frontend/profesores/controller/profesor_asignatura_que.php"]
casos_uso: ["src\\profesores\\application\\ProfesorAsignaturaQueData"]
tags: ["profesores", "profesor", "asignatura", "que"]
estado_revision: "generado"
---

# Profesor Asignatura Que

Opciones del desplegable de asignatura en profesor_asignatura_que.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/profesores/profesor_asignatura_que`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/profesores/infrastructure/ui/http/controllers/profesor_asignatura_que.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `profesores_ProfesorAsignaturaQueDataData`):
  - `aOpciones` (`array`)

## Casos De Uso

- `src\profesores\application\ProfesorAsignaturaQueData`

## Frontend Relacionado

- `frontend/profesores/controller/profesor_asignatura_que.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.