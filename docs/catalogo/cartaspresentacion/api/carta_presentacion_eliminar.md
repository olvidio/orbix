---
id: "cartaspresentacion.carta_presentacion_eliminar"
tipo: "endpoint"
modulo: "cartaspresentacion"
url: "/src/cartaspresentacion/carta_presentacion_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/cartaspresentacion/infrastructure/ui/http/controllers/carta_presentacion_eliminar.php"
entrada: ["post.id_direccion:integer", "post.id_ubi:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "cartaspresentacion_CartaPresentacionEliminarData"
respuesta_data: ["ok:bool, mensaje: string"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\cartaspresentacion\\application\\CartaPresentacionEliminar"]
tags: ["cartaspresentacion", "carta", "presentacion", "eliminar"]
estado_revision: "generado"
---

# Carta Presentacion Eliminar

Endpoint backend: elimina una `CartaPresentacion`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/cartaspresentacion/carta_presentacion_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/cartaspresentacion/infrastructure/ui/http/controllers/carta_presentacion_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_direccion` | `integer` | controller+application | No | controller+application |
| `id_ubi` | `integer` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `cartaspresentacion_CartaPresentacionEliminarData`):
  - `ok` (`bool, mensaje: string`)

## Efectos colaterales

- Mutacion: elimina una `CartaPresentacion` por `id_ubi` + `id_direccion`.
- Sucesor de la rama `que_mod=eliminar` del dispatcher `apps/cartaspresentacion/controller/cartas_presentacion_ajax.php`.

## Casos De Uso

- `src\cartaspresentacion\application\CartaPresentacionEliminar`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.