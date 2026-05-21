---
id: "actividades.actividad_nuevo_curso_ejecutar"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_nuevo_curso_ejecutar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_nuevo_curso_ejecutar.php"
entrada: ["post.ver_lista:string", "post.year:integer", "post.year_ref:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/actividades/controller/actividad_nuevo_curso.php"]
casos_uso: ["src\\actividades\\application\\ActividadNuevoCursoEjecutar"]
tags: ["actividades", "actividad", "nuevo", "curso", "ejecutar"]
estado_revision: "generado"
---

# Actividad Nuevo Curso Ejecutar

Endpoint backend para `actividad_nuevo_curso` (ejecucion).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/actividad_nuevo_curso_ejecutar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_nuevo_curso_ejecutar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ver_lista` | `string` | controller | No | controller |
| `year` | `integer` | controller | No | controller |
| `year_ref` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\actividades\application\ActividadNuevoCursoEjecutar`

## Frontend Relacionado

- `frontend/actividades/controller/actividad_nuevo_curso.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.