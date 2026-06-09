---
id: "actividadtarifas.tarifa_ubi_update"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tarifa_ubi_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_update.php"
entrada: ["post.cantidad:string", "post.ctx_update:string", "post.id_item:integer", "post.id_serie:integer", "post.id_tarifa:integer", "post.id_ubi:integer", "post.observ:string", "post.year:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no se encuentra la tarifa", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_ubi.php", "frontend/actividadtarifas/controller/tarifa_ubi_form.php", "frontend/casas/controller/calendario_ubi_resumen.php"]
casos_uso: ["src\\actividadtarifas\\application\\TarifaUbiUpdate"]
tags: ["actividadtarifas", "tarifa", "ubi", "update"]
estado_revision: "generado"
---

# Tarifa Ubi Update

Endpoint backend: crea o actualiza una `TarifaUbi`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadtarifas/tarifa_ubi_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `cantidad` | `string` | controller+application | No | controller+application |
| `ctx_update` | `string` | controller | No | controller |
| `id_item` | `integer` | controller+application | No | controller+application |
| `id_serie` | `integer` | controller+application | No | controller+application |
| `id_tarifa` | `integer` | controller+application | No | controller+application |
| `id_ubi` | `integer` | controller+application | No | controller+application |
| `observ` | `string` | controller+application | No | controller+application |
| `year` | `integer` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `no se encuentra la tarifa`
- `hay un error, no se ha guardado`

## Casos De Uso

- `src\actividadtarifas\application\TarifaUbiUpdate`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_ubi.php`
- `frontend/actividadtarifas/controller/tarifa_ubi_form.php`
- `frontend/casas/controller/calendario_ubi_resumen.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.