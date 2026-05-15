---
id: "actividadtarifas.tarifa_ubi_form_data"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tarifa_ubi_form_data"
metodos: ["GET", "POST"]
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_form_data.php"
entrada: ["post.id_item:string", "post.id_ubi:integer", "post.letra:string", "post.year:integer"]
respuesta: "standard_envelope_string_data"
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_ubi_form.php"]
casos_uso: ["src\\actividadtarifas\\application\\TarifaUbiFormData"]
tags: ["actividadtarifas", "tarifa", "ubi", "form", "data"]
estado_revision: "generado"
---

# Tarifa Ubi Form Data

Endpoint backend: datos del formulario modificar/nuevo de `TarifaUbi`.

## Endpoint

- URL: `/src/actividadtarifas/tarifa_ubi_form_data`
- Metodos registrados: `GET, POST`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_form_data.php`

## Entrada Inferida

- `post.id_item` (`string`)
- `post.id_ubi` (`integer`)
- `post.letra` (`string`)
- `post.year` (`integer`)

## Salida Inferida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Evidencia: `'', $data`

## Casos De Uso Detectados

- `src\actividadtarifas\application\TarifaUbiFormData`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_ubi_form.php`

## Revision Manual

- Completar objetivo funcional.
- Confirmar permisos/autorizacion.
- Confirmar efectos sobre datos.
- Anadir ejemplos reales de request/response.
- Marcar procesos parecidos o duplicados si aplica.
