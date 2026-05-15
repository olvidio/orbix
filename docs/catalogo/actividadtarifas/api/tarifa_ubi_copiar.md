---
id: "actividadtarifas.tarifa_ubi_copiar"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tarifa_ubi_copiar"
metodos: ["GET", "POST"]
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_copiar.php"
entrada: ["post.ctx_copiar:string"]
respuesta: "standard_envelope_string_data"
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_ubi.php", "frontend/actividadtarifas/view/tarifa_ubi.phtml"]
casos_uso: ["src\\actividadtarifas\\application\\TarifaUbiCopiar"]
tags: ["actividadtarifas", "tarifa", "ubi", "copiar"]
estado_revision: "generado"
---

# Tarifa Ubi Copiar

Endpoint backend: copiar tarifas del año anterior.

## Endpoint

- URL: `/src/actividadtarifas/tarifa_ubi_copiar`
- Metodos registrados: `GET, POST`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_copiar.php`

## Entrada Inferida

- `post.ctx_copiar` (`string`)

## Salida Inferida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Evidencia: `_("Operación no autorizada"), 'none'`

## Casos De Uso Detectados

- `src\actividadtarifas\application\TarifaUbiCopiar`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_ubi.php`
- `frontend/actividadtarifas/view/tarifa_ubi.phtml`

## Revision Manual

- Completar objetivo funcional.
- Confirmar permisos/autorizacion.
- Confirmar efectos sobre datos.
- Anadir ejemplos reales de request/response.
- Marcar procesos parecidos o duplicados si aplica.
