---
id: "actividadtarifas.tipo_tarifa_form_data"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tipo_tarifa_form_data"
metodos: ["GET", "POST"]
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tipo_tarifa_form_data.php"
entrada: ["post.id_tarifa:string"]
respuesta: "standard_envelope_string_data"
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_form.php"]
casos_uso: ["src\\actividadtarifas\\application\\TipoTarifaFormData"]
tags: ["actividadtarifas", "tipo", "tarifa", "form", "data"]
estado_revision: "generado"
---

# Tipo Tarifa Form Data

Endpoint backend: datos del formulario modificar/nuevo de `TipoTarifa`.

## Endpoint

- URL: `/src/actividadtarifas/tipo_tarifa_form_data`
- Metodos registrados: `GET, POST`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tipo_tarifa_form_data.php`

## Entrada Inferida

- `post.id_tarifa` (`string`)

## Salida Inferida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Evidencia: `'', $data`

## Casos De Uso Detectados

- `src\actividadtarifas\application\TipoTarifaFormData`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_form.php`

## Revision Manual

- Completar objetivo funcional.
- Confirmar permisos/autorizacion.
- Confirmar efectos sobre datos.
- Anadir ejemplos reales de request/response.
- Marcar procesos parecidos o duplicados si aplica.
