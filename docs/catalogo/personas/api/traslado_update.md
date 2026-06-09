---
id: "personas.traslado_update"
tipo: "endpoint"
modulo: "personas"
url: "/src/personas/traslado_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/personas/infrastructure/ui/http/controllers/traslado_update.php"
entrada: ["post.ctr_o:string", "post.dl:string", "post.f_ctr:string", "post.f_dl:string", "post.id_ctr_o:string", "post.id_pau:integer", "post.new_ctr:string", "post.new_dl:string", "post.obj_pau:string", "post.situacion:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Faltan id_pau u obj_pau", "No existe la clase de la persona", "No se encuentra la persona", "Falta una situación válida"]
frontend_referencias: ["frontend/personas/view/traslado_form.phtml"]
casos_uso: ["src\\personas\\application\\TrasladoUpdate"]
tags: ["personas", "traslado", "update"]
estado_revision: "generado"
---

# Traslado Update

Endpoint JSON: aplica traslado de centro/delegacion.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/personas/traslado_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/personas/infrastructure/ui/http/controllers/traslado_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ctr_o` | `string` | application | No | application |
| `dl` | `string` | application | No | application |
| `f_ctr` | `string` | application | No | application |
| `f_dl` | `string` | application | No | application |
| `id_ctr_o` | `string` | application | No | application |
| `id_pau` | `integer` | application | No | application |
| `new_ctr` | `string` | application | No | application |
| `new_dl` | `string` | application | No | application |
| `obj_pau` | `string` | application | No | application |
| `situacion` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Aplica el traslado de centro y/o delegacion de una persona y asegura que existe (y queda abierto) el dossier de traslados (tipo 1004).

## Errores conocidos

- `Faltan id_pau u obj_pau`
- `No existe la clase de la persona`
- `No se encuentra la persona`
- `Falta una situación válida`

## Casos De Uso

- `src\personas\application\TrasladoUpdate`

## Frontend Relacionado

- `frontend/personas/view/traslado_form.phtml`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.