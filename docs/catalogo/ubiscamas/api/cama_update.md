---
id: "ubiscamas.cama_update"
tipo: "endpoint"
modulo: "ubiscamas"
url: "/src/ubiscamas/cama_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubiscamas/infrastructure/ui/http/controllers/cama_update.php"
entrada: ["post.descripcion:string", "post.id_cama:string", "post.id_habitacion:string", "post.larga:string", "post.sel:array", "post.vip:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubiscamas/helpers/UbiscamasFormHashCompose.php"]
casos_uso: []
tags: ["ubiscamas", "cama", "update"]
estado_revision: "generado"
---

# Cama Update

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubiscamas/cama_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/cama_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `descripcion` | `string` | controller | No | controller |
| `id_cama` | `string` | controller | No | controller |
| `id_habitacion` | `string` | controller | No | controller |
| `larga` | `string` | controller | No | controller |
| `sel` | `array` | controller | No | controller |
| `vip` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/ubiscamas/helpers/UbiscamasFormHashCompose.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.