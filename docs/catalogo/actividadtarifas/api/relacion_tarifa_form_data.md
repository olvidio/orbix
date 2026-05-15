---
id: "actividadtarifas.relacion_tarifa_form_data"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/relacion_tarifa_form_data"
metodos: ["GET", "POST"]
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/relacion_tarifa_form_data.php"
entrada: ["post.id_item:string"]
respuesta: "standard_envelope_string_data"
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php"]
casos_uso: ["src\\actividadtarifas\\application\\RelacionTarifaFormData"]
tags: ["actividadtarifas", "relacion", "tarifa", "form", "data"]
estado_revision: "generado"
---

# Relacion Tarifa Form Data

Endpoint backend: datos del formulario modificar/nuevo de `RelacionTarifaTipoActividad`.

## Endpoint

- URL: `/src/actividadtarifas/relacion_tarifa_form_data`
- Metodos registrados: `GET, POST`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/relacion_tarifa_form_data.php`

## Entrada Inferida

- `post.id_item` (`string`)

## Salida Inferida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Evidencia: `'', $data`

## Casos De Uso Detectados

- `src\actividadtarifas\application\RelacionTarifaFormData`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php`

## Revision Manual

- Completar objetivo funcional.
- Confirmar permisos/autorizacion.
- Confirmar efectos sobre datos.
- Anadir ejemplos reales de request/response.
- Marcar procesos parecidos o duplicados si aplica.
