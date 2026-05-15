---
id: "actividadcargos.form_cargos_de_actividad_data"
tipo: "endpoint"
modulo: "actividadcargos"
url: "/src/actividadcargos/form_cargos_de_actividad_data"
metodos: ["GET", "POST"]
controller: "src/actividadcargos/infrastructure/ui/http/controllers/form_cargos_de_actividad_data.php"
entrada: []
respuesta: "standard_envelope_string_data"
frontend_referencias: ["frontend/actividadcargos/controller/form_cargos_de_actividad.php"]
casos_uso: ["src\\actividadcargos\\application\\FormCargosDeActividadData"]
tags: ["actividadcargos", "form", "cargos", "de", "actividad", "data"]
estado_revision: "generado"
---

# Form Cargos De Actividad Data

Descripcion funcional pendiente de revisar.

## Endpoint

- URL: `/src/actividadcargos/form_cargos_de_actividad_data`
- Metodos registrados: `GET, POST`
- Controller: `src/actividadcargos/infrastructure/ui/http/controllers/form_cargos_de_actividad_data.php`

## Entrada Inferida

No se han detectado parametros individuales mediante `filter_input`, `$_POST[...]` o `$_GET[...]`.
- Nota: el controller usa `$_POST` directamente; revisar si acepta mas campos que los listados.

## Salida Inferida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Evidencia: `$error, $result`

## Casos De Uso Detectados

- `src\actividadcargos\application\FormCargosDeActividadData`

## Frontend Relacionado

- `frontend/actividadcargos/controller/form_cargos_de_actividad.php`

## Revision Manual

- Completar objetivo funcional.
- Confirmar permisos/autorizacion.
- Confirmar efectos sobre datos.
- Anadir ejemplos reales de request/response.
- Marcar procesos parecidos o duplicados si aplica.
