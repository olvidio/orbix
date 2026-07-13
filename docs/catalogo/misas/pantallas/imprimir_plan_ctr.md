---
id: "misas.pantalla.imprimir_plan_ctr"
tipo: "pantalla_frontend"
subtipo: "descarga"
modulo: "misas"
nombre: "Imprimir Plan Ctr"
controller: "frontend/misas/controller/imprimir_plan_ctr.php"
vistas: ["frontend/misas/view/imprimir_plan_ctr.phtml"]
fragmentos_frontend: []
endpoints: ["/src/misas/ver_plan_ctr_data"]
capacidades: ["misas.ver_plan_ctr.gestionar"]
campos: ["post.empiezamax", "post.empiezamin", "post.id_ubi", "post.id_zona", "post.periodo"]
acciones: []
estado_revision: "revisado"
---

# Imprimir plan ctr

Generación PDF/mpdf del plan CTR a partir de `ver_plan_ctr`. Sin menú directo.

## Tipo

- Subtipo: `descarga`


- Controller: `frontend/misas/controller/imprimir_plan_ctr.php`

## Vistas Relacionadas

- `frontend/misas/view/imprimir_plan_ctr.phtml`

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
