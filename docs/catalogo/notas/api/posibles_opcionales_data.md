---
id: "notas.posibles_opcionales_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/posibles_opcionales_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/notas/infrastructure/ui/http/controllers/posibles_opcionales_data.php"
entrada: ["post.id_nom:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/actividadestudios/controller/form_matriculas_de_una_persona.php", "frontend/notas/controller/form_notas_de_una_persona.php"]
casos_uso: ["src\\notas\\application\\PosiblesOpcionalesData"]
tags: ["notas", "posibles", "opcionales", "data"]
estado_revision: "generado"
---

# Posibles Opcionales Data

Devuelve las asignaturas opcionales que puede cursar la persona con el contrato estandar de desplegable (ver `refactor.md` §"Desplegables devueltos por endpoints AJAX: payload + constructor en frontend").

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/posibles_opcionales_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/notas/infrastructure/ui/http/controllers/posibles_opcionales_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `integer` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\notas\application\PosiblesOpcionalesData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/form_matriculas_de_una_persona.php`
- `frontend/notas/controller/form_notas_de_una_persona.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.