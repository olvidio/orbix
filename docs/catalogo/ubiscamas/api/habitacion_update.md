---
id: "ubiscamas.habitacion_update"
tipo: "endpoint"
modulo: "ubiscamas"
url: "/src/ubiscamas/habitacion_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubiscamas/infrastructure/ui/http/controllers/habitacion_update.php"
entrada: ["post.adaptada:mixed", "post.despacho:mixed", "post.id_habitacion:string", "post.id_ubi:integer", "post.new_camas_desc:array", "post.new_camas_larga:array", "post.new_camas_vip:array", "post.nombre:string", "post.nuevo:string", "post.numero_camas:integer", "post.numero_camas_vip:integer", "post.observaciones:string", "post.orden:integer", "post.planta:string", "post.sel:array", "post.sillon:mixed", "post.tipoLavabo:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: []
casos_uso: []
tags: ["ubiscamas", "habitacion", "update"]
estado_revision: "generado"
---

# Habitacion Update

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubiscamas/habitacion_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/habitacion_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `adaptada` | `mixed` | controller | No | controller |
| `despacho` | `mixed` | controller | No | controller |
| `id_habitacion` | `string` | controller | No | controller |
| `id_ubi` | `integer` | controller | No | controller |
| `new_camas_desc` | `array` | controller | No | controller |
| `new_camas_larga` | `array` | controller | No | controller |
| `new_camas_vip` | `array` | controller | No | controller |
| `nombre` | `string` | controller | No | controller |
| `nuevo` | `string` | controller | No | controller |
| `numero_camas` | `integer` | controller | No | controller |
| `numero_camas_vip` | `integer` | controller | No | controller |
| `observaciones` | `string` | controller | No | controller |
| `orden` | `integer` | controller | No | controller |
| `planta` | `string` | controller | No | controller |
| `sel` | `array` | controller | No | controller |
| `sillon` | `mixed` | controller | No | controller |
| `tipoLavabo` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.