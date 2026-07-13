---
id: "actividadtarifas.tarifa_ubi_copiar"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tarifa_ubi_copiar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_copiar.php"
entrada: ["post.ctx_copiar:string"]
entrada_obligatoria: ["ctx_copiar"]
respuesta: "standard_envelope_string_data"
requiere_hashb: true
errores: ["Operación no autorizada", "no sé qué casa/año tengo que copiar", "función de copiar tarifas pendiente de reimplementar"]
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_ubi.php", "frontend/actividadtarifas/view/tarifa_ubi.phtml"]
casos_uso: ["src\\actividadtarifas\\application\\TarifaUbiCopiar"]
tags: ["actividadtarifas", "tarifa", "ubi", "copiar"]
estado_revision: "revisado"
---

# Tarifa Ubi Copiar

Copia las tarifas de una casa del año anterior al año actual (endpoint mantenido por paridad; lógica pendiente).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Recibe la cápsula `ctx_copiar` con `{id_ubi, year}` emitida en `tarifa_ubi_lista_data` como
`token_copiar` cuando `puede_anadir=true`. La implementación actual devuelve siempre el mensaje de
función pendiente (el legacy tenía el método roto).

## Endpoint

- URL: `/src/actividadtarifas/tarifa_ubi_copiar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_copiar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ctx_copiar` | `string` | controller | Si | Cápsula `HashB` con `{id_ubi, year}` del listado (`token_copiar`) |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito nominal: `success: true`, `data: "ok"` (hoy la operación real falla con mensaje de pendiente).

## Errores conocidos

- `Operación no autorizada`
- `no sé qué casa/año tengo que copiar`
- `función de copiar tarifas pendiente de reimplementar`

## Permisos

- Autorización vía cápsula `HashB` (`ctx_copiar`), solo emitida si `puede_anadir` en el listado
  (`have_perm_oficina('adl'|'pr'|'calendario')`).

## Casos De Uso

- `src\actividadtarifas\application\TarifaUbiCopiar`

## Frontend Relacionado

- `frontend/actividadtarifas/view/tarifa_ubi.phtml`: `fnjs_copiar_tarifas(ctx_copiar)` reenvía la
  cápsula opaca recibida del listado; no construye `id_ubi`/`year` en cliente.
