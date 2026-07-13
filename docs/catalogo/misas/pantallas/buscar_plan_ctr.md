---
id: "misas.pantalla.buscar_plan_ctr"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "misas"
nombre: "Buscar Plan Ctr"
controller: "frontend/misas/controller/buscar_plan_ctr.php"
vistas: ["frontend/misas/view/buscar_plan_ctr.phtml", "frontend/misas/view/buscar_plan_un_ctr.phtml"]
fragmentos_frontend: ["frontend/misas/controller/buscar_plan_ctr.php", "frontend/misas/controller/ver_plan_ctr.php"]
endpoints: ["/src/misas/buscar_plan_ctr_data"]
capacidades: ["misas.buscar_plan_ctr.gestionar"]
campos: ["form.empiezamax", "form.empiezamin", "form.id_ubi", "form.id_zona", "form.periodo", "post.id_zona"]
acciones: ["fnjs_buscar_plan_ctr", "fnjs_ver_plan_ctr"]
estado_revision: "revisado"
---

# Buscar plan ctr

Buscador de plan por centro: zona y centro según rol (ctr/sacd/jefe).

## Tipo

- Subtipo: `pantalla_principal`


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

## Ruta de menú

- **Legacy:** dre > Misas > Plan centro
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Plan ctr
