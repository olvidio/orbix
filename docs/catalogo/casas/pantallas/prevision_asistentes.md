---
id: "casas.pantalla.prevision_asistentes"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "casas"
nombre: "Prevision Asistentes"
controller: "frontend/casas/controller/prevision_asistentes.php"
vistas: ["frontend/casas/view/prevision_asistentes.phtml"]
fragmentos_frontend: ["frontend/casas/controller/prevision_asistentes.php"]
endpoints: ["/src/casas/ingreso_plazas_previstas_update", "/src/casas/prevision_asistentes_data"]
capacidades: ["casas.ingreso_plazas_previstas.gestionar", "casas.prevision_asistentes.gestionar"]
campos: ["form.empiezamax", "form.empiezamin", "form.extendida", "form.iactividad_val", "form.iasistentes_val", "form.mi_of", "form.periodo", "form.year", "html.refresh", "post.empiezamax", "post.empiezamin", "post.mi_of", "post.periodo", "post.year"]
acciones: ["fnjs_buscar", "fnjs_enviar_formulario", "fnjs_left_side_hide"]
estado_revision: "generado"
---

# Prevision Asistentes

Pantalla `prevision_asistentes`: tabla editable con las plazas previstas por actividad.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/casas/controller/prevision_asistentes.php`

## Vistas Relacionadas

- `frontend/casas/view/prevision_asistentes.phtml`

## Fragmentos Frontend Relacionados

- `frontend/casas/controller/prevision_asistentes.php`

## Endpoints Usados

- `/src/casas/ingreso_plazas_previstas_update`
- `/src/casas/prevision_asistentes_data`

## Capacidades Relacionadas

- `casas.ingreso_plazas_previstas.gestionar`
- `casas.prevision_asistentes.gestionar`

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.extendida`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.mi_of`
- `form.periodo`
- `form.year`
- `html.refresh`
- `post.empiezamax`
- `post.empiezamin`
- `post.mi_of`
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
