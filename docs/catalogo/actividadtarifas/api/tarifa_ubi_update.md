---
id: "actividadtarifas.tarifa_ubi_update"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tarifa_ubi_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_update.php"
entrada: ["post.cantidad:string", "post.ctx_update:string", "post.id_serie:integer", "post.id_tarifa:integer", "post.observ:string"]
entrada_obligatoria: ["ctx_update"]
respuesta: "standard_envelope_string_data"
requiere_hashb: true
errores: ["Operación no autorizada", "no se encuentra la tarifa", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_ubi.php", "frontend/actividadtarifas/controller/tarifa_ubi_form.php"]
casos_uso: ["src\\actividadtarifas\\application\\TarifaUbiUpdate"]
tags: ["actividadtarifas", "tarifa", "ubi", "update"]
estado_revision: "revisado"
---

# Tarifa Ubi Update

Crea o actualiza una `TarifaUbi` (importe de una tarifa en una casa y año).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Alta (`id_item=0` en el contexto firmado) o edición de una tarifa por casa/año. En alta el
formulario envía `id_tarifa` y `id_serie`; en edición solo `cantidad` y `observ` (la tarifa y
serie ya están en el registro). El controller abre la cápsula `ctx_update` y extrae de ella
`id_item`, `id_ubi` y `year`; los hidden homónimos del formulario se ignoran (fase transitoria).

## Endpoint

- URL: `/src/actividadtarifas/tarifa_ubi_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ctx_update` | `string` | controller | Si | Cápsula `HashB` firmada en `tarifa_ubi_form_data` (`token_update`). Contexto: alta `{id_ubi, year}`; edición `{id_item, id_ubi, year}` |
| `id_tarifa` | `integer` | POST | No | Solo en alta; tipo de tarifa elegido |
| `id_serie` | `integer` | POST | No | Solo en alta; serie (desplegable) |
| `cantidad` | `string` | POST | No | Importe en euros |
| `observ` | `string` | POST | No | Observaciones |

Los campos `id_item`, `id_ubi` y `year` del POST no los usa el controller: los lee de `ctx_update`.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- En error de negocio: `success: false`, `mensaje` con el texto del caso de uso.

## Errores conocidos

- `Operación no autorizada` (cápsula `ctx_update` inválida o caducada)
- `no se encuentra la tarifa`
- `hay un error, no se ha guardado` (puede incluir detalle del repositorio en nueva línea)

## Permisos

- Autorización vía cápsula `HashB` (`ctx_update`): solo quien recibió el token al abrir el
  formulario puede mutar ese contexto. La visibilidad de acciones en listado depende de
  `have_perm_oficina('adl')` y coincidencia de sección (`mi_sfsv`).

## Casos De Uso

- `src\actividadtarifas\application\TarifaUbiUpdate`

## Frontend Relacionado

- `frontend/actividadtarifas/view/tarifa_ubi_form.phtml`: submit con `ctx_update` y campos del form.
- `frontend/actividadtarifas/view/tarifa_ubi.phtml`: `fnjs_guardar(..., 'update')` serializa el form
  contra la URL firmada `url_update`.
