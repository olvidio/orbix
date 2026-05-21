---
id: "cartaspresentacion.carta_presentacion_update"
tipo: "endpoint"
modulo: "cartaspresentacion"
url: "/src/cartaspresentacion/carta_presentacion_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/cartaspresentacion/infrastructure/ui/http/controllers/carta_presentacion_update.php"
entrada: ["post.id_direccion:integer", "post.id_ubi:integer", "post.observ:string", "post.pres_mail:string", "post.pres_nom:string", "post.pres_telf:string", "post.zona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "cartaspresentacion_CartaPresentacionUpdateData"
respuesta_data: ["ok:bool, mensaje: string"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\cartaspresentacion\\application\\CartaPresentacionUpdate"]
tags: ["cartaspresentacion", "carta", "presentacion", "update"]
estado_revision: "generado"
---

# Carta Presentacion Update

Endpoint backend: crea / actualiza una `CartaPresentacion`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/cartaspresentacion/carta_presentacion_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/cartaspresentacion/infrastructure/ui/http/controllers/carta_presentacion_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_direccion` | `integer` | controller+application | No | controller+application |
| `id_ubi` | `integer` | controller+application | No | controller+application |
| `observ` | `string` | controller+application | No | controller+application |
| `pres_mail` | `string` | controller+application | No | controller+application |
| `pres_nom` | `string` | controller+application | No | controller+application |
| `pres_telf` | `string` | controller+application | No | controller+application |
| `zona` | `string` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `cartaspresentacion_CartaPresentacionUpdateData`):
  - `ok` (`bool, mensaje: string`)

## Efectos colaterales

- Al terminar, ejecuta `sanear()` — igual que el controlador legacy — para eliminar cartas cuyas direcciones ya no pertenecen al centro.

## Casos De Uso

- `src\cartaspresentacion\application\CartaPresentacionUpdate`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.