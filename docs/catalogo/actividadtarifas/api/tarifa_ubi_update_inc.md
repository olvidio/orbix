---
id: "actividadtarifas.tarifa_ubi_update_inc"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tarifa_ubi_update_inc"
metodos: ["GET", "POST"]
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_update_inc.php"
entrada: ["post.inc_cantidad:array"]
respuesta: "standard_envelope_string_data"
frontend_referencias: ["frontend/casas/controller/calendario_ubi_resumen.php"]
casos_uso: ["src\\actividadtarifas\\application\\TarifaUbiUpdateInc"]
tags: ["actividadtarifas", "tarifa", "ubi", "update", "inc"]
estado_revision: "generado"
---

# Tarifa Ubi Update Inc

Endpoint backend: actualiza en lote las cantidades de varias `TarifaUbi` desde el estudio economico de casa.

## Endpoint

- URL: `/src/actividadtarifas/tarifa_ubi_update_inc`
- Metodos registrados: `GET, POST`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_update_inc.php`

## Entrada Inferida

- `post.inc_cantidad` (`array`)

## Salida Inferida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Evidencia: `$error, 'ok'`

## Casos De Uso Detectados

- `src\actividadtarifas\application\TarifaUbiUpdateInc`

## Frontend Relacionado

- `frontend/casas/controller/calendario_ubi_resumen.php`

## Revision Manual

- Completar objetivo funcional.
- Confirmar permisos/autorizacion.
- Confirmar efectos sobre datos.
- Anadir ejemplos reales de request/response.
- Marcar procesos parecidos o duplicados si aplica.
