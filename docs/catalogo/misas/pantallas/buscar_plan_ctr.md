---
id: "misas.pantalla.buscar_plan_ctr"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "misas"
nombre: "Buscar Plan Ctr"
controller: "frontend/misas/controller/buscar_plan_ctr.php"
vistas: ["frontend/misas/view/buscar_plan_ctr.phtml", "frontend/misas/view/buscar_plan_un_ctr.phtml"]
fragmentos_frontend: ["frontend/misas/controller/buscar_plan_ctr.php", "frontend/misas/controller/ver_plan_ctr.php"]
endpoints: ["/src/misas/buscar_plan_ctr_data"]
capacidades: ["misas.buscar_plan_ctr.gestionar"]
campos: ["form.empiezamax", "form.empiezamin", "form.id_ubi", "form.id_zona", "form.periodo", "post.id_zona"]
acciones: ["fnjs_buscar_plan_ctr", "fnjs_ver_plan_ctr"]
estado_revision: "generado"
---

# Buscar Plan Ctr

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/misas/controller/buscar_plan_ctr.php`

## Vistas Relacionadas

- `frontend/misas/view/buscar_plan_ctr.phtml`
- `frontend/misas/view/buscar_plan_un_ctr.phtml`

## Fragmentos Frontend Relacionados

- `frontend/misas/controller/buscar_plan_ctr.php`
- `frontend/misas/controller/ver_plan_ctr.php`

## Endpoints Usados

- `/src/misas/buscar_plan_ctr_data`

## Capacidades Relacionadas

- `misas.buscar_plan_ctr.gestionar`

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.id_ubi`
- `form.id_zona`
- `form.periodo`
- `post.id_zona`

## Acciones Detectadas

- `fnjs_buscar_plan_ctr`
- `fnjs_ver_plan_ctr`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
