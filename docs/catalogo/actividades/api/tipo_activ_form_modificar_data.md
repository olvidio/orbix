---
id: "actividades.tipo_activ_form_modificar_data"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/tipo_activ_form_modificar_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/actividades/infrastructure/ui/http/controllers/tipo_activ_form_modificar_data.php"
entrada: ["post.id_tipo_activ:integer"]
entrada_obligatoria: ["id_tipo_activ"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/actividades/controller/tipo_activ_form_modificar.php"]
casos_uso: ["src\\actividades\\application\\TipoActivFormModificarData"]
tags: ["actividades", "tipo", "activ", "form_modificar", "data"]
estado_revision: "revisado"
---

# Tipo Activ Form Modificar Data

Datos del formulario de modificación de un tipo de actividad.

## Endpoint

- URL: `/src/actividades/tipo_activ_form_modificar_data`
- Controller: `src/actividades/infrastructure/ui/http/controllers/tipo_activ_form_modificar_data.php`

## Entrada

- `id_tipo_activ` (entero, obligatorio).

## Salida

- `ContestarJson::enviar` con `data`: `id_tipo_activ`, `nom_actividad`, `nom_tipo`, `ctx_guardar`
  (`HashB` para `tipo_activ_update`) y `ctx_eliminar` (`HashB` para `tipo_activ_eliminar`).
- Sin `id_tipo_activ` válido: error «Tipo no válido» (`success: false`).
- El formulario lo monta `frontend/actividades/controller/tipo_activ_form_modificar.php` con la vista
  `tipo_activ_form_modificar.phtml`.

## Casos De Uso

- `src\actividades\application\TipoActivFormModificarData`

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).
