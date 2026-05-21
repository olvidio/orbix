---
id: "ubiscamas.pantalla.cama_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "ubiscamas"
nombre: "Cama Form"
controller: "frontend/ubiscamas/controller/cama_form.php"
vistas: ["frontend/ubiscamas/view/cama_form.phtml"]
fragmentos_frontend: []
endpoints: ["/src/ubiscamas/cama_form_data"]
capacidades: ["ubiscamas.cama.gestionar"]
campos: ["html.descripcion", "html.larga", "html.vip"]
acciones: ["fnjs_cancelar", "fnjs_guardar"]
estado_revision: "generado"
---

# Cama Form

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/ubiscamas/controller/cama_form.php`

## Vistas Relacionadas

- `frontend/ubiscamas/view/cama_form.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/ubiscamas/cama_form_data`

## Capacidades Relacionadas

- `ubiscamas.cama.gestionar`

## Campos Detectados

- `html.descripcion`
- `html.larga`
- `html.vip`

## Acciones Detectadas

- `fnjs_cancelar`
- `fnjs_guardar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
