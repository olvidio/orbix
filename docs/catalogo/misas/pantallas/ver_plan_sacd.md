---
id: "misas.pantalla.ver_plan_sacd"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "misas"
nombre: "Ver Plan Sacd"
controller: "frontend/misas/controller/ver_plan_sacd.php"
vistas: ["frontend/misas/view/ver_plan_sacd.phtml"]
fragmentos_frontend: []
endpoints: ["/src/misas/ver_plan_sacd_data"]
capacidades: ["misas.ver_plan_sacd.gestionar"]
campos: ["post.empiezamax", "post.empiezamin", "post.id_sacd", "post.periodo"]
acciones: []
estado_revision: "revisado"
---

# Ver plan sacd

Resultado: lista cronológica de misas del sacerdote (`ver_plan_sacd_data`).

## Tipo

- Subtipo: `fragmento_ajax`


- Controller: `frontend/misas/controller/ver_plan_sacd.php`

## Vistas Relacionadas

- `frontend/misas/view/ver_plan_sacd.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/misas/ver_plan_sacd_data`

## Capacidades Relacionadas

- `misas.ver_plan_sacd.gestionar`

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.id_sacd`
- `post.periodo`

## Acciones Detectadas

No se han detectado acciones.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
