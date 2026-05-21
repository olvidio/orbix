---
id: "personas.stgr_update"
tipo: "endpoint"
modulo: "personas"
url: "/src/personas/stgr_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/personas/infrastructure/ui/http/controllers/stgr_update.php"
entrada: ["post.id_nom:integer", "post.id_tabla:string", "post.nivel_stgr:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No existe la clase de la persona", "No se encuentra la persona"]
frontend_referencias: ["frontend/personas/view/stgr_cambio.phtml"]
casos_uso: ["src\\personas\\application\\StgrUpdate"]
tags: ["personas", "stgr", "update"]
estado_revision: "generado"
---

# Stgr Update

Endpoint JSON: actualiza el `nivel_stgr` de una persona.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/personas/stgr_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/personas/infrastructure/ui/http/controllers/stgr_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `integer` | controller | No | controller |
| `id_tabla` | `string` | controller | No | controller |
| `nivel_stgr` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `No existe la clase de la persona`
- `No se encuentra la persona`

## Casos De Uso

- `src\personas\application\StgrUpdate`

## Frontend Relacionado

- `frontend/personas/view/stgr_cambio.phtml`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.