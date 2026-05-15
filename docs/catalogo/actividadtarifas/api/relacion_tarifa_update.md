---
id: "actividadtarifas.relacion_tarifa_update"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/relacion_tarifa_update"
metodos: ["GET", "POST"]
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/relacion_tarifa_update.php"
entrada: ["post.id_item:string", "post.id_tarifa:integer", "post.id_tipo_activ:integer"]
respuesta: "standard_envelope_string_data"
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_tipo_actividad.php", "frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php", "frontend/pasarela/controller/nombre_form.php"]
casos_uso: ["src\\actividadtarifas\\application\\RelacionTarifaUpdate"]
tags: ["actividadtarifas", "relacion", "tarifa", "update"]
estado_revision: "generado"
---

# Relacion Tarifa Update

Endpoint backend: crea o actualiza una `RelacionTarifaTipoActividad`.

## Endpoint

- URL: `/src/actividadtarifas/relacion_tarifa_update`
- Metodos registrados: `GET, POST`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/relacion_tarifa_update.php`

## Entrada Inferida

- `post.id_item` (`string`)
- `post.id_tarifa` (`integer`)
- `post.id_tipo_activ` (`integer`)

## Salida Inferida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Evidencia: `$error, 'ok'`

## Casos De Uso Detectados

- `src\actividadtarifas\application\RelacionTarifaUpdate`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_tipo_actividad.php`
- `frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php`
- `frontend/pasarela/controller/nombre_form.php`

## Revision Manual

- Completar objetivo funcional.
- Confirmar permisos/autorizacion.
- Confirmar efectos sobre datos.
- Anadir ejemplos reales de request/response.
- Marcar procesos parecidos o duplicados si aplica.
