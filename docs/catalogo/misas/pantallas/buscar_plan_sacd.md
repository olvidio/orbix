---
id: "misas.pantalla.buscar_plan_sacd"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "misas"
nombre: "Buscar Plan Sacd"
controller: "frontend/misas/controller/buscar_plan_sacd.php"
vistas: ["frontend/misas/view/buscar_plan_sacd.phtml"]
fragmentos_frontend: ["frontend/misas/controller/ver_plan_sacd.php"]
endpoints: ["/src/misas/buscar_plan_sacd_data"]
capacidades: ["misas.buscar_plan_sacd.gestionar"]
campos: ["form.empiezamax", "form.empiezamin", "form.id_sacd", "form.periodo"]
acciones: ["fnjs_ver_plan_sacd"]
estado_revision: "generado"
---

# Buscar Plan Sacd

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/misas/controller/buscar_plan_sacd.php`

## Vistas Relacionadas

- `frontend/misas/view/buscar_plan_sacd.phtml`

## Fragmentos Frontend Relacionados

- `frontend/misas/controller/ver_plan_sacd.php`

## Endpoints Usados

- `/src/misas/buscar_plan_sacd_data`

## Capacidades Relacionadas

- `misas.buscar_plan_sacd.gestionar`

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.id_sacd`
- `form.periodo`

## Acciones Detectadas

- `fnjs_ver_plan_sacd`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
