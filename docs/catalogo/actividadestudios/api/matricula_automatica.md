---
id: "actividadestudios.matricula_automatica"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/matricula_automatica"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/matricula_automatica.php"
entrada: ["post.id_activ:integer", "post.id_pau:integer", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/actividadestudios/controller/matricular.php"]
casos_uso: ["src\\actividadestudios\\application\\MatriculaAutomatica"]
tags: ["actividadestudios", "matricula", "automatica"]
estado_revision: "generado"
---

# Matricula Automatica

Matricula masivamente a una o varias personas en las asignaturas del plan de estudios de su actividad vigente.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadestudios/matricula_automatica`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/matricula_automatica.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | No | application |
| `id_pau` | `integer` | application | No | application |
| `sel` | `array` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\actividadestudios\application\MatriculaAutomatica`

## Frontend Relacionado

- `frontend/actividadestudios/controller/matricular.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.