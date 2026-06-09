---
id: "actividadestudios.profesores_desplegable_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/profesores_desplegable_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/profesores_desplegable_data.php"
entrada: ["post.id_activ:integer", "post.id_asignatura:integer", "post.id_profesor:integer", "post.salida:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadestudios_ProfesoresDesplegableDataData"
respuesta_data: ["id:string, opciones: list<array{0: int|string, 1: string}>, blanco: bool, val_blanco: string, selected: int"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php"]
casos_uso: ["src\\actividadestudios\\application\\ProfesoresDesplegableData"]
tags: ["actividadestudios", "profesores", "desplegable", "data"]
estado_revision: "generado"
---

# Profesores Desplegable Data

Devuelve los datos (`id`, `opciones`, `selected`, `blanco`) para construir un `<select>` de profesores en el form de `ActividadAsignatura`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadestudios/profesores_desplegable_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/profesores_desplegable_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | No | application |
| `id_asignatura` | `integer` | application | No | application |
| `id_profesor` | `integer` | application | No | application |
| `salida` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadestudios_ProfesoresDesplegableDataData`):
  - `id` (`string, opciones: list<array{0: int|string, 1: string}>, blanco: bool, val_blanco: string, selected: int`)

## Casos De Uso

- `src\actividadestudios\application\ProfesoresDesplegableData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.