---
id: "asistentes.pantalla.form_actividades_de_una_persona"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "asistentes"
nombre: "Form Actividades De Una Persona"
controller: "frontend/asistentes/controller/form_actividades_de_una_persona.php"
vistas: ["frontend/asistentes/view/form_actividades_de_una_persona.phtml"]
fragmentos_frontend: []
endpoints: ["/src/asistentes/form_actividades_de_una_persona_data"]
capacidades: ["asistentes.form_actividades_de_una_persona.gestionar"]
campos: ["html.est_ok", "html.falta", "html.guardar", "html.observ", "html.propio"]
acciones: ["fnjs_cmb_propietario", "fnjs_construir_desplegable_propietario", "fnjs_guardar"]
estado_revision: "generado"
---

# Form Actividades De Una Persona

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/asistentes/controller/form_actividades_de_una_persona.php`

## Vistas Relacionadas

- `frontend/asistentes/view/form_actividades_de_una_persona.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/asistentes/form_actividades_de_una_persona_data`

## Capacidades Relacionadas

- `asistentes.form_actividades_de_una_persona.gestionar`

## Campos Detectados

- `html.est_ok`
- `html.falta`
- `html.guardar`
- `html.observ`
- `html.propio`

## Acciones Detectadas

- `fnjs_cmb_propietario`
- `fnjs_construir_desplegable_propietario`
- `fnjs_guardar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
