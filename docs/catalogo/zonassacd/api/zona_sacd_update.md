---
id: "zonassacd.zona_sacd_update"
tipo: "endpoint"
modulo: "zonassacd"
url: "/src/zonassacd/zona_sacd_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/zonassacd/infrastructure/ui/http/controllers/zona_sacd_update.php"
entrada: ["post.acumular:integer", "post.id_zona:string", "post.id_zona_new:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/zonassacd/controller/zona_sacd_update_ajax.php"]
casos_uso: ["src\\zonassacd\\application\\ZonaSacdUpdate"]
tags: ["zonassacd", "zona", "sacd", "update"]
estado_revision: "generado"
---

# Zona Sacd Update

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/zonassacd/zona_sacd_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_sacd_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `acumular` | `integer` | controller | No | controller |
| `id_zona` | `string` | controller | No | controller |
| `id_zona_new` | `string` | controller | No | controller |
| `sel` | `array` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\zonassacd\application\ZonaSacdUpdate`

## Frontend Relacionado

- `frontend/zonassacd/controller/zona_sacd_update_ajax.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.