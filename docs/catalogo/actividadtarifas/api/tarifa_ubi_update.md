---
id: "actividadtarifas.tarifa_ubi_update"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tarifa_ubi_update"
metodos: ["GET", "POST"]
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_update.php"
entrada: ["post.cantidad:string", "post.ctx_update:string", "post.id_serie:integer", "post.id_tarifa:integer", "post.observ:string"]
respuesta: "standard_envelope_string_data"
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_ubi.php", "frontend/actividadtarifas/controller/tarifa_ubi_form.php", "frontend/casas/controller/calendario_ubi_resumen.php"]
casos_uso: ["src\\actividadtarifas\\application\\TarifaUbiUpdate"]
tags: ["actividadtarifas", "tarifa", "ubi", "update"]
estado_revision: "generado"
---

# Tarifa Ubi Update

Endpoint backend: crea o actualiza una `TarifaUbi`.

## Endpoint

- URL: `/src/actividadtarifas/tarifa_ubi_update`
- Metodos registrados: `GET, POST`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_update.php`

## Entrada Inferida

- `post.cantidad` (`string`)
- `post.ctx_update` (`string`)
- `post.id_serie` (`integer`)
- `post.id_tarifa` (`integer`)
- `post.observ` (`string`)

## Salida Inferida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Evidencia: `_("Operación no autorizada"), 'none'`

## Casos De Uso Detectados

- `src\actividadtarifas\application\TarifaUbiUpdate`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_ubi.php`
- `frontend/actividadtarifas/controller/tarifa_ubi_form.php`
- `frontend/casas/controller/calendario_ubi_resumen.php`

## Revision Manual

- Completar objetivo funcional.
- Confirmar permisos/autorizacion.
- Confirmar efectos sobre datos.
- Anadir ejemplos reales de request/response.
- Marcar procesos parecidos o duplicados si aplica.
