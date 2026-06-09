---
id: "actividadtarifas.tarifa_ubi_eliminar"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tarifa_ubi_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_eliminar.php"
entrada: ["post.ctx_eliminar:string", "post.id_item:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no sé cuál he de borrar", "no se encuentra la tarifa", "hay un error, no se ha borrado"]
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
| `ctx_eliminar` | `string` | controller | No | controller |
| `id_item` | `integer` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Mutacion: elimina una `TarifaUbi`.

## Errores conocidos

- `no sé cuál he de borrar`
- `no se encuentra la tarifa`
- `hay un error, no se ha borrado`

## Casos De Uso

- `src\actividadtarifas\application\TarifaUbiEliminar`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_ubi.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.