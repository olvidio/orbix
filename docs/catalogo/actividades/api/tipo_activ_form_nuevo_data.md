---
id: "actividades.tipo_activ_form_nuevo_data"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/tipo_activ_form_nuevo_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/actividades/infrastructure/ui/http/controllers/tipo_activ_form_nuevo_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/actividades/controller/tipo_activ_form_nuevo.php"]
casos_uso: ["src\\actividades\\application\\TipoActivFormNuevoData"]
tags: ["actividades", "tipo", "activ", "form_nuevo", "data"]
estado_revision: "revisado"
---

# Tipo Activ Form Nuevo Data

Datos del formulario de alta de un tipo de actividad.

## Endpoint

- URL: `/src/actividades/tipo_activ_form_nuevo_data`
- Controller: `src/actividades/infrastructure/ui/http/controllers/tipo_activ_form_nuevo_data.php`

## Entrada

Sin parámetros.

## Salida

- `ContestarJson::enviar` con `data`: `{ "ctx_nuevo": string }` (cápsula `HashB` para `tipo_activ_nuevo`).
- El formulario (widget `ActividadTipo` en modo gestión, `id_nom_tipo_activ`, `nom_tipo_activ`) lo monta
  `frontend/actividades/controller/tipo_activ_form_nuevo.php` con la vista `tipo_activ_form_nuevo.phtml`.

## Casos De Uso

- `src\actividades\application\TipoActivFormNuevoData`

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).
