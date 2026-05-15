---
id: "actividadtarifas.relacion_tarifa_eliminar"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/relacion_tarifa_eliminar"
metodos: ["GET", "POST"]
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/relacion_tarifa_eliminar.php"
entrada: ["post.id_item:integer"]
respuesta: "standard_envelope_string_data"
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_tipo_actividad.php"]
casos_uso: ["src\\actividadtarifas\\application\\RelacionTarifaEliminar"]
tags: ["actividadtarifas", "relacion", "tarifa", "eliminar"]
estado_revision: "generado"
---

# Relacion Tarifa Eliminar

Endpoint backend: elimina una `RelacionTarifaTipoActividad`.

## Endpoint

- URL: `/src/actividadtarifas/relacion_tarifa_eliminar`
- Metodos registrados: `GET, POST`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/relacion_tarifa_eliminar.php`

## Entrada Inferida

- `post.id_item` (`integer`)

## Salida Inferida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Evidencia: `$error, 'ok'`

## Casos De Uso Detectados

- `src\actividadtarifas\application\RelacionTarifaEliminar`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_tipo_actividad.php`

## Revision Manual

- Completar objetivo funcional.
- Confirmar permisos/autorizacion.
- Confirmar efectos sobre datos.
- Anadir ejemplos reales de request/response.
- Marcar procesos parecidos o duplicados si aplica.
