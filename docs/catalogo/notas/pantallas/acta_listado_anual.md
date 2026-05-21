---
id: "notas.pantalla.acta_listado_anual"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "notas"
nombre: "Acta Listado Anual"
controller: "frontend/notas/controller/acta_listado_anual.php"
vistas: ["frontend/notas/view/acta_listado_anual.phtml"]
fragmentos_frontend: ["frontend/notas/controller/acta_listado_anual.php"]
endpoints: ["/src/notas/acta_listado_anual_data"]
capacidades: ["notas.acta_listado_anual.gestionar"]
campos: ["form.empiezamax", "form.empiezamin", "form.iactividad_val", "form.iasistentes_val", "form.periodo", "form.year", "html.refresh", "post.empiezamax", "post.empiezamin", "post.periodo", "post.year"]
acciones: ["fnjs_buscar", "fnjs_enviar_formulario", "fnjs_left_side_hide"]
estado_revision: "generado"
---

# Acta Listado Anual

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/notas/controller/acta_listado_anual.php`

## Vistas Relacionadas

- `frontend/notas/view/acta_listado_anual.phtml`

## Fragmentos Frontend Relacionados

- `frontend/notas/controller/acta_listado_anual.php`

## Endpoints Usados

- `/src/notas/acta_listado_anual_data`

## Capacidades Relacionadas

- `notas.acta_listado_anual.gestionar`

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.periodo`
- `form.year`
- `html.refresh`
- `post.empiezamax`
- `post.empiezamin`
- `post.periodo`
- `post.year`

## Acciones Detectadas

- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
