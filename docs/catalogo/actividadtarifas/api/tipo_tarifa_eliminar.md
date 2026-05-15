---
id: "actividadtarifas.tipo_tarifa_eliminar"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tipo_tarifa_eliminar"
metodos: ["GET", "POST"]
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tipo_tarifa_eliminar.php"
entrada: ["post.id_tarifa:integer"]
respuesta: "standard_envelope_string_data"
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa.php"]
casos_uso: ["src\\actividadtarifas\\application\\TipoTarifaEliminar"]
tags: ["actividadtarifas", "tipo", "tarifa", "eliminar"]
estado_revision: "generado"
---

# Tipo Tarifa Eliminar

Endpoint backend: elimina un `TipoTarifa`.

## Endpoint

- URL: `/src/actividadtarifas/tipo_tarifa_eliminar`
- Metodos registrados: `GET, POST`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tipo_tarifa_eliminar.php`

## Entrada Inferida

- `post.id_tarifa` (`integer`)

## Salida Inferida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Evidencia: `$error, 'ok'`

## Casos De Uso Detectados

- `src\actividadtarifas\application\TipoTarifaEliminar`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa.php`

## Revision Manual

- Completar objetivo funcional.
- Confirmar permisos/autorizacion.
- Confirmar efectos sobre datos.
- Anadir ejemplos reales de request/response.
- Marcar procesos parecidos o duplicados si aplica.
