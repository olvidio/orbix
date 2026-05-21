---
id: "asistentes.pantalla.form_asistentes_a_una_actividad"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "asistentes"
nombre: "Form Asistentes A Una Actividad"
controller: "frontend/asistentes/controller/form_asistentes_a_una_actividad.php"
vistas: ["frontend/asistentes/view/form_asistentes_a_una_actividad.phtml"]
fragmentos_frontend: []
endpoints: ["/src/asistentes/form_asistentes_a_una_actividad_data"]
capacidades: ["asistentes.form_asistentes_a_una_actividad.gestionar"]
campos: ["html.est_ok", "html.falta", "html.guardar", "html.guardar2", "html.observ", "html.observ_est", "html.propio", "post.actualizar"]
acciones: ["fnjs_cmb_propietario", "fnjs_construir_desplegable_propietario", "fnjs_enviar_formulario", "fnjs_guardar", "fnjs_nuevo"]
estado_revision: "generado"
---

# Form Asistentes A Una Actividad

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/asistentes/controller/form_asistentes_a_una_actividad.php`

## Vistas Relacionadas

- `frontend/asistentes/view/form_asistentes_a_una_actividad.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/asistentes/form_asistentes_a_una_actividad_data`

## Capacidades Relacionadas

- `asistentes.form_asistentes_a_una_actividad.gestionar`

## Campos Detectados

- `html.est_ok`
- `html.falta`
- `html.guardar`
- `html.guardar2`
- `html.observ`
- `html.observ_est`
- `html.propio`
- `post.actualizar`

## Acciones Detectadas

- `fnjs_cmb_propietario`
- `fnjs_construir_desplegable_propietario`
- `fnjs_enviar_formulario`
- `fnjs_guardar`
- `fnjs_nuevo`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
