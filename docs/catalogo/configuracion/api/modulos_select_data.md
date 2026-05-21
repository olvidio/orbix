---
id: "configuracion.modulos_select_data"
tipo: "endpoint"
modulo: "configuracion"
url: "/src/configuracion/modulos_select_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/configuracion/infrastructure/ui/http/controllers/modulos_select_data.php"
entrada: ["post.id_sel:string", "post.restored_id_sel:string", "post.restored_scroll_id:string", "post.scroll_id:string", "post.stack:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/configuracion/controller/modulos_select.php"]
casos_uso: ["src\\configuracion\\application\\ModulosSelectData"]
tags: ["configuracion", "modulos", "select", "data"]
estado_revision: "generado"
---

# Modulos Select Data

JSON para {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/configuracion/modulos_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/configuracion/infrastructure/ui/http/controllers/modulos_select_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_sel` | `string` | application | No | application |
| `restored_id_sel` | `string` | application | No | application |
| `restored_scroll_id` | `string` | application | No | application |
| `scroll_id` | `string` | application | No | application |
| `stack` | `mixed` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\configuracion\application\ModulosSelectData`

## Frontend Relacionado

- `frontend/configuracion/controller/modulos_select.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.