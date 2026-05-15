---
id: "actividadtarifas.tipo_tarifa_lista_data"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tipo_tarifa_lista_data"
metodos: ["GET", "POST"]
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tipo_tarifa_lista_data.php"
entrada: []
respuesta: "standard_envelope_string_data"
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa.php", "frontend/actividadtarifas/controller/tarifa_lista.php"]
casos_uso: ["src\\actividadtarifas\\application\\TipoTarifaListaData"]
tags: ["actividadtarifas", "tipo", "tarifa", "lista", "data"]
estado_revision: "generado"
---

# Tipo Tarifa Lista Data

Endpoint backend: listado del catalogo de tipos de tarifa.

## Endpoint

- URL: `/src/actividadtarifas/tipo_tarifa_lista_data`
- Metodos registrados: `GET, POST`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tipo_tarifa_lista_data.php`

## Entrada Inferida

No se han detectado parametros individuales mediante `filter_input`, `$_POST[...]` o `$_GET[...]`.

## Salida Inferida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Evidencia: `'', $data`

## Casos De Uso Detectados

- `src\actividadtarifas\application\TipoTarifaListaData`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa.php`
- `frontend/actividadtarifas/controller/tarifa_lista.php`

## Revision Manual

- Completar objetivo funcional.
- Confirmar permisos/autorizacion.
- Confirmar efectos sobre datos.
- Anadir ejemplos reales de request/response.
- Marcar procesos parecidos o duplicados si aplica.
