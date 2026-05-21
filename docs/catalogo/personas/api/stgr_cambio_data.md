---
id: "personas.stgr_cambio_data"
tipo: "endpoint"
modulo: "personas"
url: "/src/personas/stgr_cambio_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/personas/infrastructure/ui/http/controllers/stgr_cambio_data.php"
entrada: ["post.id_nom:integer", "post.id_tabla:string", "post.sel:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/personas/controller/stgr_cambio.php"]
casos_uso: ["src\\personas\\application\\StgrCambioData"]
tags: ["personas", "stgr", "cambio", "data"]
estado_revision: "generado"
---

# Stgr Cambio Data

Endpoint JSON: datos para el formulario `stgr_cambio.phtml`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/personas/stgr_cambio_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/personas/infrastructure/ui/http/controllers/stgr_cambio_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `integer` | application | No | application |
| `id_tabla` | `string` | application | No | application |
| `sel` | `mixed` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\personas\application\StgrCambioData`

## Frontend Relacionado

- `frontend/personas/controller/stgr_cambio.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.