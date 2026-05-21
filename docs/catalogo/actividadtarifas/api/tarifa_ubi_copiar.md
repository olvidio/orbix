---
id: "actividadtarifas.tarifa_ubi_copiar"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tarifa_ubi_copiar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_copiar.php"
entrada: ["post.ctx_copiar:string", "post.id_ubi:integer", "post.year:integer"]
entrada_obligatoria: ["ctx_copiar"]
respuesta: "standard_envelope_string_data"
requiere_hashb: true
hashb_campo: "ctx_copiar"
hashb_action: "tarifa_ubi_copiar"
errores: ["no sé qué casa/año tengo que copiar", "función de copiar tarifas pendiente de reimplementar", "Operación no autorizada"]
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_ubi.php", "frontend/actividadtarifas/view/tarifa_ubi.phtml"]
casos_uso: ["src\\actividadtarifas\\application\\TarifaUbiCopiar"]
tags: ["actividadtarifas", "tarifa", "ubi", "copiar"]
estado_revision: "generado"
---

# Tarifa Ubi Copiar

Endpoint backend: copiar tarifas del año anterior.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadtarifas/tarifa_ubi_copiar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_copiar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ctx_copiar` | `string` | controller | Si | controller |
| `id_ubi` | `integer` | application | No | application; ignorado en body si viene en cápsula HashB |
| `year` | `integer` | application | No | application; ignorado en body si viene en cápsula HashB |

## Autorizacion HashB

- Campo POST: `ctx_copiar`
- Accion: `tarifa_ubi_copiar`
- Cápsula invalida: `success: false`, `mensaje: "Operación no autorizada"`.
- Ver `documentacion/hash_arquitectura.md`.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `no sé qué casa/año tengo que copiar`
- `función de copiar tarifas pendiente de reimplementar`
- `Operación no autorizada`

## Casos De Uso

- `src\actividadtarifas\application\TarifaUbiCopiar`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_ubi.php`
- `frontend/actividadtarifas/view/tarifa_ubi.phtml`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.