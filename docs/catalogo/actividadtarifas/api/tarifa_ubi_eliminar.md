---
id: "actividadtarifas.tarifa_ubi_eliminar"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tarifa_ubi_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_eliminar.php"
entrada: ["post.ctx_eliminar:string", "post.id_item:integer"]
entrada_obligatoria: ["ctx_eliminar"]
respuesta: "standard_envelope_string_data"
requiere_hashb: true
hashb_campo: "ctx_eliminar"
hashb_action: "tarifa_ubi_eliminar"
errores: ["no sé cuál he de borrar", "no se encuentra la tarifa", "hay un error, no se ha borrado", "Operación no autorizada"]
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_ubi.php"]
casos_uso: ["src\\actividadtarifas\\application\\TarifaUbiEliminar"]
tags: ["actividadtarifas", "tarifa", "ubi", "eliminar"]
estado_revision: "generado"
---

# Tarifa Ubi Eliminar

Endpoint backend: elimina una `TarifaUbi`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadtarifas/tarifa_ubi_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ctx_eliminar` | `string` | controller | Si | controller |
| `id_item` | `integer` | application | No | application; ignorado en body si viene en cápsula HashB |

## Autorizacion HashB

- Campo POST: `ctx_eliminar`
- Accion: `tarifa_ubi_eliminar`
- Cápsula invalida: `success: false`, `mensaje: "Operación no autorizada"`.
- Ver `documentacion/hash_arquitectura.md`.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Mutacion: elimina una `TarifaUbi`.
- Sucesor de las ramas `borrar` y `tar_ubi_eliminar` del dispatcher legacy `apps/actividadtarifas/controller/tarifa_ajax.php` (ambas ejecutaban la misma accion con nombres distintos).

## Errores conocidos

- `no sé cuál he de borrar`
- `no se encuentra la tarifa`
- `hay un error, no se ha borrado`
- `Operación no autorizada`

## Casos De Uso

- `src\actividadtarifas\application\TarifaUbiEliminar`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_ubi.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.