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
estado_revision: "revisado"
---

# Ver plan ctr

Resultado: cuadrícula encargo×día del centro (`ver_plan_ctr_data`) con leyenda de sacds.

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

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
