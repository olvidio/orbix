---
id: "actividadtarifas.tipo_tarifa_update"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tipo_tarifa_update"
metodos: ["GET", "POST"]
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tipo_tarifa_update.php"
entrada: ["post.id_tarifa:string", "post.letra:string", "post.modo:string", "post.observ:string"]
respuesta: "standard_envelope_string_data"
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa.php", "frontend/actividadtarifas/controller/tarifa_form.php", "frontend/actividadtarifas/view/tarifa_form.phtml"]
casos_uso: ["src\\actividadtarifas\\application\\TipoTarifaUpdate"]
tags: ["actividadtarifas", "tipo", "tarifa", "update"]
estado_revision: "generado"
---

# Tipo Tarifa Update

Endpoint backend: crea o actualiza un `TipoTarifa`.

## Endpoint

- URL: `/src/actividadtarifas/tipo_tarifa_update`
- Metodos registrados: `GET, POST`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tipo_tarifa_update.php`

## Entrada Inferida

- `post.id_tarifa` (`string`)
- `post.letra` (`string`)
- `post.modo` (`string`)
- `post.observ` (`string`)

## Salida Inferida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Evidencia: `$error, 'ok'`

## Casos De Uso Detectados

- `src\actividadtarifas\application\TipoTarifaUpdate`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa.php`
- `frontend/actividadtarifas/controller/tarifa_form.php`
- `frontend/actividadtarifas/view/tarifa_form.phtml`

## Revision Manual

- Completar objetivo funcional.
- Confirmar permisos/autorizacion.
- Confirmar efectos sobre datos.
- Anadir ejemplos reales de request/response.
- Marcar procesos parecidos o duplicados si aplica.
