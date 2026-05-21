---
id: "personas.personas_editar_data"
tipo: "endpoint"
modulo: "personas"
url: "/src/personas/personas_editar_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/personas/infrastructure/ui/http/controllers/personas_editar_data.php"
entrada: ["post.apellido1:string", "post.id_nom:integer", "post.nuevo:integer", "post.obj_pau:string", "post.sel:mixed", "post.tabla:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/personas/controller/personas_editar.php"]
casos_uso: ["src\\personas\\application\\PersonasEditarData"]
tags: ["personas", "editar", "data"]
estado_revision: "generado"
---

# Personas Editar Data

Endpoint JSON: datos para la ficha `personas_editar.phtml`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/personas/personas_editar_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/personas/infrastructure/ui/http/controllers/personas_editar_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `apellido1` | `string` | application | No | application |
| `id_nom` | `integer` | application | No | application |
| `nuevo` | `integer` | application | No | application |
| `obj_pau` | `string` | application | No | application |
| `sel` | `mixed` | application | No | application |
| `tabla` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\personas\application\PersonasEditarData`

## Frontend Relacionado

- `frontend/personas/controller/personas_editar.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.