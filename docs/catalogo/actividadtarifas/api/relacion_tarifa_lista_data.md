---
id: "actividadtarifas.relacion_tarifa_lista_data"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/relacion_tarifa_lista_data"
metodos: ["GET", "POST"]
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/relacion_tarifa_lista_data.php"
entrada: []
respuesta: "standard_envelope_string_data"
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_tipo_actividad_lista.php"]
casos_uso: ["src\\actividadtarifas\\application\\RelacionTarifaListaData"]
tags: ["actividadtarifas", "relacion", "tarifa", "lista", "data"]
estado_revision: "generado"
---

# Relacion Tarifa Lista Data

Endpoint backend: listado de relaciones tarifa ↔ tipo actividad.

## Endpoint

- URL: `/src/actividadtarifas/relacion_tarifa_lista_data`
- Metodos registrados: `GET, POST`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/relacion_tarifa_lista_data.php`

## Entrada Inferida

No se han detectado parametros individuales mediante `filter_input`, `$_POST[...]` o `$_GET[...]`.

## Salida Inferida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Evidencia: `'', $data`

## Casos De Uso Detectados

- `src\actividadtarifas\application\RelacionTarifaListaData`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_tipo_actividad_lista.php`

## Revision Manual

- Completar objetivo funcional.
- Confirmar permisos/autorizacion.
- Confirmar efectos sobre datos.
- Anadir ejemplos reales de request/response.
- Marcar procesos parecidos o duplicados si aplica.
