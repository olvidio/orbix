---
id: "misas.pantalla.ver_plan_ctr"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "misas"
nombre: "Ver Plan Ctr"
controller: "frontend/misas/controller/ver_plan_ctr.php"
vistas: ["frontend/misas/view/ver_plan_ctr.phtml"]
fragmentos_frontend: []
endpoints: ["/src/misas/ver_plan_ctr_data"]
capacidades: ["misas.ver_plan_ctr.gestionar"]
campos: ["post.empiezamax", "post.empiezamin", "post.id_ubi", "post.id_zona", "post.periodo"]
acciones: []
estado_revision: "generado"
---

# Ver Plan Ctr

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/misas/controller/ver_plan_ctr.php`

## Vistas Relacionadas

- `frontend/misas/view/ver_plan_ctr.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/misas/ver_plan_ctr_data`

## Capacidades Relacionadas

- `misas.ver_plan_ctr.gestionar`

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.id_ubi`
- `post.id_zona`
- `post.periodo`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
